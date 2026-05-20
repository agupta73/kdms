# Phase 2 validation report — DeduplicationService

**Date:** _fill after staging deploy_  
**Status:** Template — complete after deploy and manual tests  
**Production gate:** ⚠️ **NOT production-ready** until all items below pass with Phase 1.5 + 3.

Spec: `docs/phase2-deduplication-spec.md`

---

## Prerequisites

- [ ] Run `api/config/DB Files/Phase_2_remarks_column.sql` on staging/production
- [ ] Deploy **kdms-api** with `includes/DeduplicationService.php`, `api/deduplicateDevotee.php`, `api/dedupHints.php`, `api/adminMergeDevotees.php`, `api/reserveDevoteeKey.php`, `api/managePhoto.php` (stageOnly), `upsertDevotee` dedup path
- [ ] Deploy **kdms-registration** with reserve-key + dedup-first register flow
- [ ] `kdms` DB user has DELETE on `devotee` and UPDATE on all child tables listed in spec

---

## 1. Unit / logic tests (manual or automated)

| Case | Expected | Pass |
|------|----------|------|
| Exact Aadhaar match (existing row) | `action: merged`, survivor = existing | [ ] |
| Different ID type + different number, same name | `action: inserted` (no merge) | [ ] |
| Same name + DOB, Levenshtein ≤ 2 | `action: merged` | [ ] |
| Same name + DOB, distance > 2 | `action: inserted` | [ ] |
| Three matches, different update times | Survivor = latest `Devotee_Record_Update_Date_Time` | [ ] |
| Name + station only (score 60) | `action: flagged_new` + alias row | [ ] |

---

## 2. API integration

```bash
# 401 invalid key
curl -s -o /dev/null -w "%{http_code}\n" -X POST "$API/api/deduplicateDevotee.php" \
  -H "Content-Type: application/json" \
  -H "X-KDMS-SERVICE-KEY: wrong" \
  -d '{"Devotee_First_Name":"A","Devotee_Last_Name":"B","Devotee_ID_Type":"Aadhaar","Devotee_ID_Number":"999900000099"}'
# Expect 401 + {"ok":false,"error":"invalid_service_key"}

# 200 with valid key
curl -s -X POST "$API/api/deduplicateDevotee.php" \
  -H "Content-Type: application/json" \
  -H "X-KDMS-SERVICE-KEY: $KDMS_SERVICE_KEY" \
  -d '{"Devotee_Key":"P260520999","Devotee_First_Name":"Test",...}'
```

| Check | Pass |
|-------|------|
| Missing/wrong service key → 401 `invalid_service_key` | [ ] |
| Valid key → 200 + `Devotee_Key` + `action` | [ ] |

---

## 3. PWA registration (Phase 1.5 + 2)

| Check | Pass |
|-------|------|
| Page load reserves `Devotee_Key` | [ ] |
| OCR/selfie paths use reserved key | [ ] |
| Duplicate Aadhaar → merge, single survivor in `devotee` | [ ] |
| `devotee_merge_archive` row for merged-away key (if was inserted) | [ ] |
| `devotee_aliases` row(s) | [ ] |
| Survivor `Comments` + `devotee_remarks` DEDUP audit | [ ] |
| `card_print_log` for **survivor** key only | [ ] |
| Child tables: no orphan rows for merged-away key | [ ] |

---

## 4. Admin UI (`UI/addDevoteeI.php`)

| Check | Pass |
|-------|------|
| **New** Add Devotee → `Devotee_Key` shown on load (reserved, no `devotee` row yet) | [ ] |
| Upload photo/ID before save → works with reserved key only | [ ] |
| Save new devotee → dedup runs; duplicate Aadhaar merges to survivor | [ ] |
| Edit existing devotee → duplicate hints panel loads | [ ] |
| Manual merge → survivor retained, TBM archived + deleted | [ ] |
| Accommodation/seva/attendance rows repointed (not deleted) | [ ] |

---

## 5. Combined go-live gate

- [ ] Phase 1.5 validation (`docs/validation_report_phase1.5.md`) still passes
- [ ] Phase 2 validation (this document) passes on staging
- [ ] Ready for combined **1.5 + 2 + 3** production release

---

## Notes

- Fuzzy name matching uses byte `levenshtein()` (Latin names); document UTF-8 limitation for v1.
- `findDuplicates` scans recent devotees for fuzzy signals; consider SQL indexes if performance is slow.
