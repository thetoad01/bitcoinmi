<?php

namespace App\Contracts;

use App\Models\BinanceSpotPrice;

interface BinancePriceRepository
{
    /**
     * Persist a Binance spot price record.
     *
     * @param  array{symbol: string, price: float}  $data
     * @return BinanceSpotPrice
     */
    public function create(array $data): BinanceSpotPrice;
}
