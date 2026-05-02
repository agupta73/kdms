#!/bin/sh
set -e
# Cloud Run sets PORT (default 8080). Stock php:apache listens on 80 → probe fails.
P="${PORT:-8080}"
printf 'Listen %s\n' "$P" > /etc/apache2/ports.conf

VHOST_DEFAULT="/etc/apache2/sites-enabled/000-default.conf"
if [ -f "$VHOST_DEFAULT" ]; then
	sed -i -E "s/<VirtualHost[[:space:]]\\*:[0-9]+>/<VirtualHost *:${P}>/" "$VHOST_DEFAULT" 2>/dev/null || true
fi
if ! grep -q 'ServerName ' /etc/apache2/apache2.conf 2>/dev/null; then
	echo 'ServerName localhost' >> /etc/apache2/apache2.conf
fi
exec apache2-foreground
