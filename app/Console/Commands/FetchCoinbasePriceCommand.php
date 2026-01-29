<?php

namespace App\Console\Commands;

use App\Clients\CoinbaseClient;
use Illuminate\Console\Command;

class FetchCoinbasePriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coinbase:fetch-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and save Coinbase BTC price';

    public function __construct(
        private CoinbaseClient $client
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching Coinbase price...');

        $result = $this->client->fetchAndSave();
        
        if ($result) {
            $this->info("Successfully saved Coinbase price: $" . number_format($result->amount, 2));
            return Command::SUCCESS;
        } else {
            $this->error('Failed to fetch and save Coinbase price');
            return Command::FAILURE;
        }
    }
}
