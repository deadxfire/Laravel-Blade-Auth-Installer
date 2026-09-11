# Laravel Blade Auth Installer

> **Production-Ready, OWASP-Hardened Laravel Authentication Scaffolding with 2FA (TOTP), Metrics Dashboard, User Profile, and 5 Distinct Tailwind CSS Themes.**
> Created by **Arindam Makar** &bull; Powered by **[Laravel Auth Builder](http://laravelbuild.laureon.in)**.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/arindammakar/laravel-auth-installer.svg?style=flat-square)](https://packagist.org/packages/arindammakar/laravel-auth-installer)
[![Total Downloads](https://img.shields.io/packagist/dt/arindammakar/laravel-auth-installer.svg?style=flat-square)](https://packagist.org/packages/arindammakar/laravel-auth-installer)
[![License: MIT](https://img.shields.io/badge/License-MIT-indigo.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.2-blue.svg?style=flat-square)](https://php.net)
[![Laravel Framework](https://img.shields.io/badge/Laravel-11%20%7C%2012%20%7C%2013-red.svg?style=flat-square)](https://laravel.com)

A modern, zero-dependency alternative to Laravel Breeze that imports secure, production-ready authentication scaffolding directly into any Laravel application with zero Node.js build steps required.

---

## Key Features & Capabilities

- 🚀 **Zero-Build Instant Setup**: Works out of the box with embedded Tailwind CDN fallback &mdash; no `npm run build` or Node.js required.
- 🎨 **5 Distinct Design Themes**:
  - **Modern Indigo Glass**: Deep space dark glassmorphism with glowing purple/indigo accents.
  - **Classic Corporate SaaS**: Pure light mode enterprise aesthetic with crisp white cards and royal blue buttons.
  - **Minimal Brutalist Monochrome**: High-contrast black & white Swiss architecture with 2px borders and hard shadows.
  - **DevOps Cyber Charcoal Slate**: Gunmetal dark mode with phosphor emerald green monospace typography.
  - **Simple Clean Airy**: Warm light mode with pill-rounded inputs and sky-blue accents.
- 🔐 **Two-Factor Authentication (2FA)**: Zero-dependency pure PHP RFC 6238 TOTP engine (Google Authenticator, Authy, 1Password) with QR code setup, 6-digit confirmation, and 8 emergency recovery codes.
- 📊 **Complete Metrics Dashboard**: Built-in responsive dashboard layout with security metrics, recent activity, and navigation.
- 👤 **Full User Profile**: Credential management, secure password updates (with current password verification), 2FA controls, and password-confirmed account deletion.
- ✉️ **Email Verification & Password Confirmation**: Throttled verification notifications and sensitive area password confirmation.
- 🛡️ **OWASP Hardened**:
  - Open redirect defense on login via path sanitization.
  - Brute-force rate limiting with exponential delays.
  - Constant-time verification on security challenges.
  - Session fixation and CSRF protection.
  - Anti-enumeration email responses on password resets.
- 🧩 **Universal Blade Layouts**: Automatic aliasing for `<x-auth-layout>`, `<x-layouts.auth>`, `<x-auth.auth-layout>`, `<x-guest-layout>`, `<x-layouts.guest>`.

---

## Requirements

- **PHP**: ^8.2 or ^8.3 or ^8.4 or ^8.5
- **Laravel**: ^11.0, ^12.0, or ^13.0

---

## Installation

Install the package into your Laravel application via Composer:

```bash
composer require arindammakar/laravel-auth-installer --dev
```

---

## Usage

Generate and copy your preferred design link from **[laravelbuild.laureon.in](http://laravelbuild.laureon.in)**, then run:

```bash
php artisan auth:import http://laravelbuild.laureon.in/install/default
```

### Or choose a specific design preset:

```bash
# Modern Indigo
php artisan auth:import http://laravelbuild.laureon.in/install/modern

# Classic Corporate Blue
php artisan auth:import http://laravelbuild.laureon.in/install/classic

# Minimal Monochrome
php artisan auth:import http://laravelbuild.laureon.in/install/minimal

# Slate Zinc
php artisan auth:import http://laravelbuild.laureon.in/install/slate

# Simple Clean
php artisan auth:import http://laravelbuild.laureon.in/install/simple
```

### Next Steps after Import:

```bash
# Run migrations to create 2FA columns on users table
php artisan migrate
```

### Options

```bash
# Overwrite existing conflicting files to switch theme or update scaffolding:
php artisan auth:import http://laravelbuild.laureon.in/install/default --force

# Preview what files will be created without modifying the project:
php artisan auth:import http://laravelbuild.laureon.in/install/default --dry-run
```

---

## What Gets Installed

1. **Controllers**: Authentication controllers (`LoginController.php`, `RegisterController.php`, etc.), `TwoFactorAuthenticationController.php`, `TwoFactorAuthenticatedSessionController.php`, `DashboardController.php`, `ProfileController.php`.
2. **Services**: Pure RFC 6238 TOTP Engine (`app/Services/Auth/TotpService.php`) &mdash; zero external dependencies required.
3. **Form Requests**: Validated and throttled inputs (`LoginRequest.php`, `RegisterRequest.php`, `ProfileUpdateRequest.php`).
4. **Views**: 5 distinct design themes across Blade templates (`resources/views/auth/*`, `resources/views/components/auth/*`, `resources/views/dashboard.blade.php`, `resources/views/profile/edit.blade.php`).
5. **Routes**: Clean, isolated auth routes (`routes/auth.php` loaded automatically into `routes/web.php`).
6. **Migrations**: Non-destructive 2FA columns migration (`database/migrations/xxxx_xx_xx_xxxxxx_add_two_factor_columns_to_users_table.php`).

---

## Credits & Author

- **Author & Provider**: Arindam Makar
- **Platform**: [Laravel Auth Builder](http://laravelbuild.laureon.in)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
