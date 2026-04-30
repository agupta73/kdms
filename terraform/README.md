# Terraform — KDMS on GCP

Terraform root stack for split Cloud Run (v2) services in GCP. It manages service definitions (image, scaling, env vars, Cloud SQL volume where needed, ingress, labels) and optional **public invoker** IAM (`allUsers` → `roles/run.invoker`).

## What this stack manages

- `google_cloud_run_v2_service.kdms` — Cloud Run service **`kdms-prod`** (UI/web)
- `google_cloud_run_v2_service.kdms_api` — Cloud Run service **`kdms-api-prod`** (API)
- `google_cloud_run_v2_service.kdms_reports` — optional **`kdms-reports-prod`** when `enable_reports_service = true`
- `google_cloud_run_v2_service.kdms_ocr` — optional **`kdms-ocr-prod`** when `enable_ocr_service = true`
- matching IAM invoker bindings per service when `*_allow_unauthenticated = true`

## What it does **not** manage

State bucket **`gs://kdms-tf-state`**, Artifact Registry repo **`apps`**, runtime service accounts, Secret Manager secrets, MySQL instance or users, VPC, Workload Identity Federation, and CI are **out of scope** — created or maintained outside this stack. See **[Bootstrap, CI, and Artifact Registry](#bootstrap-ci-and-artifact-registry)** for one-time GCP / GitHub setup.

## Bootstrap, CI, and Artifact Registry

### Runtime access (production)

The **`run-kdms@...`** service account must have **`roles/cloudsql.client`** on the Cloud SQL instance and Secret Manager access to **`kdms-app-key`**, **`kdms-db-password`**, and **`kdms-service-key`** (usually granted when the SA and secrets were bootstrapped).

### GitHub Actions — build and push

1. In GCP, bind the Workload Identity **provider** under `projects/684080887473/locations/global/workloadIdentityPools/...` so the GitHub repository **`agupta73/kdms`** can impersonate **`ci-builder-kdms@project-12f4b54b-d692-4583-83b.iam.gserviceaccount.com`**.

2. Grant **`ci-builder-kdms`** Artifact Registry **writer** (and **Service Account User** if needed) on repository **`apps`** in **`asia-south1`**.

3. In this GitHub repo, add repository variable **`GCP_WIF_PROVIDER`** (Settings → Secrets and variables → Actions → Variables) with the full provider resource name, for example:  
   `projects/684080887473/locations/global/workloadIdentityPools/github-pool/providers/github-provider`

4. Pushes to **`main`** run **Build and push service images to Artifact Registry** and build/push all four images:
   - `kdms-main`
   - `kdms-api`
   - `kdms-reports`
   - `kdms-ocr`
   using the **short commit SHA** and **`branch-main`** tags.
   You can also run it manually: **Actions** → **Build and push service images to Artifact Registry** → **Run workflow**.

5. Add repository variables for external service repos:
   - `KMREPORTS_REPO_URL`
   - `KDMS_OCR_REPO_URL`

CI does **not** deploy Cloud Run; roll out by applying this stack from **`terraform/`** (after setting **`image_digest`** or **`image_tag`** in **`terraform.tfvars`**) or with **`gcloud run services update`**.

### Artifact Registry

Repository: **`apps`** in **`asia-south1`**, image **`kdms`**:  
`asia-south1-docker.pkg.dev/project-12f4b54b-d692-4583-83b/apps/kdms`

Create the **`apps`** repository in GCP if it does not exist yet (once per project/region), or codify it in a separate bootstrap stack if you choose.

### Production image: digest or tag

- **Digest (recommended):** set **`image_digest`** to the **`sha256:…`** value from Artifact Registry for the image you intend to run, and set **`image_tag`** to **`""`**. Terraform deploys `…/kdms@sha256:…`, so the revision always matches that manifest — no ambiguity if a tag is reused elsewhere (e.g. GitHub vs GCP).
- **Tag only:** set **`image_digest`** to **`""`** and **`image_tag`** to the **short git SHA** tag CI pushed to Artifact Registry. Do **not** use floating tags like **`branch-main`** for production; treat the short SHA as **immutable** (never move that tag to another image).

See **`variables.tf`** and **`terraform.tfvars.example`** for **`app_url`** and other inputs.

## One-time setup

From the repository root:

```bash
cd terraform
cp terraform.tfvars.example terraform.tfvars
# set image_digest (and image_tag = "") or image_tag (and image_digest = "")

terraform init
terraform workspace select prod || terraform workspace new prod
bash import.sh
terraform plan
```

## Day-to-day deploys (new image)

After CI builds and pushes images:

1. Update **`terraform.tfvars`**:
   - `image_digest`/`image_tag` for `kdms-main`
   - `api_image_digest`/`api_image_tag` for `kdms-api` (or leave both empty to reuse `kdms-main` image)
   - `reports_image_digest`/`reports_image_tag` (or `reports_image_uri`) for `kdms-reports`
   - `ocr_image_digest`/`ocr_image_tag` (or `ocr_image_uri`) for `kdms-ocr`
   - set `api_url`, `reports_url`, `ocr_url` to live service URLs
   - enable services with `enable_reports_service` and `enable_ocr_service` as needed
2. Plan and apply (from **`terraform/`**):

```bash
cd terraform
terraform plan -out=plan.tfplan
terraform apply plan.tfplan
```

## Rollback

Set **`image_digest`** or **`image_tag`** back to the previous known-good value, then **`terraform plan`** and **`terraform apply`** as above.

## Teardown

```bash
cd terraform
terraform destroy
```

This removes only the Cloud Run service and the invoker IAM binding managed here. The database, secrets, service accounts, and registry remain.

## Troubleshooting

- **Env var order** in **`main.tf`** follows the live **`kdms-prod`** revision (plain vars, then **`APP_KEY`** / **`DB_PASSWORD`**, then **`APP_URL`**). Do not reorder without checking **`terraform plan`** against GCP.
- If **`terraform plan`** keeps changing **annotations** such as **`client_version`** or **`run.googleapis.com/client-name`** on the revision template, add those attributes to **`lifecycle.ignore_changes`** on **`google_cloud_run_v2_service.kdms`** (same pattern as **`run.googleapis.com/operation-id`**).
- If **`terraform plan`** shows churn on **`container`** fields Google normalizes server-side (for example **`image_pull_policy`** if it appears in your provider version), add them to **`lifecycle.ignore_changes`**.
- If the **invoker** binding drifts, confirm the service was not switched with **`gcloud run services update --no-allow-unauthenticated`** (or re-run **`import.sh`** after aligning with the live policy).
