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

        // Create a valid minimal PDF 1.4 file programmatically
        $filePath = "certificates/{$code}.pdf";
        
        $userName = $enrollment->user->name;
        $courseTitle = $enrollment->course->title;
        $dateStr = now()->toDateString();
        
        // Escape parenthesis for PDF text compatibility
        $userNameEscaped = str_replace(['(', ')'], ['\\(', '\\)'], $userName);
        $courseTitleEscaped = str_replace(['(', ')'], ['\\(', '\\)'], $courseTitle);

        $stream = "BT\n"
                . "/F1 24 Tf\n"
                . "70 700 Td\n"
                . "(CERTIFICATE OF COMPLETION) Tj\n"
                . "/F1 14 Tf\n"
                . "0 -50 Td\n"
                . "(This is to certify that) Tj\n"
                . "0 -30 Td\n"
                . "({$userNameEscaped}) Tj\n"
                . "0 -30 Td\n"
                . "(has successfully completed the course) Tj\n"
                . "0 -30 Td\n"
                . "({$courseTitleEscaped}) Tj\n"
                . "0 -50 Td\n"
                . "(Issued on: {$dateStr}) Tj\n"
                . "0 -20 Td\n"
                . "(Verification Code: {$code}) Tj\n"
                . "ET";

        $pdf = "%PDF-1.4\n"
             . "1 0 obj <</Type /Catalog /Pages 2 0 R>> endobj\n"
             . "2 0 obj <</Type /Pages /Kids [3 0 R] /Count 1>> endobj\n"
             . "3 0 obj <</Type /Page /Parent 2 0 R /Resources <</Font <</F1 4 0 R>>>> /MediaBox [0 0 595 842] /Contents 5 0 R>> endobj\n"
             . "4 0 obj <</Type /Font /Subtype /Type1 /BaseFont /Helvetica>> endobj\n"
             . "5 0 obj <</Length " . strlen($stream) . ">> stream\n" . $stream . "\nendstream\nendobj\n"
             . "xref\n"
             . "0 6\n"
             . "0000000000 65535 f\n"
             . "trailer <</Size 6 /Root 1 0 R>>\n"
             . "startxref\n"
             . "120\n"
             . "%%EOF";

        Storage::disk('public')->put($filePath, $pdf);

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
