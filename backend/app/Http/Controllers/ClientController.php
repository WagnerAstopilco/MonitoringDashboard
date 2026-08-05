<?php

namespace App\Http\Controllers;


use App\Models\Client;
use App\Http\Resources\ClientResource;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;


class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('transactions')->get();
        return ClientResource::collection($clients);
    }
    
    public function store(StoreClientRequest $request)
    {
        $validatedData = $request->validated();
        $client = Client::create($validatedData);
        return new ClientResource($client);
    }

    public function show(Client $client)
    {
        $client->load('transactions');
        return new ClientResource($client);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $validatedData = $request->validated();
        $client->update($validatedData);
        return new ClientResource($client);
    }
}
