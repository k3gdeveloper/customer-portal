<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapDashboardController extends Controller
{
    public function index()
    {
        // Defina seus tokens aqui
        $appToken = 'vlNi3Fp2MCPwFIInAofxTkCo4xvIBZH9Prq11nqq';
        $sessionToken = 'q0n8b38056881ek9b5p007lsid';

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

            // Filtre os tickets para incluir aqueles cujos locations_id estão presentes nas localizações
            $filteredTickets = collect($tickets)->filter(function ($ticket) use ($locationMap) {
                return $locationMap->has($ticket['locations_id']);
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

        // Passe os dados para a view
        return view('map-dashboard', ['tickets' => $mappedTickets]);
    }
}
