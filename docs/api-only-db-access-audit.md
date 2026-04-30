# API-only DB Access Audit

Date: 2026-04-30

Scope:
- `/Applications/XAMPP/xamppfiles/htdocs/kdms`
- `/Applications/XAMPP/xamppfiles/htdocs/kmreports`

Goal:
- Enforce that UI layers do not access MySQL directly.
- Keep DB access in API/business-logic services.

## Method

Searched for direct DB usage patterns:
- `include ... config/database.php`
- `new Database(...)`
- `new PDO(...)`
- `mysqli_connect`, `mysql_connect`

## Findings

### KDMS (`kdms`)

- `UI/registration.php` previously included `api/config/database.php` and instantiated `Database`.
- This was refactored to use `Logic/clsDevoteeSearch.php` (HTTP to `api/searchDevotee.php`) instead of direct DB access.

Current status:
- No direct DB connections found under `kdms/UI/*.php`.
- DB access remains in `kdms/api/*` and `kdms/api/config/database.php` (expected).

### KMReports (`kmreports`)

- No direct DB connection primitives found in KMReports UI/report pages.
- KMReports accesses data via service handlers (`Logic/*`) and API calls, not direct DB clients.

## Remaining risk / follow-up

- Continue to reject any new UI-layer DB includes in code review.
- For Option B split, ensure server-side handlers call `kdms-api` URL, not local loopback DB-backed code paths.
- Add CI grep checks to fail builds if DB primitives appear under UI folders.
