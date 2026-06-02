-- Grant kdms_reg permission to bump accommodation_availability on PWA registration.
-- Run on production Cloud SQL (mysql-kdms-prod), schema kdms_prod, as admin.

GRANT SELECT, UPDATE ON kdms_prod.accommodation_availability TO 'kdms_reg'@'%';

FLUSH PRIVILEGES;
