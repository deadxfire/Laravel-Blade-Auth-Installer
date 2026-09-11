<?php

namespace LaravelAuth\Installer\Commands;

use Illuminate\Console\Command;

class InstallAuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:install
                            {url? : Optional installation URL or token from Laravel Auth Builder}
                            {--force : Overwrite existing files without asking}
                            {--dry-run : Simulate installation without modifying files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively install authentication components from Laravel Auth Builder.';

    public function handle(): int
    {
        $url = $this->argument('url');

        if (empty($url)) {
            $this->info('Laravel Auth Builder Installer');
            $url = $this->ask('Please enter your Laravel Auth Builder installation URL or token');
        }

        if (empty($url)) {
            $this->error('An installation URL or token is required.');

            return self::FAILURE;
        }

        return $this->call('auth:import', [
            'url' => $url,
            '--force' => (bool) $this->option('force'),
            '--dry-run' => (bool) $this->option('dry-run'),
        ]);
    }
}
