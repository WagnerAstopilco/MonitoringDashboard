<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Resources\ServiceResource;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use Illuminate\Support\Facades\Storage;

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
        if ($request->hasFile('service_image')) {
            $path = $request->file('service_image')->store('image/services', 'public');
            $validatedData['service_image'] = $path;
        }
        $promotions = $validatedData['promotions'] ?? [];
        unset($validatedData['promotions']);
        $service = Service::create($validatedData);
        $service->promotions()->sync($promotions);
        $service['profit'] = $service->price - $service->cost;
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
        if ($request->hasFile('service_image')) {

            if ($service->service_image) {
                Storage::disk('public')->delete($service->service_image);
            }

            $path = $request->file('service_image')
                ->store('image/services', 'public');

            $validatedData['service_image'] = $path;
        }
        $promotionsIds = $validatedData['promotions'] ?? [];
        unset($validatedData['promotions']);
        $service->update($validatedData);
        $service->promotions()->sync($promotionsIds);
        $service['profit'] = $service->price - $service->cost;
        return new ServiceResource($service->load('promotions'));
    }

    /**
     * Change the status of the specified resource.
     */
    public function changeStatus(Service $service)
    {
        $service->status = !$service->status;
        $service->save();
        return new ServiceResource($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        if ($service->transactionDetails()()->exists()) {
            return response()->json(['message' => 'No se puede eliminar el servicio porque tiene transacciones asociadas'], 400);
        }
        
        if ($service->service_image) {
            Storage::disk('public')->delete($service->service_image);
        }
        $service->delete();
        return response()->json(['message' => 'Servicio eliminado correctamente'], 200);
    }
}
