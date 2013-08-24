SassCompiler
============

The SassCompiler provides a CakePHP Sass Component which automates the process of compiling .scss (not yet .sass) files to css. It uses the PHP interface provided by http://leafo.net/scssphp/

## Requirements

The master branch has the following requirements:

* CakePHP 2.2.0 or greater.
* PHP 5.3.0 or greater.

## Installation

* Clone/Copy the files in this directory into `app/Plugin/SassCompiler`
* Ensure the plugin is loaded in `app/Config/bootstrap.php` by calling `CakePlugin::load('SassCompiler');`
* Include the Sass component in your `AppController.php`:
   * `public $components = array('SassCompiler.Sass');`

## Documentation

The component will check for scss files to (re)compile automatically when:
 * Debug level is > 0
 * autoRun is set to true in the component settings
 * Cache-time expires

In a live environment one can force the component to (re)compile all scss files by supplying forceSassToCompile=true in the request string.

The component writes cache-files to your CakePHP's cache-directory in a subdirectory called "SassComponent".
All scss files should be placed in the `app/sass` directory (to generate css-files in the default `webroot/css` directory).
scss files for the plugin and themes should be stored in `app/Plugin/{pluginname}/sass` or `app/View/Themed/{themename}/sass`.

The default duration time for the cache is 4 hours.
After that time the cache expires and after a new request the component will check for updated or added scss files.

### Possible Component Settings
	public $components = array(
		'SassCompiler.Sass' 	=> array(
			'sourceFolder'		=> 'sass',						// Where to look for .scss files, (From the APP directory)
			'targetFolder'		=> false,						// Where to put the generated css (From the webroot directory)
			'formatter'			=> 'scss_formatter_compressed',	// PHPSass compatible style (compressed or nested)
			'forceCompiling'	=> false,						// Always recompile
			'autoRun'			=> false						// Check if compilation is necessary, this ignores the CakePHP Debug setting
		)
	);

## License
GNU General Public License, version 3 (GPL-3.0)
http://opensource.org/licenses/GPL-3.0