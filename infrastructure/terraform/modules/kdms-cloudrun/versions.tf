terraform {
  required_version = ">= 1.5.0"
  required_providers {
    google = {
      source  = "hashicorp/google"
      version = ">= 5.40, < 7.0.0"
    }
  }
}
# Configure the Google provider in the root module: project, region, default permissions.
