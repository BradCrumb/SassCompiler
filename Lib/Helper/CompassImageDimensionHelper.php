<?php
App::uses('SassHelper', 'SassCompiler.Lib/Helper');
App::uses('View', 'View');
App::uses('Helper', 'View');

/**
 * CompassImageDimensionHelper
 * ===
 *
 * CakePHP Implementation of the Compass Image Dimension Helper
 *
 * @see http://compass-style.org/reference/compass/helpers/image-dimensions/
 *
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2014 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class CompassImageDimensionHelper extends SassHelper {

/**
 * Returns the width of the image found at the path supplied by $image relative to your project's images directory.
 *
 * @return String Width of the image in pixels (px)
 */
	public function imageWidth() {
		return array(
			'name' => 'image-width',
			'call' => function($args) {
				$image = $args[0][2][0];

				return getimagesize($this->__getFullImagePath($image))[0] . 'px';
			}
		);
	}

/**
 * Returns the height of the image found at the path supplied by $image relative to your project's images directory.
 *
 * @return String Height of the image in pixels (px)
 */
	public function imageHeight() {
		return array(
			'name' => 'image-height',
			'call' => function($args) {
				$image = $args[0][2][0];

				return getimagesize($this->__getFullImagePath($image))[1] . 'px';
			}
		);
	}
}