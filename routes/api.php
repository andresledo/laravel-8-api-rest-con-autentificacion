<?php

use App\Http\Controllers\V1\ProductsController;
use App\Http\Controllers\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    //Prefijo V1, todo lo que este dentro de este grupo se accedera escribiendo v1 en el navegador, es decir /api/v1/*

    Route::post('login', [AuthController::class, 'authenticate']);
    Route::post('register', [AuthController::class, 'register']);

    Route::get('products', [ProductsController::class, 'index']);
    Route::get('products/{id}', [ProductsController::class, 'show']);

    Route::group(['middleware' => ['jwt.verify']], function() {
        //Todo lo que este dentro de este grupo requiere verificación de usuario.

        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('get-user', [AuthController::class, 'getUser']);

        Route::post('products', [ProductsController::class, 'store']);
        Route::put('products/{id}', [ProductsController::class, 'update']);
        Route::delete('products/{id}', [ProductsController::class, 'destroy']);

    });
});
