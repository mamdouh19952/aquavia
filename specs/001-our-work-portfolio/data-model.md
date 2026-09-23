# Phase 1 Data Model: Aquavia Pools Bilingual Website & Our Work Portfolio

## WorkProject

Represents one completed pool project shown in the "Our Work" portfolio (spec Key Entity: "Our Work
Entry (Project)").

| Field | Type | Rules |
|---|---|---|
| `id` | bigint, PK | auto-increment |
| `title_ar` | string(255) | required |
| `title_en` | string(255) | required |
| `description_ar` | text | required |
| `description_en` | text | required |
| `location` | string(255) | required — free-text place name (e.g. "Cairo, Egypt"), not a structured address |
| `created_at` / `updated_at` | timestamps | managed by Eloquent |

**Relationships**:
- `WorkProject` implements Spatie's `HasMedia` — one media collection, `gallery`, holding one or
  more images (FR-010). The first item in the collection (upload/registration order) is the
  thumbnail used in the public grid (FR-005); see `research.md` for the ordering decision.

**Validation rules** (enforced in `StoreWorkProjectRequest` / `UpdateWorkProjectRequest`, per
FR-009):
- `title_ar`, `title_en`: required, string, max 255.
- `description_ar`, `description_en`: required, string.
- `location`: required, string, max 255.
- `photos`: required on create (at least one image), optional on update (existing gallery is kept
  unless new photos are supplied or an existing one is explicitly removed); each file must be an
  image under a reasonable size/type limit (e.g. jpg/png/webp, ≤ 8 MB) — mirrors the "never trust
  client-supplied filenames or MIME types" rule in `CLAUDE.md`.

**State/lifecycle**: No draft/published distinction (per spec Assumptions) — a `WorkProject` is
visible on the public site as soon as it is created (FR-011), until edited or deleted.

**Deletion**: Hard delete (no soft-deletes column) — when the admin deletes an entry (FR-011,
Edge Case: deleted entry shows "not found"), its media is removed via
`MediaService::deleteMedia()` and the record removed. This matches the small scope in Principle V;
soft-deletes would add a lifecycle state the spec never asked for.

## User (existing, reused — not modified by this feature)

Already defined by the bootstrap template (`app/Models/User.php`, migrations
`0001_01_01_000000_create_users_table` and `2026_08_23_000001_add_is_admin_to_users_table`).

| Field (relevant to this feature) | Purpose |
|---|---|
| `is_admin` | Gates access to `admin.*` routes via the existing `admin` middleware (spec Key Entity: "Admin") |

No new fields are added to `User` for this feature. There is exactly one row with `is_admin = true`
for this project (spec Assumption: single admin, no roles/permissions).

## Media (Spatie, existing — not modified by this feature)

Spatie's own `media` table (already migrated: `2026_08_23_035005_create_media_table`) stores each
uploaded photo, polymorphically related to its `WorkProject`. Accessed only through
`App\Services\MediaService` per Constitution Principle IV — no direct Spatie calls from
controllers.
