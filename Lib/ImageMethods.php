<?php

trait ImageMethods {

/**
 * Return an array of image names that make up this sprite
 *
 * @return [type] [description]
 */
	public function spriteNames() {
		$names = array();

		foreach ($this->imageNames as $name) {
			$names[] = str_replace('.png', '', basename($name));
		}

		return $names;
	}

/**
 * Fetches the Sprite::Image object for the supplied name
 *
 * @param  [type] $name [description]
 * @return [type]       [description]
 */
	public function imageFor($name) {
		foreach ($this->images as $image) {
			if ($image->name() == $name) {
				return $image;
			}
		}
	}
}