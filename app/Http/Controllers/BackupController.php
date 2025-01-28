<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devices;
use App\Models\Backups; // Certifique-se de usar o modelo correto
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->id_company) {
            return redirect('/login')->with('error', 'Usuário não autenticado ou sem empresa associada.');
        }

        $idCompany = $user->id_company;

        $company = Company::find($idCompany);

        if (!$company || $company->status != 1) {
            return redirect()->back()->with('error', 'Empresa não encontrada ou inativa.');
        }

        $devices = Devices::with(['backups' => function ($query) {
                $query->latest();
            }])
            ->where('id_company', $idCompany)
            ->paginate(10);

        return view('bkserver.index', compact('devices', 'idCompany'));
    }

    public function show($id_device)
    {
        $device = Devices::findOrFail($id_device);
        $backup = Backups::where('id_device', $device->id)->latest()->first();
        $backups = Backups::where('id_device', $device->id)->get();

        return view('bkserver.device-details', compact('device', 'backup', 'backups'));
    }

    public function compare(Request $request)
    {
        $backup1 = Backups::find($request->input('backup1'));
        $backup2 = Backups::find($request->input('backup2'));

        if (!$backup1 || !$backup2) {
            return redirect()->back()->withErrors('Um dos backups não foi encontrado.');
        }

        // Verifique se os dispositivos associados aos backups existem
        $device1 = Devices::find($backup1->id_device);
        $device2 = Devices::find($backup2->id_device);

        if (!$device1 || !$device2) {
            return response()->json(['error' => 'Device not found or is null'], 404);
        }

        // Compare os dados dos backups
        $comparisonResult = $this->diff($backup1->text, $backup2->text);

        return view('bkserver.device-details', [
            'device' => $device1, // Assumindo que ambos os backups pertencem ao mesmo dispositivo
            'backups' => $device1->backups,
            'comparisonResult' => $comparisonResult,
            'backup1Data' => $backup1->text,
            'backup2Data' => $backup2->text,
            'backup1' => $backup1, // Passando o objeto backup1 para a view
            'backup2' => $backup2, // Passando o objeto backup2 para a view
        ]);
    }

    private function diff($data1, $data2)
    {
        // Implementar a lógica de comparação real aqui (exemplo simplificado)
        return strcmp($data1, $data2) === 0 ? 'Os backups são idênticos' : 'Os backups são diferentes';
    }

    public function download(Request $request)
    {
        // Valide o ID do backup
        $request->validate([
            'backupDownload' => 'required|exists:backup,id',
        ]);

        // Encontre o backup pelo ID
        $backup = Backups::findOrFail($request->backupDownload);

        // Encontre o dispositivo associado ao backup
        $device = Devices::findOrFail($backup->id_device);

        // Conteúdo do backup
        $backupContent = $backup->text; // Certifique-se de que o modelo Backup tem o campo text

        // Nome do arquivo para download
        $fileName = $device->name . '_' . $backup->created_at->format('Y-m-d_H-i-s') . '.txt';

        // Crie a resposta para download
        $headers = [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return Response::make($backupContent, 200, $headers);
    }
}
