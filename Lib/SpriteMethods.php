<?php
App::uses('Image', 'SassCompiler.Lib');
App::uses('GdEngine', 'SassCompiler.Lib/Engine');

trait SpriteMethods {

	//private $__ident = '/(?-mix:-?(?-mix:[_a-zA-Z]|(?-mix:[\x{80}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}])|(?-mix:(?-mix:\\(?-mix:[0-9a-fA-F]){1,6}[ \t\r\n\f]?)|\\[ -~\x{80}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]))(?-mix:[a-zA-Z0-9_-]|(?-mix:[\x{80}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}])|(?-mix:(?-mix:\\(?-mix:[0-9a-fA-F]){1,6}[ \t\r\n\f]?)|\\[ -~\x{80}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]))*)/';
	//
	private $spriteVersion = "2";

	public function validate() {
		$names = $this->spriteNames();

		foreach ($names as $name) {
			if (false && !preg_match($this->__ident, $name)) {
				throw new CakeException("{$name} must be a legal css identifier");
			}
		}
	}

	public function computeImageMetadata() {
		$this->width = 0;

		$this->__initImages();
		$this->computeImagePositions();
		$this->__initEngine();
	}

	private function __initImages() {
		$this->images = array();

		foreach ($this->imageNames as $relativeImage) {
			$this->images[] = new Image($relativeImage);
		}
	}

	private function __initEngine() {
		$this->engine = new GdEngine();
		$this->engine->width = $this->width;
		$this->engine->height = $this->height;
		$this->engine->images = $this->images;
	}

/**
 * Does this sprite need to be generated
 *
 * @return boolean
 */
	private function __generationRequired() {
		return !file_exists($this->__filename());
	}

	private function __filename() {
		return WWW_ROOT . Configure::read('App.imageBaseUrl') . $this->path . "-s" . $this->uniquenessHash() . ".png";
	}

	public function uniquenessHash() {
		if (!isset($this->uniquenessHash)) {
			$sum = $this->spriteVersion . $this->path .	$this->__layout;

			foreach ($this->images as $image) {
				$sum .= $image->relativeFile;
				$sum .= $image->height();
				$sum .= $image->width();
				$sum .= $image->repeat;
				$sum .= $image->spacing;
				$sum .= $image->position;
				$sum .= $image->digest();
			}

			return md5($sum);
		}

		return $this->uniquenessHash;
	}

	public function generate() {
		if ($this->__generationRequired()) {
			//@Todo: Cleanup function

			$this->engine->constructSprite();
			$this->__save();
		}
	}

	private function __save() {
		$this->engine->save($this->__filename());
	}
}