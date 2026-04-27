output "url" {
  description = "Live HTTPS URL (after deploy and DNS, if any)"
  value       = google_cloud_run_v2_service.kdms.uri
}

output "name" {
  value = google_cloud_run_v2_service.kdms.name
}
