<?php
App::uses('Image', 'SassCompiler.Lib');

trait SpriteMethods {

	//private $__ident = '/(?-mix:-?(?-mix:[_a-zA-Z]|(?-mix:[\x{80}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}])|(?-mix:(?-mix:\\(?-mix:[0-9a-fA-F]){1,6}[ \t\r\n\f]?)|\\[ -~\x{80}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]))(?-mix:[a-zA-Z0-9_-]|(?-mix:[\x{80}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}])|(?-mix:(?-mix:\\(?-mix:[0-9a-fA-F]){1,6}[ \t\r\n\f]?)|\\[ -~\x{80}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]))*)/';

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
	}

	private function __initImages() {
		$this->images = array();

		foreach ($this->imageNames as $relativeImage) {
			$this->images[] = new Image($relativeImage);
		}
	}
}