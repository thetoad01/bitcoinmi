<?php

namespace App\Http\Controllers;

use App\Clients\CoinbaseClient;
use App\Models\CoinbaseSpotPrice;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Number of minutes to cache price data before hitting API again
     */
    private const CACHE_MINUTES = 5;

    public function index()
    {
        $client = new CoinbaseClient();
        
        // Check if we have a recent price in the database
        $savedPrice = CoinbaseSpotPrice::getRecent(self::CACHE_MINUTES);
        
        // If no recent price exists, fetch from API (which always saves)
        if (!$savedPrice) {
            $savedPrice = $client->fetchAndSave();
        }
        
        // Format data for view
        $data = null;
        if ($savedPrice) {
            // Use saved model data
            $data = [
                'data' => [
                    'amount' => $savedPrice->amount,
                    'currency' => $savedPrice->currency,
                    'base' => $savedPrice->coin,
                ]
            ];
        }

        return view('welcome', [
            'data' => $data
        ]);
    }
}
