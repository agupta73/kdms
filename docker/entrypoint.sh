#!/bin/sh
set -e
# Cloud Run and local docker: listen on $PORT (default 8080)
P="${PORT:-8080}"
printf 'Listen %s\n' "$P" > /etc/apache2/ports.conf

VHOST_DEFAULT="/etc/apache2/sites-enabled/000-default.conf"
VHOST_ROOT="/etc/apache2/sites-available/kdms-vhost.conf"
VHOST_PREFIX="/etc/apache2/sites-available/kdms-vhost-prefix.conf"

# Local Docker: serve under /kdms (see docker-compose KDMS_APACHE_USE_PREFIX). Production keeps site root.
if [ "${KDMS_APACHE_USE_PREFIX:-}" = "1" ] && [ -f "$VHOST_PREFIX" ]; then
	cp "$VHOST_PREFIX" "$VHOST_DEFAULT"
elif [ -f "$VHOST_ROOT" ]; then
	cp "$VHOST_ROOT" "$VHOST_DEFAULT"
fi

# Match VirtualHost to Listen — requests hit :8080 but <VirtualHost *:80> never matches → 403.
if [ -f "$VHOST_DEFAULT" ]; then
	sed -i -E "s/<VirtualHost[[:space:]]\\*:[0-9]+>/<VirtualHost *:${P}>/" "$VHOST_DEFAULT" 2>/dev/null || true
fi
if ! grep -q 'ServerName ' /etc/apache2/apache2.conf 2>/dev/null; then
	echo 'ServerName localhost' >> /etc/apache2/apache2.conf
fi
exec apache2-foreground
