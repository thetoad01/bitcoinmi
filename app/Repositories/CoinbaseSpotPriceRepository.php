<?php

namespace App\Repositories;

use App\Contracts\CoinbasePriceRepository;
use App\Models\CoinbaseSpotPrice;

class CoinbaseSpotPriceRepository implements CoinbasePriceRepository
{
    /**
     * Persist a Coinbase spot price record.
     *
     * @param  array{coin: string, currency: string, amount: float}  $data
     * @return CoinbaseSpotPrice
     */
    public function create(array $data): CoinbaseSpotPrice
    {
        return CoinbaseSpotPrice::create($data);
    }
}
