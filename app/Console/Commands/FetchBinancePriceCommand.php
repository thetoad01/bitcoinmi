<?php

namespace App\Console\Commands;

use App\Clients\BinanceClient;
use Illuminate\Console\Command;

class FetchBinancePriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'binance:fetch-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and save Binance BTC price';

    public function __construct(
        private BinanceClient $client
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching Binance price...');

        $result = $this->client->fetchAndSave();
        
        if ($result) {
            $this->info("Successfully saved Binance price: $" . number_format($result->price, 2));
            return Command::SUCCESS;
        } else {
            $this->error('Failed to fetch and save Binance price');
            return Command::FAILURE;
        }
    }
}
