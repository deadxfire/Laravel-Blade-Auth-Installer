<?php

namespace LaravelAuth\Installer\Installers;

class RouteInstaller extends BaseInstaller
{
    public function getPlannedFiles(array $config): array
    {
        return [
            'routes/auth.php',
        ];
    }

    public function install(array $config, string $basePath, array &$installedFiles, bool $force = false): void
    {
        $authRoutesPath = $basePath.'/routes/auth.php';

        $this->copyStub(
            $this->stubPath('routes/auth.php.stub'),
            $authRoutesPath,
            $installedFiles,
            $force
        );

        // Ensure routes/web.php includes routes/auth.php
        $webRoutesPath = $basePath.'/routes/web.php';
        if ($this->files->exists($webRoutesPath)) {
            $webContent = $this->files->get($webRoutesPath);
            if (! str_contains($webContent, "require __DIR__.'/auth.php';") && ! str_contains($webContent, "require __DIR__ . '/auth.php';")) {
                $this->files->append($webRoutesPath, "\n\nrequire __DIR__.'/auth.php';\n");
            }
        }
    }
}
