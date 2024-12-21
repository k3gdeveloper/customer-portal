<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MapDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Defina seus tokens aqui
        $appToken = 'vlNi3Fp2MCPwFIInAofxTkCo4xvIBZH9Prq11nqq';
        $sessionToken = 'asdg9jl2utqavcappimtf226d1';

        // Capture as datas de início e fim do request
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

            // Filtrar os tickets incluindo a lógica de data
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
                    // Inclua outros campos, como `date_creation`, se necessário
                }
                return $ticket;
            })->all();
        } else {
            $mappedTickets = [];
        }

        // Passe os dados para a view
        return view('map-dashboard', ['tickets' => $mappedTickets]);
    }
}
