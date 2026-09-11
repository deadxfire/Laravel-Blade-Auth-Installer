<?php

namespace LaravelAuth\Installer\Installers;

class ControllerInstaller extends BaseInstaller
{
    public function getPlannedFiles(array $config): array
    {
        $features = $config['features'] ?? [];
        $security = $config['security'] ?? [];
        $planned = [];

        if (! empty($features['login']) || ! empty($features['registration'])) {
            $planned[] = 'app/Http/Controllers/DashboardController.php';
            $planned[] = 'app/Http/Controllers/ProfileController.php';
        }

        if (! empty($features['login'])) {
            $planned[] = 'app/Http/Controllers/Auth/LoginController.php';
        }

        if (! empty($features['registration'])) {
            $planned[] = 'app/Http/Controllers/Auth/RegisterController.php';
        }

        if (! empty($features['forgot_password'])) {
            $planned[] = 'app/Http/Controllers/Auth/ForgotPasswordController.php';
        }

        if (! empty($features['password_reset'])) {
            $planned[] = 'app/Http/Controllers/Auth/ResetPasswordController.php';
        }

        if (! empty($features['email_verification'])) {
            $planned[] = 'app/Http/Controllers/Auth/EmailVerificationPromptController.php';
            $planned[] = 'app/Http/Controllers/Auth/VerifyEmailController.php';
            $planned[] = 'app/Http/Controllers/Auth/EmailVerificationNotificationController.php';
        }

        if (! empty($security['password_confirmation'])) {
            $planned[] = 'app/Http/Controllers/Auth/ConfirmablePasswordController.php';
        }

        return $planned;
    }

    public function install(array $config, string $basePath, array &$installedFiles, bool $force = false): void
    {
        $features = $config['features'] ?? [];
        $security = $config['security'] ?? [];

        if (! empty($features['login']) || ! empty($features['registration'])) {
            $this->copyStub(
                $this->stubPath('controllers/DashboardController.stub'),
                $basePath.'/app/Http/Controllers/DashboardController.php',
                $installedFiles,
                $force
            );

            $this->copyStub(
                $this->stubPath('controllers/ProfileController.stub'),
                $basePath.'/app/Http/Controllers/ProfileController.php',
                $installedFiles,
                $force
            );
        }

        if (! empty($features['login'])) {
            $this->copyStub(
                $this->stubPath('controllers/LoginController.stub'),
                $basePath.'/app/Http/Controllers/Auth/LoginController.php',
                $installedFiles,
                $force
            );
        }

        if (! empty($features['registration'])) {
            $this->copyStub(
                $this->stubPath('controllers/RegisterController.stub'),
                $basePath.'/app/Http/Controllers/Auth/RegisterController.php',
                $installedFiles,
                $force
            );
        }

        if (! empty($features['forgot_password'])) {
            $this->copyStub(
                $this->stubPath('controllers/ForgotPasswordController.stub'),
                $basePath.'/app/Http/Controllers/Auth/ForgotPasswordController.php',
                $installedFiles,
                $force
            );
        }

        if (! empty($features['password_reset'])) {
            $this->copyStub(
                $this->stubPath('controllers/ResetPasswordController.stub'),
                $basePath.'/app/Http/Controllers/Auth/ResetPasswordController.php',
                $installedFiles,
                $force
            );
        }

        if (! empty($features['email_verification'])) {
            $this->copyStub(
                $this->stubPath('controllers/EmailVerificationPromptController.stub'),
                $basePath.'/app/Http/Controllers/Auth/EmailVerificationPromptController.php',
                $installedFiles,
                $force
            );
            $this->copyStub(
                $this->stubPath('controllers/VerifyEmailController.stub'),
                $basePath.'/app/Http/Controllers/Auth/VerifyEmailController.php',
                $installedFiles,
                $force
            );
            $this->copyStub(
                $this->stubPath('controllers/EmailVerificationNotificationController.stub'),
                $basePath.'/app/Http/Controllers/Auth/EmailVerificationNotificationController.php',
                $installedFiles,
                $force
            );
        }

        if (! empty($security['password_confirmation'])) {
            $this->copyStub(
                $this->stubPath('controllers/ConfirmablePasswordController.stub'),
                $basePath.'/app/Http/Controllers/Auth/ConfirmablePasswordController.php',
                $installedFiles,
                $force
            );
        }
    }
}
