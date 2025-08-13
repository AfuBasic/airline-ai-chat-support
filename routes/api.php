<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/auth', [AuthController::class, 'index']);
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::group(['prefix' => 'chat'], function() {
    Route::get('/start', [ChatController::class,'startChat']);
    Route::group(['prefix' => '{conversation}'], function() {
        Route::post('/message/send', [MessageController::class,'sendMessage']);
        Route::post('/message/send/agent', [MessageController::class,'sendAgentMessage']);
    });

});
