<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;


class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('transactions')->get();
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = 'gatoNegro2026+';
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['status'] = 'active';
        $validatedData['must_change_password'] = true;

        $user = User::create($validatedData);
        $user->assignRole($validatedData['role']);
        return (new UserResource($user))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('transactions');
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validatedData = $request->validated();
        if ($request->user()->username === 'visita') {
            return response()->json([
                'message' => 'El usuario demo no puede modificar su perfil.'
            ], 403);
        }
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        $user->update($validatedData);
        $user->syncRoles([$validatedData['role']]);
        return new UserResource($user);
    }

    /**
     * Change status of the specified resource in storage.
     */
    public function changeStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'inactive' : 'active';

        $user->save();

        $user->refresh();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->transactions()->exists()) {
            return response()->json(['message' => 'No se puede eliminar el usuario porque tiene transacciones asociadas'], 400);
        }
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }
}
