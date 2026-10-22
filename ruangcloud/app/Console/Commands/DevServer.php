<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DevServer extends Command
{
    protected $signature   = 'dev';
    protected $description = 'Jalankan server pengembangan: artisan serve, Vite, dan queue worker secara bersamaan';

    public function handle(): int
    {
        $this->info('🚀 Memulai lingkungan pengembangan RuangCloud...');

        // Bersihkan proses lama yang mungkin masih menggantung di port 8000 atau 5173
        $this->cleanupPorts([8000, 5173]);
        $this->newLine();

        $base = base_path();

        $createProcess = function (string $name) use ($base): Process {
            $commands = [
                'serve' => [PHP_BINARY, 'artisan', 'serve'],
                'vite'  => (DIRECTORY_SEPARATOR === '\\') ? ['cmd', '/c', 'npm', 'run', 'dev'] : ['npm', 'run', 'dev'],
                'queue' => [PHP_BINARY, 'artisan', 'queue:listen', '--tries=1', '--timeout=3600'],
            ];

            $process = new Process($commands[$name], $base);
            $process->setTimeout(null);
            $process->setTty(false);

            return $process;
        };

        $colors = [
            'serve' => '<fg=green>',
            'vite'  => '<fg=cyan>',
            'queue' => '<fg=yellow>',
        ];

        $makeOutput = function (string $name) use ($colors) {
            return function (string $type, string $buffer) use ($name, $colors) {
                $color = $colors[$name] ?? '<fg=white>';
                $tag   = strtoupper(str_pad($name, 5));
                foreach (explode("\n", rtrim($buffer)) as $line) {
                    if (trim($line) !== '') {
                        $this->line("{$color}[{$tag}]</> {$line}");
                    }
                }
            };
        };

        $processes = [
            'serve' => $createProcess('serve'),
            'vite'  => $createProcess('vite'),
            'queue' => $createProcess('queue'),
        ];

        foreach ($processes as $name => $process) {
            $process->start($makeOutput($name));
        }

        $this->info('✅ Semua proses berjalan (Server: http://127.0.0.1:8000, Vite: http://localhost:5173).');
        $this->info('Tekan <fg=red>Ctrl+C</> untuk menghentikan seluruh layanan.');
        $this->newLine();

        // Pastikan child processes dihentikan saat parent dihentikan
        register_shutdown_function(function () use (&$processes) {
            foreach ($processes as $process) {
                if ($process->isRunning()) {
                    $process->stop(1);
                }
            }
        });

        $failCounts = ['serve' => 0, 'vite' => 0, 'queue' => 0];

        // Loop dan awasi proses di latar belakang
        while (true) {
            foreach ($processes as $name => $process) {
                if (! $process->isRunning()) {
                    $failCounts[$name]++;
                    if ($failCounts[$name] > 3) {
                        // Jangan spam jika proses gagal terus menerus (misal error fatal)
                        continue;
                    }

                    $this->warn("⚠️  Proses [{$name}] terhenti. Menghidupkan ulang dalam 2 detik...");
                    sleep(2);
                    $newProcess = $createProcess($name);
                    $newProcess->start($makeOutput($name));
                    $processes[$name] = $newProcess;
                } else {
                    $failCounts[$name] = 0;
                }
            }
            usleep(1_000_000); // 1 detik
        }

        return self::SUCCESS;
    }

    private function cleanupPorts(array $ports): void
    {
        if (DIRECTORY_SEPARATOR !== '\\') {
            return;
        }

        foreach ($ports as $port) {
            $output = @shell_exec("netstat -ano -p tcp | findstr :{$port} 2>nul");
            if (! $output) {
                continue;
            }

            foreach (explode("\n", trim($output)) as $line) {
                if (preg_match('/LISTENING\s+(\d+)/i', $line, $matches)) {
                    $pid = (int) $matches[1];
                    if ($pid > 0 && $pid !== getmypid()) {
                        @shell_exec("taskkill /F /T /PID {$pid} 2>nul");
                    }
                }
            }
        }
    }
}
