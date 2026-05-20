# Phase 2 — spec modification summary

**Date:** 2026-05-20  
**Status:** Implementation complete in repo; staging validation pending before production deploy.

Full spec: [`phase2-deduplication-spec.md`](phase2-deduplication-spec.md)  
Validation: [`validation_report_phase2.md`](validation_report_phase2.md)

---

## What changed from pre-Phase-2 behavior

| Area | Before | After (Phase 2) |
|------|--------|-----------------|
| **Day-visitor PWA** | INSERT `devotee` early; dedup optional/404 fallback | Reserve key → dedup **before** INSERT; fail closed; print queue uses survivor |
| **Staff / resident Add Devotee** | `managePhoto` without key created stub `devotee` on photo/ID upload | Reserve key on page load → stage photo/ID only → dedup on save → repoint media on merge |
| **Merge** | `mergeDevoteeRecords()` hard-deleted history | Archive TBM → repoint child tables → alias → DELETE TBM `devotee` only |
| **Service auth** | Opaque failure on bad key | `401` + `{"ok":false,"error":"invalid_service_key"}` |
| **Remarks** | Short `remark` column | `remark` TEXT + DEDUP audit JSON; `Comments` ≤250 for UI text |
| **Admin** | No dedup UI | `addDevoteeI.php` hints + manual merge only |

---

## Resident / staff Add Devotee (this pass)

Aligned with PWA **Option A + early key**:

1. **`UI/addDevoteeI.php`** — On new record (no `?devotee_key=`), server reserves `Devotee_Key` via `Devotee::generateId()` (no DB row).
2. **`api/managePhoto.php`** — Requires `devotee_key`; `stageOnly` when no `devotee` row.
3. **`api/Interface/Image.php`** — `$stageOnly` skips stub `devotee` INSERT.
4. **`api/Interface/devotees.php` → `upsertDevotee()`** — If key not in `devotee`, run `DeduplicationService` before `PROC_REPLACE_DEVOTEE_W_SEVA_I`; `repointStagedMediaKeys()` on merge.
5. **`assets/js/pages/capture.js`**, **`captureID.js`** — Always send reserved key; removed “upload without key → create devotee” branch.
6. **`api/reserveDevoteeKey.php`** — Optional session-auth reserve endpoint (page uses server-side reserve today).

OCR scanner (`assets/js/ocr_reader/main.js`) already passes `devotee_key` to `managePhoto`; no change required.

---

## Unchanged (per your answers)

- **Fuzzy search:** v1 scans up to 5000 rows; tune in prod if needed.
- **UTF-8 names:** byte `levenshtein()` in v1 (documented in validation report).
- **DB user:** existing `kdms` app user (not a separate API user).
- **Signals / thresholds:** as locked in spec (signal 5 never auto-merge).

---

## Files touched (Phase 2 + resident)

| Layer | Paths |
|-------|--------|
| Core | `includes/IdNormalizer.php`, `includes/DeduplicationService.php` |
| API | `api/deduplicateDevotee.php`, `api/dedupHints.php`, `api/adminMergeDevotees.php`, `api/reserveDevoteeKey.php`, `api/managePhoto.php`, `api/Interface/Image.php`, `api/Interface/devotees.php` |
| DDL | `api/config/DB Files/Phase_2_remarks_column.sql` |
| UI | `UI/addDevoteeI.php`, `assets/js/pages/capture.js`, `captureID.js`, `assets/js/main/add_devotee.js` |
| Registration (PWA) | `kdms-registration` reserve + dedup-first (separate repo path if deployed separately) |
| Docs | `docs/phase2-deduplication-spec.md`, this summary, `docs/validation_report_phase2.md` |

**Removed:** `mergeDevoteeRecords()` from `api/Interface/devotees.php`.

---

## Deploy checklist (when you proceed)

1. Run **`Phase_2_remarks_column.sql`** on target DB.
2. Deploy **kdms-api** (all rows in table above).
3. Deploy **kdms-registration** if using day-visitor flow.
4. Confirm **`kdms`** user: DELETE on `devotee`, UPDATE on repointed child tables.
5. Complete **`validation_report_phase2.md`** on staging (sections 3–4 include resident checks).

---

## Production gate

Do **not** treat production as ready until validation report sections pass on staging with Phase 1.5 + 2 builds deployed.
