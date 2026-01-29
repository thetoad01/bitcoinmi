<?php

namespace Tests\Unit;

use App\Clients\GeminiClient;
use App\Contracts\GeminiPriceRepository;
use App\Models\GeminiSpotPrice;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiClientTest extends TestCase
{
    /**
     * fetchAndSave() calls the repository create() with the expected payload when the API returns 200.
     * No database is used; we assert the persistence layer was invoked correctly.
     */
    public function test_fetch_and_save_calls_repository_create_with_payload_when_api_returns_200(): void
    {
        $endpoint = 'https://fake.example/v1/pubticker/BTCUSD';

        Http::fake([
            $endpoint => Http::response([
                'bid' => '49999.50',
                'ask' => '50000.75',
                'last' => '50000.25',
                'volume' => [
                    'BTC' => '1234.56',
                    'USD' => '61728000.00',
                    'timestamp' => 1706544000000,
                ],
            ], 200),
        ]);

        $repository = $this->createMock(GeminiPriceRepository::class);
        $savedModel = $this->createStub(GeminiSpotPrice::class);

        $repository->expects($this->once())
            ->method('create')
            ->with([
                'bid' => 49999.5,
                'ask' => 50000.75,
                'last' => 50000.25,
                'volume_btc' => 1234.56,
                'volume_usd' => 61728000.0,
                'volume_timestamp' => 1706544000000,
            ])
            ->willReturn($savedModel);

        $client = new GeminiClient(
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
        $endpoint = 'https://fake.example/v1/pubticker/BTCUSD';

        Http::fake([
            $endpoint => Http::response('Server Error', 500),
        ]);

        $repository = $this->createMock(GeminiPriceRepository::class);
        $repository->expects($this->never())->method('create');

        $client = new GeminiClient(
            $endpoint,
            app(\Illuminate\Http\Client\Factory::class),
            $repository
        );

        $result = $client->fetchAndSave();

        $this->assertNull($result);
    }
}
