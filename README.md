# How-to
* Create an empty text file, name it "cache.manifest", and give it the appropriate permissions.
* Require "CacheManifest.php" and run the code below from the base URL of your site (so the paths are correct).
* Add a reference to the file in the html tag of each document, e.g. <code>html manifest="cache.manifest"</code>.
* Make sure the server delivers the file with the mime-type "text/cache-manifest".

# Simple Usage
	$manifest = new CacheManifest();
	$manifest->create(); // creates a file named cache.manifest and includes common web file types

# Extended Usage
	$manifest = new CacheManifest();
	$manifest	->toFile("mycache.manifest")
				->withDirectories(array('./', './images/', './stylesheets/'))
				->withFileTypes(array('jpg', 'gif', 'png', 'css', 'js'))
				->withNetworkFiles(array('myservice.php'))
				->withFallbacks(array("/ offline.html"))
				->skippingFiles(array("robots.txt"))
				->create();