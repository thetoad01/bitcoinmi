<?php

namespace App\Contracts;

use App\Models\CoinbaseSpotPrice;

interface CoinbasePriceRepository
{
    /**
     * Persist a Coinbase spot price record.
     *
     * @param  array{coin: string, currency: string, amount: float}  $data
     * @return CoinbaseSpotPrice
     */
    public function create(array $data): CoinbaseSpotPrice;
}
