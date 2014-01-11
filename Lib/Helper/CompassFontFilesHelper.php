<?php
App::uses('SassHelper', 'SassCompiler.Lib/Helper');
App::uses('View', 'View');
App::uses('Helper', 'View');

/**
 * CompassFontFilesHelper
 * ===
 *
 * CakePHP Implementation of the Compass Font Files Helper
 *
 * @see http://compass-style.org/reference/compass/helpers/font-files/
 *
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2014 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class CompassFontFilesHelper extends SassHelper {

/**
 * All valid font types
 *
 * @var array
 */
	private $__fontTypes = array(
		'woff'		=> 'woff',
		'otf'		=> 'opentype',
		'opentype'	=> 'opentype',
		'ttf'		=> 'truetype',
		'truetype'	=> 'truetype',
		'svg'		=> 'svg',
		'eot'		=> 'embedded-opentype'
	);

/**
 * The font-files function takes a list of arguments containing the path to each font files relative to your project's font directory.
 *
 * This helper is used with the font-face mixin and is what makes it possible to pass any number of font files.
 *
 * @throws CakeException when font type can't be found
 *
 * @todo Better way to get the font url, better to use the CompassUrlHelper
 */
	public function fontFiles() {
		return array(
			'name' => 'font-files',
			'call' => function($args) {
				$fonts = Hash::extract($args, '{n}.2.0');
				$files = array();

				foreach ($fonts as $font) {
					$dotExplode = explode('.', $font);
					$ext = end($dotExplode);

					if (!isset($this->__fontTypes[$ext])) {
						throw new CakeException('Could not determine font type for ' . $font);
					}

					$fontUrl = $this->Helper->assetUrl($this->Helper->webroot('fonts' . DS . $font));

					$files[] = "url('{$fontUrl}') format('{$this->__fontTypes[$ext]}')";
				}

				return implode(', ', $files);
			}
		);
	}
}