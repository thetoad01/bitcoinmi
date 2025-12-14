<?php

namespace App\Jobs;

use App\Clients\GeminiClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchGeminiPrice implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = new GeminiClient();
        $result = $client->fetchAndSave();
        
        if ($result) {
            Log::info('Gemini price fetched and saved successfully', [
                'id' => $result->id,
                'last' => $result->last,
                'created_at' => $result->created_at
            ]);
        } else {
            Log::warning('Gemini price fetch and save returned null');
        }
    }
}
