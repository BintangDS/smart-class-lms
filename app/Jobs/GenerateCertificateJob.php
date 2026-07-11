<?php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Services\CertificateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Enrollment $enrollment
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CertificateService $certificateService): void
    {
        $certificateService->generate($this->enrollment);
    }
}
