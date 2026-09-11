<?php

namespace LaravelAuth\Installer\Installers;

class MigrationInstaller extends BaseInstaller
{
    public function getPlannedFiles(array $config): array
    {
        // Migrations are typically already standard in Laravel or handled via artisan migrate
        return [];
    }

    public function install(array $config, string $basePath, array &$installedFiles, bool $force = false): void
    {
        // Verified: Standard Laravel projects provide the users and password_reset_tokens migrations.
    }
}
