<?php
App::uses('SassHelper', 'SassCompiler.Lib/Helper');
App::uses('View', 'View');
App::uses('Helper', 'View');

/**
 * CompassInlineDataHelper
 * ===
 *
 * CakePHP Implementation of the Compass Font Files Helper
 *
 * @see http://compass-style.org/reference/compass/helpers/inline-data/
 *
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2014 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class CompassInlineDataHelper extends SassHelper {

/**
 * Embeds the contents of an image directly inside your stylesheet, eliminating the need for another HTTP request.
 * For small images, this can be a performance benefit at the cost of a larger generated CSS file.
 * Like the image-url() helper, the path specified should be relative to your project's images directory.
 *
 * @param String $image Image relative to image
 * @param String $mime-type Given mime type
 */
	public function inlineImage() {
		return array(
			'name' => 'inline-image',
			'call' => function($args) {
				$image = $args[0][2][0];
				$mimeType = isset($args[1][2][0]) ? $args[1][2][0] : null;

				$realPath = $this->__getFullImagePath($image);

				return $this->__inlineImageString($this->__data($realPath), $this->__calculateMimeType($realPath, $mimeType));
			}
		);
	}

/**
 * Like the font-files() helper, but the font file is embedded within the generated CSS file.
 */
	public function inlineFontFiles() {
		return array(
			'name' => 'inline-font-files',
			'call' => function($args) {
				$files = array();

				$amount = count($args);
				for ($i = 0;$i < $amount;$i++) {
					$path = $args[$i][2][0];
					$type = $args[++$i][1];

					$realPath = $this->_getFullAssetPath('fonts' . DS . $path);

					$url = $this->__inlineImageString($this->__data($realPath), $this->__calculateMimeType($realPath));
					$files[] = "{$url} format('" . $type . "')";
				}


				return implode(', ', $files);
			}
		);
	}

/**
 * Get inline image string
 *
 * @param String $data Base64 encoded image
 * @param String $mimeType Mime type of the image
 *
 * @return String Inline image
 */
	private function __inlineImageString($data, $mimeType) {
		return "url('data:{$mimeType};base64,{$data}')";
	}

/**
 * Get data for a given path
 *
 * @throws CakeException when File can't be found
 *
 * @param String $path Full filepath
 *
 * @return String Base64 encoded data of a file
 */
	private function __data($path) {
		if (!file_exists($path)) {
			throw new CakeException("File not found or cannot be read: {$path}");
		}

		return base64_encode(file_get_contents($path));
	}

/**
 * Search for the correct mimetype
 *
 * @throws CakeException when mimetype can't be found
 *
 * @param String $path Full filepath
 * @param String $mimeType Given mimetype (optional)
 *
 * @return String Founded mimetype
 */
	private function __calculateMimeType($path, $mimeType = null) {
		if (!empty($mimeType)) {
			return $mimeType;
		}

		switch(true) {
			case preg_match('/\.png$/i', $path):
				return 'image/png';
			case preg_match('/\.jpe?g$/i', $path):
				return 'image/jpeg';
			case preg_match('/\.gif$/i', $path):
				return 'image/gif';
			case preg_match('/\.svg$/i', $path):
				return 'image/svg+xml';
			case preg_match('/\.otf$/i', $path):
				return 'font/opentype';
			case preg_match('/\.eot$/i', $path):
				return 'application/vnd.ms-fontobject';
			case preg_match('/\.ttf$/i', $path):
				return 'font/truetype';
			case preg_match('/\.woff$/i', $path):
				return 'application/x-font-woff';
			case preg_match('/\.off$/i', $path):
				return 'font/openfont';
		}

		throw new CakeException("A mime type could not be determined for {$path}, please specify one explicitly.");
	}
}