<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devices;
use App\Models\Backups;
use App\Models\Company;

class DeviceController extends Controller
{
    public function showDetails($id, $idCompany)
    {
        $user = auth()->user();

        if (!$user || !$user->id_company) {
            return redirect('/login')->with('error', 'Usuário não autenticado ou sem empresa associada.');
        }

        $company = Company::find($idCompany);

        if (!$company || $company->status != 1) {
            return redirect()->back()->with('error', 'Empresa não encontrada ou inativa.');
        }

        $device = Devices::find($id);
        if (!$device) {
            return redirect()->back()->with('error', 'Dispositivo não encontrado.');
        }

        $backups = $device->backups()->orderBy('created_at', 'desc')->get();

        return view('device.details', compact('device', 'backups', 'idCompany'));
    }

    public function compare(Request $request)
    {
        $backup1Id = $request->input('backup1');
        $backup2Id = $request->input('backup2');

        $backup1 = Backups::find($backup1Id);
        $backup2 = Backups::find($backup2Id);

        if (!$backup1 || !$backup2) {
            return redirect()->back()->withErrors('Um ou ambos os backups não foram encontrados.');
        }

        $device = $backup1->device;
        if (!$device) {
            return response()->json(['error' => 'Device not found or is null'], 404);
        }

        $backup1Data = $backup1->data ?? '';
        $backup2Data = $backup2->data ?? '';

        $comparisonResult = $this->compareText($backup1Data, $backup2Data);

        return view('device.details', [
            'device' => $device,
            'backups' => $device->backups,
            'backup1Data' => $backup1Data,
            'backup2Data' => $backup2Data,
            'comparisonResult' => $comparisonResult,
            'idCompany' => $device->company_id
        ]);
    }

    private function compareText(string $text1, string $text2): string
    {
        $result = '';
        $lines1 = explode("\n", $text1);
        $lines2 = explode("\n", $text2);

        $maxLines = max(count($lines1), count($lines2));

        for ($lineNumber = 0; $lineNumber < $maxLines; $lineNumber++) {
            $line1 = $lines1[$lineNumber] ?? '[Linha ausente]';
            $line2 = $lines2[$lineNumber] ?? '[Linha ausente]';

            if ($line1 !== $line2) {
                $result .= "Linha " . ($lineNumber + 1) . ": '{$line1}' !== '{$line2}'\n";
            }
        }

        return $result ?: 'Os backups são idênticos.';
    }
}
