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
        $validatedData['must_change_password'] = true;

        $user = User::create($validatedData);
        return new UserResource($user);
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
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        $user->update($validatedData);
        return new UserResource($user);
    }

    public function resetPassword(User $user)
    {
        $user->update([
            'password' => Hash::make('gatoNegro2026+'),
            'must_change_password' => true,
        ]);

        return new UserResource($user);
    }

    public function changePassword(UpdateUserRequest $request, User $user)
    {
        $validatedData = $request->validated();
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
            $validatedData['must_change_password'] = false;
        }
        $user->update($validatedData);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user['status'] = 'inactive';
        $user->save();
        return response()->json(['message' => 'Usuario desactivado correctamente'], 200);
    }
}
