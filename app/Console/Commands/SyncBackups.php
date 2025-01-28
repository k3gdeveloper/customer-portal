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
            'Ubiquiti' => 'cat /tmp/system.cfg',
        ];

        $sshUser = 'suporte';
        $sshJumpHost = '189.126.90.0';
        $sshJumpPort = 50422;
        $rsaKeyPath = config('app.rsa_patch');
        $rsaKey = PublicKeyLoader::load(file_get_contents($rsaKeyPath));

        foreach (Devices::all() as $device) {
            try {
                $deviceCommand = $commands[$device->so] ?? null;

                if (!$deviceCommand) {
                    $this->logError($device, "Comando não configurado para o tipo de dispositivo: {$device->so}");
                    continue;
                }

                $this->info("Estabelecendo conexão SSH com o dispositivo {$device->name} (ID: {$device->id})...");

                // Conectando ao jump host
                $jumpSsh = new SSH2($sshJumpHost, $sshJumpPort, 15); // Timeout de 15 segundos
                if (!$jumpSsh->login($sshUser, $rsaKey)) {
                    $this->logError($device, "Falha ao autenticar no jump host.");
                    continue;
                }

                $this->info("Conectado ao jump host. Iniciando proxy para o dispositivo {$device->ip}...");

                // Criando o comando de proxy para o dispositivo
                $proxyCommand = "ssh -o StrictHostKeyChecking=no -oHostKeyAlgorithms=+ssh-rsa -o UserKnownHostsFile=/dev/null -p {$device->ssh} {$device->user}@{$device->ip}";
                $jumpSsh->write("{$proxyCommand}\n");
                $jumpSsh->exec('export TERM=xterm'); // Configurar PTY para interatividade
                $jumpSsh->setTimeout(1); // Timeout ajustado para 15 segundos

                // Lendo a resposta inicial
                $response = $jumpSsh->read();
                if (preg_match('/password:\s*$/i', $response)) {
                    $jumpSsh->write("{$device->password}\n");
                    $response = $jumpSsh->read();
                }

                if (strpos($response, 'Welcome') === false && strpos($response, 'Last login') === false) {
                    $this->logError($device, "Erro ao conectar ao dispositivo. Resposta: {$response}");
                    $jumpSsh->disconnect(); // Desconectando para evitar recursos travados
                    continue;
                }

                $this->info("Conexão ao dispositivo {$device->name} foi estabelecida!");

                // Executando o comando no dispositivo
                $this->info("Executando comando no dispositivo: {$deviceCommand}");
                $jumpSsh->write("{$deviceCommand}\n");
                $responseSSH = $jumpSsh->read();

                $cleanedResponse = str_replace($deviceCommand, '', $responseSSH);

                if (!empty($cleanedResponse)) {
                    $this->saveBackup($device, $cleanedResponse);
                    $this->info("Backup do dispositivo {$device->name} (ID: {$device->id}) finalizado com sucesso.");
                } else {
                    $this->logError($device, "Erro: Sem saída do comando para o dispositivo.");
                }

                $jumpSsh->disconnect(); // Certifique-se de liberar recursos após finalizar
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

    private function saveBackup($device, $cleanedResponse)
    {
        try {
            $backup = new Backups();
            $backup->text = $cleanedResponse;
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
