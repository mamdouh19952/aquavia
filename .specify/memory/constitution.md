<!--
SYNC IMPACT REPORT
==================
Version change: [TEMPLATE] → 1.0.0 (initial ratification for Aquavia Pools — replaces unrelated
  leftover "Medical Platform Constitution" content inherited from the bootstrap template)

Principles filled:
  - [PRINCIPLE_1_NAME] → I. Server-Rendered Simplicity (Blade-Only, No API)
  - [PRINCIPLE_2_NAME] → II. Security by Default (NON-NEGOTIABLE)
  - [PRINCIPLE_3_NAME] → III. Bilingual & RTL as a First-Class Concern
  - [PRINCIPLE_4_NAME] → IV. Media Handled Through One Gate
  - [PRINCIPLE_5_NAME] → V. Simplicity & Testability Matched to Scope

Sections filled:
  - [SECTION_2_NAME] → Tech Stack Constraints
  - [SECTION_3_NAME] → Development Workflow

Templates reviewed:
  ✅ .specify/templates/plan-template.md  — Constitution Check section is generic and compatible
  ✅ .specify/templates/spec-template.md  — No constitution-specific references; fully compatible
  ✅ .specify/templates/tasks-template.md — Task structure aligns with principle-driven phases
  ✅ .specify/templates/constitution-template.md — Source template; no update needed
  ✅ CLAUDE.md — Project Specifics section already reflects these principles (RTL/localization
     yes, single admin, no Policies, Spatie MediaService)

Deferred TODOs:
  None — all fields resolved from the conversation brief and CLAUDE.md project specifics.
-->

# Aquavia Pools Constitution

## Core Principles

### I. Server-Rendered Simplicity (Blade-Only, No API)

The site MUST be built as a server-rendered Laravel Blade application. Routes live exclusively in
`routes/web.php`; `routes/api.php`, API controllers, and JSON endpoints MUST NOT be introduced
unless a genuine, explicitly-requested integration requires one. Controllers MUST stay thin —
simple CRUD logic lives directly in the controller. A Service or Repository class MUST NOT be
introduced by default; only add one when logic is reused across multiple controllers, is
transactional across multiple models, or has grown complex enough to hurt controller readability.
No frontend framework (Vue, React, or similar) and no SPA-like behavior — Vanilla JavaScript
(Axios when AJAX is genuinely needed) only.

**Rationale**: This project is a five-section brochure site with exactly one CRUD resource. A
web-only, server-rendered architecture with no premature layering is the entire correct shape for
that scope — anything more is complexity the project will never need.

### II. Security by Default (NON-NEGOTIABLE)

Every state-changing route MUST validate input through a dedicated Form Request — never inline
`$request->validate()` inside a controller. Every Eloquent model MUST declare an explicit
`$fillable`; `$guarded = []` is prohibited. CSRF protection MUST remain enabled on every
state-changing web request. The application has exactly **one** admin account (session-based
auth, no public registration, no multi-role permission system) — every non-public route MUST sit
behind the `auth` middleware, and no client-supplied `id` is ever trusted for a write without
server-side ownership/existence checks. Secrets, stack traces, and raw SQL errors MUST never reach
the end user; `.env`, credentials, `vendor/`, and `node_modules/` MUST never be committed.

**Rationale**: A single-admin marketing site has a small attack surface, but the common CRUD
vulnerabilities (mass assignment, missing CSRF, unvalidated input) are exactly as dangerous here as
on a larger system, and cost nothing extra to prevent correctly from the start.

### III. Bilingual & RTL as a First-Class Concern

The public site MUST be fully bilingual: Arabic (default) and English, switchable per visitor and
persisted in session. All static section copy (Home, About, Services, Contact) MUST come from
Laravel translation files (`lang/ar`, `lang/en`) — never hard-coded strings mixed with markup. The
one dynamic resource, "Our Work", MUST store its title and description per-locale (Arabic and
English columns/fields), never a single untranslated value. When Arabic is active, the layout MUST
render right-to-left using Bootstrap 5's RTL build — logical properties/utilities over hard-coded
`left`/`right`.

**Rationale**: Bilingual support was a stated requirement from day one, not an afterthought; baking
it into the translation-file and per-locale-column structure from the first migration avoids a
painful retrofit later.

### IV. Media Handled Through One Gate

All image uploads — the "Our Work" gallery, single or multiple images per entry — MUST go through
`spatie/laravel-medialibrary`, wrapped in `App\Services\MediaService` (`upload`, `update`,
`deleteMedia`, `deleteMediaItem`). Controllers MUST NOT call Spatie's API directly. Each "Our Work"
entry MUST support one-or-more images with an unambiguous primary/thumbnail image used in the
portfolio grid; the remaining images appear on that entry's detail page.

**Rationale**: A single upload gate keeps validation (file type/size), storage conventions, and
future changes (e.g. adding image optimization) in one place instead of scattered across
controllers.

### V. Simplicity & Testability Matched to Scope

Every feature MUST start with the simplest implementation that satisfies the requirement — this is
a small brochure site, not a platform, and MUST NOT be built like one. Abstractions MUST NOT be
introduced preemptively. PHPUnit feature tests MUST cover the "Our Work" CRUD: the happy path, the
validation-failure path, and the auth-required path; trivial framework behavior MUST NOT be tested.
Test assertions target observable HTTP/Blade behavior (status codes, redirect targets, rendered
content, database state), not internal implementation details.

**Rationale**: YAGNI keeps a small project small. Feature tests at the HTTP layer catch real
regressions in the one flow that actually has logic (the CRUD) without brittle unit-mocking.

## Tech Stack Constraints

- **Language/Runtime**: PHP ^8.2
- **Framework**: Laravel ^12.0
- **Database**: SQLite for local development; MySQL in production (final choice depends on hosting)
- **UI**: Blade + Bootstrap 5 (RTL build when Arabic is active) — no inline `<style>` blocks
- **JavaScript**: Vanilla JS; Axios only for genuinely necessary AJAX — no SPA behavior
- **Media**: `spatie/laravel-medialibrary`, wrapped in `MediaService`
- **Notifications**: Toastr, via the shared `toast` partial
- **Build Tool**: Vite (already configured via `vite.config.js`)
- **Testing**: PHPUnit, configured in `phpunit.xml`
- **Package policy**: No new Composer or npm packages without checking in first

## Development Workflow

1. **Migration-first schema changes** — database schema changes MUST be introduced via Artisan
   migrations; manual SQL alterations are prohibited.
2. **`Route::resource()` for "Our Work"** — the CRUD resource follows Laravel's standard resource
   routing and naming (`admin.work.index`, `admin.work.store`, etc.).
3. **Form Request validation** — every controller action accepting user input MUST use a dedicated
   Form Request class; inline `$request->validate()` is not permitted in new code.
4. **Blade-only responses** — controllers return Blade views or redirects; no JSON responses unless
   an explicitly-requested AJAX interaction genuinely requires one.
5. **Single admin guard** — no Policies, no role/permission package; the `auth` middleware alone
   gates the admin area, since there is exactly one admin account.
6. **Feature-test gate** — the "Our Work" CRUD is not considered done until its feature tests
   (happy path, validation failure, auth-required) pass.

## Governance

This constitution supersedes all conflicting ad-hoc conventions in the project. Amendments MUST be
documented as an update to this file, incrementing the version per semantic versioning (MAJOR:
principle removal/redefinition; MINOR: new principle or section added; PATCH: clarification or
wording fix), and updating the Sync Impact Report comment at the top.

All feature work MUST verify compliance with the principles above before being considered complete.
Complexity violations (e.g., a Service layer added without the justification in Principle I) MUST
be documented in the relevant plan's Complexity Tracking table.

Runtime development guidance is maintained in `CLAUDE.md` at the project root.

**Version**: 1.0.0 | **Ratified**: 2026-09-17 | **Last Amended**: 2026-09-17
