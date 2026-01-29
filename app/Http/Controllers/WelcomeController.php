<?php

namespace App\Http\Controllers;

use App\Clients\CoinbaseClient;
use App\Models\SpotPriceRequest;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Number of minutes to cache price data before hitting API again
     */
    private const CACHE_MINUTES = 5;

    public function __construct(
        private CoinbaseClient $client
    ) {}

    public function index()
    {
        // Check if we have a recent price in the database
        $savedPrice = SpotPriceRequest::getRecent('coinbase', self::CACHE_MINUTES);

        // If no recent price exists, fetch from API and save
        if (!$savedPrice) {
            $recentPrice = $this->client->fetch();

            // Check if API call was successful
            if ($recentPrice && isset($recentPrice['data'])) {
                // Save to SpotPriceRequest
                $savedPrice = SpotPriceRequest::create([
                    'exchange' => 'coinbase',
                    'price' => $recentPrice['data']['amount']
                ]);
            } else {
                // API call failed, return view with no data
                return view('welcome', ['data' => null]);
            }
        }

        // Format data for view (we now always have a savedPrice)
        $data = [
            'data' => [
                'amount' => $savedPrice->price,
                'currency' => 'USD',
                'base' => 'BTC',
            ]
        ];

        return view('welcome', [
            'data' => $data
        ]);
    }
}
