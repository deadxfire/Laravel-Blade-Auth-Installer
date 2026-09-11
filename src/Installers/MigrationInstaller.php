<?php

namespace LaravelAuth\Installer\Installers;

class MigrationInstaller extends BaseInstaller
{
    public function getPlannedFiles(array $config): array
    {
        $features = $config['features'] ?? [];

        if (! empty($features['two_factor'])) {
            return ['database/migrations/xxxx_xx_xx_xxxxxx_add_two_factor_columns_to_users_table.php'];
        }

        return [];
    }

    public function install(array $config, string $basePath, array &$installedFiles, bool $force = false): void
    {
        $features = $config['features'] ?? [];

        if (! empty($features['two_factor'])) {
            $migrationsPath = $basePath.'/database/migrations';
            $existing = glob($migrationsPath.'/*_add_two_factor_columns_to_users_table.php');

            if (! empty($existing)) {
                $target = $existing[0];
            } else {
                $timestamp = date('Y_m_d_His');
                $target = "{$migrationsPath}/{$timestamp}_add_two_factor_columns_to_users_table.php";
            }

            $this->copyStub(
                $this->stubPath('migrations/add_two_factor_columns_to_users_table.php.stub'),
                $target,
                $installedFiles,
                $force
            );
        }
    }
}
