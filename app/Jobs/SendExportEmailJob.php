<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\ExportEmail;
use Illuminate\Support\Facades\Mail;

class SendExportEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $filename
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * Send report to user.
         */
        Mail::to('test@test.com')
            // Aqui podemos apenas utilizar ->queue()!
            ->send(new ExportEmail($this->filename));
    }
}
