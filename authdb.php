<?php
    $dbhost = "athena.nitc.ac.in";
    $dbusername = "rna";
    $dbpassword = "rna";
    $dbname = "rna";

    $connection = mysql_connect($dbhost, $dbusername, $dbpassword) or die('Could not connect');
    $db = mysql_select_db($dbname);
?>
