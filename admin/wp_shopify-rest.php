<?

  class ShopifyApi extends WP_REST_Controller {

    public $logged_in;

    public function __construct ( $logged_in ) {

      $this->logged_in = $logged_in;

    }

    public function register_routes() {

      $version = '1';
      $namespace = 'shopify/v' . $version;
      $base = 'products';

      register_rest_route( $namespace, '/' . $base, [

        [
          'methods' => WP_REST_Server::READABLE,
          'callback' => [ $this, 'get_products' ],
          'permission_callback' => [ $this, 'get_products_permissions_check' ],
          'args' => [

            'id' => [

              'validate_callback' => function ( $param, $request, $key ) {

								return is_numeric( $param );

							}

            ]

          ],

        ],

      ] );

      register_rest_route( $namespace, '/products/(?P<id>[\d]+)', [

        [
          'methods' => WP_REST_Server::READABLE,
          'callback' => [ $this, 'get_product' ],
          'permission_callback' => [ $this, 'get_products_permissions_check' ],
          'args' => [

            'id' => [

              'validate_callback' => function ( $param, $request, $key ) {

								return is_numeric( $param );

							}

            ]

          ],

        ],

      ] );

      register_rest_route( $namespace, '/collection/(?P<c_id>[\d]+)/(?P<p_count>[\d]+)/(?P<p_number>[\d]+)', [

        [
          'methods' => WP_REST_Server::READABLE,
          'callback' => [ $this, 'get_products_from' ],
          'args' => [

            'id' => [

              'validate_callback' => function ( $param, $request, $key ) {

								return is_numeric( $param );

							}

            ]

          ],

        ],

      ] );

      register_rest_route( $namespace, '/variants/(?P<id>[\d]+)', [

        [
          'methods' => WP_REST_Server::READABLE,
          'callback' => [ $this, 'get_variant' ],
          'permission_callback' => [ $this, 'get_variant_permissions_check' ],
          'args' => [

            'id' => [

              'validate_callback' => function ( $param, $request, $key ) {

								return is_numeric( $param );

							}

            ]

          ],

        ],

      ] );

    }

    /**
     * Get a list of products from Shopify
     */
    public function get_products ( $request ) {

      if( $request ) {

        $products = Wordpress_Shopify_Api::forge( ENDPOINT_PRODUCTS, [ 'collection_id' => $request->get_param('id') ], false )->get_products();

        $data = [

          'status' => 'success',
          'code' => 200,
          'data' => ( $products ? $products : null ),

        ];

      } else {

        $data = [

          'status' => 'error',
          'code' => 403,
          'message' => 'Permission forbidden',

        ];

      }

      return new WP_REST_Response( $data, 200 );

    }

    /**
     * Get a single product
     */
    public function get_product ( $request ) {

      if( $request ) {

        $product = Wordpress_Shopify_Api::forge( ENDPOINT_PRODUCT.'/'.$request->get_param('id').'.json' )->get_product();

        $data = [

          'status' => 'success',
          'code' => 200,
          'data' => ( $product ? $product : null ),

        ];

      } else {

        $data = [

          'status' => 'error',
          'code' => 403,
          'message' => 'Permission forbidden',

        ];

      }

      return new WP_REST_Response( $data, 200 );

    }

    /**
     * Gets products from collection
     */
    public function get_products_from ( $request ) {

      if( $request ) {

        $collectionId = $request->get_param('c_id');
        $productsPageNumber = $request->get_param('p_number');
        $productsPaginateCount = $request->get_param('p_count');

        $products = Wordpress_Shopify_Api::forge( ENDPOINT_PRODUCTS,
          [
            'collection_id' => $collectionId,
            'limit' => $productsPaginateCount,
            'page' => $productsPageNumber
          ]
        )->get_products();

        $data = [

          'status' => 'success',
          'code' => 200,
          'data' => $products

        ];

      } else {

        $data = [

          'status' => 'error',
          'code' => 403,
          'message' => 'Permission forbidden',

        ];

      }

      return new WP_REST_Response( $data, 200 );

    }

    /**
     * Get a single variant
     */
    public function get_variant ( $request ) {

      if( $request ) {

        $variant = Wordpress_Shopify_Api::forge( ENDPOINT_VARIANT.'/'.$request->get_param('id').'.json' )->get_variant();

        $data = [

          'status' => 'success',
          'code' => 200,
          'data' => ( $variant ? $variant : null ),

        ];

      } else {

        $data = [

          'status' => 'error',
          'code' => 403,
          'message' => 'Permission forbidden',

        ];

      }

      return new WP_REST_Response( $data, 200 );

    }

    /**
     * Check if a given request has access to get items
     */
    public function get_products_permissions_check( $request ) {

      return $this->logged_in;

    }

    /**
     * Check if a given request has access to get variant
     */
    public function get_variant_permissions_check( $request ) {

      return $this->logged_in;

    }

  }

  add_action( 'rest_api_init', function () {

    $logged_in = is_user_logged_in();

    $shopify = new ShopifyApi( $logged_in );
    $shopify->register_routes();

  } );

?>
