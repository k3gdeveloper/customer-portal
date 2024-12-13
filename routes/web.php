<?php

use Illuminate\Support\Facades\Route;

// Rota para a página de boas-vindas
Route::get('/', function () {
    return view('welcome');
});

// Rotas de autenticação
Auth::routes();

// Rota para a página inicial após login
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rotas para os produtos
Route::get('/produto1', [App\Http\Controllers\ProdutoController::class, 'showProduto1'])->name('produto1');
Route::get('/produto2', [App\Http\Controllers\ProdutoController::class, 'showProduto2'])->name('produto2');
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
Route::get('/map-dashboard', [App\Http\Controllers\MapDashboardController::class, 'index'])->name('map-dashboard');


use App\Http\Controllers\ProductController;

Route::get('/home', [ProductController::class, 'index'])->name('home');


