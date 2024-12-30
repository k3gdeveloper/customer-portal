<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapDashboardController;
use App\Http\Controllers\ProductController;

// Redireciona para a página de login ou home se estiver logado
Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
});

// Rotas de autenticação
Auth::routes();

// Rota para a página inicial após login
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/produto1', [ProdutoController::class, 'showProduto1'])->name('produto1');
    Route::get('/produto2', [ProdutoController::class, 'showProduto2'])->name('produto2');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/map-dashboard', [MapDashboardController::class, 'index'])->name('map-dashboard');
    Route::get('/home', [ProductController::class, 'index'])->name('home');
});
