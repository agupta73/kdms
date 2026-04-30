<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/web_session.php';
http_response_code(410);
header('Content-Type: text/plain; charset=utf-8');
echo 'Excel export has been removed from KDMS.';
