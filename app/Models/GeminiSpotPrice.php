<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeminiSpotPrice extends Model
{
    use HasFactory;

    protected $table = 'geminis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bid',
        'ask',
        'last',
        'volume_btc',
        'volume_usd',
        'volume_timestamp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bid' => 'float',
        'ask' => 'float',
        'last' => 'float',
        'volume_btc' => 'float',
        'volume_usd' => 'float',
        'volume_timestamp' => 'integer',
    ];

    /**
     * Get a recent price record within the specified cache window
     * Returns null if no recent price exists
     *
     * @param int $cacheMinutes Number of minutes to look back
     * @return GeminiSpotPrice|null
     */
    public static function getRecent(int $cacheMinutes = 5): ?self
    {
        $cacheWindowStart = now()->subMinutes($cacheMinutes);
        
        return static::where('created_at', '>=', $cacheWindowStart)
            ->latest()
            ->first();
    }
}
