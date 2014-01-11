<?php
App::uses('SassHelper', 'SassCompiler.Lib/Helper');
App::uses('View', 'View');
App::uses('Helper', 'View');

/**
 * CompassUrlHelper
 * ===
 *
 * CakePHP Implementation of the Compass URL Helper
 *
 * @see http://compass-style.org/reference/compass/helpers/urls/
 *
 * These url helpers isolate your stylesheets from environmental differences.
 * They allow you to write the same stylesheets and use them locally without a web server,
 * and then change them to be using asset hosts in production.
 *
 * They might also insulate you against some code reorganization changes.
 *
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2014 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class CompassUrlHelper extends SassHelper {

/**
 * Generates a path to an asset found relative to the project's css directory.
 * Passing a true value as the second argument will cause pronly the path to be returned instead of a url() function
 *
 * @return String Url to the CSS file
 */
	public function stylesheetUrl() {
		return array(
			'name' => 'stylesheet-url',
			'call' => function($args) {
				$path = $args[0][2][0];
				$onlyPath = isset($args[1]) ? $args[1] : false;

				if (strpos($path, '//') !== false) {
					$url = $path;
				} else {
					$url = $this->Helper->assetUrl($path, array('pathPrefix' => Configure::read('App.cssBaseUrl'), 'ext' => '.css'));

					if (Configure::read('Asset.filter.css')) {
						$pos = strpos($url, Configure::read('App.cssBaseUrl'));
						if ($pos !== false) {
							$url = substr($url, 0, $pos) . 'ccss/' . substr($url, $pos + strlen(Configure::read('App.cssBaseUrl')));
						}
					}
				}

				if ($onlyPath) {
					return $path;
				}

				return "url('" . $url . "')";
			}
		);
	}

/**
 * Generates a path to an asset found relative to the project's font directory.
 * Passing a true value as the second argument will cause pronly the path to be returned instead of a url() function
 *
 * @return String Url to the font file
 */
	public function fontUrl() {
		return array(
			'name' => 'font-url',
			'call' => function($args) {
				$path = $args[0][2][0];
				$onlyPath = isset($args[1]) ? $args[1] : false;

				$path = $this->Helper->assetUrl($this->Helper->webroot('fonts' . DS . $path));

				if ($onlyPath) {
					return $path;
				}

				return "url('" . $path . "')";
			}
		);
	}

/**
 * Generates a path to an asset found relative to the project's img directory.
 * Passing a true value as the second argument will cause pronly the path to be returned instead of a url() function
 *
 * @return String Url to the image file
 */
	public function imageUrl() {
		return array(
			'name' => 'image-url',
			'call' => function($args) {
				$path = $args[0][2][0];
				$onlyPath = isset($args[1]) ? $args[1] : false;
				//$cacheBuster = $args[2];

				$path = $this->Helper->assetUrl(Configure::read('App.imageBaseUrl') . $path);

				if ($onlyPath) {
					return $path;
				}

				return "url('" . $this->Helper->assetUrl($path) . "')";
			}
		);
	}
}