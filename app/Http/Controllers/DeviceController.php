<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devices;
use App\Models\Backups;

class DeviceController extends Controller
{
    public function showDetails($id)
    {
        $device = Devices::findOrFail($id);
        $backups = $device->backups()->orderBy('created_at', 'desc')->get();

        return view('device.details', compact('device', 'backups'));
    }

    public function compare(Request $request)
    {
        $backup1Id = $request->input('backup1');
        $backup2Id = $request->input('backup2');

        $backup1 = Backups::find($backup1Id);
        $backup2 = Backups::find($backup2Id);

        if (!$backup1 || !$backup2) {
            return redirect()->back()->withErrors([
                'error' => 'Um ou ambos os backups não foram encontrados.'
            ]);
        }

        $backup1Data = $backup1->data ?? '';
        $backup2Data = $backup2->data ?? '';

        $comparisonResult = $this->compareText($backup1Data, $backup2Data);

        return view('bkserver.device-details', [
            'device' => $backup1->device,
            'backups' => $backup1->device->backups,
            'backup1Data' => $backup1Data,
            'backup2Data' => $backup2Data,
            'comparisonResult' => $comparisonResult
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
