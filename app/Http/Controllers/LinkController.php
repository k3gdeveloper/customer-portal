<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Link;

class LinkController extends Controller
{
    public function createLinkForUser(Request $request, $userId)
    {
        // Encontre o usuário pelo ID
        $user = User::findOrFail($userId);

        // Crie um novo link para o usuário
        $link = $user->links()->create([
            'graphic' => 'https://meta.k3gsolutions.com.br/public/dashboard/b70d150f-1c40-4eab-b6aa-fe59f738f3de',
            'map' => 'https://meta.k3gsolutions.com.br/public/dashboard/' . $userId,
            'ticket' => 'https://meta.k3gsolutions.com.br/public/dashboard/ticket-id',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Link criado com sucesso!',
            'link' => $link,
        ]);
    }
}
