<?php

namespace App\Http\Controllers;

use App\Models\Devices;
use App\Models\Backups;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index(Request $request)
{
    // Buscar dispositivos do banco de dados com base no id_company do usuário logado
    $idCompany = auth()->user()->id_company; // Supondo que o id_company esteja no usuário logado

    $perPage = 10;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();

    // Filtrando dispositivos do id_company do usuário logado
    $devices = Devices::with(['backups' => function ($query) {
        $query->latest();
    }])
    ->where('id_company', $idCompany) // Filtra os dispositivos pela empresa do usuário
    ->paginate($perPage);

    // Passando a variável $idCompany para a view
    return view('bkserver.index', compact('devices', 'idCompany'));
}


    public function show($id_device)
    {
        // Encontrar o dispositivo pelo ID no banco de dados
        $device = Devices::findOrFail($id_device);

        // Buscar o último backup associado ao dispositivo
        $backup = Backups::where('id_device', $device->id)->latest()->first();

        // Obter todos os backups associados ao dispositivo para a comparação
        $backups = Backups::where('id_device', $device->id)->get();

        return view('bkserver.device-details', compact('device', 'backup', 'backups'));
    }

    public function compare(Request $request)
    {
        $backup1 = Backups::find($request->input('backup1'));
        $backup2 = Backups::find($request->input('backup2'));

        // Verificar se ambos os backups foram encontrados
        if (!$backup1 || !$backup2) {
            return redirect()->back()->withErrors('Um dos backups não foi encontrado.');
        }

        // Lógica de comparação entre os backups (exemplo simplificado)
        $comparisonResult = $this->diff($backup1->data, $backup2->data);

        return view('bkserver.device-details', [
            'device' => $backup1->device,
            'backups' => $backup1->device->backups,
            'comparisonResult' => $comparisonResult,
        ]);
    }

    private function diff($data1, $data2)
    {
        // Implementar a lógica de comparação real aqui (exemplo simplificado)
        return strcmp($data1, $data2) === 0 ? 'Os backups são idênticos' : 'Os backups são diferentes';
    }
}
