<?php

namespace App\Clients;

use App\Models\CoinbaseSpotPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoinbaseClient
{
    protected $endpoint;

    public function __construct()
    {
        $this->endpoint = 'https://api.coinbase.com/v2/prices/BTC-USD/spot';
    }

    /**
     * Fetch price from Coinbase API and save to database
     *
     * @return CoinbaseSpotPrice|null Returns the model instance on success, null on failure
     */
    public function fetchAndSave(): ?CoinbaseSpotPrice
    {
        try {
            // 1. Hit the API endpoint
            $response = Http::get($this->endpoint);

            // 2. Confirm response status
            if ($response->status() !== 200) {
                Log::warning('Coinbase API returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            // 3. Confirm we have data with expected structure
            if (!isset($data['data']['base']) || 
                !isset($data['data']['currency']) || 
                !isset($data['data']['amount'])) {
                Log::warning('Coinbase API response missing expected data structure', [
                    'response' => $data
                ]);
                return null;
            }

            // 4. Write data to table (timestamps will be in America/Detroit as per app timezone)
            $coinbasePrice = CoinbaseSpotPrice::create([
                'coin' => $data['data']['base'],
                'currency' => $data['data']['currency'],
                'amount' => (float) $data['data']['amount'],
            ]);

            return $coinbasePrice;

        } catch (\Throwable $e) {
            Log::error('Error fetching Coinbase price', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Fetch price from Coinbase API without saving to database
     * Useful for just retrieving current price
     *
     * @return array|null Returns the API response data or null on failure
     */
    public function fetch(): ?array
    {
        try {
            $response = Http::get($this->endpoint);

            if ($response->status() !== 200) {
                Log::warning('Coinbase API returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            if (!isset($data['data'])) {
                Log::warning('Coinbase API response missing data key', [
                    'response' => $data
                ]);
                return null;
            }

            return $data;

        } catch (\Throwable $e) {
            Log::error('Error fetching Coinbase price', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
