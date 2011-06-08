<?php
/**
 * Generates a cache manifest file for using a site offline.
 *		
 * 		● Create an empty manifest file and give it the appropriate permissions.
 *		● Run this script from the base URL of your site so the paths are correct.
 *		● Add a reference to the file in the html tag of each document, e.g. <html manifest="cache.manifest">.
 *		● Make sure the server delivers the file with the mime-type "text/cache-manifest".
 *
 * Simple usage:
 *		$manifest = new CacheManifest();
 * 		$manifest->create(); // creates a file named cache.manifest and includes common web file types
 * 
 * Full usage:
 *		$manifest = new CacheManifest();
 * 		$manifest	->toFile("mycache.manifest")
 *					->withDirectories(array('./', './images/', './stylesheets/'))
 *					->withFileTypes(array('jpg', 'gif', 'png', 'css', 'js'))
 *					->withNetworkFiles(array('myservice.php'))
 *					->withFallbacks(array("/ offline.html"))
 *					->skippingFiles(array("robots.txt"))
 *					->create();
 *
 * TODO Create file if it doesn't exist
 *
 * @author Nicolas Schudel
 * @version 0.8
 * @copyright Copyright (c) 2011 Nicolas Schudel
 * @license BSD New
 * @see http://www.w3.org/TR/offline-webapps/
 * @see http://www.html5laboratory.com/working-offline.php
 */
class CacheManifest {
	protected $manifestFile = "cache.manifest";
	protected $directoryPaths = array("./");
	protected $fileTypes = array("css", "flv", "gif", "htm", "html", "ico", "jpeg", "jpg", "js", "mp4", "php", "png", "swf", "svg", "webm");
	protected $fallbacks;
	protected $networkFiles;
	protected $skipFiles = array();

	protected function reducePath($s) {
		if(substr_compare($s, "./", 0, 3)) {
			$s = substr($s, 2);
		}
		return $s;
	}

	protected function scanDirectoriesForFiles($paths, $fileTypes, $skipFiles) {
		$cacheList = array();
		foreach($paths as $path) {
			$files = scandir($path);
			foreach($files as $file) {
				// If the file is not in the skipfiles array and the extension matches one of the filetypes.
				if(!in_array($file, $skipFiles) && preg_match('/\.(' . join("|", $fileTypes) . ')$/i', $file)) {
					$cacheList[] = $path . $file;
				}
			}
		}
		return $cacheList;
	}

	protected function generateContent($cache, $networkFiles, $fallbacks) {
		// Write CACHE
		$content = "CACHE MANIFEST\n";
		if(is_array($cache) && count($cache) > 0) {
			$content .= "\nCACHE:\n";
			foreach($cache as $s) {
				$content .= $this->reducePath($s) . "\n";
			}
		}
		if(is_array($networkFiles) && count($networkFiles) > 0) {
			$content .= "\NETWORK:\n";
			foreach($networkFiles as $s) {
				$content .= $this->reducePath($s) . "\n";
			}
		}
		if(is_array($fallbacks) && count($fallbacks) > 0) {
			$content .= "\nFALLBACK:\n";
			foreach($fallbacks as $s) {
				$content .= $s . "\n";
			}
		}
		// Add version, in order to force the browser to reload the manifest file
		$content .= "\n# version " . gmdate("Ymdhm");
		return $content;
	}

	protected function writeFile($manifestFile, $content) {
		$writeTo = fopen($manifestFile, 'w') or die('Could not open the file "' . $manifestFile . '". Make sure that the file permissions are correct.');
		fwrite($writeTo, $content);
		return  fclose($writeTo);
	}

	public function toFile($manifestFile) {
		$this->manifestFile = $manifestFile;
		return $this;
	}

	public function withDirectories($directoryPaths) {
		$this->directoryPathsArray = $directoryPaths;
		return $this;
	}

	public function withFileTypes($fileTypes) {
		$this->fileTypes = $fileTypes;
		return $this;
	}
	
	public function withNetworkFiles($networkFiles) {
		$this->networkFiles = $networkFiles;
		return $this;
	}

	public function withFallbacks($fallbacks) {
		$this->fallbacks = $fallbacks;
		return $this;
	}
	
	public function skippingFiles($skipFiles) {
		$this->skipFiles = $skipFiles;
		return $this;
	}

	public function create() {
		$files = $this->scanDirectoriesForFiles($this->directoryPathsArray, $this->fileTypes, $this->skipFiles);
		$content = $this->generateContent($files, $this->networkFiles, $this->fallbacks);
		return $this->writeFile($this->manifestFile, $content);
	}

}
