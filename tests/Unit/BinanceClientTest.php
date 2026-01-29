<?php

namespace Tests\Unit;

use App\Clients\BinanceClient;
use App\Contracts\BinancePriceRepository;
use App\Models\BinanceSpotPrice;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BinanceClientTest extends TestCase
{
    /**
     * fetchAndSave() calls the repository create() with the expected payload when the API returns 200.
     * No database is used; we assert the persistence layer was invoked correctly.
     */
    public function test_fetch_and_save_calls_repository_create_with_payload_when_api_returns_200(): void
    {
        $endpoint = 'https://fake.example/api/v3/ticker/price?symbol=BTCUSDT';

        Http::fake([
            $endpoint => Http::response([
                'symbol' => 'BTCUSDT',
                'price' => '50000.25',
            ], 200),
        ]);

        $repository = $this->createMock(BinancePriceRepository::class);
        $savedModel = $this->createStub(BinanceSpotPrice::class);

        $repository->expects($this->once())
            ->method('create')
            ->with([
                'symbol' => 'BTCUSDT',
                'price' => 50000.25,
            ])
            ->willReturn($savedModel);

        $client = new BinanceClient(
            $endpoint,
            app(\Illuminate\Http\Client\Factory::class),
            $repository
        );

        $result = $client->fetchAndSave();

        $this->assertSame($savedModel, $result);
    }

    /**
     * fetchAndSave() does not call the repository when the API returns non-200.
     */
    public function test_fetch_and_save_does_not_call_repository_when_api_returns_non_200(): void
    {
        $endpoint = 'https://fake.example/api/v3/ticker/price?symbol=BTCUSDT';

        Http::fake([
            $endpoint => Http::response('Server Error', 500),
        ]);

        $repository = $this->createMock(BinancePriceRepository::class);
        $repository->expects($this->never())->method('create');

        $client = new BinanceClient(
            $endpoint,
            app(\Illuminate\Http\Client\Factory::class),
            $repository
        );

        $result = $client->fetchAndSave();

        $this->assertNull($result);
    }
}
