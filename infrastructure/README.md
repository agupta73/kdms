# Infrastructure

## Terraform (Cloud Run)

The reusable module is at [`terraform/modules/kdms-cloudrun`](terraform/modules/kdms-cloudrun). Copy it into the **`kdms-gcp-infra`** repository (or reference this repo with a Terraform `source` and version tag) and use:

- **State**: `gs://kdms-tf-state` in the [Google backend configuration](https://www.terraform.io/language/settings/backends/gcs) for that stack.
- **Image**: do **not** set the image in Terraform to `branch-main` for production; use the **immutable 7-char SHA** tag that CI published (see Actions run output).
- **Service account `run-kdms@...`**: must have `roles/cloudsql.client` for the target instance and, if you use the Secret Manager env bindings, access is granted by the `google_secret_manager_secret_iam_member` resources in the module (in addition to any org policies).

**Inputs you must set** in the `kdms-gcp-infra` root module: `container_image`, `webroot_url`, `api_base_url` (match your Cloud Run URL, e.g. `https://kdms-prod-xxxxx-uc.a.run.app/`), `cloudsql_connection_name` (if using the built-in volume), `kdms_event_id`, `db_user`, etc.

## GitHub Actions (this repo)

1. In GCP, bind the Workload Identity **provider** in `projects/684080887473/locations/global/workloadIdentityPools/github-pool/providers/...` so that the GitHub repository **`agupta73/kdms`** can impersonate **`ci-builder-kdms@project-12f4b54b-d692-4583-83b.iam.gserviceaccount.com`**.

2. Grant **`ci-builder-kdms`** the Artifact Registry **writer** (and **Service Account User** if needed) role on the `apps` repository in **asia-south1**.

3. In the GitHub repo, add a **repository secret** (or variable) `WIF_PROVIDER` with the full resource name, for example:  
   `projects/684080887473/locations/global/workloadIdentityPools/github-pool/providers/github-provider`  
   (confirm the project number, pool id, and provider id in the [GCP console](https://console.cloud.google.com/iam-admin/workload-identity-pools).)

4. Pushes to **`main`** run `Build and push to Artifact Registry` and tag the image with the **short commit SHA** and the rolling alias **`branch-main`**.

CI does **not** deploy Cloud Run; apply Terraform in **`kdms-gcp-infra`** (or `gcloud run services update` with the new image digest) to roll out a new version.

## Artifact Registry

Repository: **`apps`** in **`asia-south1`**, image name **`kdms`**, i.e.  
`asia-south1-docker.pkg.dev/project-12f4b54b-d692-4583-83b/apps/kdms`

Create the repository in GCP if it does not exist yet (once per project/region), or add a one-time Terraform `google_artifact_registry_repository` in the infra repo.
