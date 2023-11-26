<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPaymentController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\LeagueUserController;
use App\Http\Controllers\DuelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

// Routes pour l'authentification
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


// Routes pour le modèle User
Route::resource('users', UserController::class);

// Routes pour le modèle UserPayment
Route::resource('user-payments', UserPaymentController::class);

// Routes pour le modèle League
Route::resource('leagues', LeagueController::class);

// Routes pour le modèle LeagueUser
Route::resource('league-users', LeagueUserController::class);

// Routes pour le modèle Duel
Route::resource('duels', DuelController::class);

// Routes pour le modèle Dash
Route::get('dash/{query_value}', [DashController::class, 'index']);
