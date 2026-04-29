locals {
  sql_connection_name = coalesce(
    var.cloudsql_connection_name,
    "${var.project_id}:${var.region}:${var.cloudsql_instance}"
  )

  # Pin by digest (preferred) or by tag — see variables image_digest / image_tag.
  image_digest_hex = trimspace(var.image_digest) == "" ? "" : replace(trimspace(lower(var.image_digest)), "sha256:", "")
  image_base       = "${var.region}-docker.pkg.dev/${var.project_id}/${var.ar_repo}/${var.image_name}"
  image_uri        = local.image_digest_hex != "" ? "${local.image_base}@sha256:${local.image_digest_hex}" : "${local.image_base}:${trimspace(var.image_tag)}"

  # Public HTTPS URL without trailing slash (site_config derives WEBROOT / API URLs from these).
  # Cloud Run revision URL has no /kdms segment — Docker local uses kdms-prefix vhost separately.
  app_public_base = trimsuffix(trimspace(var.app_url), "/")

  # Plain env vars before APP_URL (secrets follow; APP_URL applied last in main.tf).
  env_vars_plain_prefix = {
    APP_ENV         = "production"
    APP_DEBUG       = "false"
    LOG_CHANNEL     = "stderr"
    SESSION_DRIVER  = "cookie"
    CACHE_DRIVER    = "array"
    TRUSTED_PROXIES = "*"
    KDMS_EVENT_ID   = var.kdms_event_id
    WEBROOT_URL     = "${local.app_public_base}/"
    API_BASE_URL    = "${local.app_public_base}/api/"
    # Server-side curl (login/API) hits Apache on loopback — no /kdms prefix (production vhost is root DocRoot).
    KDMS_INTERNAL_ORIGIN = "http://127.0.0.1:${var.container_port}"
    # PDO expects KDMS_* vars (see api/config/database.php); Laravel-style DB_* are kept for Composer/tools.
    KDMS_DB_NAME   = var.db_name
    KDMS_DB_USER   = var.db_username
    KDMS_DB_SOCKET = "/cloudsql/${local.sql_connection_name}"
    DB_CONNECTION  = "mysql"
    DB_HOST        = "/cloudsql/${local.sql_connection_name}"
    DB_PORT        = "3306"
    DB_DATABASE    = var.db_name
    DB_USERNAME    = var.db_username
  }

  ordered_plain_prefix_keys = [
    "APP_ENV",
    "APP_DEBUG",
    "LOG_CHANNEL",
    "SESSION_DRIVER",
    "CACHE_DRIVER",
    "TRUSTED_PROXIES",
    "KDMS_EVENT_ID",
    "WEBROOT_URL",
    "API_BASE_URL",
    "KDMS_INTERNAL_ORIGIN",
    "KDMS_DB_NAME",
    "KDMS_DB_USER",
    "KDMS_DB_SOCKET",
    "DB_CONNECTION",
    "DB_HOST",
    "DB_PORT",
    "DB_DATABASE",
    "DB_USERNAME",
  ]

  # Full plain var set (APP_URL is applied after secret envs in main.tf).
  env_vars = merge(local.env_vars_plain_prefix, { APP_URL = var.app_url })

  secret_env_vars = {
    APP_KEY = {
      secret  = var.secret_app_key
      version = "latest"
    }
    DB_PASSWORD = {
      secret  = var.secret_db_password
      version = "latest"
    }
    KDMS_DB_PASSWORD = {
      secret  = var.secret_db_password
      version = "latest"
    }
  }

  ordered_secret_env_keys = ["APP_KEY", "DB_PASSWORD", "KDMS_DB_PASSWORD"]
}
