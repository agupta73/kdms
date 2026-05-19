# Phase 1a: devotee / ID photos (private bucket; bucket-relative paths in DB).

resource "google_service_account" "kdms_registration" {
  account_id   = "run-kdms-registration"
  display_name = "KDMS day-visitor registration (Cloud Run, Phase 1.5+)"
  project      = var.project_id
}

resource "google_storage_bucket" "kdms_photos" {
  name                        = var.gcs_photos_bucket_name
  location                    = var.region
  project                     = var.project_id
  uniform_bucket_level_access = true
  public_access_prevention    = "enforced"

  labels = var.labels
}

# kdms-api / kdms-main Cloud Run runtime SA (existing).
resource "google_storage_bucket_iam_member" "kdms_api_photos_object_admin" {
  bucket = google_storage_bucket.kdms_photos.name
  role   = "roles/storage.objectAdmin"
  member = "serviceAccount:${var.runtime_sa_email}"
}

# kdms-registration service (Phase 1.5); IAM bound now so bucket policy is stable.
resource "google_storage_bucket_iam_member" "kdms_registration_photos_object_admin" {
  bucket = google_storage_bucket.kdms_photos.name
  role   = "roles/storage.objectAdmin"
  member = "serviceAccount:${google_service_account.kdms_registration.email}"
}

output "gcs_photos_bucket_name" {
  description = "GCS bucket for devotee photos and ID images."
  value       = google_storage_bucket.kdms_photos.name
}

resource "google_project_iam_member" "kdms_registration_documentai" {
  project = var.project_id
  role    = "roles/documentai.apiUser"
  member  = "serviceAccount:${google_service_account.kdms_registration.email}"
}

resource "google_project_iam_member" "kdms_registration_cloudsql_client" {
  project = var.project_id
  role    = "roles/cloudsql.client"
  member  = "serviceAccount:${google_service_account.kdms_registration.email}"
}

resource "google_project_iam_member" "kdms_registration_secret_accessor" {
  project = var.project_id
  role    = "roles/secretmanager.secretAccessor"
  member  = "serviceAccount:${google_service_account.kdms_registration.email}"
}

output "kdms_registration_service_account_email" {
  description = "Service account for kdms-registration Cloud Run (Phase 1.5)."
  value       = google_service_account.kdms_registration.email
}
