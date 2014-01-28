<?php
App::uses('SassHelper', 'SassCompiler.Lib/Helper');
App::uses('CompassUrlHelper', 'SassCompiler.Lib/Helper');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('SpriteMap', 'SassCompiler.Lib');
App::uses('SassNumber', 'SassCompiler.Lib');

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

	public function implementedFunctions() {
		return array(
			'sprite-map'		=> 'spriteMap',
			'sprite'			=> 'sprite',
			'sprite-map-name'	=> 'spriteMapName',
			'sprite-file'		=> 'spriteFile',
			'sprite-url'		=> 'spriteUrl',
			'sprite-position'	=> 'spritePosition'
		);
	}

/**
 * Generates a css sprite map from the files matching the glob pattern.
 * Uses the keyword-style arguments passed in to control the placement.
 *
 * Only PNG files can be made into css sprites at this time.
 *
 * @return SpriteMap Generated spritemap
 */
	public function spriteMap($args) {
		return SpriteMap::fromUri($this, $args[0][2][0]);
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
 * @throws CakeException When sprite is not given
 *
 * @return String Generated background image with position
 */
	public function sprite($args) {
		$map = $args[0][1];
		$sprite = $this->__convertSpriteName(isset($args[1][1]) ? $args[1][1] : null);
		$offsetX = isset($args[2]) ? $args[2] : new SassNumber(0);
		$offsetY = isset($args[3]) ? $args[3] : new SassNumber(0);

		$this->__verifyMap($map, 'sprite');

		if (empty($sprite) || !is_string($sprite)) {
			throw new CakeException("(The second argument to sprite-position must be a sprite name. See http://beta.compass-style.org/help/tutorials/spriting/ for more information.)");
		}

		$url = $this->spriteUrl($map);
		$position = $this->spritePosition(array(
			array(1 => $map),
			array(1 => $sprite),
			$offsetX,
			$offsetY
		));

		return $url . ' ' . $position;
	}

/**
 * Returns the name of a css sprite map The name is derived from the folder than contains the css sprites.
 *
 * @return String Sprite map name
 */
	public function spriteMapName($args) {
		if ($args instanceof SpriteMap) {
			$map = $args;
		} else {
			$map = $args[0][1];
		}

		$this->__verifyMap($map, "sprite-map-name");

		return '"' . $map->name . '"';
	}

/**
 * Returns the relative path (from the images directory) to the original file used when construction the sprite.
 * This is suitable for passing to the image-width and image-height helpers.
 *
 * @return String Original image path
 */
	public function spriteFile() {
		$map = $args[0][1];
		$sprite = $this->__convertSpriteName(isset($args[1][1]) ? $args[1][1] : null);

		$this->__verifyMap($map, 'sprite-file');
		$this->__verifySprite($sprite);

		if ($image = $map->imageFor($sprite)) {
			return $image;
		}

		$this->__missingImage($map, $sprite);
	}

/**
 * Returns a url to the sprite image.
 *
 * @return String Sprite image url
 */
	public function spriteUrl($args) {
		if ($args instanceof SpriteMap) {
			$map = $args;
		}

		$this->__verifyMap($map, "sprite-url");
		$map->generate();

		$compassUrlHelper = new CompassUrlHelper();
		return $compassUrlHelper->generatedImageUrl($map->path . '-s' . $map->uniquenessHash() . '.png');
	}

/**
 * Returns the position for the original image in the sprite.
 *
 * This is suitable for use as a value to background-position:
 *
 * $icons: sprite-map("icons/*.png");
 * background-position: sprite-position($icons, new);
 *
 * Might generate something like:
 *
 * background-position: 0 -34px;
 *
 * You can adjust the background relative to this position by passing values for
 * `$offset-x` and `$offset-y`:
 *
 * $icons: sprite-map("icons/*.png");
 * background-position: sprite-position($icons, new, 3px, -2px);
 *
 * Would change the above output to:
 *
 * background-position: 3px -36px;
 *
 * @throws CakeException When sprite is not given
 *
 * @return [type]       [description]
 */
	public function spritePosition($args) {
		$map = $args[0][1];
		$sprite = $this->__convertSpriteName(isset($args[1][1]) ? $args[1][1] : null);
		$offsetX = isset($args[2]) ? $args[2] : new SassNumber(0);
		$offsetY = isset($args[3]) ? $args[3] : new SassNumber(0);

		$this->__verifyMap($map, 'sprite-position');

		if (empty($sprite) || !is_string($sprite)) {
			throw new CakeException("(The second argument to sprite-position must be a sprite name. See http://beta.compass-style.org/help/tutorials/spriting/ for more information.)");
		}

		$image = $map->imageFor($sprite);

		if (!$image) {
			$this->__missingImage($map, $sprite);
		}

		if ($offsetX->units == '%') {//Percentage

		} else {
			$x = $offsetX->value - $image->left;
			$x = new SassNumber($x, 'px');
		}

		$y = $offsetY->value - $image->top;
		$y = new SassNumber($y, 'px');

		return $x . ' ' . $y;
	}

	private function __verifyMap($map, $error = "sprite") {
		if (!$map instanceof SpriteMap) {
			$this->__missingSprite($error);
		}
	}

	private function __verifySprite($sprite) {
		if (!is_string($sprite)) {
			throw new CakeException("The second argument to sprite() must be a sprite name. See http://beta.compass-style.org/help/tutorials/spriting/ for more information.");
		}
	}

	private function __missingSprite($functionName) {
		throw new CakeException("The first argument to {$functionName}() must be a sprite map. See http://beta.compass-style.org/help/tutorials/spriting/ for more information.");
	}

	private function __missingImage($map, $sprite) {
		$spriteNames = implode(', ', $map->spriteNames());
		throw new CakeException("No sprite called {$sprite} found in sprite map {$map->path}/{$map->name}. Did you mean one of: {$spriteNames}");
	}

	private function __convertSpriteName($sprite) {
		switch($sprite) {
			default:
				return $sprite;
		}
	}
}