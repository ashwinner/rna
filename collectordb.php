<?php
    $dbhost = "localhost";
    $dbusername = "collectordb";
    $dbpassword = "collectordb";
    $dbname = "collectordb";

    $connection = mysql_connect($dbhost, $dbusername, $dbpassword) or die('Could not connect');
    $db = mysql_select_db($dbname);
?>
