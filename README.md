# KDMS (Devotee management)

## Local (Docker)

1. **Composer (optional on host, for local PHP):** `composer install`
2. **Environment:** `cp .env.example .env` and set `KDMS_DB_HOST`, `KDMS_DB_NAME`, `KDMS_DB_USER`, `KDMS_DB_PASSWORD` to reach MySQL. For DB on the Mac host from Docker, use `KDMS_DB_HOST=host.docker.internal:3306`.
3. **Run:** `docker compose up --build`
4. **URL:** [http://localhost:108/](http://localhost:108/) (main UI, e.g. [http://localhost:108/UI/login.php](http://localhost:108/UI/login.php)) — the compose file sets `WEBROOT_URL` and `API_BASE_URL` for the container.

`docker-compose` maps host port **108** to container **8080** (Cloud Run / Apache listen port).

## Configuration

- `site_config.php` — public URLs: prefer `WEBROOT_URL` + `API_BASE_URL`, or legacy behavior from `HTTP_HOST` and `KDMS_PATH_SEGMENT` (default `kdms`).
- `api/config/database.php` — all credentials via environment variables; see `.env.example`.

## CI / production

- **Build & push (GitHub Actions, branch `main`):** pushes to  
  `asia-south1-docker.pkg.dev/project-12f4b54b-d692-4583-83b/apps/kdms:<short-sha>` and `...:branch-main`.
- **WIF:** set repository secret `WIF_PROVIDER` to the full Workload Identity provider name (see `infrastructure/README.md`).
- **Cloud Run + Terraform** — deploy the image from the immutable SHA tag using the module in `infrastructure/terraform/modules/kdms-cloudrun` (typically from the `kdms-gcp-infra` repo with state in `gs://kdms-tf-state`).

## Database import (optional)

For large imports, set `max_allowed_packet` on MySQL as before, then:

```bash
mysql -h … -u … -p kdms < shared/your-dump.sql
```

The previous XAMPP path [http://localhost:909/kdms/UI/login.php](http://localhost:909/kdms/UI/login.php) is replaced by the Docker URL above when using the included compose file.
