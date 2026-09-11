<?php

namespace LaravelAuth\Installer\Commands;

use Illuminate\Console\Command;
use LaravelAuth\Installer\Services\AuthInstaller;
use LaravelAuth\Installer\Services\ConfigurationValidator;
use LaravelAuth\Installer\Services\InstallationClient;
use Throwable;

class ImportAuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:import
                            {url : The installation URL or token from Laravel Auth Builder}
                            {--force : Overwrite existing files without asking}
                            {--dry-run : Simulate installation without modifying files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import and install authentication components from a Laravel Auth Builder URL.';

    public function handle(
        InstallationClient $client,
        ConfigurationValidator $validator,
        AuthInstaller $installer
    ): int {
        $url = $this->argument('url');
        $isDryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $this->line('');
        $this->info('Laravel Auth Builder Installer');
        $this->line('');

        // Step 1 - Fetch remote configuration
        $this->comment("Fetching installation configuration from: {$url}...");
        try {
            $rawPayload = $client->fetch($url);
        } catch (Throwable $e) {
            $this->error('Failed to retrieve configuration: '.$e->getMessage());

            return self::FAILURE;
        }

        // Step 2 - Validate configuration schema
        try {
            $config = $validator->validate($rawPayload);
        } catch (Throwable $e) {
            $this->error('Invalid configuration payload: '.$e->getMessage());

            return self::FAILURE;
        }

        // Step 3 - Display configuration summary
        $this->line('');
        $this->line('<fg=white;options=bold>Configuration:</>');

        $features = $config['features'] ?? [];
        $featureLabels = [
            'login' => 'Login',
            'registration' => 'Registration',
            'forgot_password' => 'Forgot Password',
            'password_reset' => 'Password Reset',
            'email_verification' => 'Email Verification',
            'remember_me' => 'Remember Me',
            'two_factor' => '2FA',
        ];

        foreach ($featureLabels as $key => $label) {
            $enabled = ! empty($features[$key]);
            $statusStr = $enabled ? '<fg=green;options=bold>YES</>' : '<fg=yellow>NO</>';
            $paddedLabel = str_pad("  {$label}", 22);
            $this->line("{$paddedLabel} {$statusStr}");
        }

        $theme = $config['ui']['theme'] ?? 'modern';
        $paddedTheme = str_pad('  Theme Aesthetic', 22);
        $this->line("{$paddedTheme} <fg=cyan>{$theme}</>");

        // Step 4 - Compute planned files
        $plannedFiles = $installer->getPlannedFiles($config);

        $this->line('');
        $this->line('<fg=white;options=bold>Files to be created:</>');
        if (empty($plannedFiles)) {
            $this->line('  (None)');
        } else {
            foreach ($plannedFiles as $file) {
                $this->line("  {$file}");
            }
        }
        $this->line('');

        // Step 5 - Handle Dry Run
        if ($isDryRun) {
            $this->info('No changes have been made.');

            return self::SUCCESS;
        }

        // Step 6 - Prompt for confirmation unless --force
        if (! $force) {
            if (! $this->confirm('Do you wish to proceed with installing these authentication components?', true)) {
                $this->warn('Installation cancelled by user. No changes were made.');

                return self::SUCCESS;
            }
        }

        // Step 7 - Execute installation
        $this->line('');
        $this->comment('Installing trusted authentication components...');

        try {
            $installed = $installer->install($config, base_path(), $force);
        } catch (Throwable $e) {
            $this->error('Installation error encountered: '.$e->getMessage());
            $this->warn('Any modified files were rolled back safely.');

            return self::FAILURE;
        }

        $this->line('');
        $this->info('Authentication components installed successfully!');
        $this->line('');
        $this->line('<fg=white;options=bold>Next Steps:</>');
        $this->line('  1. Run database migrations: <fg=cyan>php artisan migrate</>');
        $this->line('  2. Compile frontend assets: <fg=cyan>npm run build</>');
        $this->line('  3. Visit <fg=cyan>/login</> or <fg=cyan>/register</> in your browser');
        $this->line('');

        return self::SUCCESS;
    }
}
