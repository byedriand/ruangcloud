<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DevServer extends Command
{
    protected $signature   = 'dev';
    protected $description = 'Jalankan server pengembangan: artisan serve, Vite, dan queue:work secara bersamaan';

    public function handle(): int
    {
        $this->info('🚀 Memulai lingkungan pengembangan RuangCloud...');
        $this->newLine();

        $base = base_path();

        $processes = [
            'serve'  => new Process(['php', 'artisan', 'serve'],                $base),
            'vite'   => new Process(['npm', 'run', 'dev'],                      $base),
            'queue'  => new Process(['php', 'artisan', 'queue:work', '--timeout=3600'], $base),
        ];

        $colors = [
            'serve' => '<fg=green>',
            'vite'  => '<fg=cyan>',
            'queue' => '<fg=yellow>',
        ];

        foreach ($processes as $name => $process) {
            $process->setTimeout(null);
            $process->setTty(false);
            $process->start(function (string $type, string $buffer) use ($name, $colors) {
                $color = $colors[$name] ?? '<fg=white>';
                $tag   = strtoupper(str_pad($name, 5));
                foreach (explode("\n", rtrim($buffer)) as $line) {
                    if (trim($line) !== '') {
                        $this->line("{$color}[{$tag}]</> {$line}");
                    }
                }
            });
        }

        $this->info('✅ Semua proses berjalan. Tekan <fg=red>Ctrl+C</> untuk menghentikan.');
        $this->newLine();

        // Loop hingga semua proses selesai atau Ctrl+C
        while (array_filter($processes, fn($p) => $p->isRunning())) {
            usleep(200_000); // 200ms
        }

        // Pastikan semua proses dihentikan
        foreach ($processes as $process) {
            $process->stop();
        }

        return self::SUCCESS;
    }
}
