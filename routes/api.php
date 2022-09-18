<?php

use App\Models\User as User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController as AuthController;
use App\Http\Controllers\IndexController as IndexController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/forgot', [AuthController::class, 'forgot'])->name('forgot');
    Route::post('/code', [AuthController::class, 'code'])->name('code');
    Route::post('/changePassword', [AuthController::class, 'changePassword'])->name('changePassword');
});

Route::group(['prefix' => 'auth', 'middleware' => "api_auth"], function () {
    Route::post('/rebootpassword', [AuthController::class, 'rebootpassword'])->name('rebootpassword');
    Route::post('/change', [AuthController::class, 'change'])->name('change');
    Route::post('/view', [AuthController::class, 'view'])->name('authview');
});
