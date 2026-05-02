# Apache + PHP for kmreports; must listen on $PORT for Cloud Run (default 8080).
FROM php:8.3-apache

WORKDIR /var/www/html
COPY . .

# Root redirect; case/URL compatibility (Linux containers are case-sensitive).
RUN printf '%s\n' '<?php' 'header("Location: /UI/login.php");' 'exit;' > /var/www/html/index.php \
	&& if [ -f /var/www/html/UI/index.php ]; then cp /var/www/html/UI/index.php /var/www/html/UI/Index.php; fi \
	&& ln -snf /var/www/html/Reports /var/www/html/reports

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENV PORT=8080
EXPOSE 8080
ENTRYPOINT ["/entrypoint.sh"]
