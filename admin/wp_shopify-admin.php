<?php

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * @link       http://example.com
	 * @since      1.0.0
	 *
	 * @package    Wordpress Shopify
	 * @subpackage Wordpress Shopify/admin
	 */

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @package    Wordpress Shopify
	 * @subpackage Wordpress Shopify/admin
	 * @author     Josh Grierson <joshua.grierson@propcom.co.uk>
	 */
	class Wordpress_Shopify_Admin {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $Wordpress_Shopify    The ID of this plugin.
		 */
		private $Wordpress_Shopify;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;

		/**
		 * The options for this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $options    The options for this plugin.
		 */
		private $options;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string    $plugin_name       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */
		public function __construct( $Wordpress_Shopify, $version ) {

			$this->load_dependencies();

			$this->Wordpress_Shopify = $Wordpress_Shopify;
			$this->version = $version;

			$this->options = new Wordpress_Shopify_Options();

		}

		public function load_dependencies() {

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp_shopify-endpoints.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp_shopify-exception.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp_shopify-queue.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wp_shopify-api.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp_shopify-tester.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp_shopify-email.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wp_shopify-options.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wp_shopify-rest.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp_shopify-multipass.php';

		}

		/**
		 * Register the media product button
		 *
		 * @since    1.0.0
		 */
		public function add_product_button () {

			add_thickbox();

			$shopify_collections = Wordpress_Shopify_Api::forge( ENDPOINT_COLLECTIONS )->get_collections();

			ob_start();
			?>
				<div id="wp-shopify-modal" style="display: none;">
					<div class="wp-shopify-modal">

						<? if( !empty($shopify_collections) ): ?>

							<? foreach($shopify_collections as $collection): ?>
								<div class="wsm-collection">

									<a class="js-load-products" href="javascript:void(0);" data-id="<?= $collection->id ?>">

										<div class="image"><img src="<?= ($collection->image->src ? $collection->image->src : plugins_url( 'images/blank.png', __FILE__ ) ) ?>" alt="<?= $collection->title ?>"></div>
										<div class="title">
											<h3><?= $collection->title ?></h3>
											<p>Created At: <?= date_i18n( 'M j, h:m a T', strtotime($collection->created_at) ) ?></p>
										</div>

									</a>

								</div>
							<? endforeach; ?>

						<? else: ?>
							<p>No collections available. Collections are needed on your shop so as to add products.</p>
						<? endif; ?>

					</div>
				</div>
				<script type="text/javascript">
					window.endpoint = '<?= get_home_url().'/wp-json/shopify/v1/products/' ?>';
				</script>
			<?

			echo ob_get_clean();

			echo '<a href="#TB_inline?width=900&height=800&inlineId=wp-shopify-modal" id="insert-shopify-product" class="thickbox button" name="Shopify Products - Choose a product">Add Product</a>';

		}

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Plugin_Name_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Plugin_Name_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_style( $this->Wordpress_Shopify, plugin_dir_url( __FILE__ ) . 'css/wp_shopify-admin.css', array(), $this->version, 'all' );

		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Plugin_Name_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Plugin_Name_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_script( $this->Wordpress_Shopify, plugin_dir_url( __FILE__ ) . 'js/wp_shopify-admin.js', array( 'jquery' ), $this->version, false );

		}

	}
