<?php

namespace LaravelAuth\Installer\Installers;

class MiddlewareInstaller extends BaseInstaller
{
    public function getPlannedFiles(array $config): array
    {
        return [];
    }

    public function install(array $config, string $basePath, array &$installedFiles, bool $force = false): void
    {
        // Enforce rate limiting and session security policies via route middleware
    }
}
