# Aquavia Pools — Project Rules (Laravel Blade)

Senior Full Stack Laravel Developer. Write clean, simple, production-ready code. Follow this project's patterns; do not introduce unnecessary architectures.

## Role

Build a bilingual (Arabic/English) marketing website for Aquavia Pools (pool design, construction & maintenance). The site is almost entirely static content except one CRUD area — "Our Work" (portfolio/projects) — managed from a simple admin panel. Focus on clarity and maintainability, not over-engineering.

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2+, Eloquent ORM, MySQL/SQLite.
- **Frontend**: Blade templates + Bootstrap 5, Vanilla JavaScript (Axios when needed).
- **Rendering**: Server-side (Blade) only. No SPA.
- **Media**: `spatie/laravel-medialibrary` (wrapped in `MediaService`).
- **Assets**: Vite + Laravel Vite plugin.
- **Notifications**: Toastr (via toast partials).

## Architecture — Simple & Server-Rendered

### Routing

- **Web-only by default** (`routes/web.php`).
- Controllers return Blade views or redirects.
- **Optional API**: Only create `routes/api.php` if explicitly needed (rare, opt-in).
- Prefer normal form submissions + server-side redirects over AJAX.

### Controllers & Logic

- Keep controllers focused and readable.
- **No Service layer by default** — put simple logic directly in controllers.
- Introduce a Service only if:
  - Logic is reused across multiple controllers.
  - Business workflow is genuinely complex.
  - Needs transaction management across models.
  - Controller readability suffers.
- No Repository pattern by default.
- Use `Route::resource()` for standard CRUD.

### Models & Validation

- Use Eloquent directly (default data-access layer).
- Use Form Requests for validation (reuse them across controllers).
- Use `$fillable` explicitly; never use `$guarded = []`.
- Eager-load relationships to prevent N+1.
- Use database transactions (`DB::transaction()`) only when multiple related writes must stay consistent.

### Media/Uploads

- Use `spatie/laravel-medialibrary` for any file/image upload (single or gallery).
- Wrap ALL Spatie calls in `App\Services\MediaService`:
  - `upload(Model $model, UploadedFile $file, string $collection = 'default')`
  - `update(Model $model, UploadedFile $file, ?Media $oldMedia, string $collection = 'default')`
  - `deleteMedia(Model $model, string $collection = 'default')`
  - `deleteMediaItem(Media $media)`
- Controllers call `MediaService` methods only; never call Spatie's API directly.

## Blade & Frontend

### Views & Structure

```
resources/views/
├── layouts/
│   ├── app.blade.php       (main authenticated layout)
│   └── guest.blade.php     (login/register layout)
├── components/             (reusable Blade components)
│   ├── button.blade.php
│   ├── input.blade.php
│   ├── alert.blade.php
│   └── ...
├── partials/
│   ├── toast.blade.php     (Toastr notifications)
│   ├── navbar.blade.php
│   └── ...
├── admin/                  (admin features)
│   └── products/
│       ├── index.blade.php
│       ├── create.blade.php
│       ├── edit.blade.php
│       └── show.blade.php
└── welcome.blade.php       (home page)
```

### Blade Best Practices

- Use `{{ }}` for escaped output (default).
- Use `{!! !!}` only for trusted HTML.
- Include `@csrf` in every state-changing form.
- Use `@method()` for PUT, PATCH, DELETE forms.
- Use `old()` to preserve form values on validation error.
- Display validation errors consistently (use a shared `errors` partial).
- Keep business logic out of Blade — controllers prepare all data.

### Components (Reusable Blade Elements)

Create components for repeated UI patterns:
- Form fields (input, select, textarea, file).
- Buttons.
- Alerts, badges, badges.
- Cards.
- Modals.
- Tables.
- Pagination.

Do NOT create a component for every fragment. Only use components for elements that repeat or have shared styling/behavior.

## JavaScript & Interactivity

### Default Approach

- Vanilla JavaScript (no frameworks).
- Axios for AJAX when needed.
- Keep scripts small and localized.
- Do NOT build frontend state management.

### Use Cases

JavaScript is for simple, local interactions:
- Confirmation dialogs.
- Modal open/close.
- Show/hide elements.
- Tabs, toggles, accordions.
- Image preview before upload.
- Client-side form enhancements.
- Preventing duplicate form submissions.

### AJAX (Optional)

AJAX is **opt-in**, not default.

Use Axios + AJAX only when:
1. The user explicitly requests async behavior, OR
2. A normal server-rendered flow truly cannot provide the UX.

Default flow:
```
Form → Web Route → Controller → Database → Redirect → Blade View
```

If AJAX is used:
- Validate ALL data server-side (never trust client).
- Return JSON only from endpoints that need it.
- Protect POST/PUT/DELETE requests with CSRF token.
- Return appropriate HTTP status codes.

## Bootstrap & Styling

- **Bootstrap 5 only** (no custom CSS frameworks).
- Use Bootstrap utility classes (`d-flex`, `mb-3`, etc.).
- Keep custom CSS minimal (only when Bootstrap utilities fall short).
- Follow existing project styling.
- Responsive design by default.
- RTL-ready (if project uses Arabic).

## Forms

- Use Laravel form submissions (POST, PUT, PATCH, DELETE).
- Include `@csrf` on every form.
- Use Form Requests for validation.
- Preserve old values with `old()` on error.
- Display errors clearly (via shared error component).
- Do NOT convert to AJAX unless explicitly required.

## Notifications

- **Toastr** for user feedback.
- Controller sets flash messages: `->with('success', 'Data saved!')`
- Blade `toast` partial renders Toastr JS.
- One shared notification system; no duplicates.

## Authorization & Security

- Use middleware for role-based access (`auth`, `admin`, etc.).
- Use Policies for resource ownership checks.
- Enforce authorization server-side.
- Never trust user-supplied IDs on owned resources.
- Validate all input server-side.
- Escape Blade output by default (`{{ }}`).
- Never commit `.env`, secrets, or credentials.

## Performance

- Eager-load relationships (prevent N+1).
- Paginate large result sets.
- Select only required columns when appropriate.
- No unnecessary caching, Redis, or queues (use them only if proven benefit).
- Avoid expensive queries in Blade.

## Accessibility

- Use semantic HTML.
- Label every form input.
- Use meaningful button text.
- Add `aria-label` to icon-only buttons.
- Provide `alt` text on images.
- Ensure modals and interactive controls are keyboard accessible.

## Testing

- Feature tests for important web flows (authentication, CRUD, authorization).
- Test both success and failure scenarios.
- Do NOT test trivial framework behavior.

## Git & Commits

- Keep commits focused on one task.
- Do not modify unrelated files.
- Do not commit `.env`, secrets, or generated files (`vendor/`, `node_modules/`).
- Write clear, concise commit messages.

## Before Writing Code

1. Inspect existing routes (`routes/web.php`).
2. Check relevant controllers and models.
3. Check Form Requests and validation.
4. Check existing Blade layouts, components, and partials.
5. Check Bootstrap version and styling approach.
6. Check notification system (Toastr setup).
7. Check media/upload approach if relevant.
8. Reuse existing patterns; don't introduce new architecture.
9. Briefly explain implementation plan before large changes.
10. Do NOT create API endpoints unless explicitly required.

## Project Specifics

- **Laravel version**: 12.x
- **PHP version**: 8.2+
- **Database**: SQLite (dev) / MySQL (production, TBD by hosting)
- **Bootstrap version**: 5.3
- **JavaScript**: Vanilla JS + Axios
- **Authentication**: Laravel session auth — **one admin only**, no public user registration/accounts. The public site has no login-gated areas.
- **Areas**: Admin (single role, "Our Work" CRUD only) + Public bilingual site
- **RTL**: Yes — full RTL layout when Arabic is active (Bootstrap 5 RTL build)
- **Localization**: Yes — Arabic (default) + English, switchable per visitor, persisted in session. Laravel translation files (`lang/ar`, `lang/en`) for all static UI/section text.
- **Notifications**: Toastr
- **Media handling**: Spatie Media Library + MediaService — each "Our Work" project has one or more images (gallery); first/primary image shown as the card thumbnail, full gallery shown on the project detail page.
- **Vite**: Yes
- **Services**: Only when needed (not by default) — the single "Our Work" CRUD resource stays in its controller.
- **Policies**: No — single admin role, no ownership/multi-user authorization concerns.

### Domain specifics (Aquavia Pools)

- Public site sections (static, not admin-editable): Home, About, Services, **Our Work** (dynamic), Contact.
- "Our Work" is the only admin-editable content: each entry has a title, description, location, and one-or-more images — both title/description stored per-locale (ar + en).
- Admin can create/edit/delete "Our Work" entries only. No other section is editable through the admin.
- Branding: name "Aquavia Pools", tagline "Design • Construction • Maintenance", color palette navy blue + turquoise/aqua, existing logo assets used as-is (light and dark variants).
- A floating WhatsApp contact button appears site-wide (inspired by the reference site reviewed during planning).
