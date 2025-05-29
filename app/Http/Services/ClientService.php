<?php

namespace App\Http\Services;

use App\Models\Client;

class ClientService
{
    protected Client $model;

    public function __construct(Client $client)
    {
        $this->model = $client;
    }

    public function createOrUpdate(array $data): Client
    {
        $client = Client::updateOrCreate(
            ['phone' => $data['phone']],
            $data
        );

        return $client;
    }
}
