<?php

namespace App\Http\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;

class ClientService
{
    protected Client $model;

    public function __construct(Client $client)
    {
        $this->model = $client;
    }

    public function createOrUpdate(array $data): Client|Model
    {
        $client = Client::updateOrCreate(
            ['phone' => $data['phone']],
            $data
        );

        return $client;
    }
}
