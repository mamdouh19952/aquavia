---
description: "Task list for Aquavia Pools Bilingual Website & Our Work Portfolio"
---

# Tasks: Aquavia Pools Bilingual Website & Our Work Portfolio

**Input**: Design documents from `/specs/001-our-work-portfolio/`

**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/routes.md, quickstart.md
(all present)

**Tests**: Included — the constitution's Development Workflow §6 makes the feature-test gate
mandatory for this project, not optional.

**Organization**: Tasks are grouped by user story so each story can be implemented and tested
independently.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1–US4)
- File paths are relative to the repository root (`aquavia-pools/`)

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Prepare translation and asset scaffolding shared by every story

- [X] T001 Remove the public `/register` route from `routes/web.php` (spec Assumption: exactly one
      admin, no public registration) — leave `AuthController::register`/the register view in place
      but unrouted
- [X] T002 [P] Create `lang/ar/site.php` with the full key set for static/public-facing copy (nav,
      home, about, services, contact, footer, our-work labels, admin form labels)
- [X] T003 [P] Create `lang/en/site.php` mirroring the same keys in English
- [X] T004 [P] Add Bootstrap 5's RTL-compiled stylesheet as an alternate asset (alongside the
      existing LTR build) so the layout can pick the correct one per locale

**Checkpoint**: Translation keys and both Bootstrap builds exist; nothing references them yet.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST exist before any user story can be built

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [X] T005 Create migration `database/migrations/2026_09_17_000000_create_work_projects_table.php`
      (`title_ar`, `title_en`, `description_ar`, `description_en`, `location`, timestamps — see
      `data-model.md`)
- [X] T006 Run `php artisan migrate` to apply T005
- [X] T007 Create `app/Models/WorkProject.php` — `HasMedia` (Spatie), explicit `$fillable`,
      `registerMediaCollections()` defining the single `gallery` collection (depends on T005)
- [X] T008 [P] Create `app/Http/Middleware/SetLocale.php` — reads locale from session (default
      `ar`), calls `App::setLocale()`, exposes resolved text direction to views
- [X] T009 Register `SetLocale` in the global middleware stack in `bootstrap/app.php` (depends on
      T008)
- [X] T010 [P] Create `app/Http/Controllers/LocaleController.php` with `switch(string $locale)` —
      validates `locale` is `ar` or `en`, stores it in session, redirects back
- [X] T011 Add `GET /lang/{locale}` route named `locale.switch` in `routes/web.php` (depends on
      T010)
- [X] T012 Update `resources/views/layouts/public.blade.php`: set `<html lang dir="rtl|ltr">` from
      the middleware-resolved locale, load the matching Bootstrap build (T004), add language-switch
      links to `locale.switch` (depends on T009, T011)
- [X] T013 [P] Create `resources/views/partials/whatsapp-button.blade.php` (floating button, `
      https://wa.me/<company-number>` link) and include it in `layouts/public.blade.php` (depends
      on T012)

**Checkpoint**: Foundation ready — every user story phase below can now start.

---

## Phase 3: User Story 1 - Visitor Views the Bilingual Company Site (Priority: P1) 🎯 MVP

**Goal**: A visitor can browse Home/About/Services/Contact in Arabic (default, RTL) or English
(LTR), with the choice persisting across navigation.

**Independent Test**: Load the site with zero `work_projects` rows; confirm all four pages render
in both languages with correct text direction, and the language choice survives navigation.

### Tests for User Story 1

- [X] T014 [P] [US1] Feature test in `tests/Feature/BilingualStaticSiteTest.php`: `/` defaults to
      Arabic + `dir="rtl"`; switching locale updates direction and persists on the next request

### Implementation for User Story 1

- [X] T015 [P] [US1] Create `app/Http/Controllers/PageController.php` with `home()`, `about()`,
      `services()`, `contact()` — each returns its Blade view with no business logic
- [X] T016 [US1] Add `GET /`, `/about`, `/services`, `/contact` routes (`home`, `about`, `services`,
      `contact`) in `routes/web.php` (depends on T015)
- [X] T017 [P] [US1] Create `resources/views/public/home.blade.php` using `lang/*/site.php` keys
- [X] T018 [P] [US1] Create `resources/views/public/about.blade.php`
- [X] T019 [P] [US1] Create `resources/views/public/services.blade.php`
- [X] T020 [P] [US1] Create `resources/views/public/contact.blade.php` (structure only — contact
      details content is completed in User Story 4)

**Checkpoint**: User Story 1 is fully functional and independently testable.

---

## Phase 4: User Story 2 - Visitor Browses Completed Pool Projects (Priority: P1)

**Goal**: A visitor can see the "Our Work" grid and open any entry's full gallery, description, and
location.

**Independent Test**: Seed a couple of sample `WorkProject` rows (with media); confirm the grid and
detail page render correctly, and confirm the empty-state and "not found" cases separately.

### Tests for User Story 2

- [X] T021 [P] [US2] Feature test in `tests/Feature/WorkPortfolioPublicTest.php`: empty-state
      message with zero entries; grid shows thumbnail+title with entries; detail page shows full
      gallery/description/location; a deleted/missing entry shows the "not found" view

### Implementation for User Story 2

- [X] T022 [P] [US2] Create `app/Http/Controllers/WorkController.php` with `index()` (list all
      `WorkProject`s) and `show(WorkProject $workProject)` (depends on T007)
- [X] T023 [US2] Add `GET /our-work` (`work.index`) and `GET /our-work/{workProject}` (`work.show`)
      routes in `routes/web.php` (depends on T022)
- [X] T024 [P] [US2] Create `resources/views/public/work/index.blade.php` — grid using each
      project's first gallery image as the thumbnail; empty-state message when there are none
      (FR-007)
- [X] T025 [P] [US2] Create `resources/views/public/work/show.blade.php` — full photo gallery,
      description, location
- [X] T026 [P] [US2] Add a "not found" view/handling for a missing `WorkProject` with a link back
      to `work.index` (FR-015)

**Checkpoint**: User Stories 1 AND 2 both work independently.

---

## Phase 5: User Story 3 - Admin Manages the Portfolio (Priority: P2)

**Goal**: The admin can create, edit, and delete "Our Work" entries (bilingual fields, location,
photos) from a simple panel, with unauthenticated access blocked.

**Independent Test**: Log in as the seeded admin; create, edit, then delete an entry; confirm each
change is immediately visible (or removed) on the public pages from User Story 2; confirm a logged
-out visitor is redirected away from `/admin/work*`.

### Tests for User Story 3

- [X] T027 [P] [US3] Feature test in `tests/Feature/Admin/WorkCrudTest.php`: unauthenticated request
      to `admin.work.index` redirects to login; create/edit/delete happy path; validation failure
      (missing English title) returns errors and preserves other submitted fields

### Implementation for User Story 3

- [X] T028 [P] [US3] Create `app/Http/Requests/Admin/StoreWorkProjectRequest.php` — validation rules
      from `data-model.md` (bilingual title/description required, location required, ≥1 photo
      required)
- [X] T029 [P] [US3] Create `app/Http/Requests/Admin/UpdateWorkProjectRequest.php` — same rules,
      photos optional on update
- [X] T030 [US3] Create `app/Http/Controllers/Admin/WorkController.php` (`index`, `create`, `store`,
      `edit`, `update`, `destroy`) — uses `MediaService` for all photo handling, never calls Spatie
      directly (depends on T007, T028, T029)
- [X] T031 [US3] Add `Route::resource('work', Admin\WorkController::class)->names('admin.work.*')`
      inside the existing `auth`+`admin` middleware group in `routes/web.php` (depends on T030)
- [X] T032 [P] [US3] Create `resources/views/admin/work/index.blade.php` — list with edit/delete
      actions
- [X] T033 [P] [US3] Create `resources/views/admin/work/create.blade.php` — bilingual fields +
      multi-photo upload with image preview
- [X] T034 [P] [US3] Create `resources/views/admin/work/edit.blade.php` — prefilled form, existing
      gallery shown with per-photo removal
- [X] T035 [US3] Wire Toastr success/error flash messages for store/update/destroy using the
      existing `partials/toast.blade.php` (depends on T030)

**Checkpoint**: User Stories 1, 2, AND 3 all work independently.

---

## Phase 6: User Story 4 - Visitor Contacts Aquavia Pools (Priority: P3)

**Goal**: A visitor can reach the company via the floating WhatsApp button or the Contact page's
details, in either language.

**Independent Test**: From any page, confirm the WhatsApp button opens a chat to the correct
number; confirm the Contact page shows correct phone/email/address+map/social links in both
languages.

### Tests for User Story 4

- [X] T036 [P] [US4] Feature test in `tests/Feature/ContactAndWhatsAppTest.php`: Contact page
      contains phone, email, address/map link, and social links in both locales; WhatsApp partial
      is present on a sampled public page

### Implementation for User Story 4

- [X] T037 [US4] Populate `resources/views/public/contact.blade.php` (from T020) with the real
      phone/email/address+map-link/social-link content, sourced from `lang/*/site.php`
- [X] T038 [US4] Confirm `partials/whatsapp-button.blade.php` (T013) uses the real company WhatsApp
      number and renders on every public page via the shared layout

**Checkpoint**: All four user stories are independently functional.

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that span multiple user stories

- [X] T039 [P] Create `database/seeders/AdminUserSeeder.php` seeding the single admin account for
      local/demo use
- [X] T040 [P] Accessibility pass on all new views: `alt` text on every "Our Work" image, a linked
      `<label>` on every form input, `aria-label` on icon-only buttons (WhatsApp button, gallery
      controls) — per `CLAUDE.md` §Accessibility
- [X] T041 Confirm no leftover links/references to the removed `/register` route (T001) anywhere in
      the views or tests
- [X] T042 Run `quickstart.md` end-to-end manually and confirm every scenario matches its expected
      outcome
- [X] T043 Run `php artisan test` and confirm the full suite passes

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — start immediately
- **Foundational (Phase 2)**: Depends on Phase 1 (needs `lang/*/site.php` and the RTL asset to wire
  into the layout) — BLOCKS all user stories
- **User Stories (Phase 3–6)**: All depend on Foundational (Phase 2) completion
  - US1 (Phase 3) and US2 (Phase 4) have no dependency on each other — can proceed in parallel
  - US3 (Phase 5) depends on the `WorkProject` model (T007, from Foundational) but not on US1/US2
    being finished — can proceed in parallel with them
  - US4 (Phase 6) only needs the Contact page shell from US1 (T020) and the WhatsApp partial from
    Foundational (T013) — logically sequenced after US1 for the Contact page, but its own test/task
    work is otherwise independent
- **Polish (Phase 7)**: Depends on all four user stories being complete

### Parallel Opportunities

- All `[P]`-marked Setup tasks (T002–T004) run in parallel
- Within Foundational, T008/T010 (middleware/controller) run in parallel with each other; T005 must
  finish before T007
- Once Foundational is done, US1 and US2 implementation can proceed fully in parallel (different
  files); US3 can also start in parallel once T007 exists
- All view-creation tasks marked `[P]` within a story (e.g. T017–T020, T024–T026, T032–T034) touch
  different files and can be built in parallel

---

## Parallel Example: User Story 2

```bash
# After Foundational (Phase 2) is complete:
Task: "Create app/Http/Controllers/WorkController.php with index() and show()"
Task: "Create resources/views/public/work/index.blade.php"
Task: "Create resources/views/public/work/show.blade.php"
Task: "Add a not-found view/handling for a missing WorkProject"
```

---

## Implementation Strategy

### MVP First (User Stories 1 & 2 only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (blocks everything else)
3. Complete Phase 3 (US1) and Phase 4 (US2) — together these make the site live and credible even
   before the admin panel exists (seed a few `WorkProject` rows manually via tinker/seeder)
4. **STOP and VALIDATE**: run `quickstart.md` Scenarios 1–2 independently
5. Deploy/demo if ready

### Incremental Delivery

1. Setup + Foundational → foundation ready
2. Add US1 + US2 → validate → this is the demoable MVP
3. Add US3 → validate → admin can now maintain the portfolio without a developer
4. Add US4 → validate → contact/WhatsApp polish complete
5. Phase 7 polish → ship

---

## Notes

- `[P]` tasks touch different files with no dependency on an incomplete task
- Every task lists its exact file path — no task should require guessing a location
- Commit after each task or logical group
- Stop at any checkpoint to validate a story independently before moving on
- Tests are included per the constitution's mandatory feature-test gate — write them before (or
  alongside) the implementation they cover, and make sure they fail first if following TDD
