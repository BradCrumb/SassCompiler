<?php
App::import('Vendor', 'SassCompiler.scssphp', array('file' => 'scssphp' . DS . 'scss.inc.php'));
App::uses('MySassImportNode', 'SassCompiler.Lib');

/**
 * SassCompiler
 *
 * @author Patrick Langendoen <github-bradcrumb@patricklangendoen.nl>
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2013 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class SassCompiler extends scssc {

/**
 * Check if a (re)compile is needed
 * @param  array $in
 * @param  boolean $force
 *
 * @return array or null
 */
	public function cachedCompile($in, $force = false) {
		// assume no root
		$root = null;

		if (is_string($in)) {
			$root = $in;
		} elseif (is_array($in) && isset($in['root'])) {
			if ($force || !isset($in['files'])) {
				// If we are forcing a recompile or if for some reason the
				// structure does not contain any file information we should
				// specify the root to trigger a rebuild.
				$root = $in['root'];
			} elseif (isset($in['files'])) {
				$in['files'] = json_decode($in['files']);
				foreach ($in['files'] as $fname => $ftime) {
					if (!file_exists($fname) || filemtime($fname) > $ftime) {
						// One of the files we knew about previously has changed
						// so we should look at our incoming root again.
						$root = $in['root'];
						break;
					}
				}
			}
		} else {
			return null;
		}

		if ($root !== null) {
			// If we have a root value which means we should rebuild.
			return array(
					'root' => $root,
					'compiled' => $this->compileFile($root),
					'files' => json_encode($this->allParsedFiles()),
					//'variables' => json_encode($this->registeredVars),
					'functions' => json_encode($this->userFunctions),
					'formatter' => $this->formatter,
					//'comments' => $this->preserveComments,
					'importDirs' => json_encode((array)$this->importPaths),
					'updated' => time(),
			);
		} else {
			// No changes, pass back the structure
			// we were given initially.
			return $in;
		}
	}

	public function compileFile($fname, $outFname = null) {
		if (!is_readable($fname)) {
			throw new Exception('load error: failed to find ' . $fname);
		}

		$pathInfo = pathinfo($fname);

		$oldImport = $this->importPaths;

		$this->importPaths = (array)$this->importPaths;
		$this->importPaths[] = $pathInfo['dirname'] . '/';

		$out = $this->compile(file_get_contents($fname), $fname);

		$this->importPaths = $oldImport;

		$this->parsedFiles[] = $fname;

		if ($outFname !== null) {
			return file_put_contents($outFname, $out);
		}

		return $out;
	}

	public function allParsedFiles() {
		$tmpParsedFiles = $this->getParsedFiles();
		$parsedFiles = array();

		foreach ($tmpParsedFiles as $file) {
			$parsedFiles[$file] = filemtime($file);
		}

		return $parsedFiles;
	}

	public function registerHelper($helperName) {
		$helperClass = $helperName . 'Helper';

		App::uses($helperClass, 'SassCompiler.Lib/Helper');

		$helper = new $helperClass();

		/*$methods = get_class_methods($helper);

		if (($key = array_search('__construct', $methods)) !== false) {
			unset($methods[$key]);
		}*/

		$methods = $helper->getHelperFunctions();
		//debug($methods);exit();

		foreach ($methods as $method) {
			//if (substr($method, 0, 1) != '_') {
			//	$function = $helper->{$method}();
				$this->registerFunction($method['name'], $method['call']);
			//}
		}
	}
}