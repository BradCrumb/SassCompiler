<?php

trait LayoutMethods {

	private $__layout = 'vertical';

	public function computeImagePositions() {
		switch ($this->__layout) {
			case 'smart':
				break;
			case 'diagonal':
				break;
			case 'horizontal':
				break;
			default:
				$this->width = $this->__widthForVerticalLayout();
				$this->__calulateVerticalPositions();
				$this->height = $this->__heightForVerticalLayout();
				//@todo: Repeat-X
				break;
		}
	}

	private function __widthForVerticalLayout() {
		$max = 0;

		foreach ($this->images as $image) {
			if ($image->width() > $max) {
				$max = $image->width();
			}
		}

		return $max;
	}

	private function __heightForVerticalLayout() {
		$last = end($this->images);

		return $last->top + $last->height();
	}

/**
 * @todo Spacing and Left position
 *
 * @return [type] [description]
 */
	private function __calulateVerticalPositions() {
		$amount = count($this->images);

		for ($i = 0;$i < $amount;$i++) {
			//$this->images[$i].left = $this->width - $this->images[$i]->width();
			$this->images[$i]->left = 0;

			if ($i == 0) {
				continue;
			}

			$lastImage = $this->images[$i - 1];
			$this->images[$i]->top = $lastImage->top + $lastImage->height();
		}
	}
}