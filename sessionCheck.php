<?php

declare(strict_types=1);

// Central session gates for KDMS UI and JSON backends (see includes/web_session.php, includes/api_session.php).

$debug = false;
$result = true;

$config_data = include __DIR__ . '/site_config.php';

if ($debug) {
    echo '<br>current page ID: ';
    var_dump($current_page_id ?? null);
    echo '<br>Search result of current page ID: ';
    var_dump(isset($_SESSION['Access']) ? explode(',', (string) $_SESSION['Access']) : []);
    var_dump(isset($current_page_id, $_SESSION['Access'])
        ? array_search($current_page_id, explode(',', (string) $_SESSION['Access']), true)
        : false);
    echo '<br>entire session: ';
    var_dump($_SESSION ?? []);
    echo '<br>';
}

if (session_status() === PHP_SESSION_DISABLED) {
    $result = false;
} else {
    if (! isset($_SESSION['eventDesc'])) {
        $result = false;
    } elseif ($_SESSION['eventDesc'] === '') {
        $result = false;
    }
    if ($debug) {
        echo '<br> result : ';
        var_dump($result);
    }

    if (! isset($_SESSION['LoginID'])) {
        $result = false;
    } elseif ($_SESSION['LoginID'] === '') {
        $result = false;
    }

    if ($debug) {
        echo '<br> result : ';
        var_dump($result);
    }

    if (! isset($_SESSION['Role'])) {
        $result = false;
    } elseif ($_SESSION['Role'] === '') {
        $result = false;
    }

    if ($debug) {
        echo '<br> result : ';
        var_dump($result);
    }

    if ($config_data['check_access']) {
        if (! isset($_SESSION['Access'])) {
            $result = false;
        } elseif ($_SESSION['Access'] === '') {
            $result = false;
        } elseif (isset($current_page_id)) {
            $accessParts = explode(',', (string) $_SESSION['Access']);
            if (! in_array($current_page_id, $accessParts, true)) {
                require_once __DIR__ . '/includes/kdms_log.php';
                kdms_log('WARNING', 'KDMS page ACL denied', ['page_id' => $current_page_id]);
                if (defined('KDMS_AUTH_RESPONSE_JSON') && KDMS_AUTH_RESPONSE_JSON === true) {
                    if (! headers_sent()) {
                        header('Content-Type: application/json; charset=utf-8');
                        header('HTTP/1.1 403 Forbidden');
                    }
                    echo '{"ok":false,"error":"forbidden"}';
                    exit;
                }
                echo '<b>YOU DON\'T HAVE ACCESS TO THIS PAGE!!</b>';
                exit;
            }
        }
    }
}

if ($debug) {
    echo '<br> result : ';
    var_dump($result);
}

if (! $result) {
    require_once __DIR__ . '/includes/kdms_log.php';
    kdms_log('NOTICE', 'KDMS authentication or session check failed');

    if (defined('KDMS_AUTH_RESPONSE_JSON') && KDMS_AUTH_RESPONSE_JSON === true) {
        if (! headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('HTTP/1.1 401 Unauthorized');
        }
        echo '{"ok":false,"error":"unauthenticated"}';

        exit;
    }

    $url = $config_data['webroot'] . 'UI/login.php';

    header('Location: ' . $url);

    exit;
}
