#!/usr/bin/env bash
# Create Document AI ID_PROOFING_PROCESSOR via REST (no gcloud documentai CLI).
set -euo pipefail

PROJECT_ID="${PROJECT_ID:-project-12f4b54b-d692-4583-83b}"
LOCATION="${LOCATION:-us}"
DISPLAY_NAME="${DISPLAY_NAME:-KDMS ID Parser}"
PROCESSOR_TYPE="${PROCESSOR_TYPE:-ID_PROOFING_PROCESSOR}"

echo "Enabling documentai.googleapis.com..."
gcloud services enable documentai.googleapis.com --project="$PROJECT_ID"

BODY=$(printf '{"type":"%s","displayName":"%s"}' "$PROCESSOR_TYPE" "$DISPLAY_NAME")
URL="https://${LOCATION}-documentai.googleapis.com/v1/projects/${PROJECT_ID}/locations/${LOCATION}/processors"

echo "Creating processor at ${URL} ..."
RESP=$(curl -s -X POST \
  -H "Authorization: Bearer $(gcloud auth print-access-token)" \
  -H "Content-Type: application/json; charset=utf-8" \
  -d "$BODY" \
  "$URL")

if echo "$RESP" | grep -q '"error"'; then
  echo "$RESP" | python3 -m json.tool 2>/dev/null || echo "$RESP"
  exit 1
fi

echo "$RESP" | python3 -m json.tool
NAME=$(echo "$RESP" | python3 -c "import sys,json; print(json.load(sys.stdin).get('name',''))")
echo ""
echo "Store in Secret Manager (document-ai-processor-id):"
echo "  $NAME"
