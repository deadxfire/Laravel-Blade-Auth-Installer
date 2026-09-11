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
            $planned[] = 'resources/views/layouts/app.blade.php';
            $planned[] = 'resources/views/components/app-layout.blade.php';
            $planned[] = 'resources/views/components/layouts/app.blade.php';
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

        if (! empty($features['two_factor'])) {
            $planned[] = 'resources/views/auth/two-factor-challenge.blade.php';
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

        // 3. Authenticated App Layout, Dashboard, and Profile Views
        if (! empty($features['login']) || ! empty($features['registration'])) {
            $appLayoutTargets = [
                'resources/views/layouts/app.blade.php',
                'resources/views/components/app-layout.blade.php',
                'resources/views/components/layouts/app.blade.php',
            ];

            foreach ($appLayoutTargets as $target) {
                $this->copyStub(
                    $this->stubPath('views/app-layout.blade.stub'),
                    $basePath.'/'.$target,
                    $installedFiles,
                    $force,
                    $replacements
                );
            }

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

        if (! empty($features['two_factor'])) {
            $this->copyStub(
                $this->stubPath('views/auth/two-factor-challenge.blade.stub'),
                $basePath.'/resources/views/auth/two-factor-challenge.blade.php',
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
                'h-full bg-slate-950 text-slate-100 antialiased' => 'h-full bg-slate-100 text-slate-900 antialiased',
                'h-full bg-slate-950 text-slate-100' => 'h-full bg-slate-50 text-slate-900',
                'min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 selection:bg-indigo-500 selection:text-white' => 'min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-100 selection:bg-blue-600 selection:text-white',
                'bg-slate-950 text-slate-200' => 'bg-slate-100 text-slate-800',
                'bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-black/60 space-y-6' => 'bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xl shadow-slate-300/40 space-y-6',
                'bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl' => 'bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xl shadow-slate-300/40',
                'from-indigo-600 via-indigo-500 to-purple-500' => 'from-blue-600 to-blue-700',
                'from-indigo-600 to-indigo-500' => 'from-blue-600 to-blue-600',
                'hover:from-indigo-500 hover:to-indigo-400' => 'hover:from-blue-700 hover:to-blue-700',
                'from-indigo-950/60 via-slate-900 to-slate-900' => 'from-blue-100 via-slate-50 to-white',
                'shadow-indigo-500/25' => 'shadow-blue-600/20',
                'shadow-indigo-600/25' => 'shadow-blue-600/20',
                'tracking-tight text-white' => 'tracking-tight text-slate-900',
                'text-slate-400' => 'text-slate-500',
                'text-slate-300' => 'text-slate-700',
                'bg-slate-950 px-3.5 py-2.5 text-sm text-white placeholder-slate-500' => 'bg-slate-50 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400',
                'border border-slate-700' => 'border border-slate-300',
                'focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20' => 'focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20',
                'focus:border-indigo-500' => 'focus:border-blue-500',
                'focus:ring-indigo-500' => 'focus:ring-blue-500',
                'border-slate-700 bg-slate-950 text-indigo-600' => 'border-slate-300 bg-white text-blue-600',
                'text-indigo-400 hover:text-indigo-300' => 'text-blue-600 hover:text-blue-700',
                'text-indigo-400' => 'text-blue-600',
                'text-indigo-300' => 'text-blue-700',
                'bg-indigo-600' => 'bg-blue-600',
                'bg-indigo-500' => 'bg-blue-500',
                'border-indigo-500' => 'border-blue-500',
                'border-b border-slate-800 bg-slate-900/80' => 'border-b border-slate-200 bg-white shadow-sm',
                'bg-slate-900 border border-slate-800' => 'bg-white border border-slate-200 shadow-sm',
                'bg-slate-900/60 border border-slate-800' => 'bg-white border border-slate-200 shadow-sm',
                'bg-slate-800 hover:bg-slate-700 text-slate-200' => 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200',
                'border-slate-800' => 'border-slate-200',
            ],
            'minimal' => [
                'h-full bg-slate-950 text-slate-100 antialiased' => 'h-full bg-zinc-100 text-black antialiased',
                'h-full bg-slate-950 text-slate-100' => 'h-full bg-zinc-100 text-black',
                'min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 selection:bg-indigo-500 selection:text-white' => 'min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-zinc-100 selection:bg-black selection:text-white',
                'bg-slate-950 text-slate-200' => 'bg-zinc-100 text-black',
                'bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-black/60 space-y-6' => 'bg-white border-2 border-black rounded-none p-6 sm:p-8 shadow-[8px_8px_0px_0px_#000000] space-y-6',
                'bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl' => 'bg-white border-2 border-black rounded-none p-6 sm:p-8 shadow-[8px_8px_0px_0px_#000000]',
                'w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-500 flex items-center justify-center shadow-xl shadow-indigo-500/25' => 'w-12 h-12 rounded-none bg-black text-white border-2 border-black flex items-center justify-center font-mono font-black',
                'tracking-tight text-white' => 'uppercase tracking-tight text-black font-black',
                'text-slate-400' => 'text-zinc-600 font-medium',
                'text-slate-300' => 'text-black font-bold uppercase tracking-wider',
                'rounded-xl bg-slate-950 px-3.5 py-2.5 text-sm text-white placeholder-slate-500' => 'rounded-none bg-white px-3.5 py-2.5 text-sm text-black placeholder-zinc-400 font-mono',
                'border border-slate-700' => 'border-2 border-black',
                'focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20' => 'focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2',
                'border-slate-700 bg-slate-950 text-indigo-600' => 'border-2 border-black bg-white text-black',
                'rounded-xl px-4 py-2.5' => 'rounded-none px-4 py-2.5 uppercase font-mono tracking-wider font-bold',
                'bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white focus:ring-indigo-500/40 shadow-lg shadow-indigo-600/25 active:scale-[0.99]' => 'bg-black hover:bg-zinc-800 text-white rounded-none border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,0.3)] active:translate-x-0.5 active:translate-y-0.5',
                'from-indigo-600 to-indigo-500' => 'from-zinc-900 to-black',
                'hover:from-indigo-500 hover:to-indigo-400' => 'hover:from-black hover:to-zinc-900',
                'from-indigo-600 via-indigo-500 to-purple-500' => 'from-zinc-100 via-zinc-200 to-zinc-300',
                'from-indigo-950/60 via-slate-900 to-slate-900' => 'from-zinc-900 via-zinc-950 to-black',
                'focus:border-indigo-500' => 'focus:border-black',
                'focus:ring-indigo-500' => 'focus:ring-black',
                'text-indigo-400 hover:text-indigo-300' => 'text-black underline font-bold hover:text-zinc-600',
                'text-indigo-400' => 'text-black font-bold',
                'text-indigo-300' => 'text-zinc-800',
                'bg-indigo-600' => 'bg-black text-white',
                'bg-indigo-500' => 'bg-zinc-900 text-white',
                'border-indigo-500' => 'border-black',
                'border-b border-slate-800 bg-slate-900/80' => 'border-b-2 border-black bg-white text-black',
                'bg-slate-900 border border-slate-800 rounded-2xl' => 'bg-white border-2 border-black rounded-none shadow-[4px_4px_0px_0px_#000000] text-black',
                'bg-slate-900/60 border border-slate-800 rounded-xl' => 'bg-white border-2 border-black rounded-none text-black',
                'bg-slate-800 hover:bg-slate-700 text-slate-200' => 'bg-black hover:bg-zinc-800 text-white rounded-none border border-black',
                'border-slate-800' => 'border-black',
            ],
            'slate' => [
                'h-full bg-slate-950 text-slate-100 antialiased' => 'h-full bg-[#0a0f12] text-slate-200 antialiased font-mono',
                'h-full bg-slate-950 text-slate-100' => 'h-full bg-[#0a0f12] text-slate-200 font-mono',
                'min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 selection:bg-indigo-500 selection:text-white' => 'min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-[#0a0f12] selection:bg-emerald-500 selection:text-slate-950',
                'bg-slate-950 text-slate-200' => 'bg-[#0a0f12] text-slate-200',
                'bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-black/60 space-y-6' => 'bg-[#11181c] border border-emerald-500/25 rounded-xl p-6 sm:p-8 shadow-2xl shadow-emerald-950/40 space-y-6 font-mono',
                'bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl' => 'bg-[#11181c] border border-emerald-500/25 rounded-xl p-6 sm:p-8 shadow-2xl shadow-emerald-950/40 font-mono',
                'from-indigo-600 via-indigo-500 to-purple-500' => 'from-emerald-600 via-teal-500 to-emerald-500',
                'from-indigo-600 to-indigo-500' => 'from-emerald-600 to-teal-600',
                'hover:from-indigo-500 hover:to-indigo-400' => 'hover:from-emerald-500 hover:to-teal-500',
                'from-indigo-950/60 via-slate-900 to-slate-900' => 'from-emerald-950/60 via-slate-900 to-slate-900',
                'shadow-indigo-500/25' => 'shadow-emerald-500/20',
                'shadow-indigo-600/25' => 'shadow-emerald-600/30',
                'tracking-tight text-white' => 'tracking-tight text-emerald-400 font-mono',
                'text-slate-400' => 'text-emerald-400/60 font-mono text-xs',
                'text-slate-300' => 'text-emerald-300/80 font-mono',
                'bg-slate-950 px-3.5 py-2.5 text-sm text-white placeholder-slate-500' => 'bg-[#070b0e] px-3.5 py-2.5 text-sm text-emerald-300 font-mono placeholder-slate-600',
                'border border-slate-700' => 'border border-slate-700/80',
                'focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20' => 'focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20',
                'focus:border-indigo-500' => 'focus:border-emerald-500',
                'focus:ring-indigo-500' => 'focus:ring-emerald-500',
                'border-slate-700 bg-slate-950 text-indigo-600' => 'border-slate-700 bg-[#070b0e] text-emerald-500',
                'text-indigo-400 hover:text-indigo-300' => 'text-emerald-400 hover:text-emerald-300 font-mono',
                'text-indigo-400' => 'text-emerald-400',
                'text-indigo-300' => 'text-emerald-300',
                'bg-indigo-600' => 'bg-emerald-600 text-slate-950',
                'bg-indigo-500' => 'bg-emerald-500 text-slate-950',
                'border-indigo-500' => 'border-emerald-500',
                'border-b border-slate-800 bg-slate-900/80' => 'border-b border-emerald-500/20 bg-[#0c1216] font-mono',
                'bg-slate-900 border border-slate-800' => 'bg-[#11181c] border border-emerald-500/20',
                'bg-slate-900/60 border border-slate-800' => 'bg-[#11181c]/80 border border-emerald-500/20',
                'border-slate-800' => 'border-slate-800/80',
            ],
            'simple' => [
                'h-full bg-slate-950 text-slate-100 antialiased' => 'h-full bg-slate-50 text-slate-800 antialiased',
                'h-full bg-slate-950 text-slate-100' => 'h-full bg-slate-50 text-slate-800',
                'min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 selection:bg-indigo-500 selection:text-white' => 'min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50 selection:bg-sky-500 selection:text-white',
                'bg-slate-950 text-slate-200' => 'bg-slate-50 text-slate-700',
                'bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-black/60 space-y-6' => 'bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/60 space-y-6',
                'bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl' => 'bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/60',
                'from-indigo-600 via-indigo-500 to-purple-500' => 'from-sky-500 to-cyan-500',
                'from-indigo-600 to-indigo-500' => 'from-sky-500 to-sky-500',
                'hover:from-indigo-500 hover:to-indigo-400' => 'hover:from-sky-600 hover:to-sky-600',
                'from-indigo-950/60 via-slate-900 to-slate-900' => 'from-sky-100 via-slate-50 to-white',
                'shadow-indigo-500/25' => 'shadow-sky-500/20',
                'shadow-indigo-600/25' => 'shadow-sky-500/20',
                'tracking-tight text-white' => 'text-slate-800 font-semibold tracking-normal',
                'text-slate-400' => 'text-slate-500',
                'text-slate-300' => 'text-slate-600 font-medium',
                'rounded-xl bg-slate-950 px-3.5 py-2.5 text-sm text-white placeholder-slate-500' => 'rounded-2xl bg-slate-100/80 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400',
                'border border-slate-700' => 'border border-transparent',
                'focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20' => 'focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20',
                'focus:border-indigo-500' => 'focus:border-sky-500',
                'focus:ring-indigo-500' => 'focus:ring-sky-500',
                'border-slate-700 bg-slate-950 text-indigo-600' => 'border-slate-300 bg-slate-100 text-sky-600',
                'rounded-xl px-4 py-2.5' => 'rounded-2xl px-4 py-2.5 font-medium',
                'text-indigo-400 hover:text-indigo-300' => 'text-sky-600 hover:text-sky-700',
                'text-indigo-400' => 'text-sky-600',
                'text-indigo-300' => 'text-sky-700',
                'bg-indigo-600' => 'bg-sky-500',
                'bg-indigo-500' => 'bg-sky-400',
                'border-indigo-500' => 'border-sky-500',
                'border-b border-slate-800 bg-slate-900/80' => 'border-b border-slate-200 bg-white/90 backdrop-blur-md text-slate-800',
                'bg-slate-900 border border-slate-800 rounded-2xl' => 'bg-white border border-slate-200/80 rounded-3xl shadow-sm text-slate-800',
                'bg-slate-900/60 border border-slate-800 rounded-xl' => 'bg-white border border-slate-200/80 rounded-2xl shadow-sm text-slate-800',
                'bg-slate-800 hover:bg-slate-700 text-slate-200' => 'bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl',
                'border-slate-800' => 'border-slate-200',
            ],
            default => [],
        };
    }
}
