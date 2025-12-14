<?php

namespace App\Http\Controllers;

use App\Clients\BinanceClient;
use App\Models\BinanceSpotPrice;
use Illuminate\Http\Request;

class BinancePriceController extends Controller
{
    /**
     * Number of minutes to cache price data before hitting API again
     */
    private const CACHE_MINUTES = 5;

    public function index()
    {
        $client = new BinanceClient();
        
        // Check if we need to save a new price (only if 5+ minutes since last save)
        $recentPrice = BinanceSpotPrice::getRecent(self::CACHE_MINUTES);
        if (!$recentPrice) {
            // Fetch from API and save (always saves when hitting API)
            $recentPrice = $client->fetchAndSave();
        }
        
        // Get current spot price for display (use saved price if available, otherwise fetch fresh)
        $spot = null;
        if ($recentPrice) {
            $spot = $recentPrice->price;
        } else {
            // Fallback: fetch without saving if save failed but API works
            $spotData = $client->fetch();
            $spot = $spotData['price'] ?? null;
        }

        // Get price records from the last 24 hours
        $result = BinanceSpotPrice::where('created_at', '>=', now()->subDay())
            ->latest()
            ->get();

        // Format the data for display
        $result->each(function ($item, $key) {
            $item->price_description = '$' . number_format($item->price, 2);
            
            // The created_at is stored in America/Detroit timezone in the database as a string
            // Get the raw string value
            $dateString = $item->created_at instanceof \Carbon\Carbon 
                ? $item->created_at->format('Y-m-d H:i:s')
                : (string) $item->created_at;
            
            // Parse the date string as America/Detroit timezone
            $detroitTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dateString, 'America/Detroit');
            
            // Format the date string for display BEFORE changing timezone (format in Detroit time)
            $item->date = $detroitTime->toDayDateTimeString();
            
            // Convert to UTC timestamp for Highcharts
            // Highcharts will convert this UTC timestamp to the browser's local timezone for display
            $item->timestamp = $detroitTime->setTimezone('UTC')->timestamp * 1000;
        });

        // Calculate average and difference
        $average = $result->isNotEmpty() ? $result->pluck('price')->average() : 0;
        $diff_from_average = $result->isNotEmpty() && $result->first() 
            ? $result->first()->price - $average 
            : 0;

        return view('price-history.binance-index', [
            'spot' => $spot,
            'average' => $average,
            'diff_from_average' => $diff_from_average,
            'data' => $result,
        ]);
    }
}
