<?php

namespace LaravelAuth\Installer\Services;

use InvalidArgumentException;

class ConfigurationValidator
{
    /**
     * Supported theme values.
     *
     * @var list<string>
     */
    public const SUPPORTED_THEMES = ['modern', 'classic', 'minimal', 'slate'];

    /**
     * Supported feature keys.
     *
     * @var list<string>
     */
    public const SUPPORTED_FEATURES = [
        'login',
        'registration',
        'forgot_password',
        'password_reset',
        'email_verification',
        'remember_me',
        'two_factor',
    ];

    /**
     * Supported security keys.
     *
     * @var list<string>
     */
    public const SUPPORTED_SECURITY = [
        'login_rate_limiting',
        'password_confirmation',
        'session_security',
    ];

    /**
     * Validate the raw configuration payload and return the sanitized structure.
     *
     * @param  array<string, mixed>  $payload
     * @return array{version: string, framework: string, ui: array{engine: string, theme: string, css: string}, features: array<string, bool>, security: array<string, bool>}
     */
    public function validate(array $payload): array
    {
        if (empty($payload)) {
            throw new InvalidArgumentException('Configuration payload cannot be empty.');
        }

        // Check version
        $version = $payload['version'] ?? null;
        if ($version !== '1.0') {
            throw new InvalidArgumentException("Unsupported configuration version [{$version}]. Expected [1.0].");
        }

        // Check framework
        $framework = $payload['framework'] ?? null;
        if ($framework !== 'laravel') {
            throw new InvalidArgumentException("Unsupported framework target [{$framework}]. Expected [laravel].");
        }

        // Validate UI
        $ui = $payload['ui'] ?? null;
        if (! is_array($ui)) {
            throw new InvalidArgumentException('Missing or invalid [ui] configuration block.');
        }

        $engine = $ui['engine'] ?? null;
        if ($engine !== 'blade') {
            throw new InvalidArgumentException("Unsupported UI engine [{$engine}]. Only [blade] is supported.");
        }

        $css = $ui['css'] ?? null;
        if ($css !== 'tailwind') {
            throw new InvalidArgumentException("Unsupported CSS framework [{$css}]. Only [tailwind] is supported.");
        }

        $theme = $ui['theme'] ?? 'modern';
        if (! in_array($theme, self::SUPPORTED_THEMES, true)) {
            throw new InvalidArgumentException("Unsupported theme [{$theme}]. Allowed themes: ".implode(', ', self::SUPPORTED_THEMES));
        }

        // Validate Features
        $featuresInput = $payload['features'] ?? [];
        if (! is_array($featuresInput)) {
            throw new InvalidArgumentException('The [features] configuration must be an array.');
        }

        $features = [];
        foreach (self::SUPPORTED_FEATURES as $featureKey) {
            $val = $featuresInput[$featureKey] ?? false;
            if (! is_bool($val) && ! is_numeric($val) && ! is_string($val)) {
                throw new InvalidArgumentException("Feature [{$featureKey}] must be a boolean value.");
            }
            $features[$featureKey] = filter_var($val, FILTER_VALIDATE_BOOLEAN);
        }

        // Validate Security
        $securityInput = $payload['security'] ?? [];
        if (! is_array($securityInput)) {
            throw new InvalidArgumentException('The [security] configuration must be an array.');
        }

        $security = [];
        foreach (self::SUPPORTED_SECURITY as $secKey) {
            $val = $securityInput[$secKey] ?? false;
            if (! is_bool($val) && ! is_numeric($val) && ! is_string($val)) {
                throw new InvalidArgumentException("Security policy [{$secKey}] must be a boolean value.");
            }
            $security[$secKey] = filter_var($val, FILTER_VALIDATE_BOOLEAN);
        }

        // Ensure no executable PHP or string code in any values
        $this->ensureNoExecutableContent($payload);

        return [
            'version' => '1.0',
            'framework' => 'laravel',
            'ui' => [
                'engine' => 'blade',
                'theme' => $theme,
                'css' => 'tailwind',
            ],
            'features' => $features,
            'security' => $security,
        ];
    }

    /**
     * Recursively scan payload to guarantee no code injection or executable scripts are present.
     */
    protected function ensureNoExecutableContent(mixed $data): void
    {
        if (is_string($data)) {
            if (preg_match('/(<\?php|<\?=|\beval\s*\(|\bsystem\s*\(|\bexec\s*\(|\$this|\bfunction\s*\()/i', $data)) {
                throw new InvalidArgumentException('Security violation: Executable code pattern detected in configuration.');
            }
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($key) && preg_match('/(<\?php|\beval|\bsystem)/i', $key)) {
                    throw new InvalidArgumentException('Security violation: Malformed key in configuration.');
                }
                $this->ensureNoExecutableContent($value);
            }
        }
    }
}
