# Image Gallery

## Overview
A PHP web application for managing and displaying images, originally created as a university course project. Built using object-oriented PHP following the MVC architectural pattern.

## Setup

This project is designed to run inside a Nix-managed devenv shell, with direnv automatically loading it when you enter the repository. That keeps the local environment reproducible and ensures the same PHP runtime, Composer dependencies, MongoDB, and supporting services are available on every machine.

### Prerequisites

- [Nix](https://nixos.org/download/)
- [devenv](https://devenv.sh/)
- [direnv](https://direnv.net/)

### Quick Start

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

## Features
- Session-based user registration and authentication
- Image upload and storage
- Image processing using the GD library
- Pagination and search

## Planned rewrite
This project will be rewritten to incorporate more modern practices and technologies.
- [x] Development environment using Nix and [devenv](https://devenv.sh/)
- [ ] Update to latest PHP version
  - [ ] Refactor controllers to return [PSR-7 compliant responses](https://www.php-fig.org/psr/psr-7/) instead of views
  - [ ] Get rid of unnecessary `static` usage
- [ ] Clean seperation of concerns into controllers, services, and repositories
- [ ] Introduction of interfaces and abstractions
- [ ] Implementation of proper dependency injection
- [ ] Migration from MongoDB to PostgreSQL
- [ ] S3-compatible cloud object storage for images
- [ ] Test coverage

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
