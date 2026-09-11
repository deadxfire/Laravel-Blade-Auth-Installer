<?php

namespace LaravelAuth\Installer\Installers;

class ViewInstaller extends BaseInstaller
{
    /**
     * Get a list of relative target file paths that this installer will create or modify.
     *
     * @param  array<string, mixed>  $config
     * @return list<string>
     */
    public function getPlannedFiles(array $config): array
    {
        $features = $config['features'] ?? [];
        $security = $config['security'] ?? [];

        $planned = [
            'resources/views/components/auth/auth-layout.blade.php',
            'resources/views/components/auth/alert.blade.php',
            'resources/views/components/auth/validation-errors.blade.php',
            'resources/views/components/auth/input.blade.php',
            'resources/views/components/auth/password-input.blade.php',
            'resources/views/components/auth/button.blade.php',
            'resources/views/components/auth/checkbox.blade.php',
            'resources/views/components/auth-layout.blade.php',
            'resources/views/layouts/auth.blade.php',
        ];

        if (! empty($features['login']) || ! empty($features['registration'])) {
            $planned[] = 'resources/views/dashboard.blade.php';
            $planned[] = 'resources/views/profile/edit.blade.php';
        }

        if (! empty($features['login'])) {
            $planned[] = 'resources/views/auth/login.blade.php';
        }

        if (! empty($features['registration'])) {
            $planned[] = 'resources/views/auth/register.blade.php';
        }

        if (! empty($features['forgot_password'])) {
            $planned[] = 'resources/views/auth/forgot-password.blade.php';
        }

        if (! empty($features['password_reset'])) {
            $planned[] = 'resources/views/auth/reset-password.blade.php';
        }

        if (! empty($features['email_verification'])) {
            $planned[] = 'resources/views/auth/verify-email.blade.php';
        }

        if (! empty($security['password_confirmation'])) {
            $planned[] = 'resources/views/auth/confirm-password.blade.php';
        }

        return array_values(array_unique($planned));
    }

    /**
     * Install authentication blade views and components.
     *
     * @param  array<string, mixed>  $config
     * @param  list<string>  $installedFiles
     */
    public function install(array $config, string $basePath, array &$installedFiles, bool $force = false): void
    {
        $features = $config['features'] ?? [];
        $security = $config['security'] ?? [];
        $theme = $config['ui']['theme'] ?? 'modern';

        // Theme-specific color replacements
        $replacements = $this->getThemeReplacements($theme);

        // 1. Reusable Auth Components
        $components = [
            'auth-layout.blade.stub' => [
                'resources/views/components/auth/auth-layout.blade.php',
                'resources/views/components/auth-layout.blade.php',
                'resources/views/components/auth/layout.blade.php',
                'resources/views/components/layouts/auth.blade.php',
                'resources/views/components/layouts/guest.blade.php',
                'resources/views/components/guest-layout.blade.php',
            ],
            'alert.blade.stub' => [
                'resources/views/components/auth/alert.blade.php',
                'resources/views/components/auth-alert.blade.php',
            ],
            'validation-errors.blade.stub' => [
                'resources/views/components/auth/validation-errors.blade.php',
                'resources/views/components/auth-validation-errors.blade.php',
            ],
            'input.blade.stub' => [
                'resources/views/components/auth/input.blade.php',
                'resources/views/components/auth-input.blade.php',
            ],
            'password-input.blade.stub' => [
                'resources/views/components/auth/password-input.blade.php',
                'resources/views/components/auth-password-input.blade.php',
            ],
            'button.blade.stub' => [
                'resources/views/components/auth/button.blade.php',
                'resources/views/components/auth-button.blade.php',
            ],
            'checkbox.blade.stub' => [
                'resources/views/components/auth/checkbox.blade.php',
                'resources/views/components/auth-checkbox.blade.php',
            ],
        ];

        foreach ($components as $stub => $targets) {
            foreach ($targets as $target) {
                $this->copyStub(
                    $this->stubPath('views/components/auth/'.$stub),
                    $basePath.'/'.$target,
                    $installedFiles,
                    $force,
                    $replacements
                );
            }
        }

        // 2. Base layout fallback
        $this->copyStub(
            $this->stubPath('views/layout.blade.stub'),
            $basePath.'/resources/views/layouts/auth.blade.php',
            $installedFiles,
            $force,
            $replacements
        );

        $this->copyStub(
            $this->stubPath('views/layout.blade.stub'),
            $basePath.'/resources/views/layouts/guest.blade.php',
            $installedFiles,
            $force,
            $replacements
        );

        // 3. Dashboard and Profile Views
        if (! empty($features['login']) || ! empty($features['registration'])) {
            $this->copyStub(
                $this->stubPath('views/dashboard.blade.stub'),
                $basePath.'/resources/views/dashboard.blade.php',
                $installedFiles,
                $force,
                $replacements
            );

            $this->copyStub(
                $this->stubPath('views/profile/edit.blade.stub'),
                $basePath.'/resources/views/profile/edit.blade.php',
                $installedFiles,
                $force,
                $replacements
            );
        }

        // 4. Feature Views
        if (! empty($features['login'])) {
            $this->copyStub(
                $this->stubPath('views/login.blade.stub'),
                $basePath.'/resources/views/auth/login.blade.php',
                $installedFiles,
                $force,
                $replacements
            );
        }

        if (! empty($features['registration'])) {
            $this->copyStub(
                $this->stubPath('views/register.blade.stub'),
                $basePath.'/resources/views/auth/register.blade.php',
                $installedFiles,
                $force,
                $replacements
            );
        }

        if (! empty($features['forgot_password'])) {
            $this->copyStub(
                $this->stubPath('views/forgot-password.blade.stub'),
                $basePath.'/resources/views/auth/forgot-password.blade.php',
                $installedFiles,
                $force,
                $replacements
            );
        }

        if (! empty($features['password_reset'])) {
            $this->copyStub(
                $this->stubPath('views/reset-password.blade.stub'),
                $basePath.'/resources/views/auth/reset-password.blade.php',
                $installedFiles,
                $force,
                $replacements
            );
        }

        if (! empty($features['email_verification'])) {
            $this->copyStub(
                $this->stubPath('views/verify-email.blade.stub'),
                $basePath.'/resources/views/auth/verify-email.blade.php',
                $installedFiles,
                $force,
                $replacements
            );
        }

        if (! empty($security['password_confirmation'])) {
            $this->copyStub(
                $this->stubPath('views/confirm-password.blade.stub'),
                $basePath.'/resources/views/auth/confirm-password.blade.php',
                $installedFiles,
                $force,
                $replacements
            );
        }
    }

    /**
     * Resolve Tailwind CSS theme color replacements.
     *
     * @return array<string, string>
     */
    protected function getThemeReplacements(string $theme): array
    {
        return match ($theme) {
            'classic' => [
                'from-indigo-600 to-indigo-500' => 'from-blue-600 to-blue-500',
                'hover:from-indigo-500 hover:to-indigo-400' => 'hover:from-blue-500 hover:to-blue-400',
                'from-indigo-600 via-indigo-500 to-purple-500' => 'from-blue-600 via-blue-500 to-cyan-500',
                'from-indigo-950/60 via-slate-900 to-slate-900' => 'from-blue-950/60 via-slate-900 to-slate-900',
                'focus:border-indigo-500' => 'focus:border-blue-500',
                'focus:ring-indigo-500' => 'focus:ring-blue-500',
                'text-indigo-400' => 'text-blue-400',
                'text-indigo-300' => 'text-blue-300',
                'bg-indigo-600' => 'bg-blue-600',
                'bg-indigo-500' => 'bg-blue-500',
                'border-indigo-500' => 'border-blue-500',
                'shadow-indigo-600' => 'shadow-blue-600',
                'shadow-indigo-500' => 'shadow-blue-500',
            ],
            'minimal' => [
                'from-indigo-600 to-indigo-500' => 'from-zinc-100 to-zinc-200 text-black',
                'hover:from-indigo-500 hover:to-indigo-400' => 'hover:from-white hover:to-zinc-100 text-black',
                'from-indigo-600 via-indigo-500 to-purple-500' => 'from-zinc-100 via-zinc-200 to-zinc-300',
                'from-indigo-950/60 via-slate-900 to-slate-900' => 'from-zinc-900 via-zinc-950 to-black',
                'focus:border-indigo-500' => 'focus:border-white',
                'focus:ring-indigo-500' => 'focus:ring-white',
                'text-indigo-400' => 'text-zinc-300',
                'text-indigo-300' => 'text-white',
                'bg-indigo-600' => 'bg-white text-black',
                'bg-indigo-500' => 'bg-zinc-200 text-black',
                'border-indigo-500' => 'border-zinc-700',
                'shadow-indigo-600' => 'shadow-zinc-900',
                'shadow-indigo-500' => 'shadow-zinc-900',
            ],
            'slate' => [
                'from-indigo-600 to-indigo-500' => 'from-emerald-600 to-emerald-500',
                'hover:from-indigo-500 hover:to-indigo-400' => 'hover:from-emerald-500 hover:to-emerald-400',
                'from-indigo-600 via-indigo-500 to-purple-500' => 'from-emerald-600 via-teal-500 to-emerald-500',
                'from-indigo-950/60 via-slate-900 to-slate-900' => 'from-emerald-950/60 via-slate-900 to-slate-900',
                'focus:border-indigo-500' => 'focus:border-emerald-500',
                'focus:ring-indigo-500' => 'focus:ring-emerald-500',
                'text-indigo-400' => 'text-emerald-400',
                'text-indigo-300' => 'text-emerald-300',
                'bg-indigo-600' => 'bg-emerald-600',
                'bg-indigo-500' => 'bg-emerald-500',
                'border-indigo-500' => 'border-emerald-500',
                'shadow-indigo-600' => 'shadow-emerald-600',
                'shadow-indigo-500' => 'shadow-emerald-500',
            ],
            'simple' => [
                'from-indigo-600 to-indigo-500' => 'from-sky-600 to-sky-500',
                'hover:from-indigo-500 hover:to-indigo-400' => 'hover:from-sky-500 hover:to-sky-400',
                'from-indigo-600 via-indigo-500 to-purple-500' => 'from-sky-600 via-sky-500 to-cyan-500',
                'from-indigo-950/60 via-slate-900 to-slate-900' => 'from-sky-950/60 via-slate-900 to-slate-900',
                'focus:border-indigo-500' => 'focus:border-sky-500',
                'focus:ring-indigo-500' => 'focus:ring-sky-500',
                'text-indigo-400' => 'text-sky-400',
                'text-indigo-300' => 'text-sky-300',
                'bg-indigo-600' => 'bg-sky-600',
                'bg-indigo-500' => 'bg-sky-500',
                'border-indigo-500' => 'border-sky-500',
                'shadow-indigo-600' => 'shadow-sky-600',
                'shadow-indigo-500' => 'shadow-sky-500',
            ],
            default => [],
        };
    }
}
