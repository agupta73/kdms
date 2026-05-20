# Phase 2 — Deduplication (locked decisions)

Production gate remains **Phase 1.5 + 2 + 3** together. This document captures answers from product review (2026-05-20) for implementation.

## Registration flow (Option A + early `Devotee_Key`)

**Problem:** Photos and ID images need a stable key before the main `devotee` row exists.

**Solution (recommended — simple and future-proof):**

1. **Reserve `Devotee_Key` early** via `GET /api/reserve-devotee-key` when the PWA loads (or on first scan).
2. Store GCS objects under paths that include that key, e.g. `id-staging/YYYY-MM-DD/{Devotee_Key}.jpg` and `devotee-selfies/YYYY-MM-DD/{Devotee_Key}.jpg`.
3. On **final submit**, registration calls **`deduplicateDevotee.php` before any `INSERT` into `devotee`** (fail closed if API errors).
4. Outcomes:
   - **`merged`** — survivor is an existing key; registration **does not insert** `devotee`; attaches `devotee_id` / `devotee_photo` / accommodation to **survivor**; print queue uses survivor.
   - **`inserted`** / **`flagged_new`** — registration **inserts** using the reserved candidate key; print queue uses that key.
5. **No row is inserted before dedup**, so `idx_devotee_id_unique_key` never fires on duplicate Aadhaar.

Future **image-based dedup** can reuse the same reserved key and GCS paths without changing the submit contract.

## `mergeDevoteeRecords()`

- **Remove** the stub in `api/Interface/devotees.php` (it performed hard deletes, not merge).
- Real logic lives in **`includes/DeduplicationService.php`** only.

## Child tables — **repoint** (preserve history)

On merge, **UPDATE `Devotee_Key` / `devotee_key` to survivor** — do **not** delete accommodation, seva, amenities, attendance, etc.

Tables to repoint (same breadth as `deleteDevoteeRecord` + print tables):

| Table | Column |
|-------|--------|
| `devotee_accomodation` | `Devotee_Key` |
| `devotee_seva` | `Devotee_Key` |
| `devotee_photo` | `Devotee_Key` |
| `devotee_id` | `Devotee_Key` |
| `devotee_remarks` | `devotee_key` |
| `devotee_attendance` | `devotee_key` |
| `devotee_amenities_allocation` | `Devotee_Key` |
| `devotee_demographics` | `Devotee_Key` |
| `office_duty` | `Devotee_Key` |
| `office_duty_archive` | `Devotee_Key` |
| `print_log` | `Devotee_Key` |
| `card_print_log` | `Devotee_Key` |
| `card_print_archive` | `Devotee_Key` |

**`card_print_log`:** if survivor already has a row, **delete TBM’s queue row** before repoint (avoid duplicate PK). Archive policy: repoint archive rows for audit continuity.

After merge, call existing procs (no new triggers): `PROC_REFRESH_ACCO_COUNT_W_EVENT`, `PROC_REFRESH_AMENITIES_COUNT`, `PROC_REFRESH_SEVA_COUNT_I` for active event.

## `devotee_remarks`

- Increase `remark` column to **`TEXT`** (JSON / multi-line audit allowed).
- Dedup audit: `remark_type = 'DEDUP'`, `remark_event` = active event id.
- Short note also on `devotee.Comments` (VARCHAR 250 max).

## `devotee_aliases` semantics

| Scenario | `Base_Devotee_Key` | `Alias_Devotee_Key` | `Merge_Source` |
|----------|-------------------|---------------------|----------------|
| Auto-merge (TBM absorbed) | Survivor | Merged-away key | `auto_definite` or `auto_fuzzy_review` |
| Review link (Signal 5, no merge) | **New** reserved key | **Suggested** duplicate key | `auto_fuzzy_review` |
| Reserved key not used as row (merge before insert) | Survivor | Reserved candidate key | `auto_definite` |
| Manual admin merge | Survivor | TBM key | `manual` |

`Alias_Devotee_Key` **may be NULL** only when there is no specific duplicate key (batch / ID-only hint). For Signal 5 with a known duplicate, set both keys.

## Signals (unchanged thresholds)

| Signal | Score | Auto-merge (≥ 80) |
|--------|-------|-------------------|
| 1 Same normalized ID | 100 | Yes |
| 2 Different type **and** number | — | Never (exclusion) |
| 3 Name + DOB, Levenshtein ≤ 2 | 90 | Yes |
| 4 Name + phone (last 10 digits) | 80 | Yes |
| 5 Name + station | 60 | No — `flagged_new` + alias |

Name: trim, uppercase, collapse spaces; compare `FIRST + ' ' + LAST` via byte `levenshtein()` (v1 Latin; document UTF-8 limitation).

## API

### `POST api/deduplicateDevotee.php`

- Auth: `X-KDMS-SERVICE-KEY` via `api_session.php`.
- **Invalid key:** HTTP **401** + `{"ok":false,"error":"invalid_service_key"}` (before session redirect).
- Mode **`register`** (default): dedup only — **no INSERT** into `devotee`; returns `{status, action, Devotee_Key, merge_score?, alias_count?}`.
- Body: same fields as registration + **`Devotee_Key`** (reserved candidate).

### `GET api/dedupHints.php`

- Session auth (staff).
- Query `devotee_key` → potential duplicates (read-only).

### `POST api/adminMergeDevotees.php`

- Session auth; `Merge_Source = manual`.

## DB user

- Use existing **`kdms`** application user (not a separate kdms-api-only user).
- Must have **DELETE on `devotee`** for merge hard-delete of TBM rows only.

## Registration integration

- Remove 404 fallback in `KdmsApiClient::deduplicate()`.
- Dedup failure → registration **fails** with user-safe message.
- `addToPrintQueue` always uses **final survivor** `Devotee_Key`.

## Admin UI

- **`UI/addDevoteeI.php`** only — sidebar/panel for hints + manual merge (no auto-merge from UI).

## Resident / staff Add Devotee (`UI/addDevoteeI.php`)

Same pattern as day-visitor PWA:

1. **New record:** PHP reserves `Devotee_Key` on page load (no `devotee` row yet).
2. **Photo / ID upload** (`managePhoto.php` api_type 3/4): requires `devotee_key`; stages rows in `devotee_photo` / `devotee_id` only (`$stageOnly`) — **no** stub `devotee` INSERT.
3. **Save** (`upsertDevotee`): if no `devotee` row exists for that key, run `DeduplicationService` **before** `PROC_REPLACE_DEVOTEE_W_SEVA_I`; on merge, repoint staged photo/ID to survivor key.
4. **Edit existing** (`?devotee_key=`): unchanged save path; dedup hints panel when key present.

API: `GET api/reserveDevoteeKey.php` (session auth) for optional client-side reserve; page load uses server-side reserve today.

## Validation

See `docs/validation_report_phase2.md`.
