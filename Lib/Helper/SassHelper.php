<?php
/**
 * SassHelper
 * ===
 *
 * Abstract class for Sass Helpers
 *
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2014 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class SassHelper {

/**
 * Constructor
 */
	public function __construct() {
		$null = null;
		$View = new View($null);
		$this->Helper = new Helper($View);
	}

	protected function _getFullAssetPath($path) {
		$filepath = preg_replace(
			'/^' . preg_quote($this->Helper->request->webroot, '/') . '/',
			'',
			urldecode($path)
		);
		$webrootPath = WWW_ROOT . str_replace('/', DS, $filepath);
		if (file_exists($webrootPath)) {
			//@codingStandardsIgnoreStart
			return $webrootPath;
			//@codingStandardsIgnoreEnd
		}
		$segments = explode('/', ltrim($filepath, '/'));
		if ($segments[0] === 'theme') {
			$theme = $segments[1];
			unset($segments[0], $segments[1]);
			$themePath = App::themePath($theme) . 'webroot' . DS . implode(DS, $segments);
			//@codingStandardsIgnoreStart
			return $themePath;
			//@codingStandardsIgnoreEnd
		} else {
			$plugin = Inflector::camelize($segments[0]);
			if (CakePlugin::loaded($plugin)) {
				unset($segments[0]);
				$pluginPath = CakePlugin::path($plugin) . 'webroot' . DS . implode(DS, $segments);
				//@codingStandardsIgnoreStart
				return $pluginPath;
				//@codingStandardsIgnoreEnd
			}
		}

		return false;
	}

/**
 * Get full path to image
 *
 * @param  String $image Image path
 *
 * @return String Full image path
 */
	protected function __getFullImagePath($image) {
		return $this->_getFullAssetPath(Configure::read('App.imageBaseUrl') . $image);
	}

	public function implementedFunctions() {
		return array();
	}

	public function getHelperFunctions() {
		$functions = array();

		foreach ($this->implementedFunctions() as $name => $method) {
			$functions[] = array(
				'name' => $name,
				'call' => function($args, $compiler) use($method) {
					return $this->{$method}($args, $compiler);
				});
		}

		return $functions;
	}
}