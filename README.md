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

## Future improvements
- Add automated tests and CI
- Harden output escaping, sessions, and uploads
- Continue separating controllers, services, and repositories
- Polish the responsive UI and accessibility

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
