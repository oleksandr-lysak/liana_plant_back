<?php

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
}
