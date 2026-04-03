# Haarlem Festival App

Server-rendered PHP web application for the Haarlem Festival website and CMS.

## What This Project Contains

- Public festival pages (including Dance and Jazz)
- CMS for editing event content and schedules
- Authentication and role-gated CMS access
- Media upload flows for CMS-managed images and dance track audio snippets

## Tech Stack

- PHP (FPM)
- NGINX
- MariaDB
- FastRoute (`nikic/fast-route`)
- PHPMailer (`phpmailer/phpmailer`)
- Docker Compose
- phpMyAdmin
- smtp4dev

## Run Locally

1. Start containers:

```bash
docker compose up
```

2. Open:
- App: `http://localhost`
- phpMyAdmin: `http://localhost:8080`
- smtp4dev: `http://localhost:8025`

3. Install dependencies (first run):

```bash
docker compose run --rm php composer install
```

4. If namespaces/classes change:

```bash
docker compose run --rm php composer dump-autoload
```

## Architecture

### Layers

- Controllers: HTTP handling + view rendering
- Services: application/business orchestration
- Repositories: SQL/data access
- Mappers: request/domain/view transformations
- Views: PHP templates

Key folders:

- `app/src/Controllers`
- `app/src/Service`
- `app/src/Repository`
- `app/src/Mapper`
- `app/src/Models`
- `app/src/Views`

Routing + manual composition root are in:

- `app/public/index.php`

Examples:

- Composition root + route table: `app/public/index.php`
- Base controller helpers for JSON/auth/admin access: `app/src/Controllers/BaseController.php`
- Public dance orchestration: `app/src/Controllers/DanceController.php`
- CMS dance orchestration: `app/src/Controllers/Cms/CmsDanceController.php`
- Business logic layer: `app/src/Service/DanceService.php`
- Data access layer: `app/src/Repository/ScheduleRepository.php`
- View-model mapping: `app/src/Mapper/DanceViewModelMapper.php`

This project follows a practical MVC-style structure with service and repository layers on top of the controller/view split. Routing is centralized through FastRoute and dependencies are wired manually in `app/public/index.php`.

## Sessions And Authentication

Sessions are used for login state, role-based access, cart/program flows, and CMS authorization.

Relevant files:

- Session bootstrap: `app/public/index.php`
- Auth + admin guards: `app/src/Controllers/BaseController.php`
- Login / register / password reset flow: `app/src/Controllers/AuthController.php`
- Authentication logic: `app/src/Service/AuthService.php`
- User persistence: `app/src/Repository/UserRepository.php`

Implemented behavior:

- `session_start()` is called at application bootstrap
- Logged-in state is stored in `$_SESSION`
- CMS pages are protected with `requireAuth()` and `requireAdmin()`
- Password reset tokens are generated with `random_bytes()` and stored as SHA-256 hashes
- User passwords are stored with `password_hash()` and verified with `password_verify()`

## Content Model (CMS)

The CMS content system is page/section/item based:

- `pages`
- `page_sections`
- `section_items`

`section_items` holds reusable fields per content row, including:

- `image_path`
- `link_url`
- `item_category`

For dance detail tracks:

- Track artwork is stored in `image_path`
- Track audio URL is stored in `link_url`

## CMS Feature Areas

- Dance Home editor
- Dance Detail editor (per performer/detail slug)
- Event Schedule editor
- User management
- Ticket management (basic CMS area)

Main CMS controllers:

- `App\Controllers\Cms\CmsDanceController`
- `App\Controllers\Cms\CmsEventEditorController`
- `App\Controllers\Cms\CmsMediaController`

## Media Upload Flows

Media handling is centralized in:

- `app/src/Service/MediaService.php`

Routes:

- `POST /cms/media/upload-image` (image uploads)
- `POST /cms/media/upload-audio` (audio uploads)

Behavior:

- Validates request method, file payload, size, and MIME type
- Resolves upload target from CMS module name
- Writes file under `app/public/...`
- Updates DB reference for matched `section_item`
  - image -> `image_path`
  - audio -> `link_url`

Dance detail track audio modules use:

- `dance_detail_track_audio:{detailSlug}`
- allowed path prefix: `/audio/dance/`

Related files:

- Route definitions: `app/public/index.php`
- Media endpoints: `app/src/Controllers/Cms/CmsMediaController.php`
- Upload services: `app/src/Service/ImageUploadService.php`, `app/src/Service/AudioUploadService.php`
- Repository updates for media references: `app/src/Repository/MediaRepository.php`

## Mappers

Mapping logic is intentionally separated from controllers and mostly from services.

Examples:

- `DanceViewModelMapper`
- `CmsScheduleMapper`
- `CmsDanceMapper`
- `EventMapper`
- `PageMapper`
- `DanceMapper`
- `ScheduleMapper`

## API And JavaScript

The application includes JSON endpoints and JavaScript that updates parts of the UI without full page reloads.

Examples:

- CMS async save API routes:
  - `POST /cms/events/dance-homeAPI`
  - `POST /cms/events/dance-detail/{pageSlug}/updateAPI`
- Media upload API routes:
  - `POST /cms/media/upload-image`
  - `POST /cms/media/upload-audio`
- Personal program delete endpoint:
  - `POST /personal-program/delete`

Relevant files:

- Route definitions: `app/public/index.php`
- JSON helper: `app/src/Controllers/BaseController.php`
- CMS JSON responses: `app/src/Controllers/Cms/CmsDanceController.php`
- CMS fetch-based form handling: `app/public/js/cms/form-save-api.js`
- Personal program fetch + JSON processing: `app/public/js/personal-program-filters.js`
- Schedule UI filtering: `app/public/js/schedule-filters.js`

## Security Notes

- Passwords are hashed with `password_hash`
- Password verification uses `password_verify`
- Password reset tokens are generated with `random_bytes()` and stored hashed with SHA-256
- SQL uses prepared statements in repositories
- Output escaping uses `htmlspecialchars` in views
- CMS actions require authenticated/admin checks via base controller helpers
- Rich-text CMS input is sanitized before saving

Relevant files:

- Password hashing in persistence: `app/src/Repository/UserRepository.php`
- Login / reset verification: `app/src/Service/AuthService.php`
- HTML sanitization: `app/src/Service/HtmlSanitizerService.php`
- Admin/auth guards: `app/src/Controllers/BaseController.php`
- Prepared statements and transactions: `app/src/Repository/ScheduleRepository.php`, `app/src/Repository/PageRepository.php`

Notes:

- The project demonstrates server-side validation, output escaping, hashed credentials, and authorization checks.
- CSRF protection is not yet implemented globally, so this remains an improvement area.

## Accessibility Notes

- Semantic form structure and labels are used throughout CMS and auth forms
- Native inputs and server-side validation are used for core form flows
- Responsive layouts are used across public pages and CMS pages
- Interactive state changes are supported visually with hover / active states and transitions

Relevant files:

- Auth forms with labels and semantic inputs: `app/src/Views/auth/login.php`, `app/src/Views/auth/register.php`, `app/src/Views/auth/forgot_password.php`, `app/src/Views/auth/reset_password.php`
- CMS forms with labels and native inputs: `app/src/Views/cms/events/dance-home.php`, `app/src/Views/cms/events/dance-detail.php`, `app/src/Views/cms/events/dance-schedule.php`
- Responsive dance page styling: `app/public/css/Dance/dance-index.css`
- Shared schedule interaction styling: `app/public/css/partialViews/schedule.css`

## GDPR Considerations

This project stores personal data required for account-based festival features such as login, checkout, and personal program management.

Data used in the application:

- Account data: name, email, password hash, phone number, country, city, address, postcode
- Session data: user id, role id, email, guest token
- Password reset data: hashed reset token with expiry

Technical measures already present:

- Passwords are not stored in plain text
- Access to CMS functionality is restricted by authentication and admin authorization
- Password reset tokens are time-limited and stored hashed
- Output escaping and HTML sanitization reduce injection and stored-XSS risk

Relevant files:

- User persistence: `app/src/Repository/UserRepository.php`
- Authentication and reset flow: `app/src/Service/AuthService.php`
- Session usage and route bootstrap: `app/public/index.php`
- Authorization helpers: `app/src/Controllers/BaseController.php`
- Personal program authenticated endpoint: `app/src/Controllers/PersonalProgramController.php`


## Rubric Mapping

The project intentionally demonstrates the following rubric areas:

- CSS:
  - consistent public styling and responsive layouts in `app/public/css/...`
- Sessions:
  - login state, role checks, and user-specific program/cart behavior
- Security:
  - password hashing, prepared SQL, output escaping, HTML sanitization, admin route protection
- MVC / layered architecture:
  - controllers + services + repositories + mappers + views
- API + JavaScript:
  - JSON endpoints for CMS save flows, media uploads, and personal program updates
- Accessibility / legal considerations:
  - documented accessibility measures and documented GDPR-related technical safeguards plus current limitations


## Useful Dev Commands

Stop containers:

```bash
docker compose down
```

Run composer in the PHP container:

```bash
docker compose exec php composer install
```
