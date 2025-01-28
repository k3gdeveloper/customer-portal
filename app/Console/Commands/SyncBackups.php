<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Devices;
use App\Models\Backups;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

class SyncBackups extends Command
{
    protected $signature = 'run:syncbackups';
    protected $description = 'Backup dos dispositivos cadastrados';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $commands = [
            'MikroTik' => 'export',
            'Huawei' => 'display current-configuration | exclude irreversible-cipher | exclude community read cipher | no-more',
            'Datacom' => 'show running-config | nomore',
            'Intelbras' => 'show running-config-devel',
        ];

        $sshUser = 'suporte';
        $sshJumpHost = '189.126.90.0';
        $sshJumpPort = 50422;
        $rsaKeyPath = env('RSA_PATH');
        if (!file_exists($rsaKeyPath)) {
            $this->error("A chave RSA não foi encontrada em: {$rsaKeyPath}.");
            return 1;
        }
        $rsaKey = PublicKeyLoader::load(file_get_contents($rsaKeyPath));

        foreach (Devices::all() as $device) {
            try {
                $deviceCommand = $commands[$device->so] ?? null;

                if (!$deviceCommand) {
                    $this->logError($device, "Comando não configurado para o tipo de dispositivo: {$device->so}");
                    continue;
                }

                $this->info("Estabelecendo túnel SSH para o dispositivo {$device->name} (ID: {$device->id})...");

                // Estabelecendo túnel SSH via jump host para o dispositivo
                $tunnelCommand = "ssh -i \"{$rsaKeyPath}\" -J {$sshUser}@{$sshJumpHost}:{$sshJumpPort} -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null {$device->user}@{$device->ip} -p {$device->ssh}";
                exec($tunnelCommand);

                $deviceSsh = new SSH2($device->ip, (int)$device->ssh);
                $deviceSsh->setTimeout(30);

                if (!$deviceSsh->isConnected()) {
                    $this->logError($device, "Falha ao estabelecer túnel SSH para {$device->name}.");
                    continue;
                }

                $this->info("Conexão ao dispositivo {$device->name} foi estabelecida!");

                // Autenticação no dispositivo remoto
                $loginSuccess = $device->password
                    ? $deviceSsh->login($device->user, $device->password)
                    : $deviceSsh->login($device->user, $rsaKey);

                if (!$loginSuccess) {
                    $this->logError($device, "Falha ao autenticar no dispositivo {$device->name}.");
                    continue;
                }

                $this->info("Executando comando no dispositivo {$device->name}...");
                $responseSSH = $deviceSsh->exec($deviceCommand);

                if (!empty($responseSSH)) {
                    $this->saveBackup($device, $responseSSH);
                    $this->info("Backup do dispositivo {$device->name} (ID: {$device->id}) finalizado com sucesso.");
                } else {
                    $this->logError($device, "Erro: Sem saída do comando para o dispositivo.");
                }
            } catch (\Exception $exception) {
                $this->logError($device, "Erro ao executar o comando SSH: {$exception->getMessage()}");
            }
        }

        return 0;
    }

    private function logError($device, $message)
    {
        $message = substr($message, 0, 255);

        $device->logs()->create([
            'logtext' => $message,
            'id_device' => $device->id,
        ]);

        $this->error("{$message} para o dispositivo {$device->name} (ID: {$device->id}).");
    }

    private function saveBackup($device, $responseSSH)
    {
        try {
            $backup = new Backups();
            $backup->text = $responseSSH;
            $device->backups()->save($backup);

            $device->logs()->create([
                'logtext' => 'Backup feito com sucesso',
                'id_device' => $device->id,
            ]);
        } catch (\Exception $exception) {
            $this->logError($device, "Erro ao salvar o backup: {$exception->getMessage()}");
        }
    }
}
