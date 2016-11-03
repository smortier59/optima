<?php
// Purge les 
include_once dirname(__FILE__)."/../global.inc.php";
//dynamo::tableSchemeMaintenance("token");
$dir = __TEMP_PATH__."/filecached/";
foreach (glob($dir."*") as $file) {
	if (filemtime($file) < time() - 86400) {
	    unlink($file);
    }
}
