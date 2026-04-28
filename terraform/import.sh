#!/usr/bin/env bash
set -euo pipefail

PROJECT_ID="project-12f4b54b-d692-4583-83b"
REGION="asia-south1"
SVC="kdms-prod"

if ! terraform state list 2>/dev/null | grep -q '^google_cloud_run_v2_service\.kdms$'; then
  terraform import google_cloud_run_v2_service.kdms \
    "projects/${PROJECT_ID}/locations/${REGION}/services/${SVC}"
else
  echo "google_cloud_run_v2_service.kdms already in state"
fi

if ! terraform state list 2>/dev/null | grep -q '^google_cloud_run_v2_service_iam_binding\.kdms_invoker\[0\]$'; then
  terraform import 'google_cloud_run_v2_service_iam_binding.kdms_invoker[0]' \
    "projects/${PROJECT_ID}/locations/${REGION}/services/${SVC} roles/run.invoker"
else
  echo "google_cloud_run_v2_service_iam_binding.kdms_invoker already in state"
fi
