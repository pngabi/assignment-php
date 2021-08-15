<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\KeyController;
use App\Http\Controllers\TranslationController;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('login', [ApiController::class, 'authenticate']);
Route::post('register', [ApiController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('logout', [ApiController::class, 'logout']);
    //LanguageController
    Route::post('create_language', [LanguageController::class, 'store']);
    Route::get('languages', [LanguageController::class, 'index']);
    //KeyController
    Route::get('keys', [KeyController::class, 'index']);
    Route::get('keys/{id}', [KeyController::class, 'show']);
    Route::post('create_key', [KeyController::class, 'store']);
    Route::put('update_key/{key}',  [KeyController::class, 'update']);
    Route::delete('delete_key/{key}',  [KeyController::class, 'destroy']);
    //TranslationController
    Route::post('translation', [TranslationController::class, 'store']);
});