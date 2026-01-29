<?php

namespace App\Http\Controllers;

use App\Clients\CoinbaseClient;
use App\Models\CoinbaseSpotPrice;
use App\Models\SpotPriceRequest;
use Illuminate\Http\Request;

class CoinbasePriceController extends Controller
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
        // Check if we need to save a new price (only if 5+ minutes since last save)
        $recentPrice = SpotPriceRequest::getRecent('coinbase', self::CACHE_MINUTES);
        if (!$recentPrice) {
            // Fetch from API and save to SpotPriceRequest
            $apiData = $this->client->fetch();
            if ($apiData && isset($apiData['data'])) {
                $recentPrice = SpotPriceRequest::create([
                    'exchange' => 'coinbase',
                    'price' => $apiData['data']['amount']
                ]);
            }
        }
        
        // Get current spot price for display (use saved price if available, otherwise fetch fresh)
        $spot = null;
        if ($recentPrice) {
            $spot = $recentPrice->price;
        } else {
            // Fallback: fetch without saving if save failed but API works
            $spotData = $this->client->fetch();
            $spot = $spotData['data']['amount'] ?? null;
        }

        // Get price records from the last 24 hours
        $result = CoinbaseSpotPrice::where('created_at', '>=', now()->subDay())
            ->latest()
            ->get();

        // Format the data for display
        $result->each(function ($item, $key) {
            $item->price_description = '$' . number_format($item->amount, 2);
            
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
        $average = $result->isNotEmpty() ? $result->pluck('amount')->average() : 0;
        $diff_from_average = $result->isNotEmpty() && $result->first() 
            ? $result->first()->amount - $average 
            : 0;

        return view('price-history.coinbase.index', [
            'spot' => $spot,
            'average' => $average,
            'diff_from_average' => $diff_from_average,
            'data' => $result,
        ]);
    }

    public function show($period)
    {
        // Map period to Carbon time range
        $periods = [
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
        ];

        // Default to week if invalid period provided
        $startDate = $periods[$period] ?? $periods['week'];

        // Check if we need to save a new price (only if 5+ minutes since last save)
        $recentPrice = SpotPriceRequest::getRecent('coinbase', self::CACHE_MINUTES);
        if (!$recentPrice) {
            // Fetch from API and save to SpotPriceRequest
            $apiData = $this->client->fetch();
            if ($apiData && isset($apiData['data'])) {
                $recentPrice = SpotPriceRequest::create([
                    'exchange' => 'coinbase',
                    'price' => $apiData['data']['amount']
                ]);
            }
        }

        // Get current spot price for display (use saved price if available, otherwise fetch fresh)
        $spot = null;
        if ($recentPrice) {
            $spot = $recentPrice->price;
        } else {
            // Fallback: fetch without saving if save failed but API works
            $spotData = $this->client->fetch();
            $spot = $spotData['data']['amount'] ?? null;
        }

        // Get all price records for the specified period
        $rawData = CoinbaseSpotPrice::where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->get();

        // Group by hour in Detroit timezone and calculate averages
        $hourlyData = $rawData->groupBy(function ($item) {
            $dateString = $item->created_at instanceof \Carbon\Carbon
                ? $item->created_at->format('Y-m-d H:i:s')
                : (string) $item->created_at;

            // Parse as Detroit timezone and get hour start
            $detroitTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dateString, 'America/Detroit');
            return $detroitTime->format('Y-m-d H:00:00');
        })->map(function ($hourGroup, $hourKey) {
            $avgAmount = $hourGroup->avg('amount');

            // Create a simple object to mimic Eloquent model behavior
            $hourlyRecord = new \stdClass();
            $hourlyRecord->hour_start = $hourKey;
            $hourlyRecord->amount = $avgAmount;
            $hourlyRecord->data_points = $hourGroup->count();
            $hourlyRecord->first_timestamp = $hourGroup->first()->created_at;

            // Format for display (same as original index method)
            $hourlyRecord->price_description = '$' . number_format($avgAmount, 2);

            // Parse the hour_start as Detroit timezone
            $detroitTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $hourKey, 'America/Detroit');

            // Format the date string for display BEFORE changing timezone (format in Detroit time)
            $hourlyRecord->date = $detroitTime->toDayDateTimeString();

            // Convert to UTC timestamp for Highcharts
            // Highcharts will convert this UTC timestamp to the browser's local timezone for display
            $hourlyRecord->timestamp = $detroitTime->setTimezone('UTC')->timestamp * 1000;

            return $hourlyRecord;
        })->sortBy('hour_start');

        // Convert to collection for consistency with existing code
        $result = collect($hourlyData->values());

        // Calculate average and difference (using hourly averages)
        $average = $result->isNotEmpty() ? $result->pluck('amount')->average() : 0;
        $diff_from_average = $result->isNotEmpty() && $result->first()
            ? $result->first()->amount - $average
            : 0;

        return view('price-history.coinbase.show', [
            'spot' => $spot,
            'average' => $average,
            'diff_from_average' => $diff_from_average,
            'data' => $result,
            'period' => $period, // Pass period to view for display
        ]);
    }
}
