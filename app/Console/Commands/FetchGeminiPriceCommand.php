<?php

namespace App\Console\Commands;

use App\Clients\GeminiClient;
use Illuminate\Console\Command;

class FetchGeminiPriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gemini:fetch-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and save Gemini BTC price';

    public function __construct(
        private GeminiClient $client
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching Gemini price...');

        $result = $this->client->fetchAndSave();
        
        if ($result) {
            $this->info("Successfully saved Gemini price: $" . number_format($result->last, 2));
            return Command::SUCCESS;
        } else {
            $this->error('Failed to fetch and save Gemini price');
            return Command::FAILURE;
        }
    }
}
