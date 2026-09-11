<?php

namespace LaravelAuth\Installer\Installers;

use Illuminate\Filesystem\Filesystem;

abstract class BaseInstaller
{
    public function __construct(
        protected Filesystem $files
    ) {}

    /**
     * Get a list of relative target file paths that this installer will create or modify.
     *
     * @param  array<string, mixed>  $config
     * @return list<string>
     */
    abstract public function getPlannedFiles(array $config): array;

    /**
     * Execute the component installation.
     *
     * @param  array<string, mixed>  $config
     * @param  list<string>  $installedFiles  Reference list tracking created files for rollback.
     */
    abstract public function install(array $config, string $basePath, array &$installedFiles, bool $force = false): void;

    /**
     * Helper to safely copy a stub file to destination, ensuring parent directories exist and no path traversal occurs.
     */
    protected function copyStub(string $stubPath, string $destinationPath, array &$installedFiles, bool $force = false, array $replacements = []): void
    {
        // Enforce anti-traversal security
        if (str_contains($destinationPath, '..') || str_contains($destinationPath, "\0")) {
            throw new \InvalidArgumentException("Security violation: Path traversal sequence detected in destination path [{$destinationPath}].");
        }

        if (! $this->files->exists($stubPath)) {
            throw new \InvalidArgumentException("Source stub [{$stubPath}] does not exist.");
        }

        if ($this->files->exists($destinationPath) && ! $force) {
            return;
        }

        $directory = dirname($destinationPath);
        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        if (! empty($replacements)) {
            $content = $this->files->get($stubPath);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
            $this->files->put($destinationPath, $content);
        } else {
            $this->files->copy($stubPath, $destinationPath);
        }

        $installedFiles[] = $destinationPath;
    }

    /**
     * Resolve stub path inside resources/stubs with path traversal protection.
     */
    protected function stubPath(string $relative): string
    {
        if (str_contains($relative, '..') || str_contains($relative, "\0")) {
            throw new \InvalidArgumentException("Security violation: Path traversal sequence detected in stub path [{$relative}].");
        }

        return __DIR__.'/../../resources/stubs/'.$relative;
    }
}
