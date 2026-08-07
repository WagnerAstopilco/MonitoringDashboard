<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Http\Resources\PromotionResource;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with('services')->get();
        return PromotionResource::collection($promotions);
    }

    public function store(StorePromotionRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('promotion_image')) {
            $path = $request->file('promotion_image')->store('image/promotions', 'public');
            $validatedData['promotion_image'] = $path;
        }
        $services = $validatedData['services'] ?? [];
        unset($validatedData['services']);
        $promotion = Promotion::create($validatedData);
        $promotion->services()->sync($services);
        return new PromotionResource($promotion);
    }

    public function show(Promotion $promotion)
    {
        $promotion->load('services');
        return new PromotionResource($promotion);
    }

    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        $validatedData = $request->validated();
        if ($request->hasFile('promotion_image')) {

            if ($promotion->promotion_image) {
                Storage::disk('public')->delete($promotion->promotion_image);
            }

            $path = $request->file('promotion_image')
                ->store('image/promotions', 'public');

            $validatedData['promotion_image'] = $path;
        }
        $servicesIds = $validatedData['services'] ?? [];
        unset($validatedData['services']);
        $promotion->update($validatedData);
        $promotion->services()->sync($servicesIds);
        return new PromotionResource($promotion->load('services'));
    }

    public function changeStatus(Promotion $promotion)
    {
        $promotion->status = !$promotion->status;
        $promotion->save();
        return new PromotionResource($promotion);
    }

    public function destroy(Promotion $promotion)
    {
        if($promotion->status === 'active') {
            return response()->json(['message' => 'No se puede eliminar la promoción porque está activa'], 400);
        }

        if ($promotion->promotion_image) {
            Storage::disk('public')->delete($promotion->promotion_image);
        }
        $promotion->delete();
        return response()->json(['message' => 'Promoción eliminada correctamente'], 200);
    }
}
