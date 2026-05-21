# Stream C — Production DB switchover and BLOB migration

**Gate:** Run only after Streams A, B, and D are stable in **production**. Do not run against staging for the ~3,004 + ~2,642 legacy BLOB migration unless explicitly testing.

**Target:** Cloud SQL instance `mysql-kdms-prod`, database `kdms_prod`, user `kdms_user`.

---

## 1. Confirm Cloud Run → database wiring

```bash
gcloud run services describe kdms-api-prod \
  --region=asia-south1 \
  --project=project-12f4b54b-d692-4583-83b \
  --format="yaml(spec.template.spec.containers[0].env)"

gcloud run services describe kdms-prod \
  --region=asia-south1 \
  --project=project-12f4b54b-d692-4583-83b \
  --format="yaml(spec.template.spec.containers[0].env)"
```

Verify:

| Variable | Expected |
|----------|----------|
| `KDMS_DB_NAME` / `DB_DATABASE` | `kdms_prod` |
| `KDMS_DB_SOCKET` / Cloud SQL volume | `project-12f4b54b-d692-4583-83b:asia-south1:mysql-kdms-prod` |
| `KDMS_GCS_PHOTOS_BUCKET` | `kdms-photos` (or your prod bucket name) |

If not pointed at production, update Terraform `terraform.tfvars` (`db_name`, `cloudsql_instance`) and apply, or patch the Cloud Run revision env in Console (prefer Terraform).

---

## 2. Pre-migration checklist

- [ ] `mysql-kdms-prod` instance is **RUNNABLE** and accepting connections.
- [ ] Schema has Phase 1a columns:
  - `devotee_photo.Devotee_Photo_Gcs_Path`
  - `devotee_id.Devotee_ID_Image_Gcs_Path`
- [ ] Baseline counts on **production**:

```sql
SELECT
  (SELECT COUNT(*) FROM devotee_photo WHERE Devotee_Photo IS NOT NULL) AS blobs_photo,
  (SELECT COUNT(*) FROM devotee_photo WHERE Devotee_Photo_Gcs_Path IS NOT NULL) AS gcs_photo,
  (SELECT COUNT(*) FROM devotee_id WHERE Devotee_ID_Image IS NOT NULL) AS blobs_id,
  (SELECT COUNT(*) FROM devotee_id WHERE Devotee_ID_Image_Gcs_Path IS NOT NULL) AS gcs_id;
```

Expect roughly **~3,004** photo BLOBs and **~2,642** ID BLOBs with **0** GCS paths before migration (adjust if counts differ).

- [ ] GCS bucket exists; `kdms-api` service account has **objectAdmin** on `kdms-photos`.
- [ ] Streams A + B validated: staff upload writes GCS; grids/reports lazy-load; print cards still eager.
- [ ] On-demand backup:

```bash
gcloud sql backups create --instance=mysql-kdms-prod \
  --project=project-12f4b54b-d692-4583-83b
```

Wait until backup status is **SUCCESSFUL**.

---

## 3. Migration procedure (from Cloud Shell or trusted host)

Use Cloud SQL Auth Proxy to `mysql-kdms-prod`, set `KDMS_DB_*` env to `kdms_prod` / `kdms_user`, and ADC for GCS.

```bash
# 1. Dry-run + report
php scripts/migrate_photos_to_gcs.php --dry-run --report

# 2. Small live batch
php scripts/migrate_photos_to_gcs.php --limit=10 --report

# 3. Verify sample keys in UI + gsutil
gsutil ls gs://kdms-photos/devotee/PXXXXXXXXXX/photo.jpg

# 4. Full migration (batched, 100 rows, 100ms pause)
php scripts/migrate_photos_to_gcs.php --report

# 5. Null BLOBs only after visual verification
php scripts/null_blobs_after_migration.php --dry-run
php scripts/null_blobs_after_migration.php --limit=10
php scripts/null_blobs_after_migration.php
```

**Do not** null BLOBs until photos display correctly via GCS (`devoteePhoto.php` / lazy grids).

---

## 4. Rollback

| Situation | Action |
|-----------|--------|
| Script stopped mid-run | Rows not migrated still have BLOB; dual-read continues to work. |
| GCS wrong but BLOB intact | Stop script; fix GCS; do not run null script. |
| Data corruption | Restore Cloud SQL backup; redeploy previous revision if needed. |

---

## 5. Post-migration (manual, off-peak)

```sql
OPTIMIZE TABLE devotee_photo;
OPTIMIZE TABLE devotee_id;
```

Optional Phase 7+ (only after zero non-null BLOBs and verified backup):

```sql
-- ALTER TABLE devotee_photo DROP COLUMN Devotee_Photo;
-- ALTER TABLE devotee_id DROP COLUMN Devotee_ID_Image;
```
