<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\UserResource;
use App\Http\Services\ClientService;
use App\Http\Services\UserService;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function register(RegisterClientRequest $request, UserService $userService, ClientService $clientService): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $userService->createOrUpdateForClient($validatedData);
        $validatedData['user_id'] = $user->id;
        $client = $clientService->createOrUpdate($validatedData);

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not created '], 500);
        }

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
    }
}
