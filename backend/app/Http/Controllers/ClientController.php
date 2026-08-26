<?php

namespace App\Http\Controllers;


use App\Models\Client;
use App\Http\Resources\ClientResource;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;


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
        return (new ClientResource($client))->response()->setStatusCode(201);
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

    public function destroy(Client $client)
    {
        if ($client->transactions()->exists()) {
            return response()->json(['message' => 'No se puede eliminar el cliente porque tiene transacciones asociadas'], 400);
        }
        $client->delete();
        return response()->json(['message' => 'Cliente eliminado correctamente'], 200);
    }

    public function searchByRuc(Request $request)
    {
        $ruc = $request->input('ruc');

        $clients = Client::where('company_ruc', 'like', $ruc . '%')
            ->limit(10)
            ->get();

        return response()->json($clients);
    }
}
