<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\Coba;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated();

        $user = new User($data);
        $user->password = Hash::make($data['password']);

        $user->save();

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], 201);

        // UserResource($user);
    }

    public function login(UserLoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            $response = response()->json([
                'success' => false,
                'errors' => [
                    'message' => ['username or password is wrong']
                ]
            ], 401);

            throw new HttpResponseException($response);
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], 200);
    }

    public function get(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], 200);
    }

    public function update(UpdateUserRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            'success' => true,
        ], 200);
    }
}
