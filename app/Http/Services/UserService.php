<?php
namespace App\Http\Services;
use App\Models\Master;
use App\Models\User;

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
        $user = User::updateOrCreate(
            ['name' => $data['name']]
        );

        return $user;
    }
}
