<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;

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

        $credentials['status'] = 'active';

        if (!Auth::attempt($credentials)) {

            return response()->json([
                'message' => 'Credenciales incorrectas o usuario inactivo'
            ], 401);
        }


        $user = Auth::user();

        // Eliminar tokens anteriores (opcional)
        $user->tokens()->delete();



        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([

            'access_token' => $token,

            'token_type' => 'Bearer',

            'user' => new UserResource($user)
        ]);
    }
    public function loginDemo()
    {
        $credentials = [];
        $credentials['username']='visita';
        $credentials['password'] = 'passDemoTest';

        if (!Auth::attempt($credentials)) {

            return response()->json([
                'message' => 'Credenciales incorrectas o usuario inactivo'
            ], 401);
        }


        $user = Auth::user();

        // Eliminar tokens anteriores (opcional)
        $user->tokens()->delete();



        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([

            'access_token' => $token,

            'token_type' => 'Bearer',

            'user' => new UserResource($user)
        ]);
    }

    public function logout(Request $request)
    {

        $request
            ->user()
            ->currentAccessToken()
            ->delete();


        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $user->update($request->validated());

        return new UserResource($user->fresh());
    }

    // public function recoveryPassword(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required|string',
    //         'password' => 'required|string|confirmed',
    //     ]);

    //     $user = User::where('username', $request->username)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'Usuario no encontrado'
    //         ], 404);
    //     }

    //     $user->password = bcrypt($request->password);
    //     $user->save();

    //     return response()->json([
    //         'message' => 'Contraseña actualizada correctamente'
    //     ]);
    // }

    public function resetPassword(User $user)
    {
        $user->update([
            'password' => Hash::make('gatoNegro2026+'),
            'must_change_password' => true,
        ]);

        return new UserResource($user);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        // $request->user() = el usuario dueño del token Bearer que llega en el
        // header Authorization. No depende de ningún {id} de la URL, así que
        // es imposible cambiarle la contraseña a otro usuario por esta vía.
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {

            return response()->json([
                'message' => 'La contraseña actual es incorrecta',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return new UserResource($user);
    }
}
