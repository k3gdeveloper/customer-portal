<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GraphicController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\LinkController;

// Redireciona para a página de login ou home se estiver logado
Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
});

// Rotas de autenticação
Auth::routes();

// Rota para a página inicial após login
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/ticket', [TicketController::class, 'index'])->name('ticket');
    Route::get('/map', [MapController::class, 'index'])->name('map');
    Route::get('/graphic', [GraphicController::class, 'index'])->name('graphic');
    Route::post('/users/{userId}/links', [LinkController::class, 'createLinkForUser']);
});

// Rota para logout
