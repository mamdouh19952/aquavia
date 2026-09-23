# Feature Specification: Aquavia Pools Bilingual Website & Our Work Portfolio

**Feature Branch**: `001-our-work-portfolio`

**Created**: 2026-09-17

**Status**: Draft

**Input**: User description: "Build the Aquavia Pools bilingual (Arabic/English) marketing website. The public site has four static sections — Home, About, Services, Contact — plus one dynamic section, "Our Work", showing a portfolio of completed swimming pool projects. Visitors can switch between Arabic and English anywhere on the site, with Arabic as the default and the layout flipping to RTL. The "Our Work" section shows a grid of completed projects, each with a thumbnail image and title; clicking a project opens a detail page with the full photo gallery, description, and project location. There is exactly one admin who logs in to a simple admin panel and can create, edit, and delete "Our Work" entries (bilingual title and description, location, and one or more photos per entry) — no other part of the site is editable through the admin. A floating WhatsApp button and contact details (phone, email, address/map link, social links) are available site-wide so visitors can reach the company directly."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Visitor Views the Bilingual Company Site (Priority: P1)

A prospective client visits the Aquavia Pools website in their preferred language (Arabic by
default, or English) and navigates Home, About, Services, and Contact to learn what the company
offers, with the whole layout correctly mirrored (right-to-left) when Arabic is active.

**Why this priority**: Without a working bilingual static site, there is no usable website at all —
this is the foundation every other capability sits on.

**Independent Test**: Load the site with no portfolio data at all; confirm all four static sections
render correctly in both languages and the layout flips to RTL in Arabic.

**Acceptance Scenarios**:

1. **Given** a visitor opens the site for the first time, **When** the page loads, **Then** content
   is shown in Arabic by default with a right-to-left layout.
2. **Given** a visitor is viewing any page, **When** they choose to switch language, **Then** all
   visible text and the layout direction update to match the chosen language, and the choice
   persists as they navigate to other pages.

---

### User Story 2 - Visitor Browses Completed Pool Projects (Priority: P1)

A prospective client opens "Our Work" to see a grid of completed pool projects (thumbnail image +
title), and opens any project to see its full photo gallery, description, and location.

**Why this priority**: The portfolio of completed work is the primary evidence of credibility that
turns a visitor into a lead — it is core marketing value alongside the static pages, not an
optional extra.

**Independent Test**: With a small set of sample "Our Work" entries already in the system, load
"Our Work" and confirm the grid displays correctly and each entry's detail page shows its full
gallery, description, and location.

**Acceptance Scenarios**:

1. **Given** at least one "Our Work" entry exists, **When** a visitor opens "Our Work", **Then**
   they see a grid of entries, each showing one thumbnail image and its title.
2. **Given** a visitor is viewing the "Our Work" grid, **When** they select an entry, **Then** they
   see a detail page with all of that entry's images, its full description, and its location.
3. **Given** no "Our Work" entries exist yet, **When** a visitor opens "Our Work", **Then** they see
   a clear message indicating no projects are available yet, not an error or a broken empty layout.

---

### User Story 3 - Admin Manages the Portfolio (Priority: P2)

The one Aquavia Pools admin logs into a simple admin area and creates, edits, or deletes "Our Work"
entries — each with a bilingual title, bilingual description, location, and one or more photos —
without needing a developer.

**Why this priority**: This is what keeps "Our Work" (User Story 2) current over time and is
essential for the business long-term. It ranks below Stories 1 and 2 because the site can launch
with an initial set of entries added directly, so it does not block the very first release.

**Independent Test**: Log in as the admin and create a new "Our Work" entry with sample data and
photos; confirm it appears correctly in the public "Our Work" grid and detail page, then edit and
delete it and confirm those changes are reflected publicly.

**Acceptance Scenarios**:

1. **Given** the admin is logged in, **When** they create a new "Our Work" entry with a title,
   description, location, and at least one photo in both languages, **Then** the entry is saved and
   immediately visible on the public site.
2. **Given** an existing "Our Work" entry, **When** the admin edits its details or photos, **Then**
   the public site reflects the updated information.
3. **Given** an existing "Our Work" entry, **When** the admin deletes it, **Then** it no longer
   appears anywhere on the public site.
4. **Given** a visitor who is not logged in, **When** they attempt to reach the admin area, **Then**
   they are redirected to a login prompt and cannot view or change any content.

---

### User Story 4 - Visitor Contacts Aquavia Pools (Priority: P3)

A visitor who wants to get in touch can reach the company at any time via a floating WhatsApp
button, or find phone, email, address/map, and social links in the site's Contact section/footer.

**Why this priority**: Valuable for conversion, but the site delivers its primary marketing value
(Stories 1 and 2) even before this polish is in place; contact info can initially be static text.

**Independent Test**: From any page, confirm the WhatsApp button opens a chat addressed to the
company's number; confirm the Contact section/footer shows correct phone, email, address/map link,
and social links in both languages.

**Acceptance Scenarios**:

1. **Given** a visitor is on any page, **When** they select the floating WhatsApp button, **Then**
   a WhatsApp conversation opens addressed to the company's number.
2. **Given** a visitor opens the Contact section, **When** they view it, **Then** they see the
   company's phone, email, physical address with a map link, and social media links.

---

### Edge Cases

- What happens when an "Our Work" entry has only one photo? The detail page shows that single photo
  instead of a gallery/carousel implying more.
- How does the system handle a visitor requesting a project detail page that has since been
  deleted? It shows a clear "not found" message with a way back to the "Our Work" grid, not a
  broken page.
- What happens when the admin tries to save an "Our Work" entry missing a required field (e.g., no
  photo, or a missing title in one language)? The system rejects the save, clearly indicates which
  fields need correction, and preserves the admin's other entered data.
- What happens if a visitor's device does not have WhatsApp installed? The link still opens
  WhatsApp's web fallback so the visitor can still start a chat.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The system MUST present the public site in Arabic by default and allow visitors to
  switch to English at any time.
- **FR-002**: The system MUST render the full layout right-to-left when Arabic is active and
  left-to-right when English is active.
- **FR-003**: The system MUST persist a visitor's chosen language as they navigate between pages
  within the same visit.
- **FR-004**: The system MUST provide four static public sections — Home, About, Services, and
  Contact — with content available in both languages.
- **FR-005**: The system MUST provide an "Our Work" section listing all portfolio entries as a
  grid, each showing one thumbnail image and its title.
- **FR-006**: The system MUST allow a visitor to open any "Our Work" entry and view its full photo
  gallery, full description, and location.
- **FR-007**: The system MUST show a clear empty-state message in "Our Work" when no entries exist
  yet.
- **FR-008**: The system MUST restrict creating, editing, and deleting "Our Work" entries to an
  authenticated admin only.
- **FR-009**: The system MUST require a title, a description, and at least one photo (with title
  and description present in both languages) before an "Our Work" entry can be saved.
- **FR-010**: The system MUST allow the admin to attach one or more photos to a single "Our Work"
  entry.
- **FR-011**: The system MUST reflect any admin create, edit, or delete action on "Our Work"
  entries immediately on the public site.
- **FR-012**: The system MUST redirect an unauthenticated visitor who attempts to reach the admin
  area to a login prompt.
- **FR-013**: The system MUST provide a floating, always-accessible WhatsApp contact button across
  the public site.
- **FR-014**: The system MUST display the company's phone number, email, physical address with a
  map link, and social media links in the Contact section/footer.
- **FR-015**: The system MUST show a clear "not found" message with a way back to the "Our Work"
  grid when a visitor requests a project that no longer exists.

### Key Entities

- **Our Work Entry (Project)**: One completed pool project shown in the portfolio. Attributes:
  title (Arabic and English), description (Arabic and English), location, one or more photos (one
  designated as the primary/thumbnail), creation/update timestamps.
- **Admin**: The single user account authorized to manage "Our Work" entries. Attributes: login
  credentials only. Not tied to any content ownership model beyond this single role.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A first-time visitor can find and understand the company's services within 30 seconds
  of landing on the Home page, in either language.
- **SC-002**: A visitor can switch the entire site's language and layout direction in one action,
  with the change visible immediately.
- **SC-003**: A visitor can go from the "Our Work" grid to viewing a specific project's full details
  and photos in two clicks or fewer.
- **SC-004**: The admin can publish a new completed project (title, description, location, photos,
  both languages) to the live site in under 5 minutes without any developer assistance.
- **SC-005**: 100% of attempts to reach the admin area without logging in are blocked and redirected
  to a login prompt.
- **SC-006**: A visitor can initiate a WhatsApp conversation with the company from any page in a
  single action.

## Assumptions

- Arabic is the default language for first-time visitors; English is available as an explicit
  switch (confirmed in planning conversation).
- There is exactly one admin account; no multi-admin roles, permissions, or approval workflow are
  needed (confirmed in planning conversation).
- "Our Work" entries do not carry a category/filter system or extra structured fields (year, size,
  duration, services list) beyond title, description, location, and photos — an explicit
  simplification agreed during planning, in contrast to reference sites that include those fields.
- The Contact section provides static contact information and links (WhatsApp, phone, email, map,
  social); no contact form with server-side submission/storage is assumed unless requested later.
- Published "Our Work" entries are visible to all visitors immediately upon saving — there is no
  separate draft/published workflow.
- No maximum photo count is specified for a single "Our Work" entry; any reasonable number of
  photos per entry is supported.
