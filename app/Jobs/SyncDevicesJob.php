<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncDevicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $idCompany;

    /**
     * Cria uma nova instância do Job.
     */
    public function __construct($idCompany)
    {
        $this->idCompany = $idCompany;
    }

    /**
     * Executa o Job.
     */
    public function handle()
    {
        try {
            // Substitua o comando pelo que você quer executar no terminal
            $command = sprintf(
                'ssh -i "C:\\Github\\K3G Solutions\\customer-portal\\storage\\rsakey\\id_rsa" suporte@189.126.90.0 -p 50422 "curl -H \'Authorization: Token 3610ba07edc00a7c49964da444a8f11bedda4c39\' http://172.30.0.136/api/dcim/devices/?cf_backup=1"'
            );

            $output = shell_exec($command);

            if (!$output) {
                Log::error("Falha ao executar o comando para a empresa {$this->idCompany}");
                return;
            }

            Log::info("Comando executado com sucesso para a empresa {$this->idCompany}");
            Log::info($output);
        } catch (\Exception $e) {
            Log::error("Erro ao executar o comando: " . $e->getMessage());
        }
    }
}
