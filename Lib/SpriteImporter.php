<?php
class SpriteImporter {

	private static $__spriteImporterRegex = '_((.+/)?([^\*.]+))/(.+?)\.png_';

	public static function pathAndName($uri) {
		if (preg_match_all(self::$__spriteImporterRegex, $uri, $matches)) {
			return array($matches[1], $matches[3]);
		}

		throw new CakeException("invalid sprite path");
	}

	public static function files($uri) {
		$path = WWW_ROOT . Configure::read('App.imageBaseUrl');
		$files = glob($path . $uri);

		if (!empty($files)) {
			return $files;
		}

		throw new CakeException('No files were found in the load path matching "' . $uri . '". Your current load paths are: ' . $path);
	}
}