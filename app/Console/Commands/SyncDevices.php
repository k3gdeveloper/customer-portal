<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Devices;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SyncDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:syncdevices {id_company}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar equipamentos cadastrados no Netbox';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $idCompany = $this->argument('id_company');

        if (!$idCompany) {
            $this->error('ID da empresa não fornecido.');
            return 1;
        }

        $jumpProxy = \DB::table('jump_proxy')->where('id_company', $idCompany)->first();

        if (!$jumpProxy) {
            $this->error('Configuração de jump proxy não encontrada para a empresa com ID ' . $idCompany);
            return 1;
        }

        $sshUser = $jumpProxy->user;
        $sshJumpHost = $jumpProxy->host;
        $sshJumpPort = $jumpProxy->ssh;
        $netboxIp = $jumpProxy->ip_netbox;
        $netboxToken = $jumpProxy->token_netbox;

        $apiEndpoint = "http://$netboxIp/api/dcim/devices/?cf_backup=1";
        $output = $this->executeRemoteCurl($apiEndpoint, $sshUser, $sshJumpHost, $sshJumpPort, $netboxToken);

        if ($output === null) {
            $this->error('Falha ao obter os dispositivos da API.');
            return 1;
        }

        $devices = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Erro ao decodificar JSON: ' . json_last_error_msg());
            return 1;
        }

        $this->processDevices($devices['results'], $idCompany);
        $this->info('Devices sincronizados com sucesso.');
        return 0;
    }

    private function executeRemoteCurl(string $endpoint, string $sshUser, string $sshJumpHost, string $sshJumpPort, string $netboxToken): ?string
    {
        $rsaKeyPath = config('app.rsa_patch');
        $escapedRsaKeyPath = escapeshellarg($rsaKeyPath);

        $remoteCommand = "curl -H 'Authorization: $netboxToken' $endpoint";

        $sshCommand = sprintf(
            'ssh -i %s -p %d %s@%s "%s"',
            $escapedRsaKeyPath,
            $sshJumpPort,
            $sshUser,
            $sshJumpHost,
            $remoteCommand
        );

        $process = Process::fromShellCommandline($sshCommand);

        try {
            $process->mustRun();
            return $process->getOutput();
        } catch (ProcessFailedException $exception) {
            $this->error('Falha ao executar remote curl: ' . $exception->getMessage());
            Log::error('Falha ao executar remote curl', ['exception' => $exception]);
            return null;
        }
    }

    private function processDevices(array $devices, $idCompany): void
    {
        foreach ($devices as $device) {
            if (!Devices::where('id_netbox', $device['id'])->exists()) {
                $this->info("Adicionando dispositivo: " . $device['name']);
                $this->createOrUpdateDevice($device, true, $idCompany);
            } else {
                $this->info("Atualizando dispositivo: " . $device['name']);
                $this->createOrUpdateDevice($device, false, $idCompany);
            }
        }
    }

    private function createOrUpdateDevice(array $device, bool $isNew, $idCompany): void
    {
        $netboxIp = config('app.netboxip');
        $endpoint = "http://$netboxIp/api/ipam/services/?device_id={$device['id']}&name__ic=ssh";

        $jumpProxy = \DB::table('jump_proxy')->where('id_company', $idCompany)->first();
        if (!$jumpProxy) {
            $this->error('Configuração de jump proxy ausente.');
            return;
        }

        $output = $this->executeRemoteCurl(
            $endpoint,
            $jumpProxy->user,
            $jumpProxy->host,
            $jumpProxy->ssh,
            'Token ' . $jumpProxy->token_netbox
        );

        $resIpamServ = json_decode($output, true);

        $deviceModel = $isNew ? new Devices : Devices::firstWhere('id_netbox', $device['id']);
        $deviceModel->id_netbox = $device['id'];
        $deviceModel->name = $device['name'];
        $deviceModel->so = $device['device_type']['manufacturer']['name'];
        $deviceModel->id_company = $idCompany;

        $parts = explode("/", $device['primary_ip']['address']);
        $deviceModel->ip = $parts[0];

        if (isset($resIpamServ['results']) && !empty($resIpamServ['results'][0]['ports'])) {
            $deviceModel->ssh = $resIpamServ['results'][0]['ports'][0];
        } else {
            $deviceModel->ssh = '22';
        }

        $deviceModel->rsakey = $device['custom_fields']['backup'] ?? 0;
        $deviceModel->user = 'k3g';
        $deviceModel->password = 'suportekggg';

        try {
            $deviceModel->save();
            $this->info($isNew ? "Device salvo: " . $device['name'] : "Device atualizado: " . $device['name']);
        } catch (\Exception $e) {
            $action = $isNew ? 'salvar' : 'atualizar';
            $this->error("Erro ao $action dispositivo: " . $device['name'] . " - " . $e->getMessage());
            Log::error("Erro ao $action dispositivo", ['exception' => $e]);
        }
    }
}
