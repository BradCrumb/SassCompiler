<?php
App::uses('Engine', 'SassCompiler.Lib/Engine');

class GdEngine extends Engine {

	public function constructSprite() {
		$this->canvas = imagecreatetruecolor($this->width, $this->height);
		imagesavealpha($this->canvas, true);
		$color = imagecolorallocatealpha($this->canvas, 0, 0, 0, 127);
		imagefill($this->canvas, 0, 0, $color);

		foreach ($this->images as $image) {
			$inputPng = imagecreatefrompng($image->file());

			imagecopyresampled($this->canvas, $inputPng, $image->left, $image->top, 0, 0, $image->width(), $image->height(), $image->width(), $image->height());
		}
	}

	public function save($filename) {
		if (empty($this->canvas)) {
			$this->constructSprite();
		}

		return imagepng($this->canvas, $filename);
	}
}