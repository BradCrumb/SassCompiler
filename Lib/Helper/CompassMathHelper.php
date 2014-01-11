<?php
App::uses('SassHelper', 'SassCompiler.Lib/Helper');
App::uses('View', 'View');
App::uses('Helper', 'View');

/**
 * CompassMathHelper
 * ===
 *
 * CakePHP Implementation of the Compass Math Helper
 *
 * Sass math functions are sufficient for most cases,
 * but in those moments of extreme geekiness these additional functions can really come in handy.
 *
 * @see http://compass-style.org/reference/compass/helpers/math/
 *
 * @author Marc-Jan Barnhoorn <github-bradcrumb@marc-jan.nl>
 * @copyright 2014 (c), Patrick Langendoen & Marc-Jan Barnhoorn
 * @package SassCompiler
 * @license http://opensource.org/licenses/GPL-3.0 GNU GENERAL PUBLIC LICENSE
 */
class CompassMathHelper extends SassHelper {

/**
 * Returns the value of π.
 *
 * @return Float π
 */
	public function pi() {
		return array(
			'name' => 'pi',
			'call' => function() {
				return pi();
			}
		);
	}

/**
 * Returns the sine of a number. If the number is unitless or has a unit of deg then it will return a unitless result.
 * Unless the number has a unit of deg it will be evaluated as radians. Degrees will first be converted to radians.
 * If the number is any other unit, the units will be passed thru to the result, and the number will be treated as radians.
 *
 * @return Float The sine of the given value
 */
	public function sin() {
		return array(
			'name' => 'sin',
			'call' => function($args) {
				return sin($args[0][1]);
			}
		);
	}

/**
 * Returns the cosine of a number. If the number is unitless or has a unit of deg then it will return a unitless result.
 * Unless the number has a unit of deg it will be evaluated as radians. Degrees will first be converted to radians.
 * If the number is any other unit, the units will be passed thru to the result, and the number will be treated as radians.
 *
 * @return Float The cosine of the given value
 */
	public function cos() {
		return array(
			'name' => 'cos',
			'call' => function($args) {
				return cos($args[0][1]);
			}
		);
	}

/**
 * Returns the tangent of a number. If the number is unitless or has a unit of deg then it will return a unitless result.
 * Unless the number has a unit of deg it will be evaluated as radians. Degrees will first be converted to radians.
 * If the number is any other unit, the units will be passed thru to the result, and the number will be treated as radians.
 *
 * @return Float The tangent of the given value
 */
	public function tan() {
		return array(
			'name' => 'tan',
			'call' => function($args) {
				return tan($args[0][1]);
			}
		);
	}

/**
 * Returns the value of e.
 *
 * @see http://en.wikipedia.org/wiki/E_(mathematical_constant)
 *
 * @return Float value of e
 */
	public function e() {
		return array(
			'name' => 'e',
			'call' => function() {
				return M_E;
			}
		);
	}

/**
 * Calculates the logarithm of a number to a base. Base defaults to e.
 *
 * @return Float Logarithm of the given number
 */
	public function logarithm() {
		return array(
			'name' => 'logarithm',
			'call' => function($args) {
				$base = isset($args[1][1]) ? $args[1][1] : M_E;
				return log($args[0][1], $base);
			}
		);
	}

/**
 * Calculates the square root of a number.
 *
 * @return Float Square root of number
 */
	public function sqrt() {
		return array(
			'name' => 'sqrt',
			'call' => function($args) {
				return sqrt($args[0][1]);
			}
		);
	}

/**
 * Calculates the value of a number raised to the power of an exponent.
 *
 * @param Float $number Number to raise
 * @param Float $exponent Exponent to raise the number
 *
 * @return Float
 */
	public function pow() {
		return array(
			'name' => 'pow',
			'call' => function($args) {
				return pow($args[0][1], $args[1][1]);
			}
		);
	}
}