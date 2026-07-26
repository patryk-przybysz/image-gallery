# Image Gallery

A PHP image gallery built with a lightweight MVC-style architecture, featuring session-based authentication, public and private uploads, watermarking, search, and pagination.

## Features
- User registration and login
- Public and private image uploads
- Automatic watermarking and thumbnail generation
- Gallery browsing with pagination
- Image search

## Tech stack

### Runtime
- PHP 8.4
- MongoDB
- Caddy

### Frontend
- HTML
- CSS
- JavaScript

### Development
- Nix
- devenv
- direnv
- Composer

## Setup

This project is designed to run inside a devenv shell. The MongoDB connection variables are provided automatically by the dev environment, so no manual `.env` file is needed for local development.

### Prerequisites

- [Nix](https://nixos.org/download/)
- [devenv](https://devenv.sh/)
- [direnv](https://direnv.net/)

### Quick start

1. Clone the repository:
   ```bash
   git clone https://github.com/patryk-przybysz/image-gallery
   ```
2. Enter the project directory:
   ```bash
   cd image-gallery
   ```
3. Allow direnv once for this clone:
   ```bash
   direnv allow
   ```
   After that, direnv will automatically enter the devenv shell whenever you are in the project directory.
4. Install PHP dependencies:
   ```bash
   composer install
   ```
5. Start the development environment:
   ```bash
   devenv up
   ```
6. Open the app at [http://localhost:8080](http://localhost:8080)

## Tests

### Unit

Unit tests use [Pest](https://pestphp.com/) and do not need MongoDB, Caddy, or `devenv up`.

```bash
composer test:unit
```

### HTTP

HTTP smoke tests hit the real app over the network (Caddy + PHP-FPM + MongoDB). They require the devenv processes to be running (`devenv up`). If the stack is down, failures report a clear readiness/connectivity error instead of opaque assertion noise.

```bash
devenv up   # in another terminal, if not already running
composer test:http
```

Base URL defaults to `http://127.0.0.1:8080`. Override with `HTTP_BASE_URL` when needed:

```bash
HTTP_BASE_URL=http://127.0.0.1:8080 composer test:http
```

`composer test` runs the full Pest suite (unit + http). Prefer `composer test:unit` when the stack is not up.

## Static analysis and formatting

[PHPStan](https://phpstan.org/) (level 5 on `src` and `public`) and [Laravel Pint](https://laravel.com/docs/pint) (PSR-12 preset across the PHP tree) are available via Composer:

```bash
composer analyse
composer format:check
composer format
```

`analyse` runs PHPStan. `format:check` fails if style drifts; `format` applies Pint fixes.

## Future improvements
- Expand HTTP tests beyond smoke coverage (auth/upload/search flows) and wire CI
- Raise PHPStan level and tighten Pint coverage over time
- Harden output escaping, sessions, and uploads
- Continue separating controllers, services, and repositories
- Polish the responsive UI and accessibility

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
