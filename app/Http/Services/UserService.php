<?php
namespace App\Http\Services;
use App\Models\Client;
use App\Models\Master;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    public function createOrUpdateFromMaster(Master $master)
    {
        $user = User::updateOrCreate(
            ['name' => $master->name]
        );

        $master->user()->associate($user);
        $master->save();

        return $user;
    }

    public function createOrUpdateForClient(array $data)
    {
        return User::updateOrCreate(
            ['name' => $data['name']]
        );
    }

    public function findUserByPhone(string $phone)
    {
        $master = Master::where('phone', $phone)->with('user')->first();
        if ($master) {
            return $master->user;
        }

        $client = Client::where('phone', $phone)->with('user')->first();
        if ($client) {
            return $client->user;
        }

        return null;
    }

    public function createTokenForUser($user)
    {
        try {
            return JWTAuth::fromUser($user);
        } catch (\Exception $e) {
            return null;
        }
    }
}
