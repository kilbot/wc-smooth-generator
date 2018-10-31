<?php
/**
 * Customer data generation.
 *
 * @package SmoothGenerator\Classes
 */

namespace WC\SmoothGenerator\Generator;

/**
 * Customer data generator.
 */
class Customer extends Generator {

	/**
	 * Return a new customer.
	 *
	 * @param bool  $save Save the object before returning or not.
	 * @param array $assoc_args Arguments.
	 * @return \WC_Customer Customer object with data populated.
	 * @throws \Exception Error.
	 */
	public function generate( $save = true, $assoc_args = array() ) {

		// Make sure a unique username and e-mail are used.
		do {
			$username = $this->faker->userName();
		} while ( username_exists( $username ) );

		do {
			$email = $this->faker->safeEmail();
		} while ( email_exists( $email ) );

		$firstname   = $this->faker->firstName( $this->faker->randomElement( array( 'male', 'female' ) ) );
		$lastname    = $this->faker->lastName();
		$company     = $this->faker->company();
		$address1    = $this->faker->buildingNumber() . ' ' . $this->faker->streetName();
		$address2    = $this->faker->streetAddress();
		$city        = $this->faker->city();
		$state       = $this->faker->stateAbbr();
		$postcode    = $this->faker->postcode();
		$countrycode = $this->faker->countryCode();
		$phone       = $this->faker->e164PhoneNumber();
		$customer    = new \WC_Customer();

		$customer->set_props(
			array(
				'date_created'        => null,
				'date_modified'       => null,
				'email'               => $email,
				'first_name'          => $firstname,
				'last_name'           => $lastname,
				'display_name'        => $firstname,
				'role'                => 'customer',
				'username'            => $username,
				'password'            => $this->faker->password(),
				'billing_first_name'  => $firstname,
				'billing_last_name'   => $lastname,
				'billing_company'     => $company,
				'billing_address_1'   => $address1,
				'billing_address_2'   => $address2,
				'billing_city'        => $city,
				'billing_state'       => $state,
				'billing_postcode'    => $postcode,
				'billing_country'     => $countrycode,
				'billing_email'       => $email,
				'billing_phone'       => $phone,
				'shipping_first_name' => $firstname,
				'shipping_last_name'  => $lastname,
				'shipping_company'    => $company,
				'shipping_address_1'  => $address1,
				'shipping_address_2'  => $address2,
				'shipping_city'       => $city,
				'shipping_state'      => $state,
				'shipping_postcode'   => $postcode,
				'shipping_country'    => $countrycode,
				'is_paying_customer'  => false,
			)
		);

		if ( $save ) {
			$customer->save();
		}

		return $customer;
	}

}
