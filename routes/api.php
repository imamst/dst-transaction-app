<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    ProductController,
    TransactionController
};

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
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware(['auth:api'])->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::get('/{product:uuid}', [ProductController::class, 'show']);
            Route::middleware(['admin'])->group(function () {
                Route::post('/', [ProductController::class, 'store']);
                Route::post('/{product:uuid}', [ProductController::class, 'update']);
                Route::delete('/{product:uuid}', [ProductController::class, 'destroy']);
            });
        });

        Route::prefix('transactions')->group(function () {
            Route::middleware(['customer'])->group(function () {
                Route::post('/', [TransactionController::class, 'store']);
            });

            Route::get('/', [TransactionController::class, 'index']);
            Route::get('/{transaction:uuid}', [TransactionController::class, 'show']);
        });
    });
});
