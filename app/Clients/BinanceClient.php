<?php

namespace App\Clients;

use App\Models\BinanceSpotPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BinanceClient
{
    protected $endpoint;

    public function __construct()
    {
        $this->endpoint = 'https://api.binance.us/api/v3/ticker/price?symbol=BTCUSDT';
    }

    /**
     * Fetch price from Binance API and save to database
     *
     * @return BinanceSpotPrice|null Returns the model instance on success, null on failure
     */
    public function fetchAndSave(): ?BinanceSpotPrice
    {
        try {
            // 1. Hit the API endpoint
            $response = Http::get($this->endpoint);

            // 2. Confirm response status
            if ($response->status() !== 200) {
                Log::warning('Binance API returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            // 3. Confirm we have data with expected structure
            if (!isset($data['symbol']) || !isset($data['price'])) {
                Log::warning('Binance API response missing expected data structure', [
                    'response' => $data
                ]);
                return null;
            }

            // 4. Write data to table (timestamps will be in America/Detroit as per app timezone)
            $binancePrice = BinanceSpotPrice::create([
                'symbol' => $data['symbol'],
                'price' => (float) $data['price'],
            ]);

            return $binancePrice;

        } catch (\Throwable $e) {
            Log::error('Error fetching Binance price', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Fetch price from Binance API
     *
     * @return array|null Returns the API response data or null on failure
     */
    public function fetch(): ?array
    {
        try {
            $response = Http::get($this->endpoint);

            if ($response->status() !== 200) {
                Log::warning('Binance API returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            // Validate response structure
            if (!isset($data['price'])) {
                Log::warning('Binance API response missing expected data structure', [
                    'response' => $data
                ]);
                return null;
            }

            return $data;

        } catch (\Throwable $e) {
            Log::error('Error fetching Binance price', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get the price from Binance API
     *
     * @return float|null Returns the price or null on failure
     */
    public function getPrice(): ?float
    {
        $data = $this->fetch();
        return $data ? (float) $data['price'] : null;
    }
}
