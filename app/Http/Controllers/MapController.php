<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class MapController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\View
    {
        $user = auth()->user();

        if (!$user || !$user->id_company) {
            return redirect('/login')->with('error', 'Usuário não autenticado ou sem empresa associada.');
        }

        $idCompany = $user->id_company;

        $company = Company::find($idCompany);

        if (!$company || $company->status != 1) {
            $company = null;
            $idCompany = null;
        }

        $link = Link::where('id_company', $idCompany)->first();

        $mappedTickets = [];

        $mapId = $link ? $link->map : null;

        return view('map', compact('company', 'idCompany', 'mappedTickets', 'mapId'));
    }

}

