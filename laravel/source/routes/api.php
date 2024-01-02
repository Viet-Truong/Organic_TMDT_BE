<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api_admin\CategoryController;
use App\Http\Controllers\Api_admin\ProductController;

use App\Http\Controllers\Client_api\AuthController;
use App\Http\Controllers\Client_api\ResetPasswordController;
use App\Http\Controllers\Client_api\GoogleController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes product client repository pattern
Route::get('/getAllProduct', [ProductController::class, 'index']);
Route::post('/addProduct', [ProductController::class, 'store']);
// Routes category client
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/category', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);

// Auth
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('verify/{token}', [AuthController::class, 'verifyToken']);

// Reset password
Route::post('reset-password', [ResetPasswordController::class, 'sendMail']);
Route::post('change-password', [ResetPasswordController::class, 'resetPassword']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh', [AuthController::class, 'refresh']);
});

// login with google
Route::post('/get-google-sign-in-url', [GoogleController::class, 'getGoogleSignInUrl']);
Route::get('/callback', [GoogleController::class, 'loginCallback']); 