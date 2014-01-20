<?php

trait ImageMethods {

	public function spriteNames() {
		$names = array();

		foreach ($this->imageNames as $name) {
			$names[] = str_replace('.png', '', basename($name));
		}

		return $names;
	}
}