<?php

namespace App\Clients;

use App\Contracts\BinancePriceRepository;
use App\Models\BinanceSpotPrice;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Facades\Log;

class BinanceClient
{
    public function __construct(
        private string $endpoint,
        private ?HttpClient $http = null,
        private ?BinancePriceRepository $repository = null
    ) {
        $this->http ??= app(HttpClient::class);
        $this->repository ??= app(BinancePriceRepository::class);
    }

    /**
     * Fetch price from Binance API and save to database
     *
     * @return BinanceSpotPrice|null Returns the model instance on success, null on failure
     */
    public function fetchAndSave(): ?BinanceSpotPrice
    {
        try {
            $response = $this->http->get($this->endpoint);

            if ($response->status() !== 200) {
                Log::warning('Binance API returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            if (!isset($data['symbol']) || !isset($data['price'])) {
                Log::warning('Binance API response missing expected data structure', [
                    'response' => $data
                ]);
                return null;
            }

            // Timestamps stored in America/Detroit per app timezone.
            $binancePrice = $this->repository->create([
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
            $response = $this->http->get($this->endpoint);

            if ($response->status() !== 200) {
                Log::warning('Binance API returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

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
