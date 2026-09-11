<?php

namespace LaravelAuth\Installer\Installers;

class RequestInstaller extends BaseInstaller
{
    public function getPlannedFiles(array $config): array
    {
        $features = $config['features'] ?? [];
        $planned = [];

        if (! empty($features['login']) || ! empty($features['registration'])) {
            $planned[] = 'app/Http/Requests/ProfileUpdateRequest.php';
        }

        if (! empty($features['login'])) {
            $planned[] = 'app/Http/Requests/Auth/LoginRequest.php';
        }

        if (! empty($features['registration'])) {
            $planned[] = 'app/Http/Requests/Auth/RegisterRequest.php';
        }

        return $planned;
    }

    public function install(array $config, string $basePath, array &$installedFiles, bool $force = false): void
    {
        $features = $config['features'] ?? [];

        if (! empty($features['login']) || ! empty($features['registration'])) {
            $this->copyStub(
                $this->stubPath('requests/ProfileUpdateRequest.stub'),
                $basePath.'/app/Http/Requests/ProfileUpdateRequest.php',
                $installedFiles,
                $force
            );
        }

        if (! empty($features['login'])) {
            $this->copyStub(
                $this->stubPath('requests/LoginRequest.stub'),
                $basePath.'/app/Http/Requests/Auth/LoginRequest.php',
                $installedFiles,
                $force
            );
        }

        if (! empty($features['registration'])) {
            $this->copyStub(
                $this->stubPath('requests/RegisterRequest.stub'),
                $basePath.'/app/Http/Requests/Auth/RegisterRequest.php',
                $installedFiles,
                $force
            );
        }
    }
}
