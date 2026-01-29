<?php

namespace App\Clients;

use App\Contracts\CoinbasePriceRepository;
use App\Models\CoinbaseSpotPrice;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Facades\Log;

class CoinbaseClient
{
    public function __construct(
        private string $endpoint,
        private ?HttpClient $http = null,
        private ?CoinbasePriceRepository $repository = null
    ) {
        $this->http ??= app(HttpClient::class);
        $this->repository ??= app(CoinbasePriceRepository::class);
    }

    /**
     * Fetch price from Coinbase API and save to database
     *
     * @return CoinbaseSpotPrice|null Returns the model instance on success, null on failure
     */
    public function fetchAndSave(): ?CoinbaseSpotPrice
    {
        try {
            $response = $this->http->get($this->endpoint);

            if ($response->status() !== 200) {
                Log::warning('Coinbase API returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            if (!isset($data['data']['base']) ||
                !isset($data['data']['currency']) ||
                !isset($data['data']['amount'])) {
                Log::warning('Coinbase API response missing expected data structure', [
                    'response' => $data
                ]);
                return null;
            }

            // Timestamps stored in America/Detroit per app timezone.
            $coinbasePrice = $this->repository->create([
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
            $response = $this->http->get($this->endpoint);

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
