<?php
require_once("pdo_mysql.php");
$result = pdo_select("select unix_timestamp(max(dttm)) as dttm from bids");
echo $result[0]['dttm'];
?>