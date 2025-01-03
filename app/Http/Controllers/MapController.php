<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link; // Certifique-se de ter o modelo Link
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MapController extends Controller
{
    public function index(Request $request)
    {
        // Defina seus tokens aqui
        $appToken = 'vlNi3Fp2MCPwFIInAofxTkCo4xvIBZH9Prq11nqq';
        $sessionToken = 'nf2bnbj4hq7aujkka596vej44n';

        // Obtenha as datas de início e fim do request
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;

        // Faça a requisição GET para a API dos tickets com os tokens no header
        $ticketsResponse = Http::withHeaders([
            'App-Token' => $appToken,
            'Session-Token' => $sessionToken,
        ])->get('https://glpi.k3gsolutions.com.br/apirest.php/Ticket?dropdowns=false&get_hateoas=false&range=1-1000');

        // Faça a requisição GET para a API das localizações com os tokens no header
        $locationsResponse = Http::withHeaders([
            'App-Token' => $appToken,
            'Session-Token' => $sessionToken,
        ])->get('https://glpi.k3gsolutions.com.br/apirest.php/Location?dropdowns=false&get_hateoas=false&entities_id=1&range=1-1000');

        // Verifique se ambas as respostas foram bem-sucedidas
        if ($ticketsResponse->successful() && $locationsResponse->successful()) {
            $tickets = $ticketsResponse->json();
            $locations = $locationsResponse->json();

            // Crie um mapa de localizações com base no locations_id
            $locationMap = collect($locations)->mapWithKeys(function ($location) {
                return [$location['id'] => $location];
            });

            // Se os filtros de data não forem fornecidos, use o mês e ano atuais
            $filteredTickets = collect($tickets)->filter(function ($ticket) use ($locationMap, $startDate, $endDate) {
                if ($locationMap->has($ticket['locations_id'])) {
                    $ticketDate = Carbon::parse($ticket['date_creation']);
                    if ($startDate && $endDate) {
                        return $ticketDate->between($startDate, $endDate);
                    }
                    // Se não houver filtro de data, inclui todos os tickets válidos
                    return true;
                }
                return false;
            })->values();

            // Contar o número de incidentes por localização
            $incidentCount = $filteredTickets->groupBy('locations_id')->map(function ($tickets) {
                return $tickets->count();
            });

            // Adicione latitude, longitude, completename e incidentes aos tickets filtrados
            $mappedTickets = $filteredTickets->map(function ($ticket) use ($locationMap, $incidentCount) {
                if (isset($locationMap[$ticket['locations_id']])) {
                    $location = $locationMap[$ticket['locations_id']];
                    $ticket['latitude'] = $location['latitude'];
                    $ticket['longitude'] = $location['longitude'];
                    $ticket['completename'] = $location['completename'] ?? 'Nome completo não disponível';
                    $ticket['incidents'] = $incidentCount[$ticket['locations_id']] ?? 0;
                    $ticket['name'] = $ticket['name'];
                    $ticket['status'] = $ticket['status'];
                }
                return $ticket;
            })->all();
        } else {
            $mappedTickets = [];
        }

                // Buscar o ID do gráfico na tabela links
                $link = Link::where('user_id', auth()->id())->first();
                $mapId = $link ? $link->map : '';

        // Passe os dados para a view
        return view('map', ['tickets' => $mappedTickets, 'mapId' => $mapId]);

    }
}
