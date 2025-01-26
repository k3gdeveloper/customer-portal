<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // ou o modelo que representa seu usuário
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function getUserPermissions(Request $request, $userId)
    {
        $user = User::findOrFail($userId); // Busca o usuário pelo ID

        // Aqui você pode retornar as permissões específicas para o usuário
        $permissions = [
            'hasMonitorAccess' => $user->hasPermission('monitor_access'), // Exemplo de verificação de permissão
            'hasBackupAccess'  => $user->hasPermission('backup_access'),
            // Adicione as permissões necessárias aqui
        ];

        return response()->json($permissions);
    }
}
