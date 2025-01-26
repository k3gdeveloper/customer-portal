<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $idCompany = $user->id_company; // Obtendo o ID da empresa do usuário logado

        // Retorna a visão 'home' com o idCompany
        return view('home', compact('idCompany'));
    }
}
