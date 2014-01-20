<?php
App::uses('SpriteImporter', 'SassCompiler.Lib');
App::uses('SpriteMethods', 'SassCompiler.Lib');
App::uses('ImageMethods', 'SassCompiler.Lib');
App::uses('LayoutMethods', 'SassCompiler.Lib');

class SpriteMap {

	use ImageMethods;
	use LayoutMethods;
	use SpriteMethods;

	public $imageNames;

	public $path;

	public $name;

	public function __construct($sprites, $path, $name) {
		$this->imageNames = $sprites;
		$this->path = $path;
		$this->name = $name;

		$this->validate();
		$this->computeImageMetadata();
	}

	public static function fromUri($uri) {
		list($path, $name) = SpriteImporter::pathAndName($uri);
		$files = SpriteImporter::files($uri);

		$sprites = array();

		foreach ($files as $file) {
			$sprites[] = self::__relativeName($file);
		}

		return new self($sprites, $path, $name);
	}

	private static function __relativeName($sprite) {
		$path = WWW_ROOT . Configure::read('App.imageBaseUrl');

		return str_replace($path, '', $sprite);
	}

	public function __toString() {
		return $this->name;
	}
}