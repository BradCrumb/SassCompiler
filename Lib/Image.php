<?php

class Image {

	public function __construct($relativeImage) {
		$this->top = 0;
		$this->left = 0;

		$this->relativeFile = $relativeImage;
	}

	public function width() {
		return $this->dimensions()[0];
	}

	public function height() {
		return $this->dimensions()[1];
	}

	public function dimensions() {
		if (isset($this->dimensions)) {
			return $this->dimensions;
		}
		return $this->dimensions = getimagesize(WWW_ROOT . Configure::read('App.imageBaseUrl') . $this->relativeFile);
	}
}