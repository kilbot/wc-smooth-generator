<?php
/**
 * Abstract product generator class
 *
 * @package SmoothGenerator\Abstracts
 */

namespace WC\SmoothGenerator\Generator;

use WP_CLI;

/**
 * Product data generator.
 */
class Product extends Generator {

	/**
	 * Array of all category ids
	 *
	 * @var array
	 */
	private $category_ids = array();

	/**
	 * Array of all tag ids
	 *
	 * @var array
	 */
	private $tag_ids = array();

	/**
	 * Product constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->category_ids = $this->get_all_term_ids( 'product_cat' );
		$this->tag_ids      = $this->get_all_term_ids( 'product_tag' );
	}

	/**
	 * Return a new product.
	 *
	 * @param bool  $save Save the object before returning or not.
	 * @param array $assoc_args Arguments.
	 * @return \WC_Product The product object consisting of random data.
	 */
	public function generate( $save = true, $assoc_args = array() ) {

		// 30% chance of a variable product.
		$is_variable = $this->faker->boolean( 30 );

		// generate categories and tags (if required).
		if ( count( $this->category_ids ) < 5 ) {
			$this->category_ids = $this->generate_categories( $this->faker->numberBetween( 10, 20 ) );
		}
		if ( count( $this->tag_ids ) < 5 ) {
			$this->tag_ids = $this->generate_tags( $this->faker->numberBetween( 10, 20 ) );
		}

		if ( $is_variable ) {
			$product = self::generate_variable_product();
		} else {
			$product = self::generate_simple_product();
		}

		if ( $product ) {
			$product->save();
		}

		return $product;
	}

	/**
	 * Generate a variable product and return it.
	 *
	 * @return \WC_Product_Variable
	 */
	private function generate_variable_product() {
		$name              = $this->faker->productName();
		$will_manage_stock = $this->faker->boolean();
		$product           = new \WC_Product_Variable();
		$matrix            = array(
			'Color'    => $this->faker->productColors( $this->faker->numberBetween( 2, 4 ) ),
			'Size'     => $this->faker->productSizes( $this->faker->numberBetween( 2, 4 ) ),
			'Material' => $this->faker->productMaterials( $this->faker->numberBetween( 2, 4 ) ),
		);
		$keys              = $this->faker->randomElements( array_keys( $matrix ), $this->faker->numberBetween( 1, 3 ) );
		$attributes        = array();

		// $image_id = self::generate_image();
		// $gallery  = self::maybe_get_gallery_image_ids();
		foreach ( $keys as $key ) {
			$attribute = new \WC_Product_Attribute();
			$attribute->set_id( 0 );
			$attribute->set_name( $key );
			$attribute->set_options( $matrix[ $key ] );
			$attribute->set_position( 0 );
			$attribute->set_visible( true );
			$attribute->set_variation( true );
			$attributes[] = $attribute;
		}

		$product->set_props(
			array(
				'name'                                   => $name,
				'featured'                               => $this->faker->boolean( 10 ),
				'description'                            => $this->faker->paragraphs( $this->faker->numberBetween( 1, 5 ), true ),
				'short_description'                      => $this->faker->text(),
				'attributes'                             => $attributes,
				'tax_status'                             => 'taxable',
				'tax_class'                              => '',
				'manage_stock'                           => $will_manage_stock,
				'stock_quantity'                         => $will_manage_stock ? $this->faker->numberBetween( -100, 100 ) : null,
				'stock_status'                           => 'instock',
				'backorders'                             => $this->faker->randomElement( array( 'yes', 'no', 'notify' ) ),
				'sold_individually'                      => $this->faker->boolean( 20 ),
				'upsell_ids'                             => self::get_existing_product_ids(),
				'cross_sell_ids'                         => self::get_existing_product_ids(),
				// 'image_id'          => $image_id,
										  'category_ids' => $this->faker->randomElements( $this->category_ids, $this->faker->numberBetween( 1, count( $this->category_ids ) ) ),
				'tag_ids'                                => $this->faker->randomElements( $this->tag_ids, $this->faker->numberBetween( 1, count( $this->tag_ids ) ) ),
				// 'gallery_image_ids' => $gallery,
										  'reviews_allowed' => $this->faker->boolean(),
				'purchase_note'                          => $this->faker->boolean() ? $this->faker->text() : '',
				'menu_order'                             => $this->faker->numberBetween( 0, 10000 ),
			)
		);
		// Need to save to get an ID for variations.
		$product->save();

		// Create variations.
		$variation_attributes = wc_list_pluck( array_filter( $product->get_attributes(), 'wc_attributes_array_filter_variation' ), 'get_slugs' );
		// Allow 'Any ...' variations.
		$keys                 = array_keys( $variation_attributes );
		$random_keys          = $this->faker->randomElements( $keys, $this->faker->numberBetween( 0, count( $keys ) ) );
		$variation_attributes = array_merge( $variation_attributes, array_fill_keys( $random_keys, array( ' ' ) ) );

		$possible_attributes = array_reverse( wc_array_cartesian( $variation_attributes ) );
		foreach ( $possible_attributes as $possible_attribute ) {
			$price      = $this->faker->randomFloat( 2, 1, 1000 );
			$is_on_sale = $this->faker->boolean( 30 );
			$sale_price = $is_on_sale ? $this->faker->randomFloat( 2, 0, $price ) : '';
			$is_virtual = $this->faker->boolean( 20 );
			$variation  = new \WC_Product_Variation();
			$variation->set_props(
				array(
					'parent_id'         => $product->get_id(),
					'attributes'        => $possible_attribute,
					'regular_price'     => $price,
					'sale_price'        => $sale_price,
					'date_on_sale_from' => '',
					'date_on_sale_to'   => $this->faker->iso8601( date( 'c', strtotime( '+1 month' ) ) ),
					'tax_status'        => 'taxable',
					'tax_class'         => '',
					'manage_stock'      => $will_manage_stock,
					'stock_quantity'    => $will_manage_stock ? $this->faker->numberBetween( -100, 100 ) : null,
					'stock_status'      => 'instock',
					'weight'            => $is_virtual ? '' : $this->faker->numberBetween( 1, 200 ),
					'length'            => $is_virtual ? '' : $this->faker->numberBetween( 1, 200 ),
					'width'             => $is_virtual ? '' : $this->faker->numberBetween( 1, 200 ),
					'height'            => $is_virtual ? '' : $this->faker->numberBetween( 1, 200 ),
					'virtual'           => $is_virtual,
					'downloadable'      => false,
				// 'image_id'          => self::generate_image(),
				)
			);
			$variation->save();
		}
		$data_store = $product->get_data_store();
		$data_store->sort_all_product_variations( $product->get_id() );

		return $product;
	}

	/**
	 * Generate a simple product and return it.
	 *
	 * @return \WC_Product
	 */
	private function generate_simple_product() {
		$name              = $this->faker->productName();
		$will_manage_stock = $this->faker->boolean();
		$is_virtual        = $this->faker->boolean();
		$price             = $this->faker->randomFloat( 2, 1, 1000 );
		$is_on_sale        = $this->faker->boolean( 30 );
		$sale_price        = $is_on_sale ? $this->faker->randomFloat( 2, 0, $price ) : '';
		$product           = new \WC_Product();

		// $image_id = self::generate_image();
		// $gallery  = self::maybe_get_gallery_image_ids();
		$product->set_props(
			array(
				'name'               => $name,
				'featured'           => $this->faker->boolean(),
				'catalog_visibility' => 'visible',
				'description'        => $this->faker->paragraphs( $this->faker->numberBetween( 1, 5 ), true ),
				'short_description'  => $this->faker->text(),
				'sku'                => sanitize_title( $name ) . '-' . $this->faker->ean8,
				'regular_price'      => $price,
				'sale_price'         => $sale_price,
				'date_on_sale_from'  => '',
				'date_on_sale_to'    => $this->faker->iso8601( date( 'c', strtotime( '+1 month' ) ) ),
				'total_sales'        => $this->faker->numberBetween( 0, 10000 ),
				'tax_status'         => 'taxable',
				'tax_class'          => '',
				'manage_stock'       => $will_manage_stock,
				'stock_quantity'     => $will_manage_stock ? $this->faker->numberBetween( -100, 100 ) : null,
				'stock_status'       => 'instock',
				'backorders'         => $this->faker->randomElement( array( 'yes', 'no', 'notify' ) ),
				'sold_individually'  => $this->faker->boolean( 20 ),
				'weight'             => $is_virtual ? '' : $this->faker->numberBetween( 1, 200 ),
				'length'             => $is_virtual ? '' : $this->faker->numberBetween( 1, 200 ),
				'width'              => $is_virtual ? '' : $this->faker->numberBetween( 1, 200 ),
				'height'             => $is_virtual ? '' : $this->faker->numberBetween( 1, 200 ),
				'upsell_ids'         => self::get_existing_product_ids(),
				'cross_sell_ids'     => self::get_existing_product_ids(),
				'parent_id'          => 0,
				'reviews_allowed'    => $this->faker->boolean(),
				'purchase_note'      => $this->faker->boolean() ? $this->faker->text() : '',
				'menu_order'         => $this->faker->numberBetween( 0, 10000 ),
				'virtual'            => $is_virtual,
				'downloadable'       => false,
				'category_ids'       => $this->faker->randomElements( $this->category_ids, $this->faker->numberBetween( 1, count( $this->category_ids ) ) ),
				'tag_ids'            => $this->faker->randomElements( $this->tag_ids, $this->faker->numberBetween( 1, count( $this->tag_ids ) ) ),
				'shipping_class_id'  => 0,
			// 'image_id'           => $image_id,
			// 'gallery_image_ids'  => $gallery,
			)
		);

		return $product;
	}

	/**
	 * Generate an image gallery.
	 *
	 * @return array
	 */
	private function maybe_get_gallery_image_ids() {
		$gallery = array();

		$create_gallery = $this->faker->boolean( 10 );

		if ( ! $create_gallery ) {
			return;
		}

		for ( $i = 0; $i < rand( 1, 3 ); $i++ ) {
			$gallery[] = self::generate_image();
		}

		return $gallery;
	}

	/**
	 * Get some random existing product IDs.
	 *
	 * @param int $limit Number of term IDs to get.
	 * @return array
	 */
	protected static function get_existing_product_ids( $limit = 5 ) {
		$post_ids = get_posts(
			array(
				'numberposts' => $limit * 2,
				'orderby'     => 'date',
				'post_type'   => 'product',
				'fields'      => 'ids',
			)
		);

		if ( ! $post_ids ) {
			return array();
		}

		shuffle( $post_ids );

		return array_slice( $post_ids, 0, max( count( $post_ids ), $limit ) );
	}

	/**
	 * Return all the term ids
	 *
	 * @param string $taxonomy Taxonomy slug.
	 * @return array|int|\WP_Error
	 */
	private function get_all_term_ids( $taxonomy = 'product_cat' ) {
		return get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'fields'     => 'ids',
			)
		);
	}

	/**
	 * Generate product categories
	 *
	 * @param int $limit Number of term IDs to get.
	 * @return array
	 */
	private function generate_categories( $limit ) {
		$terms      = $this->faker->productCategoryNames( $limit );
		$parent_ids = array();
		$child_ids  = array();

		foreach ( $terms as $term ) {
			$is_child = count( $parent_ids ) > 0 && $this->faker->boolean();
			$args     = array(
				'description' => $this->faker->sentence(),
				'parent_id'   => $is_child ? $this->faker->randomElement( $parent_ids ) : 0,
			);

			$result = wp_insert_term( $term, 'product_cat', $args );
			if ( $is_child ) {
				$child_ids[] = $result['term_id'];
			} else {
				$parent_ids[] = $result['term_id'];
			}
		}

		return $parent_ids + $child_ids;
	}

	/**
	 * Generate product categories
	 *
	 * @param int $limit Number of term IDs to get.
	 * @return array
	 */
	private function generate_tags( $limit ) {
		$terms   = $this->faker->productTagNames( $limit );
		$tag_ids = array();

		foreach ( $terms as $term ) {
			$result    = wp_insert_term(
				$term, 'product_tag',
				array(
					'description' => $this->faker->sentence(),
				)
			);
			$tag_ids[] = $result['term_id'];
		}

		return $tag_ids;
	}

}
