<?php

class SassNumber {

	public $value;

	public $units;

	public function __construct($value, $units = null) {
		$this->value = $value;
		$this->units = $units;
	}

	public function __toString() {
		return $this->value . $this->units;
	}
}