<?php
App::uses('SassCompiler', 'SassCompiler.Lib');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('Component', 'Controller');
App::uses('scss.inc', 'SassCompiler.Vendor/scssphp');

/**
 * SassCompiler
 *
 * @author Patrick Langendoen <github-bradcrumb@patricklangendoen.nl>
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2013 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class SassComponent extends Component {

/**
 * Settings for the Sass Compiler Component
 *
 * forceCompiling : enabled by supplied ?forceSassToCompile=true in the url
 * autoRun: run the component even if the debug level = 0
 *
 * @var array
 */
	public $settings = array(
		'sourceFolder'		=> 'sass',						// Where to look for .scss files, (From the APP directory)
		'targetFolder'		=> false,						// Where to put the generated css (From the webroot directory)
		'formatter'			=> 'scss_formatter_compressed',	// PHPSass compatible style (compressed or nested)
		'forceCompiling'	=> false,						// Always recompile
		'autoRun'			=> false,						// Check if compilation is necessary, this ignores the CakePHP Debug setting
		'import_paths'		=> array()						// Array of paths to search for scss files when using @import, path has to be relative to the sourceFolder
	);

/**
 * Controller instance reference
 *
 * @var object
 */
	public $controller;

/**
 * Components used by SassComponent
 *
 * @var array
 */
	public $components = array('RequestHandler', 'Session');

/**
 * Contains the indexed folders consisting of scss files
 *
 * @var array
 */
	protected $_sassFolders;

/**
 * Contains the folders with processed css files
 *
 * @var array
 */
	protected $_cssFolders;

/**
 * Location of the folder where the cache-files should be stored
 *
 * @var array
 */
	protected $_cacheFolder;

/**
 * CacheKey used for the cache file.
 *
 * @var string
 */
	public $cacheKey = 'SassComponent_cache';

/**
 * Duration of the debug kit history cache
 *
 * @var string
 */
	public $cacheDuration = '+4 hours';

/**
 * Status whether component is enabled or disabled
 *
 * @var boolean
 */
	public $enabled = true;

/**
 * Holder for the SassCompiler instance
 *
 * @var SassCompiler
 */
	protected static $_instance;

/**
 * Minimum required PHP version
 *
 * @var string
 */
	protected static $_minVersionPHP = '5.3';

/**
 * Minimum required CakePHP version
 *
 * @var string
 */
	protected static $_minVersionCakePHP = '2.2.0';

/**
 * Minimum required scssc version
 *
 * @var string
 */
	protected static $_minVersionScssc = '0.0.7';

/**
 * Public constructor for the SassComponent
 *
 * @param ComponentCollection $collection
 * @param array               $settings
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->controller = $collection->getController();
		$settings = array_merge($settings, (array)Configure::read('SassCompiler'));

		parent::__construct($collection, array_merge($this->settings, (array)$settings));

		// Don't execute the component if the debuglevel is 0
		// unless compileSass requestparameter is supplied
		// if autoRun is true then ALWAYS run the component
		if (!Configure::read('debug') &&
			!isset($this->controller->request->query['forceSassToCompile']) &&
			false === $this->settings['autoRun']) {
			$this->enabled = false;
			return false;
		}

		if (isset($this->controller->request->query['forceSassToCompile'])) {
			$this->settings['forceCompiling'] = true;
		}

		$this->_checkVersion();

		$this->cacheKey .= $this->Session->read('Config.userAgent');

		$this->_createCacheConfig();

		$this->_setFolders();
	}

/**
 * Checks the versions of PHP, CakePHP and PHPSass
 *
 * @throws CakeException If one of the required versions is not available
 *
 * @return void
 */
	protected function _checkVersion() {
		if (PHP_VERSION < self::$_minVersionPHP) {
			throw new CakeException(__('The SassCompiler plugin requires PHP version %s or higher!', self::$_minVersionPHP));
		}

		if (Configure::version() < self::$_minVersionCakePHP) {
			throw new CakeException(__('The SassCompiler plugin requires CakePHP version %s or higher!', self::$_minVersionCakePHP));
		}

		$scssc = new SassCompiler();

		if ($scssc::$VERSION < self::$_minVersionScssc) {
			throw new CakeException(__('The SassCompiler plugin requires scssc version %s or higher!', self::$_minVersionLessc));
		}

		unset($scssc);
	}

/**
 * Indicated whether cache is enabled
 *
 * @return boolean
 */
	protected function _isCacheEnabled() {
		return Configure::read('Cache.disable') !== true;
	}

/**
 * Create the cache config for this component
 *
 * @return void
 */
	protected function _createCacheConfig() {
		if ($this->_isCacheEnabled()) {
			$cache = array(
				'duration' => $this->cacheDuration,
				'engine' => 'File',
				'path' => CACHE
			);
			if (isset($this->settings['cache'])) {
				$cache = array_merge($cache, $this->settings['cache']);
			}
			Cache::config('SassComponent', $cache);
		}
	}

/**
 * Set the value for a specific key in this component's cache
 *
 * @param string $key
 * @param all $value
 */
	protected function _setCacheKey($key, $value) {
		if ($this->_isCacheEnabled()) {

			$config = Cache::config('SassComponent');
			if (empty($config)) {
				return;
			}

			$sassCache = Cache::read($this->cacheKey, 'SassComponent');
			if (empty($sassCache)) {
				$sassCache = array();
			}

			$sassCache[$key] = $value;

			Cache::write($this->cacheKey, $sassCache, 'SassComponent');
		}
	}

/**
 * Get the value for a specific key in this component's cache
 *
 * @param  string $key
 *
 * @return false|value
 */
	protected function _getCacheKey($key) {
		$result = Cache::read($this->cacheKey, 'SassComponent');
		if (isset($result[$key])) {
			return $result[$key];
		}

		return false;
	}

/**
 * Remove a specific key in this component's cache
 *
 * @param  string $key
 *
 * @return void
 */
	protected function _removeCacheKey($key) {
		if ($this->_isCacheEnabled()) {
			Cache::delete('sass_compiled', 'SassComponent');
		}
	}

/**
 * Set all possible folders
 *
 * @return void
 */
	protected function _setFolders() {
		$this->_cacheFolder = CACHE . __CLASS__;

		$this->_sassFolders['default'] = $this->settings['sourceFolder']?
														APP . $this->settings['sourceFolder']:
														APP . 'sass';

		$this->_cssFolders['default'] = $this->settings['targetFolder']?
														WWW_ROOT . $this->settings['targetFolder']:
														WWW_ROOT . 'css';

		$this->_checkFolders();

		$this->_sassFolders['default'] = new Folder($this->_sassFolders['default']);
		$this->_cssFolders['default'] = new Folder($this->_cssFolders['default']);

		$this->_checkThemeFolders();
		$this->_checkPluginFolders();
	}

/**
 * Check if the sass and cache directories are present.
 *
 * If not create them
 *
 * @return void
 */
	protected function _checkFolders() {
		if (!is_dir($this->_sassFolders['default'])) {
			mkdir($this->_sassFolders['default']);
		}

		if (!is_dir($this->_cacheFolder)) {
			mkdir($this->_cacheFolder);
		}
	}

/**
 * Check all the Theme folders for sass directories
 *
 * @return void
 */
	protected function _checkThemeFolders() {
		$themedDirectory = APP . 'View' . DS . 'Themed';

		$folder = new Folder($themedDirectory);
		list($themes, $files) = $folder->read();

		foreach ($themes as $theme) {
			$sassDir = $themedDirectory . DS . $theme . DS . 'sass';
			$cssDir = $themedDirectory . DS . $theme . DS . 'webroot' . DS . 'css';

			if ($theme != '.svn' && is_dir($sassDir) && is_dir($cssDir)) {
				$this->_sassFolders[$theme] = new Folder($sassDir);
				$this->_cssFolders[$theme] = new Folder($cssDir);
			}
		}
	}

/**
 * Check all the Plugin folders for sass directories
 *
 * @return void
 */
	protected function _checkPluginFolders() {
		$pluginDirectory = APP . 'Plugin';

		$folder = new Folder($pluginDirectory);
		list($plugins, $files) = $folder->read();

		foreach ($plugins as $plugin) {
			$sassDir = $pluginDirectory . DS . $plugin . DS . 'sass';
			$cssDir = $pluginDirectory . DS . $plugin . DS . 'webroot' . DS . 'css';
			if ($plugin != '.svn' && is_dir($sassDir) && is_dir($cssDir)) {
				$this->_sassFolders[$plugin] = new Folder($sassDir);
				$this->_cssFolders[$plugin] = new Folder($cssDir);
			}
		}
	}

/**
 * Before Render
 *
 * Before a page is rendered trigger the compiler
 *
 * @param  Controller $controller The Controller where the component is loaded
 *
 * @return void
 */
	public function beforeRender(Controller $controller) {
		$this->generateCSS();
	}

/**
 * Generate the CSS from all the .scss files we can find
 *
 * @return String[] Generated CSS files
 */
	public function generateCss() {
		$generatedFiles = array();

	/**
 	 * Run the check for the up-to-date compiled .scss files when
 	 *
 	 * - The Cache does not contain an indication of the fact that the check has run
 	 * - Debug mode is set larger than 0 (suggesting development mode)
 	 * - The requested parameter has been set to force the check
 	 * - The component should run always (autorun) despite of the debuglevel
 	 */
		if (($this->_isCacheEnabled && false === $this->_getCacheKey('sass_compiled')) ||
			Configure::read('debug') > 0 ||
			true === $this->settings['autoRun'] ||
			true === $this->settings['forceCompiling']
			) {
			foreach ($this->_sassFolders as $key => $sassFolder) {
				foreach ($sassFolder->find() as $file) {
					$file = new File($file);
					if (($file->ext() == 'sass' || $file->ext() == 'scss') && substr($file->name, 0, 2) !== '._' && substr($file->name, 0, 1) !== '_') {
						$sassFile = $sassFolder->path . DS . $file->name;
						$cssFile = $this->_cssFolders[$key]->path . DS . str_replace(array('.sass','.scss'), '.css', $file->name);

						if ($this->_autoCompileSass($sassFile, $cssFile, $sassFolder->path . DS)) {
							$generatedFiles[] = $cssFile;
						}
					}
				}
			}

			$this->_setCacheKey('sass_compiled', true);
		}

		return $generatedFiles;
	}

/**
 * Compile the .scss files
 *
 * @param  string $inputFile
 * @param  string $outputFile
 *
 * @return boolean
 */
	protected function _autoCompileSass($inputFile, $outputFile) {
		$cacheFile = str_replace(DS, '_', str_replace(APP, null, $outputFile));
		$cacheFile = $this->_cacheFolder . DS . $cacheFile;
		$cacheFile = substr_replace($cacheFile, 'cache', -3);

		$cache = file_exists($cacheFile)?
			unserialize(file_get_contents($cacheFile)):
			$inputFile;

		if (!self::$_instance instanceof SassCompiler) {
			self::$_instance = new SassCompiler();
			self::$_instance->registerHelper('CompassUrl');
			self::$_instance->registerHelper('CompassImageDimension');
			self::$_instance->registerHelper('CompassConstant');

			self::$_instance->setFormatter($this->settings['formatter']);

			$paths = array();
			foreach ($this->_sassFolders as $folder) {
				foreach ($this->settings['import_paths'] as $path) {
					if ($fullPath = realpath($folder->path . DS . $path)) {
						$paths[] = $fullPath;
					}
				}
			}

			self::$_instance->setImportPaths($paths);
		}

		$newCache = self::$_instance->cachedCompile($cache, $this->settings['forceCompiling']);

		if (true === $this->settings['forceCompiling'] ||
			!is_array($cache) ||
			$newCache["updated"] > $cache["updated"]) {
			file_put_contents($cacheFile, serialize($newCache));
			file_put_contents($outputFile, $newCache['compiled']);

			return true;
		}

		return false;
	}

/**
 * Clean the generated CSS files
 *
 * @return array
 */
	public function cleanGeneratedCss() {
		$this->_removeCacheKey('sass_compiled');

		// Cleaned files that we will return
		$cleanedFiles = array();
		foreach ($this->_sassFolders as $key => $sassFolder) {
			foreach ($sassFolder->find() as $file) {
				$file = new File($file);

				if (($file->ext() == 'sass' || $file->ext() == 'scss') && substr($file->name, 0, 2) !== '._') {
					$sassFile = $sassFolder->path . DS . $file->name;
					$cssFile = $this->_cssFolders[$key]->path . DS . str_replace(array('.sass','.scss'), '.css', $file->name);

					if (file_exists($cssFile)) {
						unlink($cssFile);
						$cleanedFiles[] = $cssFile;
					}
				}
			}
		}

		// Remove all cache files at once
		if (is_dir($this->_cacheFolder)) {
			@closedir($this->_cacheFolder);
			$folder = new Folder($this->_cacheFolder);
			$folder->delete();
			unset($folder);
			$cleanedFiles[] = $this->_cacheFolder . DS . '*';
		}
		mkdir($this->_cacheFolder);

		return $cleanedFiles;
	}
}
