<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // save request data to credentials variable
        $credentials = $request->only("email", "password");
        // handling errors while doing the operation
        try {
            // -- try authentication for false, return invalid credentials
            if (!($token = JWTAuth::attempt($credentials))) {
                return response()->json(
                    ["error" => "invalid credentials"],
                    400
                );
            }
        } catch (JWTException $e) {
            // -- catch if the error not about credentials then return error
            return response()->json(["error" => "could not create token"], 500);
        }

        // if it get's here return token
        return response()->json(["token" => $token], 201);
    }

    public function register(Request $request)
    {
        // validate data
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => "required|string|email|unique:users",
            "password" => "required|string|min:6",
        ]);
        //      if validation fails return error 400
        if (!$validator) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        // create user
        $user = User::create([
            "name" => $request->get("name"),
            "email" => $request->get("email"),
            "password" => Hash::make($request->get("password")),
        ]);
        // generate token from user

        $token = JWTAuth::fromUser($user);

        // return user and token
        return response()->json(compact("user", "token"), 201);
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        try {
            // try to invalidate token
            JWTAuth::invalidate($token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully',
            ]);
        } catch (JWTException $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Sorry, the user cannot be logged out',
                ],
                500
            );
        }

        // return response()->json(auth()->user(), 201);
    }

    public function getAuthenticatedUser()
    {
        return response()->json("here", 200, $headers);
        try {
            if (!($user = JWTAuth::parseToken()->authenticate())) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }
}
