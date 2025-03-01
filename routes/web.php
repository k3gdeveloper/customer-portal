<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GraphicController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DeviceController;
use App\Models\Company;
use Illuminate\Support\Facades\Log;



// Redireciona para a página de login ou home se estiver logado
Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
});

// Rotas de autenticação
Auth::routes();

// Rota para a página inicial após login
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/ticket', [TicketController::class, 'index'])->name('ticket');
    Route::get('/map', [MapController::class, 'index'])->name('map');
    Route::get('/graphic', [GraphicController::class, 'index'])->name('graphic');
    Route::get('/bkserver/hosts', [BackupController::class, 'index'])->name('bkserver.index');
    Route::get('/download-backup', [BackupController::class, 'download'])->name('download.backup');
    Route::get('/bkserver/{id}', [BackupController::class, 'show'])->name('bkserver.show');
    Route::post('/compare-backups', [BackupController::class, 'compare'])->name('compare.backups');
    Route::get('/bkserver/hosts/{idCompany}', [BackupController::class, 'index'])->name('backups.index');
    Route::get('/bkserver/hosts/{id_device}', [BackupController::class, 'show'])->name('backups.show');
    Route::post('/compare-backups', [BackupController::class, 'compare'])->name('compare.backups');
    Route::get('/download-backup', [BackupController::class, 'download'])->name('download.backup');
/*     Route::post('/sync-devices/{id_company}', function ($id_company) {
        Artisan::call('run:syncdevices', ['id_company' => $id_company]);
        return back()->with('message', 'Sincronização iniciada com sucesso!');
    })->name('sync.devices'); */



Route::post('/sync-devices/{id_company}', function ($idCompany) {
    try {
        // Rodando o comando Artisan
        Artisan::call('run:syncdevices', [
            'id_company' => $idCompany,
        ]);

        return redirect()->back()->with('success', 'Dispositivos sincronizados com sucesso!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erro ao sincronizar dispositivos: ' . $e->getMessage());
    }
})->name('sync.devices');





// routes/web.php

Route::get('/run-syncdevices/{id_company}', function ($id_company) {
    try {
        // Dispara o Job e retorna sucesso
        $job = new \App\Jobs\SyncDevicesJob($id_company);
        $output = $job->handle(); // Executa o Job diretamente

        return response()->json(['message' => 'Sincronização executada com sucesso!', 'output' => $output]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erro ao executar a sincronização: ' . $e->getMessage()], 500);
    }
})->name('run-syncdevices');


Route::get('run-syncdevices/{id}/{idCompany}', 'DeviceController@showDetails')->name('run-syncdevices');











/* Route::get('/run-syncdevices/{id_company}', function($idCompany) {
    if (!Company::find($idCompany)) {
        return redirect()->back()->withErrors(['message' => 'Empresa não encontrada.']);
    }

    Artisan::call('run:syncdevices', ['id_company' => $idCompany]);
    return redirect()->back()->with('message', 'Sincronização realizada com sucesso!');
})->name('run-syncdevices'); */

   /*  Route::middleware(['auth'])->get('/api/user-permissions/{userId}', [PermissionController::class, 'getUserPermissions']); */

/*     Route::post('/sync-devices', [DeviceController::class, 'syncDevices']);
    Route::post('/sync-devices/{id_company}', [DeviceController::class, 'syncDevices'])->name('sync.devices'); */


/*     Route::post('/users/{userId}/links', [LinkController::class, 'createLinkForUser']); */
});

/*
Route::get('/devices', [BackupController::class, 'index'])->name('device.index');
Route::get('/devices/{id}', [BackupController::class, 'show'])->name('device.show'); */


// Criar Rota para logout
