<?php

namespace App\Http\Services;

use App\Models\FcmToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class FcmTokenService
{
    public function createOrUpdate(array $data): Model
    {
        $client = FcmToken::updateOrCreate(
            ['user_id' => $data['user_id']],
            $data
        );
        return $client;
    }

    public function saveMasterIdsToToken(string $token, array $masterIds): void
    {
        Redis::pipeline(function ($pipe) use ($token, $masterIds) {
            foreach ($masterIds as $masterId) {
                $pipe->sadd('masters:' . $masterId, $token);
            }
        });
    }

    public function getTokensForMasters(array $masterIds): array
    {
        $tokens = Redis::pipeline(function ($pipe) use ($masterIds) {
            foreach ($masterIds as $masterId) {
                $pipe->smembers('masters:' . $masterId);
            }
        });

        return array_unique(array_merge(...$tokens));
    }

}
