<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Devices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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

        $jumpProxy = DB::table('jump_proxy')->where('id_company', $idCompany)->first();

        if (!$jumpProxy) {
            $this->error('Configuração de jump proxy não encontrada para a empresa com ID ' . $idCompany);
            return 1;
        }

        $sshUser = 'suporte';
        $sshJumpHost = '189.126.90.0';
        $sshJumpPort = 50422;
        $netboxIp = '172.30.0.136';
        $netboxToken = 'Token 3610ba07edc00a7c49964da444a8f11bedda4c39';

        $apiEndpoint = "http://$netboxIp/api/dcim/devices/?cf_backup=1";
        Log::info("Requisição para o endpoint: $apiEndpoint");
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

        // Verifique o comando gerado
        $remoteCommand = "curl -H 'Authorization: $netboxToken' $endpoint";
        Log::info("Comando remoto gerado para execução: $remoteCommand");

        $sshCommand = sprintf(
            'ssh -i %s %s@%s -p %d "%s"',
            $escapedRsaKeyPath,
            $sshUser,
            $sshJumpHost,
            $sshJumpPort,
            $remoteCommand
        );

        Log::info("Comando SSH gerado para execução: $sshCommand");

        $process = Process::fromShellCommandline($sshCommand);

        try {
            $process->mustRun();
            $output = $process->getOutput();
            Log::info("Resposta do comando SSH: $output");
            return $output;
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
        Log::info("Iniciando a função createOrUpdateDevice", ['device_id' => $device['id']]);

        $netboxIp = '172.30.0.136'; // IP do Netbox
        $endpoint = "http://$netboxIp/api/ipam/services/?device_id={$device['id']}&name__ic=ssh";

        Log::info("URL da requisição:", ['url' => $endpoint]);

        // Informações físicas
        $sshUser = 'suporte';
        $sshJumpHost = '189.126.90.0';
        $sshJumpPort = 50422;
        $netboxToken = 'Token 3610ba07edc00a7c49964da444a8f11bedda4c39';

        $output = $this->executeRemoteCurl(
            $endpoint,
            $sshUser,
            $sshJumpHost,
            $sshJumpPort,
            $netboxToken
        );

        // Log detalhado da resposta bruta
        Log::info("Resposta bruta do IPAM Services para o dispositivo {$device['id']}:", [
            'output' => $output
        ]);

        // Verifique se a resposta não está vazia
        if (empty($output)) {
            $this->error("A resposta da API IPAM está vazia.");
            return;
        }

        $resIpamServ = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Erro ao decodificar JSON da resposta IPAM: " . json_last_error_msg());
            return;
        }

        $deviceModel = $isNew ? new Devices : Devices::firstWhere('id_netbox', $device['id']);
        $deviceModel->id_netbox = $device['id'];
        $deviceModel->name = $device['name'];
        $deviceModel->so = $device['device_type']['manufacturer']['name'];
        $deviceModel->id_company = $idCompany;

        $parts = explode("/", $device['primary_ip']['address']);
        $deviceModel->ip = $parts[0];

        // Aqui você traz as portas da resposta IPAM
        if (isset($resIpamServ['results']) && !empty($resIpamServ['results'])) {
            $firstResult = $resIpamServ['results'][0] ?? null;

            if (!empty($firstResult['ports'])) {
                $ports = $firstResult['ports'];

                // Verifica se há portas e pega a primeira
                if (is_array($ports) && count($ports) > 0) {
                    $deviceModel->ssh = $ports[0]; // Considera a primeira porta retornada
                    $this->info("Porta SSH encontrada para o dispositivo {$device['id']}: {$deviceModel->ssh}");
                } else {
                    $this->warn("Array de portas está vazio para o dispositivo {$device['id']}. Definindo valor padrão: 22.");
                    $deviceModel->ssh = '22'; // Valor padrão
                }
            } else {
                $this->warn("Campo 'ports' não encontrado ou está vazio para o dispositivo {$device['id']}. Definindo valor padrão: 22.");
                $deviceModel->ssh = '22'; // Valor padrão
            }
        } else {
            $this->warn("Nenhum dado válido retornado do IPAM Services para o dispositivo {$device['id']}. Definindo valor padrão: 22.");
            $deviceModel->ssh = '22'; // Valor padrão
        }

        // Log detalhado para depuração
        Log::info("Resposta completa do IPAM Services para o dispositivo {$device['id']}:", [
            'response' => $resIpamServ,
        ]);

        // Definição dos demais atributos do modelo
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
