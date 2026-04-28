output "service_url" {
  description = "HTTPS URI of the Cloud Run service."
  value       = google_cloud_run_v2_service.kdms.uri
}

output "latest_revision" {
  description = "Latest ready revision name."
  value       = google_cloud_run_v2_service.kdms.latest_ready_revision
}

output "image_deployed" {
  description = "Fully qualified container image URI from configuration."
  value       = local.image_uri
}
