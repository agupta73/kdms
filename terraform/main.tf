resource "google_cloud_run_v2_service" "kdms" {
  name     = var.service_name
  location = var.region
  project  = var.project_id
  ingress  = var.ingress

  # Only user labels — never set cloud.googleapis.com/* or run.googleapis.com/* here; the API rejects
  # system labels on update (they appear in effective_labels from GCP automatically).
  labels = var.labels

  template {
    labels = var.labels

    service_account                  = var.runtime_sa_email
    timeout                          = "300s"
    max_instance_request_concurrency = var.container_concurrency

    scaling {
      min_instance_count = var.min_instances
      max_instance_count = var.max_instances
    }

    volumes {
      name = "cloudsql"
      cloud_sql_instance {
        instances = [local.sql_connection_name]
      }
    }

    containers {
      image = local.image_uri

      ports {
        name           = "http1"
        container_port = var.container_port
      }

      resources {
        limits = {
          cpu    = var.cpu
          memory = var.memory
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

      dynamic "env" {
        for_each = local.ordered_plain_prefix_keys
        iterator = plain_key
        content {
          name  = plain_key.value
          value = local.env_vars_plain_prefix[plain_key.value]
        }
      }

      dynamic "env" {
        for_each = local.ordered_secret_env_keys
        iterator = secret_key
        content {
          name = secret_key.value
          value_source {
            secret_key_ref {
              secret  = local.secret_env_vars[secret_key.value].secret
              version = local.secret_env_vars[secret_key.value].version
            }
          }
        }
      }

      env {
        name  = "APP_URL"
        value = var.app_url
      }

      volume_mounts {
        name       = "cloudsql"
        mount_path = "/cloudsql"
      }
    }
  }

  traffic {
    type    = "TRAFFIC_TARGET_ALLOCATION_TYPE_LATEST"
    percent = 100
  }

  lifecycle {
    ignore_changes = [
      client,
      client_version,
      template[0].annotations["run.googleapis.com/operation-id"],
      labels["run.googleapis.com/satisfiesPzs"],
      labels["cloud.googleapis.com/location"],
      template[0].labels["client.knative.dev/nonce"],
    ]
  }
}

resource "google_cloud_run_v2_service_iam_binding" "kdms_invoker" {
  count = var.allow_unauthenticated ? 1 : 0

  project  = var.project_id
  location = var.region
  name     = google_cloud_run_v2_service.kdms.name
  role     = "roles/run.invoker"
  members  = ["allUsers"]

  depends_on = [google_cloud_run_v2_service.kdms]
}
