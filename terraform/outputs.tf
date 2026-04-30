output "service_url" {
  description = "HTTPS URI of the Cloud Run service."
  value       = google_cloud_run_v2_service.kdms.uri
}

output "api_service_url" {
  description = "HTTPS URI of the split kdms-api Cloud Run service."
  value       = google_cloud_run_v2_service.kdms_api.uri
}

output "reports_service_url" {
  description = "HTTPS URI of the split kdms-reports Cloud Run service."
  value       = var.enable_reports_service ? google_cloud_run_v2_service.kdms_reports[0].uri : null
}

output "ocr_service_url" {
  description = "HTTPS URI of the split kdms-ocr Cloud Run service."
  value       = var.enable_ocr_service ? google_cloud_run_v2_service.kdms_ocr[0].uri : null
}

output "latest_revision" {
  description = "Latest ready revision name."
  value       = google_cloud_run_v2_service.kdms.latest_ready_revision
}

output "api_latest_revision" {
  description = "Latest ready revision name for kdms-api."
  value       = google_cloud_run_v2_service.kdms_api.latest_ready_revision
}

output "image_deployed" {
  description = "Fully qualified container image URI from configuration."
  value       = local.image_uri
}
