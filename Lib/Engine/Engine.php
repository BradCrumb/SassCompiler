<?php

class Engine {

	public $width = null;

	public $height = null;

	public $images = null;

	public $canvas = null;

	public function __construct($width = null, $height = null, $images = null) {
		$this->width = $width;
		$this->height = $height;
		$this->images = $images;
	}

	public function constructSprite() {
		throw new CakeException("You must implement constructSprite");
	}

	public function save($filename) {
		throw new CakeException("You must implement save(filename)");
	}
}