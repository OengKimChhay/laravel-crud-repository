<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Auth\AuthController;
use App\Modules\Product\ProductController;

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

Route::controller(AuthController::class)->prefix('/auth')->group(function () {
    Route::post('/login', 'login')->name('auth.login');
    Route::post('/logout', 'logout')->name('auth.logout');
    Route::get('/me', 'me')->name('auth.me');
    Route::post('/forgot-password', 'forgotPassword')->name('auth.forgot.password');
    Route::post('/update-password/{token}/{email}', 'updatePassword')->name('auth.update.password');
});

Route::apiResource('/auth', AuthController::class);

Route::controller(ProductController::class)->prefix('/product')->group(function () {
    Route::get('/trash/{id}',           'trash')->name('product.trash');
    Route::get('/trash-many',           'trashMany')->name('product.trash-many');
    Route::delete('/delete-many',       'destroyMany')->name('product.delete-many');
    Route::delete('/force-delete/{id}', 'forceDestroy')->name('product.force-delete');
    Route::delete('/force-delete-many', 'forceDestroyMany')->name('product.force-delete-many');
    Route::put('/restore/{id}',        'restoreOne')->name('product.restore');
    Route::put('/restore-many',        'restoreMany')->name('product.restore-many');
});

Route::apiResource('/product', ProductController::class);
