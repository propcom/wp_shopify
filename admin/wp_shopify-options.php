<?

	/*
	 * Plugin Name: Wordpress Shopify
	 * Version: 1.0.0
	 * Author: Joshua Grierson
	 * Description: This plugin allows you to link your Shopify store within Wordpress.
	 */

	class Wordpress_Shopify_Options {

		public $options;

		public static function get_option($set, $property){

			try {

				$option = get_option('prop_shopify_'.$set);

				if(isset($option[$property]) && $option[$property] !== '') {

					return $option[$property];

				} else {

					return false;

				}

			} catch (Exception $e) {}

		}

		public static function print_option($set, $property){

			try {

				$option = get_option('prop_shopify_'.$set);

				if(isset($option[$property])) {

					echo $option[$property];

				} else {

					return false;

				}

			} catch (Exception $e) {}

		}

		function __construct() {

			if ( is_admin() ) {

				add_action( 'admin_menu', [ $this, 'create_menu' ] );
				add_action( 'admin_init', [ $this, 'register_settings' ] );
				add_action( 'admin_init', [ $this, 'define_settings' ] );

			}

		}

		function create_menu() {

			add_menu_page(

				'Shopify Manager',
				'Shopify',
				'administrator',
				'shopify-manager',
				[ $this, 'render_page' ],
				'dashicons-cart', 2

			);

		}

		function render_page() {

			$this->options = get_option( 'prop_shopify' ); ?>

			<div class="wrap">
				<h1>Propeller's Shopify Manager</h1>
				<form method="post" action="options.php">
					<?
						settings_fields( 'propeller_shopify' );
						do_settings_sections( 'shopify-manager' );
						submit_button();
					?>
				</form>
				<? Shopify_Api_Tester::test(); ?>
			</div>

			<?

		}

		function section_text() {

			print 'Enter your site settings below:';

		}

		function register_settings() {

			register_setting(

				'propeller_shopify',
				'prop_shopify'

			);

		}

		function define_settings() {

			/**
			 * @desc Site Settings
			 */

			add_settings_section(

				'shopify_settings',
				'Shopify Settings',
				[ $this, 'print_section_info' ],
				'shopify-manager'

			);

			add_settings_field(

				'shop',
				'Store Name',
				[ $this, 'add_field' ],
				'shopify-manager',
				'shopify_settings',
				[
					'name'    => 'shop',
					'type'    => 'text',
					'setting' => 'shopify',
				]

			);

			add_settings_field(

				'api_key',
				'Store API Key',
				[ $this, 'add_field' ],
				'shopify-manager',
				'shopify_settings',
				[
					'name'    => 'api_key',
					'type'    => 'text',
					'setting' => 'shopify',
					'note' => 'See <a href="https://help.shopify.com/api/guides/api-credentials#get-credentials-through-the-shopify-admin" target="_blank">this doc</a> to obtain your API key.',
				]

			);

			add_settings_field(

				'pass',
				'Store Password',
				[ $this, 'add_field' ],
				'shopify-manager',
				'shopify_settings',
				[
					'name'    => 'pass',
					'type'    => 'text',
					'setting' => 'shopify',
					'note' => 'See <a href="https://help.shopify.com/api/guides/api-credentials#get-credentials-through-the-shopify-admin" target="_blank">this doc</a> to obtain your Password.',
				]

			);

		}

		function add_field( array $args ) {

			switch ( $args['type'] ) {

				case 'textarea' :

					printf(

						'<textarea id="' . $args['name'] . '" name="prop_' . $args['setting'] . '[' . $args['name'] . ']" rows="' . $args['rows'] . '" cols="' . $args['cols'] . '">%s</textarea>',
						isset( $this->options[ $args['name'] ] ) ? esc_attr( $this->options[ $args['name'] ] ) : ''

					);

					if ( isset( $args['note'] ) ) {

						print(

							'<p class="description">' . $args['note'] . '</p>'

						);

					}

					break;

				default :

					printf(

						'<input type="' . $args['type'] . '" id="' . $args['name'] . '" name="prop_' . $args['setting'] . '[' . $args['name'] . ']" value="%s" class="regular-text" />',
						isset( $this->options[ $args['name'] ] ) ? esc_attr( $this->options[ $args['name'] ] ) : ''

					);

					if ( isset( $args['note'] ) ) {

						print(

							'<p class="description">' . $args['note'] . '</p>'

						);

					}

					break;

			}

		}

		function print_section_info() {

			print 'Please enter your Shopify settings below:';

		}

	}
