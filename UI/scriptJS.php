<script src="../assets/js/core/jquery.min.js" type="text/javascript"></script>
<?php
$apiBaseUrl = '';
$serviceKey = '';
if (isset($config_data) && is_array($config_data)) {
    $apiBaseUrl = isset($config_data['api_dir']) ? (string) $config_data['api_dir'] : '';
    $serviceKey = isset($config_data['service_key']) ? (string) $config_data['service_key'] : '';
}
?>
<script>
window.KDMS_API_BASE_URL = <?php echo json_encode($apiBaseUrl, JSON_UNESCAPED_SLASHES); ?>;
window.KDMS_SERVICE_KEY = <?php echo json_encode($serviceKey, JSON_UNESCAPED_SLASHES); ?>;

window.kdmsApiUrl = function (path) {
    const base = (window.KDMS_API_BASE_URL || '').replace(/\/+$/, '');
    const clean = String(path || '').replace(/^\/+/, '');
    return base ? `${base}/${clean}` : `../api/${clean}`;
};

if (window.jQuery) {
    window.jQuery.ajaxPrefilter(function (options) {
        const u = String(options.url || '');
        if (u.startsWith('../api/')) {
            options.url = window.kdmsApiUrl(u.replace('../api/', ''));
        }
        if (window.KDMS_SERVICE_KEY) {
            options.headers = options.headers || {};
            if (!options.headers['X-KDMS-SERVICE-KEY']) {
                options.headers['X-KDMS-SERVICE-KEY'] = window.KDMS_SERVICE_KEY;
            }
        }
    });
}
</script>
<script src="../assets/js/core/popper.min.js" type="text/javascript"></script>
<script src="../assets/js/core/bootstrap-material-design.min.js" type="text/javascript"></script>
<script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
<!-- Chartist JS -->
<script src="../assets/js/plugins/chartist.min.js"></script>
<!--  Notifications Plugin    -->
<script src="../assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="../assets/js/material-dashboard.min.js?v=2.1.0" type="text/javascript"></script>
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="../assets/demo/demo.js"></script>
