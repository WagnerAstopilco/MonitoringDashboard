<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Resources\ServiceResource;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::with('promotions')->get();
        return ServiceResource::collection($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $validatedData = $request->validated();
        $promotions = $validatedData['promotions'] ?? [];
        unset($validatedData['promotions']);
        $service = Service::create($validatedData);
        $service->promotions()->sync($promotions);
        $service['profit']= $service->price - $service->cost;
        return new ServiceResource($service);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->load('promotions');
        return new ServiceResource($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $validatedData = $request->validated();
        $promotionsIds = $validatedData['promotions'] ?? [];
        unset($validatedData['promotions']);
        $service->update($validatedData);
        $service->promotions()->sync($promotionsIds);
        $service['profit']= $service->price - $service->cost;
        return new ServiceResource($service->load('promotions'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service['status']='inactive';
        $service->save();
        return response()->json(['message'=> 'Servicio desactivado correctamente'], 200);
    }
}
