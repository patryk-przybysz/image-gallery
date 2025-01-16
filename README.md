# Image Gallery

## Overview
A PHP web application for managing and displaying images, originally created as a university course project. Built using object-oriented PHP following the MVC architectural pattern.

## Features
- Session-based user registration and authentication
- Image upload and storage
- Image processing using the GD library
- Pagination and search

## Planned rewrite
This project will be rewritten to incorporate more modern practices and technologies.
- [x] Development environment using Nix and [devenv](https://http://devenv.sh/)
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
