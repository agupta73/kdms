#!/bin/sh
set -e
# Cloud Run and local docker: listen on $PORT (default 8080)
P="${PORT:-8080}"
printf 'Listen %s\n' "$P" > /etc/apache2/ports.conf
# Ensure a single vhost (php:8.x-apache) uses the same port
VHOST_DEFAULT="/etc/apache2/sites-enabled/000-default.conf"
if [ -f "$VHOST_DEFAULT" ]; then
  sed -i "s/VirtualHost *:80/VirtualHost *:${P}/" "$VHOST_DEFAULT" 2>/dev/null || true
  sed -i "s/VirtualHost *:8080/VirtualHost *:${P}/" "$VHOST_DEFAULT" 2>/dev/null || true
fi
if ! grep -q 'ServerName ' /etc/apache2/apache2.conf 2>/dev/null; then
  echo 'ServerName localhost' >> /etc/apache2/apache2.conf
fi
exec apache2-foreground
