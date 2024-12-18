<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Defina seus tokens aqui
        $appToken = 'vlNi3Fp2MCPwFIInAofxTkCo4xvIBZH9Prq11nqq';
        $sessionToken = 'tokea8nlt43dsh37gmn556ci5s';

        // Obtenha o entities_id do usuário logado
        $entitiesId = Auth::user()->entities_id;

        // Capture as datas de início e fim do request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Faça a requisição GET para a API com os tokens no header
        $response = Http::withHeaders([
            'App-Token' => $appToken,
            'Session-Token' => $sessionToken,
        ])->get('https://glpi.k3gsolutions.com.br/apirest.php/Ticket?dropdowns=false&get_hateoas=false&range=1-10000');

        // Verifique se a resposta foi bem-sucedida
        if ($response->successful()) {
            $tickets = collect($response->json());
            // Filtre os tickets pelo entities_id do usuário e pelo período selecionado
            $filteredTickets = $tickets->where('entities_id', $entitiesId)->filter(function ($ticket) use ($startDate, $endDate) {
                $ticketStartDate = Carbon::parse($ticket['date']);
                $ticketEndDate = Carbon::parse($ticket['solvedate']);

                if ($startDate) {
                    $startDate = Carbon::parse($startDate);
                }
                if ($endDate) {
                    $endDate = Carbon::parse($endDate);
                }

                // Verifique se o ticket está dentro do intervalo ou se está em aberto naquele período
                return (!$startDate || $ticketStartDate >= $startDate || ($ticketEndDate && $ticketEndDate >= $startDate))
                    && (!$endDate || $ticketStartDate <= $endDate || ($ticketEndDate && $ticketEndDate <= $endDate));
            })->map(function ($ticket) {
                $ticket['solve_delay_formatted'] = $this->formatSolveDelay($ticket['solve_delay_stat']);
                $ticket['status'] = $this->mapStatus($ticket['status']);
                return $ticket;
            });
        } else {
            $filteredTickets = collect();
        }

        // Passe os dados filtrados para a view
        return view('dashboard', ['tickets' => $filteredTickets]);
    }

    private function formatSolveDelay($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return "{$days}d {$hours}h {$minutes}m {$seconds}s";
    }

    private function mapStatus($status)
    {
        $statuses = [
            1 => 'Novo',
            2 => 'Em atendimento (atribuído)',
            3 => 'Em atendimento (planejado)',
            4 => 'Pendente',
            5 => 'Solucionado',
            6 => 'Fechado',
        ];

        return $statuses[$status] ?? 'Desconhecido';
    }
}
