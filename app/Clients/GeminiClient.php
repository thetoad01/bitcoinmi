<?php

namespace App\Clients;

use App\Models\GeminiSpotPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiClient
{
    protected $endpoint;

    public function __construct()
    {
        $this->endpoint = 'https://api.gemini.com/v1/pubticker/BTCUSD';
    }

    /**
     * Fetch price from Gemini API and save to database
     *
     * @return GeminiSpotPrice|null Returns the model instance on success, null on failure
     */
    public function fetchAndSave(): ?GeminiSpotPrice
    {
        try {
            // 1. Hit the API endpoint
            $response = Http::get($this->endpoint);

            // 2. Confirm response status
            if ($response->status() !== 200) {
                Log::warning('Gemini API returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            // 3. Confirm we have data with expected structure
            if (!isset($data['bid']) || 
                !isset($data['ask']) || 
                !isset($data['last']) ||
                !isset($data['volume']['BTC']) ||
                !isset($data['volume']['USD']) ||
                !isset($data['volume']['timestamp'])) {
                Log::warning('Gemini API response missing expected data structure', [
                    'response' => $data
                ]);
                return null;
            }

            // 4. Write data to table (timestamps will be in America/Detroit as per app timezone)
            $geminiPrice = GeminiSpotPrice::create([
                'bid' => (float) $data['bid'],
                'ask' => (float) $data['ask'],
                'last' => (float) $data['last'],
                'volume_btc' => (float) $data['volume']['BTC'],
                'volume_usd' => (float) $data['volume']['USD'],
                'volume_timestamp' => (int) $data['volume']['timestamp'],
            ]);

            return $geminiPrice;

        } catch (\Throwable $e) {
            Log::error('Error fetching Gemini price', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Fetch price from Gemini API
     *
     * @return array|null Returns the API response data or null on failure
     */
    public function fetch(): ?array
    {
        try {
            $response = Http::get($this->endpoint);

            if ($response->status() !== 200) {
                Log::warning('Gemini API returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            // Validate response structure
            if (!isset($data['last'])) {
                Log::warning('Gemini API response missing expected data structure', [
                    'response' => $data
                ]);
                return null;
            }

            return $data;

        } catch (\Throwable $e) {
            Log::error('Error fetching Gemini price', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get the last trade price from Gemini API
     *
     * @return float|null Returns the last price or null on failure
     */
    public function getLastPrice(): ?float
    {
        $data = $this->fetch();
        return $data ? (float) $data['last'] : null;
    }

    /**
     * Get bid price from Gemini API
     *
     * @return float|null Returns the bid price or null on failure
     */
    public function getBidPrice(): ?float
    {
        $data = $this->fetch();
        return $data && isset($data['bid']) ? (float) $data['bid'] : null;
    }

    /**
     * Get ask price from Gemini API
     *
     * @return float|null Returns the ask price or null on failure
     */
    public function getAskPrice(): ?float
    {
        $data = $this->fetch();
        return $data && isset($data['ask']) ? (float) $data['ask'] : null;
    }
}