#!/usr/bin/env bash
# Apply PROC_REPLACE_DEVOTEE_W_SEVA_I.sql via mysql CLI (staff devotee save / upsertDevotee).
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=_apply_sql_via_proxy.sh
source "${SCRIPT_DIR}/_apply_sql_via_proxy.sh"

apply_sql_via_proxy \
  "${SCRIPT_DIR}/PROC_REPLACE_DEVOTEE_W_SEVA_I.sql" \
  "PROC_REPLACE_DEVOTEE_W_SEVA_I"
