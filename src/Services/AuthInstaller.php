<?php

namespace LaravelAuth\Installer\Services;

use Exception;
use Illuminate\Filesystem\Filesystem;
use LaravelAuth\Installer\Installers\BaseInstaller;
use LaravelAuth\Installer\Installers\ConfigInstaller;
use LaravelAuth\Installer\Installers\ControllerInstaller;
use LaravelAuth\Installer\Installers\MiddlewareInstaller;
use LaravelAuth\Installer\Installers\MigrationInstaller;
use LaravelAuth\Installer\Installers\RequestInstaller;
use LaravelAuth\Installer\Installers\RouteInstaller;
use LaravelAuth\Installer\Installers\ViewInstaller;

class AuthInstaller
{
    /**
     * @var list<BaseInstaller>
     */
    protected array $installers;

    public function __construct(
        protected Filesystem $files,
        ControllerInstaller $controllerInstaller,
        RequestInstaller $requestInstaller,
        ViewInstaller $viewInstaller,
        RouteInstaller $routeInstaller,
        MigrationInstaller $migrationInstaller,
        MiddlewareInstaller $middlewareInstaller,
        ConfigInstaller $configInstaller
    ) {
        $this->installers = [
            $controllerInstaller,
            $requestInstaller,
            $viewInstaller,
            $routeInstaller,
            $migrationInstaller,
            $middlewareInstaller,
            $configInstaller,
        ];
    }

    /**
     * Get all target files planned to be created or updated for the given configuration.
     *
     * @param  array<string, mixed>  $config
     * @return list<string>
     */
    public function getPlannedFiles(array $config): array
    {
        $allPlanned = [];

        foreach ($this->installers as $installer) {
            $allPlanned = array_merge($allPlanned, $installer->getPlannedFiles($config));
        }

        return array_values(array_unique($allPlanned));
    }

    /**
     * Run all component installers with automatic rollback upon failure.
     *
     * @param  array<string, mixed>  $config
     * @return list<string>
     *
     * @throws Exception
     */
    public function install(array $config, ?string $basePath = null, bool $force = false): array
    {
        $basePath = $basePath ?? base_path();
        $installedFiles = [];

        try {
            foreach ($this->installers as $installer) {
                $installer->install($config, $basePath, $installedFiles, $force);
            }

            return $installedFiles;
        } catch (Exception $e) {
            $this->rollback($installedFiles, $basePath);

            throw $e;
        }
    }

    /**
     * Roll back created files and restore modified files if an installation step fails.
     *
     * @param  list<string>  $installedFiles
     */
    public function rollback(array $installedFiles, ?string $basePath = null): void
    {
        $basePath = $basePath ?? base_path();

        foreach ($installedFiles as $file) {
            if ($this->files->exists($file)) {
                $this->files->delete($file);
            }
        }

        // Revert dangling require statement in routes/web.php if routes/auth.php is absent
        $authRoute = $basePath.'/routes/auth.php';
        $webRoute = $basePath.'/routes/web.php';
        if (! $this->files->exists($authRoute) && $this->files->exists($webRoute)) {
            $content = $this->files->get($webRoute);
            $cleaned = preg_replace("/\r?\n+require\s+__DIR__\s*\.\s*['\"]\/auth\.php['\"];\r?\n?/", "\n", $content);
            if ($cleaned !== null && $cleaned !== $content) {
                $this->files->put($webRoute, trim($cleaned)."\n");
            }
        }
    }
}
