# KDMS (Devotee management)

## Local (Docker)

1. **Composer (optional on host, for local PHP):** `composer install`
2. **Environment:** `cp .env.example .env` and set **`KDMS_DB_PASSWORD`** (and user/name/db) to match your MySQL. **`docker-compose.yml`** sets **`KDMS_DB_HOST=host.docker.internal:3306`** so the container reaches MySQL on the host (do not use `127.0.0.1` for DB inside Docker—it points at the container itself).
3. **Run:** `docker compose up --build`
4. **URL:** **`http://localhost/kdms/`** (compose maps host **80** → container **8080**). Login: **`http://localhost/kdms/UI/login.php`**. If binding to port **80** fails (permission or conflict), change **`ports`** to **`8080:8080`** and set **`WEBROOT_URL`** / **`API_BASE_URL`** in **`docker-compose.yml`** to use **`http://localhost:8080/kdms/`**.

`docker-compose` enables **`KDMS_APACHE_USE_PREFIX=1`** so the app is mounted under **`/kdms`**. Production (Cloud Run) does **not** set this—the image serves the app at the site root.

`docker-compose` sets `WEBROOT_URL`, `API_BASE_URL`, and `KDMS_INTERNAL_ORIGIN` for the container.

## Configuration

- `site_config.php` — public URLs: prefer `WEBROOT_URL` + `API_BASE_URL`, or legacy behavior from `HTTP_HOST` and `KDMS_PATH_SEGMENT` (default `kdms`).
- `api/config/database.php` — all credentials via environment variables; see `.env.example`.

## CI / production

- **Build & push (GitHub Actions, branch `main`):** pushes to  
  `asia-south1-docker.pkg.dev/project-12f4b54b-d692-4583-83b/apps/kdms:<short-sha>` and `...:branch-main`.
- **WIF:** set repository variable **`GCP_WIF_PROVIDER`** to the full Workload Identity provider name (see [terraform/README.md](terraform/README.md#bootstrap-ci-and-artifact-registry)).
- **Cloud Run + Terraform** — deploy the image from the immutable SHA tag using the stack in [`terraform/`](terraform/) (state bucket `gs://kdms-tf-state`; see that folder’s `README.md`).

## Database import (optional)

For large imports, set `max_allowed_packet` on MySQL as before, then:

```bash
mysql -h … -u … -p kdms < shared/your-dump.sql
```

The previous XAMPP path [http://localhost:909/kdms/UI/login.php](http://localhost:909/kdms/UI/login.php) is replaced by the Docker URL above when using the included compose file.
