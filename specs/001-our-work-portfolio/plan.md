# Implementation Plan: Aquavia Pools Bilingual Website & Our Work Portfolio

**Branch**: `001-our-work-portfolio` | **Date**: 2026-09-17 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `/specs/001-our-work-portfolio/spec.md`

## Summary

A bilingual (Arabic default / English) Laravel Blade marketing site for Aquavia Pools with four
static public sections (Home, About, Services, Contact) plus one dynamic, admin-managed section
("Our Work") showing a gallery of completed pool projects. Public routes are read-only; a single
admin account manages "Our Work" entries (bilingual title/description, location, one-or-more
photos) through a small resourceful CRUD behind session auth. No API, no Service layer, no
multi-role authorization — the whole feature is a thin, server-rendered slice on top of the
existing Blade/Bootstrap/Spatie-media bootstrap already in this repository.

## Technical Context

**Language/Version**: PHP 8.2+ / Laravel 12

**Primary Dependencies**: Laravel 12, `spatie/laravel-medialibrary` (via `MediaService`), Bootstrap
5 (RTL build), Toastr, Axios (opt-in only, not required for this feature)

**Storage**: SQLite for local development; MySQL in production (final choice depends on hosting) —
one new table, `work_projects`, plus Spatie's existing `media` table for the photo gallery

**Testing**: PHPUnit feature tests (HTTP layer): public routes, admin CRUD happy/validation/auth
paths

**Target Platform**: Web — server-rendered, responsive (desktop + mobile browsers)

**Project Type**: Single Laravel web application (web-only; no separate frontend, no API)

**Performance Goals**: Standard marketing-site expectations — pages render promptly on typical
broadband/mobile connections; no high-throughput requirement (single admin, brochure-scale traffic)

**Constraints**: Must render correctly right-to-left (Arabic) and left-to-right (English) from the
same views; exactly one admin account, no registration, no roles/permissions package; no
`routes/api.php`

**Scale/Scope**: 4 static sections + 1 admin-managed CRUD resource; realistically dozens of "Our
Work" entries, not thousands; a single concurrent admin editor

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Check | Result |
|---|---|---|
| I. Server-Rendered Simplicity (Blade-Only, No API) | Web-only routes, thin `WorkController` (public) and `Admin\WorkController` (CRUD), no Service/Repository layer — simple CRUD fits directly in the controller | PASS |
| II. Security by Default (NON-NEGOTIABLE) | `StoreWorkProjectRequest`/`UpdateWorkProjectRequest` FormRequests, explicit `$fillable` on `WorkProject`, `auth`+`admin` middleware on admin routes, CSRF via Blade `@csrf`, no client-trusted IDs (route-model binding + ownership is moot with a single admin, but existence checks via `findOrFail`/`Route::resource` still apply) | PASS |
| III. Bilingual & RTL as a First-Class Concern | Static copy in `lang/ar` + `lang/en`; `work_projects` stores `title_ar`/`title_en`/`description_ar`/`description_en`; `<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">` driven by a session-backed locale switch | PASS |
| IV. Media Handled Through One Gate | All "Our Work" photo uploads go through `MediaService` wrapping Spatie; controllers never touch Spatie directly | PASS |
| V. Simplicity & Testability Matched to Scope | No abstractions beyond what a single CRUD resource needs; feature tests cover the CRUD happy/validation/auth paths only | PASS |

No violations — Complexity Tracking table is not needed.

## Project Structure

### Documentation (this feature)

```text
specs/001-our-work-portfolio/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md         # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/
│   └── routes.md        # Phase 1 output — route/page contract (this project has no JSON API)
└── tasks.md             # Phase 2 output (/speckit-tasks — not created by this command)
```

### Source Code (repository root)

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── PageController.php          # NEW — Home/About/Services/Contact (thin, view-only)
│   │   ├── WorkController.php          # NEW — public "Our Work" index()/show()
│   │   ├── LocaleController.php        # NEW — switch(locale) sets session + redirects back
│   │   └── Admin/
│   │       ├── DashboardController.php # EXISTING — reused as-is
│   │       └── WorkController.php      # NEW — admin CRUD (index/create/store/edit/update/destroy)
│   ├── Requests/
│   │   └── Admin/
│   │       ├── StoreWorkProjectRequest.php   # NEW
│   │       └── UpdateWorkProjectRequest.php  # NEW
│   └── Middleware/
│       └── SetLocale.php               # NEW — reads session locale, calls App::setLocale()
├── Models/
│   ├── User.php                        # EXISTING — reused (has is_admin already)
│   └── WorkProject.php                 # NEW — implements Spatie's HasMedia
└── Services/
    └── MediaService.php                # EXISTING — reused as-is, no changes needed

database/migrations/
└── 2026_09_17_000000_create_work_projects_table.php   # NEW

resources/views/
├── public/
│   ├── home.blade.php                  # NEW (or repurpose welcome.blade.php)
│   ├── about.blade.php                 # NEW
│   ├── services.blade.php              # NEW
│   ├── contact.blade.php               # NEW
│   └── work/
│       ├── index.blade.php             # NEW — portfolio grid
│       └── show.blade.php              # NEW — project detail + gallery
├── admin/
│   └── work/
│       ├── index.blade.php             # NEW — admin list
│       ├── create.blade.php            # NEW
│       └── edit.blade.php              # NEW
├── layouts/
│   └── public.blade.php                # EXISTING — extended with dir="rtl|ltr", WhatsApp button, nav
└── partials/
    └── whatsapp-button.blade.php       # NEW

lang/
├── ar/site.php                         # NEW — all static/public-facing strings
└── en/site.php                         # NEW

routes/web.php                          # MODIFIED — add public + admin.work routes, remove /register
tests/Feature/
├── WorkPortfolioPublicTest.php         # NEW
├── Admin/WorkCrudTest.php              # NEW
└── LocaleSwitchTest.php                # NEW
```

**Structure Decision**: Single Laravel application (Option 1 shape, web-only). No `backend/`
`frontend/` split and no `routes/api.php` — everything lives in the existing `app/`, `resources/`,
`routes/web.php` already scaffolded in this repo, extended with the files listed above.

## Complexity Tracking

*No entries — the Constitution Check above passed without violations.*
