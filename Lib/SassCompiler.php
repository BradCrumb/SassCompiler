<?php
App::uses('SassParser', 'SassCompiler.Vendor/phpsass');
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
class SassCompiler extends SassParser {

/**
 * All parsed files
 *
 * @var array
 */
	protected $_allParsedFiles = array();

/**
 * Compile a SASS/SCSS to css
 *
 * @param String $inputFile SASS/SCSS file
 *
 * @return String Rendered CSS
 */
	public function compile($inputFile) {
		$explode = explode('.',$inputFile);
		$ext = end($explode);

		$this->syntax = $ext;

		return $this->toCss($inputFile);
	}

/**
 * Parse a sass file or Sass source code and returns the CSS.
 *
 * Inherited from SassParser, and added tracking of processed files
 *
 * @param String $source name of source file or Sass source
 * @param String $isFile If we have to deal with a file or a String
 *
 * @return string CSS
 */
	public function toCss($source, $isFile = true) {
		$css = parent::toCss($source, $isFile);

		if ($isFile) {
			$this->_addParsedFile($source);
		}

		return $css;
	}

/**
 * Check if a (re)compile is needed
 *
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
					'compiled' => $this->compile($root),
					'files' => json_encode($this->allParsedFiles($root)),
					'style' => $this->style,
					'updated' => time(),
			);
		} else {
			// No changes, pass back the structure
			// we were given initially.
			return $in;
		}
	}

/**
 * Parses a directive
 *
 * Inherited from SassParser, and added tracking of processed files
 *
 * @param SassToken token to parse
 * @param SassNode parent node
 *
 * @throws SassException When nesting not allowed and debug is enabled
 *
 * @return SassNode a Sass directive node
 */
	public function parseDirective($token, $parent) {
		if (SassDirectiveNode::extractDirective($token) == '@import') {
			if ($this->syntax == SassFile::SASS) {
				$i = 0;
				$source = '';
				$size = count($this->source);
				while ($size > $i && empty($source) && isset($this->source[$i + 1])) {
					$source = $this->source[$i++];
				}
				if (!empty($source) && $this->getLevel($source) > $token->level) {
					if ($this->debug) {
						throw new SassException('Nesting not allowed beneath @import directive', $token);
					}
				}
			}
			$node = new MySassImportNode($token, $parent);

			$files = $node->getFiles($this);

			foreach ($files as $file) {
				$this->_addParsedFile($file);
			}
		}

		return parent::parseDirective($token, $parent);
	}

/**
 * Get all parsed files
 *
 * @return array All parsed files with the modification date
 */
	public function allParsedFiles() {
		return $this->_allParsedFiles;
	}

/**
 * Add a parsed file
 *
 * @param String $file File to add to the parsed array
 */
	protected function _addParsedFile($file) {
		$this->_allParsedFiles[realpath($file)] = filemtime($file);
	}
}