<?php
App::uses('SassParser', 'SassCompiler.Vendor/phpsass');

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

	public function compile($inputFile) {
		$explode = explode('.',$inputFile);
		$ext = end($explode);

		$this->syntax = $ext;

		return $this->toCss($inputFile);
	}
}