<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotPriceRequest extends Model
{
    use HasFactory;

    protected $table = 'spot_price_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exchange',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
    ];

    /**
     * Get a recent price record within the specified cache window for a specific exchange
     * Returns null if no recent price exists
     *
     * @param string $exchange Exchange name ('coinbase', 'binance', 'gemini')
     * @param int $cacheMinutes Number of minutes to look back
     * @return SpotPriceRequest|null
     */
    public static function getRecent(string $exchange, int $cacheMinutes = 5): ?self
    {
        $cacheWindowStart = now()->subMinutes($cacheMinutes);
        
        return static::where('exchange', $exchange)
            ->where('created_at', '>=', $cacheWindowStart)
            ->latest()
            ->first();
    }
}
