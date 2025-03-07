<?php

namespace App\Http\Services;

use App\Models\Master;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    public function createOrUpdateFromMaster(Master $master)
    {
        $user = User::updateOrCreate(
            [
                'phone' => $master->phone,
            ],
            ['name' => $master->name]
        );

        $master->user()->associate($user);
        $master->save();

        return $user;
    }

    public function createOrUpdateForClient(array $data)
    {
        return User::updateOrCreate(
            [
                'name' => $data['name'],
                'phone' => $data['phone'],
            ]
        );
    }

    public function findUserByPhone(string $phone)
    {
        return User::where('phone', $phone)->first();
    }

    public function createTokenForUser($user)
    {
        try {
            return $token = JWTAuth::claims(['phone' => $user->phone])->fromUser($user);
        } catch (\Exception $e) {
            return null;
        }
    }
}
