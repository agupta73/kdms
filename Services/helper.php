<?php
$debug = false;

$config_data=include("../site_config.php");

if($debug){ echo "from service API: "; var_dump($config_data); var_dump($config_data['event_id']) ; echo " till here!!";  die;}
echo serialize($config_data);
die;
?>
