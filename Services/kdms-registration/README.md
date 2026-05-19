# kdms-registration

Public day-visitor registration (PWA + Document AI). Deployed to Cloud Run in **asia-south1**.

## Local dev

```bash
docker compose -f docker-compose.split.yml up kdms-registration kdms-api
# PWA: http://localhost:8083/
# OCR mock: DOCUMENT_AI_PROCESSOR_ID=mock
```

## API

| Method | Path | Notes |
|--------|------|--------|
| GET | `/api/health` | Liveness |
| GET | `/api/csrf-token` | Session CSRF (30 min) |
| POST | `/api/ocr-extract` | `id_image` multipart |
| GET | `/api/selfie-upload-url` | Signed GCS PUT |
| POST | `/api/register` | JSON + CSRF |

## Deploy

See `docs/document-ai-setup.md`, `scripts/create_registration_db_user.sql`, and `docs/validation_report_phase1.5.md`.
