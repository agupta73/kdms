# Terraform — KDMS on GCP

Terraform root stack for the **`kdms-prod`** Cloud Run (v2) service in GCP. It manages the service definition (image, scaling, env vars, Cloud SQL volume, ingress, labels) and optional **public invoker** IAM (`allUsers` → `roles/run.invoker`).

## What this stack manages

- `google_cloud_run_v2_service.kdms` — Cloud Run service **`kdms-prod`** in **`asia-south1`**
- `google_cloud_run_v2_service_iam_binding.kdms_invoker` — only when **`allow_unauthenticated`** is true

## What it does **not** manage

State bucket **`gs://kdms-tf-state`**, Artifact Registry repo **`apps`**, runtime service accounts, Secret Manager secrets, MySQL instance or users, VPC, Workload Identity Federation, and CI are **out of scope** — created or maintained outside this stack. See **[Bootstrap, CI, and Artifact Registry](#bootstrap-ci-and-artifact-registry)** for one-time GCP / GitHub setup.

## Bootstrap, CI, and Artifact Registry

### Runtime access (production)

The **`run-kdms@...`** service account must have **`roles/cloudsql.client`** on the Cloud SQL instance and Secret Manager access to **`kdms-app-key`** and **`kdms-db-password`** (usually granted when the SA and secrets were bootstrapped).

### GitHub Actions — build and push

1. In GCP, bind the Workload Identity **provider** under `projects/684080887473/locations/global/workloadIdentityPools/...` so the GitHub repository **`agupta73/kdms`** can impersonate **`ci-builder-kdms@project-12f4b54b-d692-4583-83b.iam.gserviceaccount.com`**.

2. Grant **`ci-builder-kdms`** Artifact Registry **writer** (and **Service Account User** if needed) on repository **`apps`** in **`asia-south1`**.

3. In this GitHub repo, add repository variable **`GCP_WIF_PROVIDER`** (Settings → Secrets and variables → Actions → Variables) with the full provider resource name, for example:  
   `projects/684080887473/locations/global/workloadIdentityPools/github-pool/providers/github-provider`

4. Pushes to **`main`** run **Build and push to Artifact Registry** and tag the image with the **short commit SHA** and **`branch-main`**. You can also run that workflow manually: **Actions** → **Build and push to Artifact Registry** → **Run workflow**.

CI does **not** deploy Cloud Run; roll out by applying this stack from **`terraform/`** (after setting **`image_tag`** in **`terraform.tfvars`**) or with **`gcloud run services update`**.

### Artifact Registry

Repository: **`apps`** in **`asia-south1`**, image **`kdms`**:  
`asia-south1-docker.pkg.dev/project-12f4b54b-d692-4583-83b/apps/kdms`

Create the **`apps`** repository in GCP if it does not exist yet (once per project/region), or codify it in a separate bootstrap stack if you choose.

### Production image tag

Do **not** set **`image_tag`** to **`branch-main`** for production; use the **immutable short SHA** that CI published (see the Actions run output).

Terraform inputs such as **`image_tag`**, **`app_url`**, and DB-related settings are documented in **`variables.tf`** and **`terraform.tfvars.example`**.

## One-time setup

From the repository root:

```bash
cd terraform
cp terraform.tfvars.example terraform.tfvars
# edit image_tag to the SHA tag currently running in production

terraform init
terraform workspace select prod || terraform workspace new prod
bash import.sh
terraform plan
```

## Day-to-day deploys (new image)

After CI builds and pushes `asia-south1-docker.pkg.dev/.../apps/kdms:<sha>`:

1. Update **`terraform.tfvars`**: `image_tag = "<new-sha>"`
2. Plan and apply (from **`terraform/`**):

```bash
cd terraform
terraform plan -out=plan.tfplan
terraform apply plan.tfplan
```

## Rollback

Set **`image_tag`** back to the previous immutable tag, then **`terraform plan`** and **`terraform apply`** as above.

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
