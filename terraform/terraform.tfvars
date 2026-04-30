# =============================================================================
# KDMS production — Cloud Run (split services)
# =============================================================================
# Commit policy: This file holds non-secret inputs only (Secret Manager ids, not
# passwords). Update image digests after each promote from CI (immutable deploy).
#
# Rollout checklist:
# 1. Push images to Artifact Registry (kdms image used for UI + API until a
#    dedicated kdms-api image exists).
# 2. Set image_digest + api_image_digest to the same digest (or leave api_* empty
#    to inherit the main kdms image).
# 3. Set app_url + api_url to the live Cloud Run HTTPS URLs (no trailing slash).
# 4. terraform plan && terraform apply
#
# Cloud Run URL shape (example): https://SERVICE-PROJECT_NUMBER.REGION.run.app
# Verify with: gcloud run services describe SERVICE --region REGION --format='value(status.url)'
# =============================================================================

project_id     = "project-12f4b54b-d692-4583-83b"
project_number = "684080887473"
region         = "asia-south1"

# -----------------------------------------------------------------------------
# UI (kdms) + API (kdms-api) — same container image, separate Cloud Run services
# -----------------------------------------------------------------------------
service_name     = "kdms-prod"
api_service_name = "kdms-api-prod"

ar_repo    = "apps"
image_name = "kdms"
# Pin exact manifest (recommended). Copy from Artifact Registry after CI push.
image_digest = "sha256:9b0c4e87347e7c88e4ba5ab7d8ebbc6d536bd9cc77f9bfbcf7d3a5d5099d9a6b"
image_tag    = ""

api_image_name   = "kdms"
api_image_digest = "sha256:9b0c4e87347e7c88e4ba5ab7d8ebbc6d536bd9cc77f9bfbcf7d3a5d5099d9a6b"
api_image_tag    = ""

runtime_sa_email  = "run-kdms@project-12f4b54b-d692-4583-83b.iam.gserviceaccount.com"
cloudsql_instance = "mysql-skm-prod"
db_name           = "kdms"
db_username       = "kdms"

# UI service — interactive pages; moderate concurrency.
min_instances         = 0
max_instances         = 5
cpu                   = "1"
memory                = "2Gi"
container_port        = 8080
container_concurrency = 80

# API service — JSON / DB heavier paths; higher concurrency cap.
api_min_instances         = 0
api_max_instances         = 10
api_cpu                   = "1"
api_memory                = "2Gi"
api_container_concurrency = 120

ingress               = "INGRESS_TRAFFIC_ALL"
allow_unauthenticated = true
api_allow_unauthenticated = true

labels = {
  app = "kdms"
  env = "prod"
}

kdms_event_id = "2026JB"

# Canonical public URLs (no trailing slash). Browser uses API_BASE_URL derived from api_url.
app_url = "https://kdms-prod-684080887473.asia-south1.run.app"
api_url = "https://kdms-api-prod-684080887473.asia-south1.run.app"

# Secret Manager secret *names* (values are not stored in this file).
secret_app_key     = "kdms-app-key"
secret_db_password = "kdms-db-password"

# -----------------------------------------------------------------------------
# Optional: kdms-reports (enable after image exists in Artifact Registry)
# -----------------------------------------------------------------------------
# When true, set reports_image_uri to e.g.
# asia-south1-docker.pkg.dev/PROJECT/apps/kdms-reports@sha256:...
# and reports_url to the deployed service URL (see terraform output reports_service_url).
enable_reports_service = false

reports_service_name = "kdms-reports-prod"
reports_image_uri    = ""
# Placeholder — replace with actual URL after first deploy or from `gcloud run services describe`.
reports_url = "https://kdms-reports-prod-684080887473.asia-south1.run.app"

reports_min_instances         = 0
reports_max_instances         = 4
reports_cpu                   = "1"
reports_memory                = "2Gi"
reports_container_concurrency = 40
reports_allow_unauthenticated = true

# -----------------------------------------------------------------------------
# Optional: kdms-ocr Python service (enable after image exists)
# -----------------------------------------------------------------------------
enable_ocr_service = false

ocr_service_name = "kdms-ocr-prod"
ocr_image_uri    = ""
ocr_url          = "https://kdms-ocr-prod-684080887473.asia-south1.run.app"

ocr_min_instances        = 0
ocr_max_instances        = 6
ocr_cpu                  = "1"
ocr_memory               = "2Gi"
ocr_container_port       = 5001
ocr_container_concurrency = 20
ocr_allow_unauthenticated = true

# Optional: only if the connection name must differ from project_id:region:instance
# cloudsql_connection_name = "project-12f4b54b-d692-4583-83b:asia-south1:mysql-skm-prod"
