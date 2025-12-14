<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinbaseSpotPrice extends Model
{
    use HasFactory;

    protected $table = 'coinbases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'coin',
        'currency',
        'amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'float',
    ];

    /**
     * Get a recent price record within the specified cache window
     * Returns null if no recent price exists
     *
     * @param int $cacheMinutes Number of minutes to look back
     * @return CoinbaseSpotPrice|null
     */
    public static function getRecent(int $cacheMinutes = 5): ?self
    {
        $cacheWindowStart = now()->subMinutes($cacheMinutes);
        
        return static::where('created_at', '>=', $cacheWindowStart)
            ->latest()
            ->first();
    }
}
