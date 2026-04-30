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

resource "google_cloud_run_v2_service" "kdms_api" {
  name     = var.api_service_name
  location = var.region
  project  = var.project_id
  ingress  = var.ingress
  labels   = var.labels

  template {
    labels = var.labels

    service_account                  = var.runtime_sa_email
    timeout                          = "300s"
    max_instance_request_concurrency = var.api_container_concurrency

    scaling {
      min_instance_count = var.api_min_instances
      max_instance_count = var.api_max_instances
    }

    volumes {
      name = "cloudsql"
      cloud_sql_instance {
        instances = [local.sql_connection_name]
      }
    }

    containers {
      image = local.api_image_uri

      ports {
        name           = "http1"
        container_port = var.container_port
      }

      resources {
        limits = {
          cpu    = var.api_cpu
          memory = var.api_memory
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
        name  = "WEBROOT_URL"
        value = "${local.app_public_base}/"
      }

      env {
        name  = "API_BASE_URL"
        value = "${local.api_public_base}/"
      }

      env {
        name  = "APP_URL"
        value = local.api_public_base
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

resource "google_cloud_run_v2_service_iam_binding" "kdms_api_invoker" {
  count = var.api_allow_unauthenticated ? 1 : 0

  project  = var.project_id
  location = var.region
  name     = google_cloud_run_v2_service.kdms_api.name
  role     = "roles/run.invoker"
  members  = ["allUsers"]

  depends_on = [google_cloud_run_v2_service.kdms_api]
}

resource "google_cloud_run_v2_service" "kdms_reports" {
  count    = var.enable_reports_service ? 1 : 0
  name     = var.reports_service_name
  location = var.region
  project  = var.project_id
  ingress  = var.ingress
  labels   = var.labels

  template {
    labels = var.labels

    service_account                  = var.runtime_sa_email
    timeout                          = "600s"
    max_instance_request_concurrency = var.reports_container_concurrency

    scaling {
      min_instance_count = var.reports_min_instances
      max_instance_count = var.reports_max_instances
    }

    containers {
      image = var.reports_image_uri

      ports {
        name           = "http1"
        container_port = var.container_port
      }

      resources {
        limits = {
          cpu    = var.reports_cpu
          memory = var.reports_memory
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
        name  = "WEBROOT_URL"
        value = "${trimsuffix(var.reports_url, "/")}/"
      }

      env {
        name  = "API_BASE_URL"
        value = "${local.api_public_base}/"
      }

      env {
        name  = "KDMS_EVENT_ID"
        value = var.kdms_event_id
      }
    }
  }

  traffic {
    type    = "TRAFFIC_TARGET_ALLOCATION_TYPE_LATEST"
    percent = 100
  }
}

resource "google_cloud_run_v2_service_iam_binding" "kdms_reports_invoker" {
  count = var.enable_reports_service && var.reports_allow_unauthenticated ? 1 : 0

  project  = var.project_id
  location = var.region
  name     = google_cloud_run_v2_service.kdms_reports[0].name
  role     = "roles/run.invoker"
  members  = ["allUsers"]

  depends_on = [google_cloud_run_v2_service.kdms_reports]
}

resource "google_cloud_run_v2_service" "kdms_ocr" {
  count    = var.enable_ocr_service ? 1 : 0
  name     = var.ocr_service_name
  location = var.region
  project  = var.project_id
  ingress  = var.ingress
  labels   = var.labels

  template {
    labels = var.labels

    service_account                  = var.runtime_sa_email
    timeout                          = "300s"
    max_instance_request_concurrency = var.ocr_container_concurrency

    scaling {
      min_instance_count = var.ocr_min_instances
      max_instance_count = var.ocr_max_instances
    }

    containers {
      image = var.ocr_image_uri

      ports {
        name           = "http1"
        container_port = var.ocr_container_port
      }

      resources {
        limits = {
          cpu    = var.ocr_cpu
          memory = var.ocr_memory
        }
        cpu_idle          = true
        startup_cpu_boost = true
      }

      startup_probe {
        tcp_socket {
          port = var.ocr_container_port
        }
        period_seconds    = 240
        timeout_seconds   = 240
        failure_threshold = 1
      }
    }
  }

  traffic {
    type    = "TRAFFIC_TARGET_ALLOCATION_TYPE_LATEST"
    percent = 100
  }
}

resource "google_cloud_run_v2_service_iam_binding" "kdms_ocr_invoker" {
  count = var.enable_ocr_service && var.ocr_allow_unauthenticated ? 1 : 0

  project  = var.project_id
  location = var.region
  name     = google_cloud_run_v2_service.kdms_ocr[0].name
  role     = "roles/run.invoker"
  members  = ["allUsers"]

  depends_on = [google_cloud_run_v2_service.kdms_ocr]
}
