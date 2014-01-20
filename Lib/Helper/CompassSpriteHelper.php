<?php
App::uses('SassHelper', 'SassCompiler.Lib/Helper');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('SpriteMap', 'SassCompiler.Lib');

/**
 * CompassSpriteHelper
 * ===
 *
 * CakePHP Implementation of the Compass Sprite Helper
 *
 * These helpers make it easier to build and to work with css sprites.
 *
 * While it is allowed to use these directly, to do so is considered "advanced usage".
 * It is recommended that you instead use the css sprite mixins that are designed to work with these functions.
 *
 * See the Spriting Tutorial for more information. (http://compass-style.org/help/tutorials/spriting/)
 *
 * @see http://compass-style.org/reference/compass/helpers/sprites/
 *
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2014 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class CompassSpriteHelper extends SassHelper {

/**
 * Generates a css sprite map from the files matching the glob pattern.
 * Uses the keyword-style arguments passed in to control the placement.
 *
 * Only PNG files can be made into css sprites at this time.
 *
 * @return [type] [description]
 */
	public function spriteMap() {
		return array(
			'name' => 'sprite-map',
			'call' => function($args) {
				return SpriteMap::fromUri($args[0][2][0]);
			}
		);
	}

/**
 *
 * Returns the image and background position for use in a single shorthand property:
 *
 * @example	$icons: sprite-map("icons/*.png"); // contains icons/new.png among others.
 *          background: sprite($icons, new) no-repeat;
 *
 * 			Becomes:
 *
 * 			background: url('/images/icons.png?12345678') 0 -24px no-repeat;
 *
 * @return [type] [description]
 */
	public function sprite() {
		return array(
			'name' => 'sprite',
			'call' => function($args) {
				$map = $args[0][1];
				$map->name = 'handig';

				return $map;
			}
		);
	}

/**
 * Returns the name of a css sprite map The name is derived from the folder than contains the css sprites.
 *
 * @return [type] [description]
 */
	public function spriteMapName() {
		return array(
			'name' => 'sprite-map-name',
			'call' => function($args) {
				return null;
			}
		);
	}

/**
 * Returns the relative path (from the images directory) to the original file used when construction the sprite.
 * This is suitable for passing to the image-width and image-height helpers.
 *
 * @return [type] [description]
 */
	public function spriteFile() {
		return array(
			'name' => 'sprite-file',
			'call' => function($args) {
				return null;
			}
		);
	}

/**
 * Returns a url to the sprite image.
 *
 * @return [type] [description]
 */
	public function spriteUrl() {
		return array(
			'name' => 'sprite-url',
			'call' => function($args) {
				return null;
			}
		);
	}

	public function spritePosition() {
		return array(
			'name' => 'sprite-position',
			'call' => function($args) {
				return null;
			}
		);
	}
}