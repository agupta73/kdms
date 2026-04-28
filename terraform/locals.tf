locals {
  sql_connection_name = coalesce(
    var.cloudsql_connection_name,
    "${var.project_id}:${var.region}:${var.cloudsql_instance}"
  )

  image_uri = "${var.region}-docker.pkg.dev/${var.project_id}/${var.ar_repo}/${var.image_name}:${var.image_tag}"

  # Plain env vars before APP_URL (order matches live revision; APP_URL is last after secrets).
  env_vars_plain_prefix = {
    APP_ENV         = "production"
    APP_DEBUG       = "false"
    LOG_CHANNEL     = "stderr"
    SESSION_DRIVER  = "cookie"
    CACHE_DRIVER    = "array"
    TRUSTED_PROXIES = "*"
    KDMS_EVENT_ID   = var.kdms_event_id
    DB_CONNECTION   = "mysql"
    DB_HOST         = "/cloudsql/${local.sql_connection_name}"
    DB_PORT         = "3306"
    DB_DATABASE     = var.db_name
    DB_USERNAME     = var.db_username
  }

  ordered_plain_prefix_keys = [
    "APP_ENV",
    "APP_DEBUG",
    "LOG_CHANNEL",
    "SESSION_DRIVER",
    "CACHE_DRIVER",
    "TRUSTED_PROXIES",
    "KDMS_EVENT_ID",
    "DB_CONNECTION",
    "DB_HOST",
    "DB_PORT",
    "DB_DATABASE",
    "DB_USERNAME",
  ]

  # Full plain var set (APP_URL is applied after secret envs in main.tf to match the live order).
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
  }

  ordered_secret_env_keys = ["APP_KEY", "DB_PASSWORD"]
}
