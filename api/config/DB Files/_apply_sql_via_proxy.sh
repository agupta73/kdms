#!/usr/bin/env bash
# Shared helper: Cloud SQL Auth Proxy + mysql apply + optional procedure verify.
# Sourced by apply_proc_options_case_sensitive.sh and apply_PROC_REPLACE_DEVOTEE_W_SEVA_I.sh
#
# Optional env:
#   GCP_PROJECT_ID, CLOUDSQL_INSTANCE, KDMS_DB_NAME, MYSQL_USER, CLOUDSQL_PROXY_PORT
#   SKIP_PROXY=1  — use proxy already listening on CLOUDSQL_PROXY_PORT
#   MYSQL_PWD     — password (avoid in shell history; prefer ~/.my.cnf)

apply_sql_via_proxy() {
  local sql_file="$1"
  local verify_procedure="${2:-}"

  local project_id="${GCP_PROJECT_ID:-project-12f4b54b-d692-4583-83b}"
  local instance="${CLOUDSQL_INSTANCE:-mysql-kdms-prod}"
  local db_name="${KDMS_DB_NAME:-kdms_prod}"
  local db_user="${MYSQL_USER:-root}"
  local proxy_port="${CLOUDSQL_PROXY_PORT:-3307}"
  local connection_name="${project_id}:asia-south1:${instance}"

  if [[ ! -f "${sql_file}" ]]; then
    echo "ERROR: SQL file not found: ${sql_file}" >&2
    return 1
  fi

  if ! command -v mysql >/dev/null 2>&1; then
    echo "ERROR: mysql client not found (brew install mysql-client)" >&2
    return 1
  fi

  local proxy_pid=""
  local started_proxy=0

  stop_proxy() {
    if [[ "${started_proxy}" -eq 1 && -n "${proxy_pid}" ]] && kill -0 "${proxy_pid}" 2>/dev/null; then
      kill -TERM "${proxy_pid}" 2>/dev/null || true
      wait "${proxy_pid}" 2>/dev/null || true
    fi
  }

  if [[ "${SKIP_PROXY:-0}" == "1" ]]; then
    echo "SKIP_PROXY=1 — using existing listener on 127.0.0.1:${proxy_port}"
  else
  if command -v lsof >/dev/null 2>&1 && lsof -nP -iTCP:"${proxy_port}" -sTCP:LISTEN >/dev/null 2>&1; then
    echo "Port ${proxy_port} already in use — using existing proxy (set SKIP_PROXY=1 to silence this)."
  else
    echo "Starting Cloud SQL Auth Proxy on 127.0.0.1:${proxy_port} (${connection_name})..."
    if command -v cloud-sql-proxy >/dev/null 2>&1; then
      cloud-sql-proxy "${connection_name}" --port "${proxy_port}" &
    elif command -v cloud_sql_proxy >/dev/null 2>&1; then
      cloud_sql_proxy -instances="${connection_name}=tcp:${proxy_port}" &
    else
      echo "ERROR: cloud-sql-proxy not found (brew install cloud-sql-proxy)" >&2
      return 1
    fi
    proxy_pid=$!
    started_proxy=1
    trap stop_proxy EXIT
    sleep 3
    echo "Proxy ready."
  fi
  fi

  echo "Applying $(basename "${sql_file}") to ${db_name}..."
  local mysql_args=(
    -h 127.0.0.1
    -P "${proxy_port}"
    -u "${db_user}"
    --batch
    --connect-timeout=30
  )
  if [[ -n "${MYSQL_PWD:-}" ]]; then
    export MYSQL_PWD
  else
    mysql_args+=(-p)
  fi

  if ! mysql "${mysql_args[@]}" "${db_name}" < "${sql_file}"; then
    echo "ERROR: mysql failed applying ${sql_file}" >&2
    return 1
  fi

  echo "mysql finished OK."

  if [[ -n "${verify_procedure}" ]]; then
    echo "Verifying procedure ${verify_procedure}..."
    local body
    body="$(mysql "${mysql_args[@]}" "${db_name}" -N -e \
      "SHOW CREATE PROCEDURE \`${verify_procedure}\`" 2>/dev/null | tail -n 1 || true)"
    if [[ -z "${body}" ]]; then
      echo "ERROR: procedure ${verify_procedure} not found after apply" >&2
      return 1
    fi
    if echo "${body}" | grep -qE '`Devotee_Accomodation`|FROM Devotee_Accomodation|INTO Devotee_Accomodation'; then
      echo "ERROR: ${verify_procedure} still references PascalCase Devotee_Accomodation" >&2
      return 1
    fi
    if echo "${body}" | grep -q 'Accommodation_Master'; then
      echo "ERROR: ${verify_procedure} still references PascalCase Accommodation_Master" >&2
      return 1
    fi
    echo "Verified: ${verify_procedure} exists and uses lowercase table names."
  fi

  stop_proxy
  trap - EXIT
  echo "Done."
  return 0
}
