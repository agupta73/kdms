#!/usr/bin/env bash
# Manual cleanup after Phase 6/7 terraform destroy of kdms-ocr-prod.
# Do NOT auto-run in CI. Review each step before executing.
#
# Optional legacy bucket (if created for kdms-ocr image uploads):
#   gsutil ls gs://kdms_ocr_image_bucket
#   gsutil -m rm -r gs://kdms_ocr_image_bucket   # only if empty / confirmed obsolete
#
# Verify OCR service gone:
#   gcloud run services list --region=asia-south1 --project=project-12f4b54b-d692-4583-83b | grep -i ocr

set -euo pipefail
echo "Phase 6/7: kdms-ocr decommission — manual steps only."
echo "1. terraform apply with enable_ocr_service = false"
echo "2. Confirm kdms-ocr-prod absent from Cloud Run list"
echo "3. Remove unused Artifact Registry kdms-ocr images if desired"
echo "4. Optional: empty/delete kdms_ocr_image_bucket (see comments in this script)"
