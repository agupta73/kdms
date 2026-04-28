terraform {
  backend "gcs" {
    bucket = "kdms-tf-state"
    prefix = "env/prod"
  }
}
