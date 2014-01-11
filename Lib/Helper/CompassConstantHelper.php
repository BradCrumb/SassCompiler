<?php
App::uses('SassHelper', 'SassCompiler.Lib/Helper');
App::uses('View', 'View');
App::uses('Helper', 'View');

/**
 * CompassConstantHelper
 * ===
 *
 * CakePHP Implementation of the Compass Constant Helper
 *
 * These helpers manipulate CSS Constants.
 *
 * @see http://compass-style.org/reference/compass/helpers/constants/
 *
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2014 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class CompassConstantHelper extends SassHelper {

/**
 * Returns the opposition position for the position given.
 *
 * @example opposite-position(left)         => right
 *          opposite-position(top)          => bottom
 *          opposite-position(center)       => center
 *          opposite-position(top left)     => bottom right
 *          opposite-position(center right) => center left
 *
 * @return String Width of the image in pixels (px)
 */
	public function oppositePosition() {
		return array(
			'name' => 'opposite-position',
			'call' => function($args) {
				//Only one position
				if ($args[0][0] == 'string') {
					$position = $args[0][2][0];

					$positions = explode(' ', $position);
				} elseif ($args[0][0] == 'list') { //multiple positions, for example "top left"
					$positions = Hash::extract($args[0][2], '{n}.1');
				}

				$opposite = '';
				foreach ($positions as $position) {
					$opposite .= ' ' . $this->__oppositeOf($position);
				}

				return trim($opposite);
			}
		);
	}

/**
 * Get the opposite of a CSS direction
 *
 * @param  String $position CSS position (left, right, bottom, top, center)
 *
 * @return String Opposite of the given direction
 */
	private function __oppositeOf($position) {
		switch($position) {
			case 'left':
				return 'right';
			case 'right':
				return 'left';
			case 'top':
				return 'bottom';
			case 'bottom':
				return 'top';
			case 'center':
				return 'center';
		}
	}
}