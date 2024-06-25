<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreExportDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $userId,
        protected string $filename
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * Save export into our DB.
         */
        $user = User::findOrFail($this->userId);

        $user->exports()->create([
            "file_name" => $this->filename,
            "user_id" => $this->userId
        ]);
    }
}
