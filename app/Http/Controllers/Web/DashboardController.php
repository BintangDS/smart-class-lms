<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Redirect or show appropriate dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->adminDashboard($request);
        } elseif ($user->role === 'instructor') {
            return $this->instructorDashboard($request);
        } else {
            return $this->studentDashboard($request);
        }
    }

    /**
     * Admin Dashboard View.
     */
    protected function adminDashboard(Request $request)
    {
        $totalUsers = User::count();
        $totalCourses = Course::count();
        
        // Avg completion rate
        $avgCompletion = Enrollment::avg('progress_percent') ?? 0;

        $users = User::latest()->paginate(10, ['*'], 'users_page');
        $courses = Course::with(['instructor', 'category'])->latest()->paginate(10, ['*'], 'courses_page');

        return view('dashboard.admin', compact('totalUsers', 'totalCourses', 'avgCompletion', 'users', 'courses'));
    }

    /**
     * Instructor Dashboard View.
     */
    protected function instructorDashboard(Request $request)
    {
        $instructorId = Auth::id();

        // Instructor courses
        $courses = Course::where('instructor_id', $instructorId)
            ->withCount('enrollments')
            ->with(['category'])
            ->latest()
            ->get();

        $totalCourses = $courses->count();

        // Total students enrolled in all instructor's courses
        $totalStudents = Enrollment::whereIn('course_id', $courses->pluck('id'))->count();

        // Submissions that need grading
        $submissionsNeedGrading = AssignmentSubmission::whereNull('score')
            ->whereHas('assignment.module.course', function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
            })
            ->with(['user', 'assignment.module.course'])
            ->latest()
            ->get();

        return view('dashboard.instructor', compact('courses', 'totalCourses', 'totalStudents', 'submissionsNeedGrading'));
    }

    /**
     * Student Dashboard View (Course Catalog + Enrolled Courses).
     */
    protected function studentDashboard(Request $request)
    {
        $student = Auth::user();

        // My Enrolled Courses
        $myEnrollments = Enrollment::where('user_id', $student->id)
            ->with(['course.instructor', 'course.category'])
            ->latest()
            ->get();

        // My Certificates
        $myCertificates = $student->certificates()->with('course')->get();

        // Catalog of available published courses that the student has not enrolled in yet
        $enrolledCourseIds = $myEnrollments->pluck('course_id');
        
        // Fetch all assignments for enrolled courses, ordered by nearest due date
        $myAssignments = \App\Models\Assignment::whereIn('module_id', function ($query) use ($enrolledCourseIds) {
            $query->select('id')
                  ->from('modules')
                  ->whereIn('course_id', $enrolledCourseIds);
        })
        ->with(['module.course', 'submissions' => function ($query) use ($student) {
            $query->where('user_id', $student->id);
        }])
        ->orderBy('due_date', 'asc')
        ->get();
        
        $query = Course::where('status', 'published')
            ->whereNotIn('id', $enrolledCourseIds)
            ->with(['instructor', 'category']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $catalogCourses = $query->latest()->paginate(9);
        $categories = \App\Models\Category::all();

        return view('dashboard.student', compact('myEnrollments', 'myCertificates', 'catalogCourses', 'categories', 'myAssignments'));
    }
}
