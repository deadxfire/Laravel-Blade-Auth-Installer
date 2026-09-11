# Laravel Auth Installer

> **Client installer CLI for Laravel Auth Builder** — Created by **Arindam Makar**.

[![License: MIT](https://img.shields.io/badge/License-MIT-indigo.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.2-blue.svg)](https://php.net)
[![Laravel Framework](https://img.shields.io/badge/Laravel-11%20%7C%2012%20%7C%2013-red.svg)](https://laravel.com)

A developer-friendly CLI installer that imports secure, production-ready authentication scaffolding directly into any Laravel application from **[Laravel Auth Builder](http://laravelbuild.laureon.in)**.

---

## Features

- 🚀 **Instant Setup**: Import ready-to-use authentication in a single Artisan command.
- 🎨 **Multiple Design Presets**: Modern Indigo, Classic Blue, Minimal Monochrome, Slate Zinc, and Simple Clean.
- 📊 **Complete Dashboard**: Built-in responsive dashboard layout with metrics cards, recent activity, and navigation.
- 👤 **Full User Profile**: Profile editing, secure password update (with current password verification), and password-confirmed account deletion.
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

### Options

```bash
# Overwrite existing conflicting files without prompt:
php artisan auth:import http://laravelbuild.laureon.in/install/default --force

# Preview what files will be created without modifying the project:
php artisan auth:import http://laravelbuild.laureon.in/install/default --dry-run
```

---

## What Gets Installed

1. **Controllers** (`app/Http/Controllers/Auth/` + `DashboardController.php` + `ProfileController.php`)
2. **Form Requests** (`LoginRequest.php`, `RegisterRequest.php`, `ProfileUpdateRequest.php`)
3. **Views** (`resources/views/auth/*`, `resources/views/components/auth/*`, `resources/views/dashboard.blade.php`, `resources/views/profile/edit.blade.php`)
4. **Routes** (`routes/auth.php` loaded automatically into `routes/web.php`)

---

## Credits & Author

- **Author & Provider**: Arindam Makar
- **Platform**: [Laravel Auth Builder](http://laravelbuild.laureon.in)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
