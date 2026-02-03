# Docker template for PHP projects
This repository provides a starting template for PHP application development.

It contains:
* NGINX webserver
* PHP FastCGI Process Manager with PDO MySQL support
* MariaDB (GPL MySQL fork)
* PHPMyAdmin
* Composer
* Composer package [nikic/fast-route](https://github.com/nikic/FastRoute) for routing
* Composer package [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) for loading `.env` config

## App info

- Start instructions: run `docker compose up` and open `http://localhost:80` in your browser.
- phpMyAdmin login details: developer/secret123

## WCAG and GDPR notes

### WCAG (accessibility)
- Semantic elements and labels are used in forms to aid screen readers (`app/src/Views/auth/login.php`, `app/src/Views/auth/register.php`).
- Form controls use native HTML input types and `required` attributes for basic validation and accessibility hints (`app/src/Views/auth/login.php`, `app/src/Views/auth/register.php`).
- Output is escaped with `htmlspecialchars` to prevent XSS and avoid injecting unexpected content into the DOM (`app/src/Views/layout.php`, `app/src/Views/home/index.php`).

### GDPR (privacy)
- User passwords are stored as hashes, not plain text, using `password_hash` (`app/src/Repository/UserRepository.php`).
- Authentication is session-based and does not expose passwords after login (`app/public/index.php`, `app/src/Controllers/AuthController.php`).

## Project features
- Authentication and sessions: register, login, logout (`app/src/Controllers/AuthController.php`, `app/public/index.php`).
- Home page starter view (`app/src/Controllers/HomeController.php`, `app/src/Views/home/index.php`).

## Rubric mapping

### CSS
- Bootstrap framework and responsive layout (`app/src/Views/layout.php`, `app/public/css/styles.css`).

### Sessions
- Session handling and auth gating (`app/public/index.php`, `app/src/Controllers/BaseController.php`).

### Security
- Parameterized queries (`app/src/Repository/UserRepository.php`).
- Password hashing (`app/src/Repository/UserRepository.php`).
- Output sanitization with `htmlspecialchars` (`app/src/Views/layout.php`, `app/src/Views/home/index.php`).
- Server-side input validation (`app/src/Controllers/AuthController.php`).

### MVC
- Controllers / services / repositories separation (`app/src/Controllers/*`, `app/src/Service/*`, `app/src/Repository/*`).
- Interfaces for integrations (`app/src/Service/Interfaces/*`, `app/src/Repository/Interfaces/*`).
- Routing and view rendering (`app/public/index.php`, `app/src/Controllers/BaseController.php`).

### Legal / Accessibility
- WCAG and GDPR notes above with file references.

## MVC architecture notes

### Layering and responsibilities
- Controllers route requests and render views (`app/src/Controllers/*`, `app/public/index.php`).
- Services encapsulate business logic (`app/src/Service/*`, `app/src/Service/Interfaces/*`).
- Repositories handle database access (`app/src/Repository/*`, `app/src/Repository/Interfaces/*`).
- Views are server-rendered PHP templates (`app/src/Views/*`).

### Routing and templating
- Routing is configured in `app/public/index.php` using FastRoute.
- Views are rendered through `BaseController::render` (`app/src/Controllers/BaseController.php`).






## Setup

1. Install Docker Desktop on Windows or Mac, or Docker Engine on Linux.
1. Clone the project

## Usage

In a terminal, from the cloned project folder, run:
```bash
docker compose up
```

### Composer Autoload

This template is configured to use Composer for PSR-4 autoloading:

- Namespace `App\\` is mapped to `app/src/`.

To install dependencies and generate the autoloader, run:

```bash
docker compose run --rm php composer install
```

If you add new classes or change namespaces, regenerate the autoloader:

```bash
docker compose run --rm php composer dump-autoload
```

Example usage is wired in `app/public/index.php` via `HomeController`.

### NGINX

NGINX will now serve files in the app/public folder.

Go to [http://localhost/](http://localhost/). You should see the home page.

### PHPMyAdmin

PHPMyAdmin provides basic database administration. It is accessible at [localhost:8080](localhost:8080).

Credentials are defined in `docker-compose.yml`. They are: developer/secret123


### Stopping the docker container

If you want to stop the containers, press Ctrl+C. 

Or run:
```bash
docker compose down
```
