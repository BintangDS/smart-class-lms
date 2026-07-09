<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Generate completion certificate for an enrollment.
     */
    public function generate(Enrollment $enrollment): Certificate
    {
        $enrollment->load(['user', 'course']);

        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', $enrollment->user_id)
            ->where('course_id', $enrollment->course_id)
            ->first();

        if ($existingCertificate) {
            return $existingCertificate;
        }

        // Generate unique code
        do {
            $code = 'CERT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (Certificate::where('certificate_code', $code)->exists());

        // Create mock PDF certificate file
        $filePath = "certificates/{$code}.pdf";
        $content = "CERTIFICATE OF COMPLETION\n\n"
                 . "This is to certify that\n"
                 . "{$enrollment->user->name}\n"
                 . "has successfully completed the course\n"
                 . "{$enrollment->course->title}\n\n"
                 . "Issued on: " . now()->toDateString() . "\n"
                 . "Verification Code: {$code}";

        Storage::disk('public')->put($filePath, $content);

        // Save certificate details
        return Certificate::create([
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'certificate_code' => $code,
            'file_path' => $filePath,
            'issued_at' => now(),
        ]);
    }
}
