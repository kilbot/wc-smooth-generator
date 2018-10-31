<?php
/**
 * WP-CLI functionality.
 *
 * @package SmoothGenerator\Classes
 */

namespace WC\SmoothGenerator;

use WC\SmoothGenerator\Generator\Customer;
use WC\SmoothGenerator\Generator\Order;
use WC\SmoothGenerator\Generator\Product;
use WP_CLI;
use WP_CLI_Command;

/**
 * WP-CLI Integration class
 */
class CLI extends WP_CLI_Command {

	/**
	 * CLI constructor.
	 */
	public function __construct() {
		register_shutdown_function( array( $this, 'shutdown' ) );
	}

	/**
	 * Catch PHP Fatal Errors and print to command line
	 */
	public function shutdown() {
		$error = error_get_last();
		if ( isset( $error['type'] ) && E_ERROR === $error['type'] ) {
			WP_CLI::error( $error['message'] );
		}
	}

	/**
	 * Generate products.
	 *
	 * ## OPTIONS
	 *
	 * <amount>
	 * : The amount of products to generate
	 * ---
	 * default: 100
	 * ---
	 *
	 * ## EXAMPLES
	 * wc generate products 100
	 *
	 * @param array $args Argumens specified.
	 * @param arrat $assoc_args Associative arguments specified.
	 */
	public function products( $args, $assoc_args ) {
		$amount    = isset( $args[0] ) ? intval( $args[0] ) : 100;
		$generator = new Product();
		$progress  = \WP_CLI\Utils\make_progress_bar( 'Generating products', $amount );
		for ( $i = 1; $i <= $amount; $i++ ) {
			try {
				$generator->generate( true, $assoc_args );
			} catch ( \Exception $e ) {
				WP_CLI::error( $e->getMessage() );
			}
			$progress->tick();
		}
		$progress->finish();
		WP_CLI::success( $amount . ' products generated.' );
	}

	/**
	 * Generate orders.
	 *
	 * ## OPTIONS
	 *
	 * <amount>
	 * : The amount of orders to generate
	 * ---
	 * default: 100
	 * ---
	 *
	 * ## EXAMPLES
	 * wc generate orders 100
	 *
	 * @param array $args Argumens specified.
	 * @param arrat $assoc_args Associative arguments specified.
	 */
	public function orders( $args, $assoc_args ) {
		$amount    = isset( $args[0] ) ? intval( $args[0] ) : 100;
		$generator = new Order();
		$progress  = \WP_CLI\Utils\make_progress_bar( 'Generating orders', $amount );
		for ( $i = 1; $i <= $amount; $i++ ) {
			try {
				$generator->generate( true, $assoc_args );
			} catch ( \Exception $e ) {
				WP_CLI::error( $e->getMessage() );
			}
			$progress->tick();
		}
		$progress->finish();
		WP_CLI::success( $amount . ' orders generated.' );
	}

	/**
	 * Generate customers.
	 *
	 * ## OPTIONS
	 *
	 * <amount>
	 * : The amount of customers to generate
	 * ---
	 * default: 100
	 * ---
	 *
	 * ## EXAMPLES
	 * wc generate customers 100
	 *
	 * @param array $args Argumens specified.
	 * @param arrat $assoc_args Associative arguments specified.
	 */
	public function customers( $args, $assoc_args ) {
		$amount    = isset( $args[0] ) ? intval( $args[0] ) : 100;
		$generator = new Customer();
		$progress  = \WP_CLI\Utils\make_progress_bar( 'Generating customers', $amount );
		for ( $i = 1; $i <= $amount; $i++ ) {
			try {
				$generator->generate();
			} catch ( \Exception $e ) {
				WP_CLI::error( $e->getMessage() );
			}
			$progress->tick();
		}
		$progress->finish();
		WP_CLI::success( $amount . ' customers generated.' );
	}
}

WP_CLI::add_command( 'wc generate products', array( 'WC\SmoothGenerator\CLI', 'products' ) );
WP_CLI::add_command(
	'wc generate orders', array( 'WC\SmoothGenerator\CLI', 'orders' ), array(
		'synopsis' => array(
			array(
				'name'     => 'id',
				'type'     => 'positional',
				'optional' => false,
			),
			array(
				'name'     => 'date-start',
				'type'     => 'assoc',
				'optional' => true,
			),
			array(
				'name'     => 'date-end',
				'type'     => 'assoc',
				'optional' => true,
			),
		),
	)
);
WP_CLI::add_command( 'wc generate customers', array( 'WC\SmoothGenerator\CLI', 'customers' ) );
