<?php
require_once '../CacheManifest.php';

$manifest = new CacheManifest();
$manifest	->withDirectories(array('./', './images/'))
			->skippingFiles(array("update_manifest.php"))
			->create();