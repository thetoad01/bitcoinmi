<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
        $rows = DB::connection('coinbase_sqlite')->table('coinbases')->orderBy('created_at', 'asc')->take(10)->get();

        $result = [];

        foreach ($rows as $r) {
            $result[] = [
                'id' => $this->ulidFromCreatedAtUtc(
                    $r->created_at,
                    $r->id,
                    1 // source id
                ),
                'old_id' => $r->id,
                'coin' => $r->coin,
                'currency' => $r->currency,
                'amount' => $r->amount,
                'created_at' => $r->created_at,
                'updated_at' => $r->updated_at,
            ];
        }

        return response()->json($result);
    }

    private function ulidFromCreatedAtUtc(string $createdAt, int $oldId, int $sourceId = 1): string 
    {
        // Parse as UTC (your table is already UTC)
        $dt = CarbonImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $createdAt,
            'UTC'
        )->utc();

        $ms = ((int) $dt->format('U')) * 1000 + (int) $dt->format('v');

        // 48-bit time -> 10 chars
        $timePart = $this->base32EncodeInt($ms, 10);

        // 80-bit entropy:
        // 2 bytes sourceId + 8 bytes oldId (big-endian)
        $entropyBytes =
            pack('n', $sourceId) . $this->packUint64BE($oldId);

        $randPart = $this->base32EncodeBytes($entropyBytes, 16);

        return $timePart . $randPart;
    }

    private function packUint64BE(int $n): string
    {
        $hi = ($n >> 32) & 0xFFFFFFFF;
        $lo = $n & 0xFFFFFFFF;
        return pack('N2', $hi, $lo);
    }

    private function base32EncodeInt(int $value, int $length): string
    {
        $alphabet = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
        $out = '';

        for ($i = 0; $i < $length; $i++) {
            $out = $alphabet[$value & 31] . $out;
            $value >>= 5;
        }

        return $out;
    }

    private function base32EncodeBytes(string $bytes, int $length): string
    {
        $alphabet = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

        $bits = 0;
        $bitCount = 0;
        $out = '';

        foreach (str_split($bytes) as $byte) {
            $bits = ($bits << 8) | ord($byte);
            $bitCount += 8;

            while ($bitCount >= 5) {
                $bitCount -= 5;
                $idx = ($bits >> $bitCount) & 31;
                $out .= $alphabet[$idx];
            }
        }

        return str_pad(substr($out, 0, $length), $length, '0', STR_PAD_LEFT);
    }
}
