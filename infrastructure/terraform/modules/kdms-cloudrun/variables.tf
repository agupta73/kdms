variable "project_id" {
  description = "GCP project (e.g. project-12f4b54b-d692-4583-83b)"
  type        = string
}

variable "region" {
  description = "Region for Cloud Run (e.g. asia-south1)"
  type        = string
}

variable "service_name" {
  description = "Cloud Run service name (e.g. kdms-prod)"
  type        = string
  default     = "kdms-prod"
}

variable "container_image" {
  description = "Full image URI, e.g. asia-south1-docker.pkg.dev/PROJECT/apps/kdms:7a3f2b1 (immutable short SHA from CI)"
  type        = string
}

variable "runtime_service_account_email" {
  description = "Service account the revision runs as (e.g. run-kdms@...)"
  type        = string
}

variable "cloudsql_connection_name" {
  description = "Optional. Cloud SQL instance connection name: project:region:instance for Unix socket. Leave empty to omit mount."
  type        = string
  default     = null
}

variable "kdms_event_id" {
  type = string
}

variable "webroot_url" {
  description = "Service public URL for WEBROOT_URL (trailing / recommended)"
  type        = string
}

variable "api_base_url" {
  description = "Service API base for API_BASE_URL, usually webroot + api/"
  type        = string
}

variable "db_name" {
  description = "MySQL database name (KDMS_DB_NAME)"
  type        = string
  default     = "kdms"
}

variable "db_inv_name" {
  type    = string
  default = "kinv2023"
}

variable "db_user" {
  description = "DB user (non-secret, e.g. kdms) — use Secret Manager for password"
  type        = string
}

variable "kdms_db_host" {
  description = "When not using the Cloud SQL volume, set TCP host:port (e.g. 10.0.0.3:3306). If null/empty, omitted when Cloud SQL socket is set."
  type        = string
  default     = null
}

variable "secret_db_password" {
  description = "Full secret resource name or id for MySQL password (e.g. kdms-db-password)"
  type        = string
  default     = "kdms-db-password"
}

variable "secret_app_key" {
  description = "Full secret name or id for app key (kdms-app-key) if the app reads it from env; optional."
  type        = string
  default     = "kdms-app-key"
}

variable "set_app_key_env" {
  description = "If true, map secret kdms-app-key to env KDMS_APP_KEY (enable when the application reads it)"
  type        = bool
  default     = false
}

variable "set_db_password_env" {
  type    = bool
  default = true
}

variable "min_instances" {
  type    = number
  default = 0
}

variable "max_instances" {
  type    = number
  default = 3
}

variable "container_cpu" {
  type    = string
  default = "1"
}

variable "container_memory" {
  type    = string
  default = "1024Mi"
}

variable "allow_unauthenticated" {
  description = "If true, grant allUsers the run.invoker role (public app)"
  type        = bool
  default     = true
}
