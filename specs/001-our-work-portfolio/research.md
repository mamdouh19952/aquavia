# Phase 0 Research: Aquavia Pools Bilingual Website & Our Work Portfolio

No items in the Technical Context were marked `NEEDS CLARIFICATION` — the stack, storage, and
scope were already fixed by the project constitution and `CLAUDE.md`. This document instead records
the key implementation-approach decisions made while turning the spec into a plan, in the same
Decision/Rationale/Alternatives format research tasks would normally produce.

## Decision: Bilingual content storage — per-locale columns, not a translations table

**Decision**: Store `title_ar`, `title_en`, `description_ar`, `description_en` as direct columns on
`work_projects`, rather than a separate `work_project_translations` table or a package like
`spatie/laravel-translatable`.

**Rationale**: Exactly two languages, fixed for the life of this project (per the constitution).
A separate translations table or polymorphic translation package is the correct answer when the
number of locales is open-ended or large; here it would be an abstraction with no concrete
duplication problem to justify it (Constitution Principle V). Direct columns are simpler to
validate (a single FormRequest with `title_ar`/`title_en` rules), simpler to query, and simpler to
seed/test.

**Alternatives considered**:
- `spatie/laravel-translatable` (JSON column per translatable field) — adds a dependency and JSON
  querying complexity for a fixed two-language project; rejected as unjustified complexity.
- Separate `work_project_translations` table (locale, field, value rows) — the right shape for an
  open-ended number of locales or dynamically-added languages; rejected, not this project's shape.

## Decision: Locale switching — session-backed, one middleware

**Decision**: A single `SetLocale` middleware reads the locale from the session (falling back to
`ar`), calls `App::setLocale()`, and exposes the resolved direction (`rtl`/`ltr`) to views. A
`LocaleController@switch(string $locale)` route stores the chosen locale in the session and
redirects back to the referring page.

**Rationale**: Matches FR-001/FR-002/FR-003 exactly (default Arabic, switchable, persisted for the
visit) with the simplest mechanism Laravel offers — no cookie-consent implications, no
route-prefix-per-locale restructuring, no package.

**Alternatives considered**:
- URL-prefixed locales (`/ar/...`, `/en/...`) — better for SEO of a bilingual site long-term, but
  requires restructuring every route and redirect; explicitly out of scope per the earlier planning
  conversation (SEO was "somewhat important, not the top priority" and the site is client-shared
  rather than search-driven). Can be revisited later without changing the data model.
- Cookie-based locale (no session) — functionally similar; session was chosen since Laravel's
  session middleware is already active for auth and flash messages, avoiding a second persistence
  mechanism.

## Decision: RTL layout — Bootstrap 5's RTL build toggled via `dir` attribute

**Decision**: Serve Bootstrap 5's RTL-compiled stylesheet when the resolved locale is `ar`, and the
standard build for `en`; set `<html dir="rtl">`/`dir="ltr"` from the middleware-resolved direction.

**Rationale**: Bootstrap 5 ships an official RTL build; no custom CSS mirroring is needed. This is
the path already anticipated by `CLAUDE.md`'s "RTL-ready" note.

**Alternatives considered**:
- A CSS-only manual RTL override — more custom CSS to maintain long-term; rejected in favor of the
  maintained official build.

## Decision: Gallery ordering and thumbnail selection

**Decision**: Use a single Spatie media collection (`gallery`) per `WorkProject`; the first media
item in upload order is the thumbnail shown in the public grid (Spatie's default collection
ordering), with no extra `is_primary` boolean column.

**Rationale**: Spatie's media collections already track order; adding a separate "is primary" flag
would duplicate that ordering concept for no functional gain at this scale (Principle V).

**Alternatives considered**:
- An explicit `is_primary` boolean on the media pivot — rejected as redundant with collection order
  for a single-admin, low-volume gallery.

## Decision: Admin gating reuses the existing `is_admin` + `admin` middleware, no Policies

**Decision**: Keep the bootstrap template's existing `users.is_admin` column and `admin` route
middleware group (already used by `Admin\DashboardController`); gate `Admin\WorkController` the
same way. No Laravel Policy is introduced.

**Rationale**: There is exactly one admin and no ownership model to enforce (nobody "owns" a
`WorkProject` other than "the admin"), so a Policy would check nothing beyond what the `admin`
middleware already guarantees. Matches Constitution Principle II and the "no Policies" decision
already recorded in `CLAUDE.md`.

**Alternatives considered**:
- A `WorkProjectPolicy` with an `update`/`delete` check — rejected as a no-op abstraction (the
  policy's own check would just be "is admin", which the middleware already enforces).
