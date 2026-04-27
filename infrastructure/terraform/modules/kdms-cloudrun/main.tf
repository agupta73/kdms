# Cloud Run (v2) for KDMS. Set `container_image` to the SHA tag built by GitHub Actions.
# Grant the runtime service account: roles/cloudsql.client, secretmanager access on the two secrets, roles/run.invoker (if not public, use your IdP only).

resource "google_secret_manager_secret_iam_member" "db_password_accessor" {
  count     = var.set_db_password_env ? 1 : 0
  project   = var.project_id
  secret_id = var.secret_db_password
  role      = "roles/secretmanager.secretAccessor"
  member    = "serviceAccount:${var.runtime_service_account_email}"
}

resource "google_secret_manager_secret_iam_member" "app_key_accessor" {
  count     = var.set_app_key_env ? 1 : 0
  project   = var.project_id
  secret_id = var.secret_app_key
  role      = "roles/secretmanager.secretAccessor"
  member    = "serviceAccount:${var.runtime_service_account_email}"
}

resource "google_cloud_run_v2_service" "kdms" {
  name     = var.service_name
  location = var.region
  project  = var.project_id
  client   = "terraform"

  template {
    service_account = var.runtime_service_account_email

    scaling {
      min_instance_count = var.min_instances
      max_instance_count = var.max_instances
    }

    timeout = "300s"

    dynamic "volumes" {
      for_each = var.cloudsql_connection_name == null || var.cloudsql_connection_name == "" ? [] : [1]
      content {
        name = "cloudsql"
        cloud_sql_instance {
          instances = [var.cloudsql_connection_name]
        }
      }
    }

    containers {
      name  = "kdms"
      image = var.container_image

      ports { container_port = 8080 }

      resources {
        limits = {
          cpu    = var.container_cpu
          memory = var.container_memory
        }
      }

      env {
        name  = "PORT"
        value = "8080"
      }
      env {
        name  = "WEBROOT_URL"
        value = var.webroot_url
      }
      env {
        name  = "API_BASE_URL"
        value = var.api_base_url
      }
      env {
        name  = "KDMS_EVENT_ID"
        value = var.kdms_event_id
      }
      env {
        name  = "KDMS_DB_NAME"
        value = var.db_name
      }
      env {
        name  = "KDMS_DB_INV_NAME"
        value = var.db_inv_name
      }
      env {
        name  = "KDMS_DB_USER"
        value = var.db_user
      }
      env {
        name  = "PHP_TZ"
        value = "Asia/Kolkata"
      }
      # Cloud SQL: socket under /cloudsql/INSTANCE when the volume is mounted
      dynamic "env" {
        for_each = (var.cloudsql_connection_name == null || var.cloudsql_connection_name == "") ? [] : [1]
        content {
          name  = "KDMS_DB_SOCKET"
          value = "/cloudsql/${var.cloudsql_connection_name}"
        }
      }
      # Private IP / local TCP: set when not using the connector socket
      dynamic "env" {
        for_each = (var.cloudsql_connection_name == null || var.cloudsql_connection_name == "") && var.kdms_db_host != null && var.kdms_db_host != "" ? [1] : []
        content {
          name  = "KDMS_DB_HOST"
          value = var.kdms_db_host
        }
      }
      dynamic "env" {
        for_each = var.set_db_password_env ? [1] : []
        content {
          name = "KDMS_DB_PASSWORD"
          value_source {
            secret_key_ref {
              secret  = var.secret_db_password
              version = "latest"
            }
          }
        }
      }
      dynamic "env" {
        for_each = var.set_app_key_env ? [1] : []
        content {
          name = "KDMS_APP_KEY"
          value_source {
            secret_key_ref {
              secret  = var.secret_app_key
              version = "latest"
            }
          }
        }
      }

      dynamic "volume_mounts" {
        for_each = var.cloudsql_connection_name == null || var.cloudsql_connection_name == "" ? [] : [1]
        content {
          name       = "cloudsql"
          mount_path = "/cloudsql"
        }
      }
    }

    annotations = (var.cloudsql_connection_name == null || var.cloudsql_connection_name == "") ? {} : {
      "run.googleapis.com/cloudsql-instances" = var.cloudsql_connection_name
    }
  }
}

resource "google_cloud_run_v2_service_iam_member" "public_invoker" {
  count    = var.allow_unauthenticated ? 1 : 0
  project  = var.project_id
  location = var.region
  name     = google_cloud_run_v2_service.kdms.name
  role     = "roles/run.invoker"
  member   = "allUsers"
}
