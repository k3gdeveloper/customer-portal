<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link; // Certifique-se de ter o modelo Link
use Carbon\Carbon;

class GraphicController extends Controller
{
    public function index(Request $request)
    {
        // Obtenha as datas de início e fim do request
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;

        // Inicialize uma lista vazia de tickets
        $mappedTickets = [];

        // Buscar o ID do gráfico na tabela links
        $link = Link::where('user_id', auth()->id())->first();
        $graphicId = $link ? $link->graphic : '';

        // Passe os dados para a view, incluindo o ID do gráfico
        return view('graphic', ['tickets' => $mappedTickets, 'graphicId' => $graphicId]);
    }
}
