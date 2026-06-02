-- =============================================================================
-- Restricted MySQL user for kdms-registration Cloud Run service
-- Run manually on Cloud SQL as admin. Replace placeholders before execution.
--
-- DB name: use your KDMS_DB_NAME (terraform default: kdms)
-- Instance: project-12f4b54b-d692-4583-83b:asia-south1:mysql-skm-prod
-- =============================================================================

-- Generate a strong password and store in Secret Manager (e.g. kdms-reg-db-password).
-- SET @reg_password = 'REPLACE_WITH_STRONG_PASSWORD';

CREATE USER IF NOT EXISTS 'kdms_reg'@'%' IDENTIFIED BY 'REPLACE_WITH_STRONG_PASSWORD';

GRANT SELECT, INSERT, UPDATE ON kdms.devotee TO 'kdms_reg'@'%';
GRANT SELECT, INSERT, UPDATE ON kdms.devotee_photo TO 'kdms_reg'@'%';
GRANT SELECT, INSERT, UPDATE ON kdms.devotee_id TO 'kdms_reg'@'%';
GRANT SELECT, INSERT, UPDATE ON kdms.devotee_accomodation TO 'kdms_reg'@'%';
GRANT SELECT ON kdms.accommodation_master TO 'kdms_reg'@'%';
GRANT SELECT, UPDATE ON kdms.accommodation_availability TO 'kdms_reg'@'%';
GRANT SELECT, INSERT ON kdms.devotee_aliases TO 'kdms_reg'@'%';
GRANT SELECT, INSERT ON kdms.devotee_merge_archive TO 'kdms_reg'@'%';
GRANT SELECT ON kdms.devotee_remarks TO 'kdms_reg'@'%';

FLUSH PRIVILEGES;

-- Verify (as kdms_reg): should fail
-- DROP TABLE devotee;
