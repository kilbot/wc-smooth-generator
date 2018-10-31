<?php
/**
 * Abstract Generator class
 *
 * @package SmoothGenerator\Abstracts
 */

namespace WC\SmoothGenerator\Generator;

use \Faker\Factory;
use \WC\SmoothGenerator\Provider\Commerce;

/**
 * Data generator base class.
 */
abstract class Generator {

	/**
	 * Faker
	 *
	 * @var \Faker\Generator
	 */
	protected $faker;

	/**
	 * Generator constructor.
	 */
	public function __construct() {
		$faker = Factory::create( get_locale() );
		$faker->addProvider( new Commerce( $faker ) );
		$this->faker = $faker;
	}

	/**
	 * Return a new object of this object type.
	 *
	 * @param bool  $save Save the object before returning or not.
	 * @param array $assoc_args Optional arguments.
	 * @return array
	 */
	abstract public function generate( $save = true, $assoc_args = array() );

	/**
	 * Get a random value from an array based on weight.
	 * Taken from https://stackoverflow.com/questions/445235/generating-random-results-by-weight-in-php
	 *
	 * @param array $weighted_values Array of value => weight options.
	 * @return mixed
	 */
	public function random_weighted_element( array $weighted_values ) {
		$rand = mt_rand( 1, (int) array_sum( $weighted_values ) );
		foreach ( $weighted_values as $key => $value ) {
			$rand -= $value;
			if ( $rand <= 0 ) {
				return $key;
			}
		}
	}

}
