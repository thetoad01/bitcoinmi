<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinanceSpotPrice extends Model
{
    use HasFactory;

    protected $table = 'binances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'symbol',
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
     * Get a recent price record within the specified cache window
     * Returns null if no recent price exists
     *
     * @param int $cacheMinutes Number of minutes to look back
     * @return BinanceSpotPrice|null
     */
    public static function getRecent(int $cacheMinutes = 5): ?self
    {
        $cacheWindowStart = now()->subMinutes($cacheMinutes);
        
        return static::where('created_at', '>=', $cacheWindowStart)
            ->latest()
            ->first();
    }
}
