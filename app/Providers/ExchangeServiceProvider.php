<?php

namespace App\Providers;

use App\Clients\BinanceClient;
use App\Clients\CoinbaseClient;
use App\Clients\GeminiClient;
use App\Contracts\BinancePriceRepository;
use App\Contracts\CoinbasePriceRepository;
use App\Contracts\GeminiPriceRepository;
use App\Repositories\BinanceSpotPriceRepository;
use App\Repositories\CoinbaseSpotPriceRepository;
use App\Repositories\GeminiSpotPriceRepository;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\ServiceProvider;

class ExchangeServiceProvider extends ServiceProvider
{
    /**
     * Register exchange-related bindings (Binance, Coinbase, Gemini clients).
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BinancePriceRepository::class, BinanceSpotPriceRepository::class);
        $this->app->bind(CoinbasePriceRepository::class, CoinbaseSpotPriceRepository::class);
        $this->app->bind(GeminiPriceRepository::class, GeminiSpotPriceRepository::class);

        $this->app->bind(BinanceClient::class, function ($app) {
            return new BinanceClient(
                config('services.binance.endpoint'),
                $app->make(HttpClient::class),
                $app->make(BinancePriceRepository::class)
            );
        });

        $this->app->bind(CoinbaseClient::class, function ($app) {
            return new CoinbaseClient(
                config('services.coinbase.endpoint'),
                $app->make(HttpClient::class),
                $app->make(CoinbasePriceRepository::class)
            );
        });

        $this->app->bind(GeminiClient::class, function ($app) {
            return new GeminiClient(
                config('services.gemini.endpoint'),
                $app->make(HttpClient::class),
                $app->make(GeminiPriceRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
