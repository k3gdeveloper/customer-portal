<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;


// Rotas de Usuário
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{user}', [UserController::class, 'update']);

// Rota de Teste

Route::get('/test', function (){
    return response()->json(['status' => 'API is working']);
 });
