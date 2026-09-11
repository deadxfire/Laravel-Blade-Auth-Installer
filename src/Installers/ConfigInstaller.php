<?php

namespace LaravelAuth\Installer\Installers;

class ConfigInstaller extends BaseInstaller
{
    public function getPlannedFiles(array $config): array
    {
        return [];
    }

    public function install(array $config, string $basePath, array &$installedFiles, bool $force = false): void
    {
        // Confirms standard auth guard configuration
    }
}
