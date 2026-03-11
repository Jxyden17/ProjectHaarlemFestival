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

- `App\Controllers\Cms\CmsDanceContentController`
- `App\Controllers\Cms\CmsEventEditorController`
- `App\Controllers\Cms\CmsMediaController`

## Media Upload Flows

Media handling is centralized in:

- `app/src/Service/MediaService.php`

Routes:

- `POST /cms/media/upload-replace` (image uploads)
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

## Security Notes

- Passwords are hashed with `password_hash`
- SQL uses prepared statements in repositories
- Output escaping uses `htmlspecialchars` in views
- CMS actions require authenticated/admin checks via base controller helpers

## Accessibility Notes

- Semantic form structure and labels are used throughout CMS and auth forms
- Native inputs and server-side validation are used for core form flows


## Useful Dev Commands

Stop containers:

```bash
docker compose down
```

Run composer in the PHP container:

```bash
docker compose exec php composer install
```
