# Document AI setup (kdms-registration)

Identity Document Parser runs in **us** or **eu** (not all processor types are available in `asia-south1`). Cloud Run stays in `asia-south1`; API latency to us/eu is acceptable (~200–400ms).

## 1. Enable API

```bash
gcloud services enable documentai.googleapis.com \
  --project=project-12f4b54b-d692-4583-83b
```

## 2. Create processor

Check current locations: [Document AI processors list](https://cloud.google.com/document-ai/docs/processors-list).

Example (US):

```bash
gcloud documentai processors create \
  --project=project-12f4b54b-d692-4583-83b \
  --location=us \
  --display-name="KDMS ID Parser" \
  --type=ID_DOCUMENT_PROCESSOR
```

Note the full resource name, e.g.:

`projects/project-12f4b54b-d692-4583-83b/locations/us/processors/PROCESSOR_ID`

## 3. Secret Manager

Store the processor resource name (not just the short id):

```bash
echo -n 'projects/.../locations/us/processors/...' | \
  gcloud secrets create document-ai-processor-id \
    --project=project-12f4b54b-d692-4583-83b \
    --data-file=-
```

Or add a new version to the existing secret id configured in `terraform.tfvars` (`secret_document_ai_processor_id`).

## 4. IAM for Cloud Run SA

```bash
gcloud projects add-iam-policy-binding project-12f4b54b-d692-4583-83b \
  --member="serviceAccount:run-kdms-registration@project-12f4b54b-d692-4583-83b.iam.gserviceaccount.com" \
  --role="roles/documentai.apiUser"
```

(Terraform also applies this via `terraform/gcs.tf`.)

## 5. Local dev without Document AI

Set `DOCUMENT_AI_PROCESSOR_ID=mock` (see `docker-compose.split.yml`). OCR returns empty fields; images still upload to GCS when credentials allow.
