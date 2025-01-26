<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use App\Models\Company;

class SyncDeviceController extends Controller
{
    public function run($idCompany)
    {
        if (!Company::find($idCompany)) {
            return redirect()->back()->withErrors(['message' => 'Empresa não encontrada.']);
        }

        Artisan::call('run:syncdevices', ['id_company' => $idCompany]);
        return redirect()->back()->with('message', 'Sincronização realizada com sucesso!');
    }
}
