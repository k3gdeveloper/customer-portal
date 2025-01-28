<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Os comandos Artisan fornecidos pela sua aplicação.
     *
     * @var array
     */
    protected $commands = [
        // Registre seus comandos aqui
        \App\Console\Commands\SyncDevices::class,
    ];

    /**
     * Define a programação de comandos.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Exemplo: $schedule->command('run:syncdevices')->daily();
    }

    /**
     * Registra o manipulador de comandos do console.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
