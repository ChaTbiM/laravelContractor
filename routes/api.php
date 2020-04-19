<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\User; // delete later

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->post('/user', function (Request $request) {
//     return $request->bearerToken()();
// });

Route::post("/login", "AuthController@login");
Route::post("/register", "AuthController@register");

Route::get("/test", function () {
    return response()->json(User::all(), 200);
});

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get("/user", "AuthController@getAuthenticatedUser");

    Route::post("/logout", "AuthController@logout");
});
