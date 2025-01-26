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
        $rsaKeyPath = config('app.rsa_patch');
        $rsaKey = PublicKeyLoader::load(file_get_contents($rsaKeyPath));

        // Conectar ao jump host
        $jumpSsh = new SSH2($sshJumpHost, $sshJumpPort);

        if (!$jumpSsh->login($sshUser, $rsaKey)) {
            $this->error("Falha ao autenticar no jump proxy {$sshJumpHost}.");
            return 1;
        }

        foreach (Devices::all() as $device) {
            try {
                $deviceCommand = $commands[$device->so] ?? null;

                if (!$deviceCommand) {
                    $this->logError($device, "Comando não configurado para o tipo de dispositivo: {$device->so}");
                    continue;
                }

                // Criar túnel SSH via jump proxy
                $jumpSsh->exec("ssh -i {$rsaKeyPath} {$device->user}@{$device->ip} -p {$device->ssh}");

                $deviceSsh = new SSH2($device->ip, $device->ssh);

                $loginSuccess = false;
                if ($device->password) {
                    $loginSuccess = $deviceSsh->login($device->user, $device->password);
                } else {
                    $loginSuccess = $deviceSsh->login($device->user, $rsaKey);
                }

                if (!$loginSuccess) {
                    $this->logError($device, "Falha ao autenticar no dispositivo {$device->name}.");
                    continue;
                }

                $responseSSH = $deviceSsh->exec($deviceCommand);

                if (!empty($responseSSH)) {
                    $backup = new Backups;
                    $backup->text = $responseSSH;
                    $device->backups()->save($backup);

                    $device->logs()->create([
                        'logtext' => 'Backup feito com sucesso',
                        'id_device' => $device->id,
                    ]);
                    $this->info("Backup do dispositivo {$device->name} (ID: {$device->id}) finalizado com sucesso.");
                } else {
                    $this->logError($device, "Erro: Sem saída do comando para o dispositivo.");
                    continue;
                }
            } catch (\Exception $exception) {
                $this->logError($device, "Erro ao executar o comando SSH: {$exception->getMessage()}");
                continue; // Pula para o próximo dispositivo
            }
        }

        return 0;
    }

    private function logError($device, $message)
    {
        // Truncar mensagem para 255 caracteres
        $message = substr($message, 0, 255);

        $device->logs()->create([
            'logtext' => $message,
            'id_device' => $device->id,
        ]);

        $this->error("{$message} para o dispositivo {$device->name} (ID: {$device->id}).");
    }
}
