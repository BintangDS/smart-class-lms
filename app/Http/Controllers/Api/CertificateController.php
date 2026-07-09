<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates for the authenticated student.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya siswa yang dapat memiliki sertifikat.',
            ], 403);
        }

        $certificates = Certificate::with(['course.instructor', 'course.category', 'user'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar sertifikat berhasil dimuat.',
            'data' => CertificateResource::collection($certificates)->response()->getData(true)
        ]);
    }

    /**
     * Verify a certificate code (Public access).
     */
    public function verify(string $code): JsonResponse
    {
        $certificate = Certificate::with(['user', 'course'])->where('certificate_code', $code)->first();

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'Sertifikat tidak valid atau tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sertifikat valid dan terverifikasi.',
            'data' => [
                'certificate_code' => $certificate->certificate_code,
                'student_name' => $certificate->user->name,
                'course_title' => $certificate->course->title,
                'file_url' => url('storage/' . $certificate->file_path),
                'issued_at' => $certificate->issued_at->toIso8601String(),
            ]
        ]);
    }
}
