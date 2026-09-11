<?php

namespace LaravelAuth\Installer\Services;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

class InstallationClient
{
    /**
     * Parse the installation URL and extract base host and token.
     *
     * @return array{base_url: string, token: string}
     */
    public function parseUrl(string $url): array
    {
        $trimmed = trim($url);

        if (empty($trimmed)) {
            throw new InvalidArgumentException('Installation URL cannot be empty.');
        }

        // If a direct token or preset was provided (alphanumeric string without slashes or scheme)
        if (preg_match('/^[a-zA-Z0-9_-]{3,256}$/', $trimmed)) {
            return [
                'base_url' => config('app.url', 'http://localhost:8000'),
                'token' => $trimmed,
            ];
        }

        $parsed = parse_url($trimmed);
        if (isset($parsed['scheme'])) {
            $scheme = strtolower($parsed['scheme']);
            if (! in_array($scheme, ['http', 'https'], true)) {
                throw new InvalidArgumentException("Security violation: Unsupported scheme [{$scheme}]. Only HTTP and HTTPS are permitted.");
            }
        } else {
            $scheme = 'https';
        }

        if (! isset($parsed['host'])) {
            throw new InvalidArgumentException("Invalid installation URL [{$trimmed}]. Must contain a valid host and path.");
        }

        $host = strtolower($parsed['host']);

        // Block cloud metadata services and link-local addresses (SSRF defense)
        if ($host === '169.254.169.254' || $host === '169.254.169.253' || $host === 'metadata.google.internal' || str_starts_with($host, '169.254.')) {
            throw new InvalidArgumentException("Security violation: Target host [{$host}] is blocked.");
        }

        $port = isset($parsed['port']) ? ':'.$parsed['port'] : '';
        $baseUrl = "{$scheme}://{$parsed['host']}{$port}";

        $path = trim($parsed['path'] ?? '', '/');
        $segments = explode('/', $path);

        $token = end($segments);
        if (! preg_match('/^[a-zA-Z0-9_-]{3,256}$/', $token)) {
            throw new InvalidArgumentException("Invalid installation token extracted from [{$trimmed}].");
        }

        return [
            'base_url' => $baseUrl,
            'token' => $token,
        ];
    }

    /**
     * Fetch the installation configuration from the remote Auth Builder API.
     *
     * @return array<string, mixed>
     */
    public function fetch(string $url): array
    {
        $parsed = $this->parseUrl($url);
        $baseUrl = rtrim($parsed['base_url'], '/');
        $token = $parsed['token'];

        $apiEndpoint = "{$baseUrl}/api/installations/{$token}";

        try {
            $response = Http::timeout(15)
                ->withoutRedirecting()
                ->withUserAgent('Laravel-Auth-Installer/1.0')
                ->acceptJson()
                ->get($apiEndpoint);

            // Fallback for v1 prefix if 404
            if ($response->status() === 404) {
                $fallbackEndpoint = "{$baseUrl}/api/v1/install/{$token}";
                $response = Http::timeout(15)
                    ->withoutRedirecting()
                    ->withUserAgent('Laravel-Auth-Installer/1.0')
                    ->acceptJson()
                    ->get($fallbackEndpoint);
            }
        } catch (\Exception $e) {
            throw new RuntimeException("Failed connecting to Auth Builder API at [{$apiEndpoint}]: ".$e->getMessage(), 0, $e);
        }

        if ($response->status() === 404) {
            throw new RuntimeException('Installation configuration not found or token has expired.');
        }

        if ($response->status() === 429) {
            throw new RuntimeException('Rate limit exceeded while contacting Auth Builder API. Please try again in a few moments.');
        }

        if (! $response->successful()) {
            throw new RuntimeException("Auth Builder API returned unexpected HTTP status [{$response->status()}].");
        }

        $json = $response->json();
        if (! is_array($json)) {
            throw new RuntimeException('Invalid JSON payload received from Auth Builder API.');
        }

        // Support both "installation" key and root configuration
        if (isset($json['installation']['configuration'])) {
            return [
                'version' => $json['installation']['version'] ?? '1.0',
                'framework' => $json['installation']['framework'] ?? 'laravel',
                'ui' => $json['installation']['configuration']['ui'] ?? [],
                'features' => $json['installation']['configuration']['features'] ?? [],
                'security' => $json['installation']['configuration']['security'] ?? [],
            ];
        }

        if (isset($json['configuration'])) {
            return $json['configuration'];
        }

        return $json;
    }
}
