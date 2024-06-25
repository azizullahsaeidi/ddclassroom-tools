<?php

namespace App\Jobs;

use App\Mail\StudentEmail;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Student $student,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $studentEmail = new StudentEmail($this->student);

        Mail::to($this->student->email)->send($studentEmail);
    }
}
