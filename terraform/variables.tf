variable "project_id" {
  description = "GCP project ID hosting Cloud Run and related resources."
  type        = string
  default     = "project-12f4b54b-d692-4583-83b"
}

variable "project_number" {
  description = "GCP project number (for documentation and cross-references)."
  type        = string
  default     = "684080887473"
}

variable "region" {
  description = "Region for the Cloud Run service."
  type        = string
  default     = "asia-south1"
}

variable "service_name" {
  description = "Cloud Run service name."
  type        = string
  default     = "kdms-prod"
}

variable "ar_repo" {
  description = "Artifact Registry repository id (short name)."
  type        = string
  default     = "apps"
}

variable "image_name" {
  description = "Artifact Registry image name within the repository."
  type        = string
  default     = "kdms"
}

variable "image_digest" {
  description = <<-EOT
    Optional sha256 digest of the pushed image — pins the exact artifact; avoids tag reuse/mismatch between registries.
    Accept 64 hex chars or "sha256:hex". When non-empty, it overrides image_tag in local.image_uri.
    After push: gcloud artifacts docker images describe REGION-docker.pkg.dev/PROJECT/apps/kdms:TAG \
      --format="value(image_summary.digest)"
  EOT
  type        = string
  default     = ""

  validation {
    condition = (
      trimspace(var.image_digest) == ""
      || can(regex("^sha256:[a-f0-9]{64}$", trimspace(lower(var.image_digest))))
      || can(regex("^[a-f0-9]{64}$", trimspace(lower(var.image_digest))))
    )
    error_message = "image_digest must be empty, 64 hex chars, or sha256: plus 64 hex chars."
  }
}

variable "image_tag" {
  description = <<-EOT
    Container tag (e.g. short git SHA) when image_digest is unset.
    Set to empty string when deploying by digest only.
  EOT
  type        = string
  default     = ""

  validation {
    condition = (
      (trimspace(var.image_digest) != "" && trimspace(var.image_tag) == "") ||
      (trimspace(var.image_digest) == "" && trimspace(var.image_tag) != "")
    )
    error_message = "Set exactly one of image_digest (recommended) or image_tag."
  }
}

variable "runtime_sa_email" {
  description = "Service account email the revision runs as."
  type        = string
  default     = "run-kdms@project-12f4b54b-d692-4583-83b.iam.gserviceaccount.com"
}

variable "cloudsql_instance" {
  description = "Cloud SQL instance id (short name); full connection name is derived in locals."
  type        = string
  default     = "mysql-skm-prod"
}

variable "cloudsql_connection_name" {
  description = "Optional override for instance connection name (project:region:instance). Leave default null to derive from project_id, region, and cloudsql_instance."
  type        = string
  default     = null
}

variable "db_name" {
  description = "MySQL database name (DB_DATABASE)."
  type        = string
  default     = "kdms"
}

variable "db_username" {
  description = "MySQL username (non-secret)."
  type        = string
  default     = "kdms"
}

variable "min_instances" {
  description = "Minimum Cloud Run instances."
  type        = number
  default     = 0
}

variable "max_instances" {
  description = "Maximum Cloud Run instances."
  type        = number
  default     = 5
}

variable "cpu" {
  description = "CPU limit for the container (Cloud Run units)."
  type        = string
  default     = "1"
}

variable "memory" {
  description = "Memory limit for the container."
  type        = string
  default     = "2Gi"
}

variable "container_port" {
  description = "Primary HTTP container port."
  type        = number
  default     = 8080
}

variable "container_concurrency" {
  description = "Maximum concurrent requests per instance (containerConcurrency)."
  type        = number
  default     = 80
}

variable "ingress" {
  description = "Ingress traffic configuration for the service."
  type        = string
  default     = "INGRESS_TRAFFIC_ALL"
}

variable "allow_unauthenticated" {
  description = "If true, bind roles/run.invoker to allUsers."
  type        = bool
  default     = true
}

variable "labels" {
  description = "Labels applied to the Cloud Run service."
  type        = map(string)
  default = {
    app = "kdms"
    env = "prod"
  }
}

variable "kdms_event_id" {
  description = "KDMS_EVENT_ID env value (e.g. calendar year)."
  type        = string
  default     = "2026"
}

variable "app_url" {
  description = "Canonical public HTTPS URL of this Cloud Run service (no trailing slash), e.g. https://myservice-xxxx.asia-south1.run.app — must NOT include a /kdms path; production DocRoot maps to /. Used for WEBROOT_URL/API_BASE_URL derivation."
  type        = string
  default     = "https://kdms-prod-zeqw3ha4ya-el.a.run.app"
}

variable "secret_app_key" {
  description = "Secret Manager secret id holding the Laravel APP_KEY."
  type        = string
  default     = "kdms-app-key"
}

variable "secret_db_password" {
  description = "Secret Manager secret id holding the MySQL password."
  type        = string
  default     = "kdms-db-password"
}
