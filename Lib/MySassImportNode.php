<?php
/**
 * MySassImportNode class.
 * Represents a CSS Import.
 *
 * @author Patrick Langendoen <github-bradcrumb@patricklangendoen.nl>
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2013 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class MySassImportNode extends SassImportNode {

/**
 * Files to import
 *
 * @var array
 */
	private $files = array();

/**
 * SassImportNode.
 *
 * @param object source token
 *
 * @return SassImportNode
 */
	public function __construct($token, $parent) {
		parent::__construct($token, $parent);

		preg_match(self::MATCH, $token->source, $matches);

		foreach (SassList::_build_list($matches[self::FILES]) as $file) {
			$this->files[] = trim($file, '"\'; ');
		}
	}

/**
 * Get imported files
 *
 * @param SassParser $parser The current parser
 *
 * @return String[] All imported files
 */
	public function getFiles($parser) {
		$files = array();
		foreach ($this->files as $file) {
			if (preg_match(self::MATCH_CSS, $file, $matches)) {
				if (isset($matches[2]) && $matches[2] == 'url') {
					$file = $matches[1];
				} else {
					$file = "url('$file')";
				}

				return array(new SassString("@import $file;"), new SassString("\n"));
			}

			$file = trim($file, '\'"');
			$files = array_merge(SassFile::get_file($file, $parser), $files);
		}

		return $files;
	}
}