<?php

namespace App\Repositories;

use App\Contracts\BinancePriceRepository;
use App\Models\BinanceSpotPrice;

class BinanceSpotPriceRepository implements BinancePriceRepository
{
    /**
     * Persist a Binance spot price record.
     *
     * @param  array{symbol: string, price: float}  $data
     * @return BinanceSpotPrice
     */
    public function create(array $data): BinanceSpotPrice
    {
        return BinanceSpotPrice::create($data);
    }
}
