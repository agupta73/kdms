#!/usr/bin/env bash
# Apply PROC_OPTIONS_CASE_SENSITIVE_TABLES.sql via mysql CLI (not Cloud SQL Studio).
set -euo pipefail

PROJECT_ID="${GCP_PROJECT_ID:-project-12f4b54b-d692-4583-83b}"
INSTANCE="${CLOUDSQL_INSTANCE:-mysql-kdms-prod}"
DB_NAME="${KDMS_DB_NAME:-kdms_prod}"
DB_USER="${MYSQL_USER:-root}"
PROXY_PORT="${CLOUDSQL_PROXY_PORT:-3307}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SQL_FILE="${SCRIPT_DIR}/PROC_OPTIONS_CASE_SENSITIVE_TABLES.sql"

if [[ ! -f "${SQL_FILE}" ]]; then
  echo "Missing SQL file: ${SQL_FILE}" >&2
  exit 1
fi

if ! command -v mysql >/dev/null 2>&1; then
  echo "mysql client not found. Install: brew install mysql-client" >&2
  exit 1
fi

CONNECTION_NAME="${PROJECT_ID}:asia-south1:${INSTANCE}"

echo "Starting Cloud SQL Auth Proxy on 127.0.0.1:${PROXY_PORT} (${CONNECTION_NAME})..."
if command -v cloud-sql-proxy >/dev/null 2>&1; then
  cloud-sql-proxy "${CONNECTION_NAME}" --port "${PROXY_PORT}" &
elif command -v cloud_sql_proxy >/dev/null 2>&1; then
  cloud_sql_proxy -instances="${CONNECTION_NAME}=tcp:${PROXY_PORT}" &
else
  echo "cloud-sql-proxy not found. Install: brew install cloud-sql-proxy" >&2
  exit 1
fi
PROXY_PID=$!
trap 'kill "${PROXY_PID}" 2>/dev/null || true' EXIT

sleep 3

echo "Applying procedures to ${DB_NAME}..."
mysql -h 127.0.0.1 -P "${PROXY_PORT}" -u "${DB_USER}" -p "${DB_NAME}" < "${SQL_FILE}"

echo "Done. Verify with:"
echo "  mysql -h 127.0.0.1 -P ${PROXY_PORT} -u ${DB_USER} -p -e \"SHOW CREATE PROCEDURE ${DB_NAME}.PROC_REFRESH_ACCO_COUNT_W_EVENT\\G\""
