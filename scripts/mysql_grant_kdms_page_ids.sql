/*
  KDMS: register page IDs (permission keys) and grant them.

  Prerequisites (adjust names to match your deployment):
    - Database has tables user_access and asset_list (see api/config/DB Files/Data Upload.sql).
    - REPLACE USE statement below.

  YES — you normally need rows in asset_list plus user_access rows for each role (user_role_key)
  that should use the page. The app exposes those keys via login as the Access CSV.

  IMPORTANT: Older schemas used asset_key VARCHAR(10). Several KDMS keys exceed 10 chars
  (e.g. KD-RPT-CARD-TMP, KD-REG-DASH). Run STEP 1 first or inserts will truncate or fail.
*/

-- USE your_database_name;

/* ========== STEP 1: widen keys (safe to run once; ignore duplicate column errors manually) ========== */

ALTER TABLE `asset_list`
  MODIFY COLUMN `asset_key` VARCHAR(64) NOT NULL;

ALTER TABLE `user_access`
  MODIFY COLUMN `asset_key` VARCHAR(64) NOT NULL;

/* ========== STEP 2: register every KD page id currently used by includes/kdms_web_page_ids.php ========== */

INSERT IGNORE INTO `asset_list` (`asset_key`, `asset_name`, `asset_updated_by`, `asset_update_date_time`) VALUES
  ('KD-DSBRD', 'KDMS.index / dashboard', 'kdms_grant_script', NOW()),
  ('KD-REG', 'KDMS.registration', 'kdms_grant_script', NOW()),
  ('KD-DISP-DVT', 'KDMS.displayDevotees', 'kdms_grant_script', NOW()),
  ('KD-PRT-ID', 'KDMS.printID', 'kdms_grant_script', NOW()),
  ('KD-RPT-SEVX', 'KDMS.reports (Excel)', 'kdms_grant_script', NOW()),
  ('KD-ACC-REC', 'KDMS.account recovery UI', 'kdms_grant_script', NOW()),
  ('KD-REG-DASH', 'KDMS.registrationCounts / getRegistrationCounts', 'kdms_grant_script', NOW()),
  ('KD-RPT-DUTY', 'KDMS.rptDutyReport', 'kdms_grant_script', NOW()),
  ('KD-RPT-CARD', 'KDMS.card print routes', 'kdms_grant_script', NOW()),
  ('KD-RPT-CARD-TMP', 'KDMS.rptCardPrint sample', 'kdms_grant_script', NOW()),
  ('KD-ACCO-I', 'KDMS.addAccommodationI', 'kdms_grant_script', NOW()),
  ('KD-ACCO-II', 'KDMS.addAccommodationII', 'kdms_grant_script', NOW()),
  ('KD-DVT-I', 'KDMS.addDevoteeI', 'kdms_grant_script', NOW()),
  ('KD-SEVA-I', 'KDMS.addSevaI', 'kdms_grant_script', NOW()),
  ('KD-SEVA-II', 'KDMS.addSevaII', 'kdms_grant_script', NOW()),
  ('KD-DVT-SCR', 'KDMS.devoteeSearch / OCR', 'kdms_grant_script', NOW()),
  ('KD-AMT-I', 'KDMS.upsertAmenityI', 'kdms_grant_script', NOW()),
  ('KD-AMT-II', 'KDMS.upsertAmenityII', 'kdms_grant_script', NOW()),
  ('KD-EVNT-I', 'KDMS.upsertEventI', 'kdms_grant_script', NOW()),
  ('KD-EVNT-II', 'KDMS.upsertEventII', 'kdms_grant_script', NOW());

/*
  If you already had older keys in asset_list (e.g. KD-DVT-DSP for display devotees, KD-PRT_ID for print),
  you can either:
    - change includes/kdms_web_page_ids.php to match those legacy keys, OR
    - add rows for both old and new keys and grant both in user_access.
*/

/* ========== STEP 3: grant every KD page id to selected roles (edit the role list) ========== */

INSERT IGNORE INTO `user_access` (`user_role_key`, `asset_key`, `access_value`, `access_updated_by`, `access_update_date_time`)
SELECT r.`role_key`, nk.`asset_key`, 'ALL', 'kdms_grant_script', NOW()
FROM (
  SELECT 'ADMIN' AS role_key
  UNION ALL SELECT 'SPRUSR'
  UNION ALL SELECT 'SUPPORT'
  -- Add more role keys that should receive all KD-* pages:
  -- UNION ALL SELECT 'YOUR_ROLE'
) AS r
CROSS JOIN (
  SELECT 'KD-DSBRD' AS asset_key UNION ALL
  SELECT 'KD-REG' UNION ALL
  SELECT 'KD-DISP-DVT' UNION ALL
  SELECT 'KD-PRT-ID' UNION ALL
  SELECT 'KD-RPT-SEVX' UNION ALL
  SELECT 'KD-ACC-REC' UNION ALL
  SELECT 'KD-REG-DASH' UNION ALL
  SELECT 'KD-RPT-DUTY' UNION ALL
  SELECT 'KD-RPT-CARD' UNION ALL
  SELECT 'KD-RPT-CARD-TMP' UNION ALL
  SELECT 'KD-ACCO-I' UNION ALL
  SELECT 'KD-ACCO-II' UNION ALL
  SELECT 'KD-DVT-I' UNION ALL
  SELECT 'KD-SEVA-I' UNION ALL
  SELECT 'KD-SEVA-II' UNION ALL
  SELECT 'KD-DVT-SCR' UNION ALL
  SELECT 'KD-AMT-I' UNION ALL
  SELECT 'KD-AMT-II' UNION ALL
  SELECT 'KD-EVNT-I' UNION ALL
  SELECT 'KD-EVNT-II'
) AS nk;

/*
 * Alternate: grant each new asset only for roles that already have KD-DSBRD —
 * use CROSS JOIN of DISTINCT user_role_key from user_access WHERE asset_key='KD-DSBRD'
 * against the UNION list nk in STEP 3 (same INSERT ... SELECT pattern).
 */
