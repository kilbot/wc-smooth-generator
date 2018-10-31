<?php
/**
 * Commerce Provider
 *
 * @package SmoothGenerator\Provider
 */

namespace WC\SmoothGenerator\Provider;

use Faker\Provider\Base;

/**
 * Class Commerce
 *
 * @package WC\SmoothGenerator\Provider
 */
class Commerce extends Base {

	/**
	 * Category List
	 *
	 * @var array
	 */
	protected static $categories = array(
		'Automotive',
		'Baby',
		'Beauty',
		'Books',
		'Clothing',
		'Computers',
		'Electronics',
		'Games',
		'Garden',
		'Grocery',
		'Health',
		'Home',
		'Industrial',
		'Jewelry',
		'Kids',
		'Movies',
		'Music',
		'Outdoors',
		'Shoes',
		'Sports',
		'Tools',
		'Toys',
	);

	/**
	 * Buzzwords List
	 *
	 * @var array
	 */
	protected static $buzzwords = array(
		'Adaptive',
		'Advanced',
		'Automated',
		'Balanced',
		'Business-focused',
		'Centralized',
		'Cloned',
		'Compatible',
		'Configurable',
		'Cross-platform',
		'Crypto',
		'Customizable',
		'Decentralized',
		'Digitized',
		'Distributed',
		'Enhanced',
		'Ergonomic',
		'Exclusive',
		'Expanded',
		'Extended',
		'Focused',
		'Fundamental',
		'Future-proofed',
		'Grass-roots',
		'Innovative',
		'Integrated',
		'Intuitive',
		'Inverse',
		'Monitored',
		'Multi-channelled',
		'Multi-lateral',
		'Multi-layered',
		'Multi-tiered',
		'Networked',
		'Open-source',
		'Organic',
		'Phased',
		'Polarised',
		'Proactive',
		'Programmable',
		'Progressive',
		'Reactive',
		'Realigned',
		'Re-contextualized',
		'Re-engineered',
		'Reverse-engineered',
		'Robust',
		'Seamless',
		'Self-enabling',
		'Sharable',
		'Stand-alone',
		'Streamlined',
		'Switchable',
		'Synergistic',
		'Team-oriented',
		'Total',
		'Universal',
		'Upgradable',
		'User-centric',
		'User-friendly',
		'Versatile',
		'Virtual',
		'Visionary',
	);

	/**
	 * Adjective List
	 *
	 * @var array
	 */
	protected static $adjectives = array(
		'Aerodynamic',
		'Amazing',
		'Awesome',
		'Cool',
		'Durable',
		'Ergonomic',
		'Fantastic',
		'Generic',
		'Gorgeous',
		'Handcrafted',
		'Handmade',
		'Heavy Duty',
		'Incredible',
		'Licensed',
		'Lightweight',
		'Practical',
		'Premium',
		'Refined',
		'Rustic',
		'Sleek',
		'Special',
		'Sweet',
		'Tasty',
		'Unbranded',
	);

	/**
	 * Colors List
	 *
	 * @var array
	 */
	protected static $colors = array(
		'Azure',
		'Black',
		'Blue',
		'Bright',
		'Brown',
		'Crimson',
		'Dark',
		'Gold',
		'Gray',
		'Green',
		'Indigo',
		'Lavender',
		'Light',
		'Magenta',
		'Multicolored',
		'Mustard',
		'Orange',
		'Pink',
		'Pinkish',
		'Purple',
		'Red',
		'Rosy',
		'Scarlet',
		'Silver',
		'Turquoise',
		'Violet',
		'White',
		'Yellow',
	);

	/**
	 * Sizes List
	 *
	 * @var array
	 */
	protected static $sizes = array(
		'Enormous',
		'Giant',
		'Gigantic',
		'Huge',
		'Immense',
		'Jumbo',
		'Large',
		'Little',
		'Long',
		'Mammoth',
		'Massive',
		'Miniature',
		'Petite',
		'Small',
		'Tall',
		'Teeny',
		'Thin',
		'Tiny',
	);

	/**
	 * Shapes List
	 *
	 * @var array
	 */
	protected static $shape = array(
		'Broad',
		'Circular',
		'Curved',
		'Cylindrical',
		'Deep',
		'Distorted',
		'Flat',
		'Fluffy',
		'Hollow',
		'Low',
		'Narrow',
		'Oval',
		'Rotund',
		'Round',
		'Skinny',
		'Square',
		'Straight',
		'Triangular',
		'Wide',
	);

	/**
	 * Materials List
	 *
	 * @var array
	 */
	protected static $materials = array(
		'Aluminum',
		'Bronze',
		'Concrete',
		'Copper',
		'Cotton',
		'Frozen',
		'Granite',
		'Iron',
		'Leather',
		'Linen',
		'Marble',
		'Metal',
		'Paper',
		'Plastic',
		'Rubber',
		'Silk',
		'Soft',
		'Steel',
		'Wooden',
		'Wool',
	);

	/**
	 * Nouns List
	 *
	 * @var array
	 */
	protected static $nouns = array(
		'Bacon',
		'Bag',
		'Ball',
		'Bench',
		'Bike',
		'Bottle',
		'Car',
		'Chair',
		'Cheese',
		'Chicken',
		'Chips',
		'Clock',
		'Coat',
		'Computer',
		'Fish',
		'Gloves',
		'Hat',
		'Hoodie',
		'Keyboard',
		'Knife',
		'Lamp',
		'Mouse',
		'Pants',
		'Pizza',
		'Plate',
		'Salad',
		'Sausages',
		'Shirt',
		'Shoes',
		'Soap',
		'Table',
		'Toothbrush',
		'Towels',
		'Tuna',
		'Wallet',
		'Watch',
	);

	/**
	 * Return product name
	 *
	 * @return string
	 */
	public function productName() {
		return implode(
			' ',
			array_filter(
				[
					$this->generator->optional( 0.3 )->randomElement( static::$sizes ),
					$this->generator->optional( 0.8 )->randomElement( static::$adjectives ),
					$this->generator->optional( 0.1 )->randomElement( static::$buzzwords ),
					$this->generator->randomElement(
						[
							$this->generator->randomElement( static::$colors ),
							$this->generator->randomElement( static::$materials ),
						]
					),
					$this->generator->randomElement( static::$nouns ),
				]
			)
		);
	}

	/**
	 * Return single category name
	 *
	 * @return string
	 */
	public function productCategoryName() {
		return $this->generator->randomElement( static::$categories );
	}

	/**
	 * Return an array of category names
	 *
	 * @param int $count Array size.
	 * @return array
	 */
	public function productCategoryNames( $count = 5 ) {
		return $this->generator->randomElements( static::$categories, $count );
	}

	/**
	 * Return an array of tag names
	 *
	 * @param int $count Array size.
	 * @return array
	 */
	public function productTagNames( $count = 5 ) {
		return $this->generator->randomElements( static::$buzzwords, $count );
	}

	/**
	 * Return an array of colors
	 *
	 * @param int $count Array size.
	 * @return array
	 */
	public function productColors( $count = 5 ) {
		return $this->generator->randomElements( static::$colors, $count );
	}

	/**
	 * Return an array of sizes
	 *
	 * @param int $count Array size.
	 * @return array
	 */
	public function productSizes( $count = 5 ) {
		return $this->generator->randomElements( static::$sizes, $count );
	}

	/**
	 * Return an array of sizes
	 *
	 * @param int $count Array size.
	 * @return array
	 */
	public function productMaterials( $count = 5 ) {
		return $this->generator->randomElements( static::$materials, $count );
	}

}
