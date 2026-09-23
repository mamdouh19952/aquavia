# Quickstart Validation: Aquavia Pools Bilingual Website & Our Work Portfolio

Run-through to prove the feature works end-to-end once implemented. This is a validation guide, not
an implementation spec — see `data-model.md` and `contracts/routes.md` for the actual shapes.

## Prerequisites

- `composer install`, `npm install` already run (done during project scaffolding)
- `.env` configured, `php artisan key:generate` run, `database/database.sqlite` created
- Migrations up to date: `php artisan migrate`
- One admin user seeded with `is_admin = true` (e.g. via `php artisan tinker` or a seeder)

## Setup commands

```bash
php artisan migrate
php artisan db:seed --class=AdminUserSeeder   # or equivalent — creates the single admin
npm run build   # or: npm run dev, in a second terminal, for local development
php artisan serve
```

## Scenario 1 — Static bilingual site (User Story 1)

1. Visit `/` with no prior session. **Expect**: Arabic content, `<html dir="rtl">`.
2. Visit `/about`, `/services`, `/contact`. **Expect**: all render in Arabic, RTL.
3. Use the language switch to English. **Expect**: page reloads in English, `<html dir="ltr">`.
4. Navigate to another page (e.g. `/services`). **Expect**: still English/LTR — the choice persisted.

## Scenario 2 — Empty portfolio (User Story 2, edge case)

1. With zero `work_projects` rows, visit `/our-work`. **Expect**: a clear "no projects yet" message,
   not an error or blank layout (FR-007).

## Scenario 3 — Admin creates a portfolio entry (User Story 3)

1. Visit `/admin/work` while logged out. **Expect**: redirected to `/login` (FR-012).
2. Log in as the admin, visit `/admin/work/create`.
3. Submit the form missing the English title. **Expect**: validation error naming the missing
   field; other entered fields are preserved (FR-009, spec edge case).
4. Submit a complete entry: `title_ar`, `title_en`, `description_ar`, `description_en`, `location`,
   and two photos. **Expect**: redirect to `admin.work.index` with a success toast.
5. Visit `/our-work` (public, logged out). **Expect**: the new entry appears in the grid with its
   first photo as the thumbnail (FR-005, FR-011).
6. Open the entry's detail page. **Expect**: both photos shown, full description, and location
   (FR-006).

## Scenario 4 — Admin edits and deletes (User Story 3)

1. As the admin, edit the entry created above — change `location` and add a third photo.
   **Expect**: public detail page reflects the change immediately (FR-011).
2. Delete the entry. **Expect**: it disappears from `/our-work`, and visiting its old detail URL
   shows the "not found" view with a link back to `/our-work` (FR-015).

## Scenario 5 — Contact & WhatsApp (User Story 4)

1. From any page, select the floating WhatsApp button. **Expect**: opens a chat addressed to the
   company's WhatsApp number (`wa.me` link), including on a device without WhatsApp installed
   (falls back to WhatsApp Web).
2. Visit `/contact`. **Expect**: phone, email, address with map link, and social links are all
   present and correct in both languages.

## Automated test run

```bash
php artisan test --filter=WorkPortfolioPublicTest
php artisan test --filter=WorkCrudTest
php artisan test --filter=LocaleSwitchTest
```

All three MUST pass before this feature is considered done (Constitution Principle V /
Development Workflow §6).
