# kdms-registration — public day-visitor PWA + Document AI (Phase 1.5)
# Region: var.region (must be asia-south1 in production tfvars).

resource "google_cloud_run_v2_service" "kdms_registration" {
  count               = var.enable_registration_service ? 1 : 0
  name                = var.registration_service_name
  location            = var.region
  project             = var.project_id
  ingress             = "INGRESS_TRAFFIC_ALL"
  deletion_protection = var.cloud_run_deletion_protection
  labels              = var.labels

  template {
    labels = var.labels

    service_account                  = google_service_account.kdms_registration.email
    timeout                          = "120s"
    max_instance_request_concurrency = var.registration_container_concurrency

    scaling {
      min_instance_count = 0
      max_instance_count = var.registration_max_instances
    }

    volumes {
      name = "cloudsql"
      cloud_sql_instance {
        instances = [local.sql_connection_name]
      }
    }

    containers {
      image = local.registration_image_uri

      ports {
        name           = "http1"
        container_port = var.container_port
      }

      resources {
        limits = {
          cpu    = var.registration_cpu
          memory = var.registration_memory
        }
        cpu_idle          = true
        startup_cpu_boost = true
      }

      startup_probe {
        tcp_socket {
          port = var.container_port
        }
        period_seconds    = 240
        timeout_seconds   = 240
        failure_threshold = 1
      }

      env {
        name  = "KDMS_API_BASE_URL"
        value = "${local.api_dir_http_base}/"
      }

      env {
        name  = "KDMS_GCS_PHOTOS_BUCKET"
        value = var.gcs_photos_bucket_name
      }

      env {
        name  = "ACTIVE_EVENT_ID"
        value = var.kdms_event_id
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
        name  = "KDMS_DB_USER"
        value = var.registration_db_username
      }

      env {
        name  = "KDMS_DB_SOCKET"
        value = "/cloudsql/${local.sql_connection_name}"
      }

      env {
        name = "KDMS_DB_PASSWORD"
        value_source {
          secret_key_ref {
            secret  = var.secret_registration_db_password
            version = "latest"
          }
        }
      }

      env {
        name = "KDMS_SERVICE_KEY"
        value_source {
          secret_key_ref {
            secret  = local.secret_env_vars["KDMS_SERVICE_KEY"].secret
            version = local.secret_env_vars["KDMS_SERVICE_KEY"].version
          }
        }
      }

      env {
        name = "DOCUMENT_AI_PROCESSOR_ID"
        value_source {
          secret_key_ref {
            secret  = var.secret_document_ai_processor_id
            version = "latest"
          }
        }
      }

      env {
        name  = "DOCUMENT_AI_PROCESSOR_VERSION"
        value = var.document_ai_processor_version
      }

      volume_mounts {
        name       = "cloudsql"
        mount_path = "/cloudsql"
      }
    }

    annotations = local.revision_template_annotations
  }

  traffic {
    type    = "TRAFFIC_TARGET_ALLOCATION_TYPE_LATEST"
    percent = 100
  }
}

resource "google_cloud_run_v2_service_iam_binding" "kdms_registration_invoker" {
  count = var.enable_registration_service && var.registration_allow_unauthenticated ? 1 : 0

  project  = var.project_id
  location = var.region
  name     = google_cloud_run_v2_service.kdms_registration[0].name
  role     = "roles/run.invoker"
  members  = ["allUsers"]

  depends_on = [google_cloud_run_v2_service.kdms_registration]
}

# -----------------------------------------------------------------------------
# Phase 6/7: retire kdms-ocr after Document AI + registration are stable in prod.
# Do NOT apply until cutover is complete. Example (commented):
#
# resource "google_cloud_run_v2_service" "kdms_ocr" {
#   count = 0  # was: var.enable_ocr_service ? 1 : 0
#   ...
# }
# Set enable_ocr_service = false in terraform.tfvars when ready.
# -----------------------------------------------------------------------------
