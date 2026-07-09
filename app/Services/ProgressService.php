<?php

namespace App\Services;

use App\Models\Enrollment;

class ProgressService
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Update progress percent and completion status for an enrollment.
     */
    public function updateProgress(Enrollment $enrollment): int
    {
        $course = $enrollment->course;
        
        // Eager load modules and lessons to avoid N+1 query
        $course->load('modules.lessons');

        // FlatMap modules to get all lesson IDs in the course
        $totalLessons = $course->modules->flatMap(function ($module) {
            return $module->lessons;
        })->count();

        if ($totalLessons === 0) {
            $enrollment->progress_percent = 0;
            $enrollment->save();
            return 0;
        }

        $completedLessons = $enrollment->lessonProgress()->count();

        // Calculate progress percentage
        $progressPercent = (int) round(($completedLessons / $totalLessons) * 100);
        $enrollment->progress_percent = min($progressPercent, 100);

        // Flag completed_at if progress is 100%
        if ($enrollment->progress_percent >= 100) {
            if (!$enrollment->completed_at) {
                $enrollment->completed_at = now();
            }
            // Generate certificate
            $this->certificateService->generate($enrollment);
        } else {
            $enrollment->completed_at = null;
        }

        $enrollment->save();

        return $enrollment->progress_percent;
    }
}
