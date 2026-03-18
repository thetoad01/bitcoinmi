<?php

namespace Tests\Unit;

use App\Clients\CoinbaseClient;
use App\Contracts\CoinbasePriceRepository;
use App\Models\CoinbaseSpotPrice;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CoinbaseClientTest extends TestCase
{
    /**
     * fetchAndSave() calls the repository create() with the expected payload when the API returns 200.
     * No database is used; we assert the persistence layer was invoked correctly.
     */
    public function test_fetch_and_save_calls_repository_create_with_payload_when_api_returns_200(): void
    {
        $endpoint = 'https://fake.example/v2/prices/BTC-USD/spot';

        Http::fake([
            $endpoint => Http::response([
                'data' => [
                    'base' => 'BTC',
                    'currency' => 'USD',
                    'amount' => '50000.25',
                ],
            ], 200),
        ]);

        $repository = $this->createMock(CoinbasePriceRepository::class);
        $savedModel = $this->createStub(CoinbaseSpotPrice::class);

        $repository->expects($this->once())
            ->method('create')
            ->with([
                'coin' => 'BTC',
                'currency' => 'USD',
                'amount' => 50000.25,
            ])
            ->willReturn($savedModel);

        $client = new CoinbaseClient(
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
        $endpoint = 'https://fake.example/v2/prices/BTC-USD/spot';

        Http::fake([
            $endpoint => Http::response('Server Error', 500),
        ]);

        $repository = $this->createMock(CoinbasePriceRepository::class);
        $repository->expects($this->never())->method('create');

        $client = new CoinbaseClient(
            $endpoint,
            app(\Illuminate\Http\Client\Factory::class),
            $repository
        );

        $result = $client->fetchAndSave();

        $this->assertNull($result);
    }
}
