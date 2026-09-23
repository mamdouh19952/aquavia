# Route Contract: Aquavia Pools Bilingual Website & Our Work Portfolio

This project is a server-rendered Blade application with no JSON API (Constitution Principle I), so
its "interface contract" is the set of web routes/pages exposed to browsers, rather than an API
schema. Every route returns an HTML Blade view or a redirect.

## Public routes (no auth)

| Method | URI | Name | Controller@action | Maps to |
|---|---|---|---|---|
| GET | `/` | `home` | `PageController@home` | US1 |
| GET | `/about` | `about` | `PageController@about` | US1 |
| GET | `/services` | `services` | `PageController@services` | US1 |
| GET | `/contact` | `contact` | `PageController@contact` | US1, US4 |
| GET | `/our-work` | `work.index` | `WorkController@index` | US2 (FR-005, FR-007) |
| GET | `/our-work/{workProject}` | `work.show` | `WorkController@show` | US2 (FR-006, FR-015 — 404 view when not found) |
| GET | `/lang/{locale}` | `locale.switch` | `LocaleController@switch` | US1 (FR-001–FR-003) — `{locale}` restricted to `ar|en`, redirects back |

## Admin routes (`auth` + `admin` middleware, prefix `admin`, name prefix `admin.`)

| Method | URI | Name | Controller@action | Maps to |
|---|---|---|---|---|
| GET | `/admin/work` | `admin.work.index` | `Admin\WorkController@index` | US3 |
| GET | `/admin/work/create` | `admin.work.create` | `Admin\WorkController@create` | US3 |
| POST | `/admin/work` | `admin.work.store` | `Admin\WorkController@store` | US3 (FR-008, FR-009, FR-010, FR-011) |
| GET | `/admin/work/{workProject}/edit` | `admin.work.edit` | `Admin\WorkController@edit` | US3 |
| PUT | `/admin/work/{workProject}` | `admin.work.update` | `Admin\WorkController@update` | US3 (FR-008, FR-009, FR-011) |
| DELETE | `/admin/work/{workProject}` | `admin.work.destroy` | `Admin\WorkController@destroy` | US3 (FR-008, FR-011) |

Unauthenticated requests to any `admin.*` route redirect to the existing `login` route (FR-012).

## Existing routes reused as-is

| Method | URI | Name | Notes |
|---|---|---|---|
| GET/POST | `/login`, `/logout` | `login`, `logout` | Already scaffolded — unchanged |
| GET/POST | `/register` | `register` | **Removed** for this project — spec Assumption: exactly one admin, no public registration |

## Site-wide element (not a route)

The floating WhatsApp button (FR-013) is a partial (`partials/whatsapp-button.blade.php`) included
in the shared public layout, linking to `https://wa.me/<company-number>` — not a server route.
