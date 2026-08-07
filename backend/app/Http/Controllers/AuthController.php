<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;



class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => [
                'required',
                'string'
            ],
            'password' => [
                'required',
                'string'
            ],
        ]);


        if(!Auth::attempt($credentials)){

            return response()->json([
                'message'=>'Credenciales incorrectas'
            ],401);

        }


        $user = Auth::user();

        // Eliminar tokens anteriores (opcional)
        $user->tokens()->delete();



        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([

            'access_token'=>$token,

            'token_type'=>'Bearer',

            'user'=>[
                'id'=>$user->id,
                'name'=>$user->name,
                'last_name'=>$user->last_name,
                'username'=>$user->username,
                'role'=>$user->role,
            ]

        ]);

    }

    public function logout(Request $request)
    {

        $request
            ->user()
            ->currentAccessToken()
            ->delete();


        return response()->json([
            'message'=>'Sesión cerrada correctamente'
        ]);

    }

    public function me(Request $request)
    {

        return response()->json(
            $request->user()
        );

    }
    public function recoveryPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'message' => 'Contraseña actualizada correctamente'
        ]);
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

}
