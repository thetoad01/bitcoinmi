<?php

namespace App\Repositories;

use App\Contracts\GeminiPriceRepository;
use App\Models\GeminiSpotPrice;

class GeminiSpotPriceRepository implements GeminiPriceRepository
{
    /**
     * Persist a Gemini spot price record.
     *
     * @param  array{bid: float, ask: float, last: float, volume_btc: float, volume_usd: float, volume_timestamp: int}  $data
     * @return GeminiSpotPrice
     */
    public function create(array $data): GeminiSpotPrice
    {
        return GeminiSpotPrice::create($data);
    }
}
