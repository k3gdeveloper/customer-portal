<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class MapController extends Controller
{
    public function index(Request $request)
    {
        // Obter o usuário autenticado
        $user = auth()->user();

        // Verifique se o usuário está autenticado e possui uma empresa
        if (!$user || !$user->id_company) {
            return redirect('/login')->with('error', 'Usuário não autenticado ou sem empresa associada.');
        }

        // Obter a empresa associada ao usuário
        $idCompany = Company::find($user->id_company);

        // Verifique se a empresa existe e está ativa
        if (!$idCompany || $idCompany->status != 1) {
            return redirect('/error-page')->with('error', 'Empresa não encontrada ou inativa.');
        }

        // Buscar o link associado à empresa usando id_company
        $link = Link::where('id_company', $user->id_company)->first();

        // Verifique se o link foi encontrado
        if (!$link) {
            // Caso não encontre um link, defina uma variável adequada e permita o carregamento da página
            $mapId = null;
            $mappedTickets = []; // Inicialize uma lista vazia de tickets ou busque dados adicionais aqui, se necessário

            return view('map', [
                'tickets' => $mappedTickets,
                'mapId' => $mapId,
                'company' => $idCompany,
            ]);
        } else {
            // Caso o link seja encontrado, passe os dados para a view
            $mapId = $link->map;
            $mappedTickets = []; // Inicialize uma lista vazia de tickets ou busque dados adicionais aqui, se necessário

            return view('map', [
                'tickets' => $mappedTickets,
                'mapId' => $mapId,
                'company' => $idCompany,
            ]);
        }
    }
}
?>
