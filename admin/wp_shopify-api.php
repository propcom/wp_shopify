<?

  /*
  * @class: Wordpress_Shopify_Api
  * @description: Shopify api class for returning json data from shopify api
  */

  class Wordpress_Shopify_Api {

    /*
    * @var: http_args
    * @description: WP remote get args
    */
    private $http_args = [

      'timeout' => 30,
      'redirection' => 3,
      'headers' => [

        'Content-Type' => 'application/json'

      ],
      'sslverify' => false,

    ];

    /*
    * @var: data
    * @description: Holder for returned data
    */
    private $data = '';

    /*
    * @var: headers
    * @description: Holder for response headers
    */
    private $headers = [];

    /*
    * @var: endpoint
    * @description: Holder for current endpoint being used
    */
    private $endpoint = '';

    /*
    * @var: base_url
    * @description: Holder for base url
    */
    private $base_url = '';

    /*
    * @var: query
    * @description: Holder for current query
    */
    private $query = [];

    /*
    * @var: inventory
    * @description: Holder for inventory quantity
    */
    private $inventory = [];

    /*
    * @var: track
    * @description: Track low stock on inventory quantity
    */
    private $track = false;

    /*
    * @var: track_level
    * @description: Track stock level on inventory quantity
    */
    private $track_level = 0;

    /*
    * @function __construct
    */
    protected function __construct ( $endpoint, $query, $exc_handler, $type, $args ) {

      $this->init( $endpoint, $query, $exc_handler, $type, $args );

    }

    /*
    * @function init
    */
    protected function init ( $endpoint, $query, $exc_handler, $type, $args ) {

      $this->inventory = [];
      $this->track_level = ( get_option('prop_shopify')['inventory_level'] ? get_option('prop_shopify')['inventory_level'] : 0 );

      $this->query = $query;
      $this->endpoint = $endpoint;

      $this->http_args = array_merge( $this->http_args, $args );

      if( !empty($query) ) {

        $this->base_url = $this->build_base_url().$endpoint.'?'.urldecode(http_build_query($query));

      } else {

        $this->base_url = $this->build_base_url().$endpoint;

      }

      if( $this->is_options_valid() ) {

        if( $exc_handler ) {

          try {

            $this->data = ( $type == 'GET' ? $this->send_get_request( $this->base_url, $this->http_args, true ) : $this->send_post_request( $this->base_url, $this->http_args, true ) );

          } catch (Wordpress_Shopify_Api_Exception $exception) {

            $this->data = $exception->getMessage();

          }

        } else {

          $this->data = ( $type == 'GET' ? $this->send_get_request( $this->base_url, $this->http_args ) : $this->send_post_request( $this->base_url, $this->http_args ) );

        }

      }

    }

    /*
    * @function: get_products
    * @description: Gets all products from shop
    * @return: Products
    */
    public function get_products () {

      return ( isset( $this->data->products ) ? $this->data->products : null );

    }

    /*
    * @function: get_product
    * @description: Gets single product from shop
    * @return: Product
    */
    public function get_product () {

      return ( isset( $this->data->product ) ? $this->data->product : null );

    }

    /*
    * @function: get_variant
    * @description: Gets single variant from shop
    * @return: Variant
    */
    public function get_variant () {

      return ( isset( $this->data->variant ) ? $this->data->variant : null );

    }

    /*
    * @function: get_collections
    * @description: Gets all collections from shop
    * @return: Collections
    */
    public function get_collections () {

      return ( isset( $this->data->custom_collections ) ? $this->data->custom_collections : null );

    }

    /*
    * @function: get_collection
    * @description: Gets single collections from shop
    * @return: Collection
    */
    public function get_collection () {

      return ( isset( $this->data->custom_collection ) ? $this->data->custom_collection : null );

    }

    /*
    * @function: get_orders
    * @description: Gets orders for single customer from shop
    * @return: Orders
    */
    public function get_orders () {

      return ( isset( $this->data->orders ) ? $this->data->orders : null );

    }

    /*
    * @function: get_ab_checkouts
    * @description: Gets list of abandoned checkouts
    * @return: Abandoned Checkouts
    */
    public function get_ab_checkouts () {

      return ( isset( $this->data->checkouts ) ? $this->data->checkouts : null );

    }

    /*
    * @function: get_customer
    * @description: Gets single customer profile, based on endpoint
    * @return: Customer
    */
    public function get_customer () {

      $customer = null;

      if( isset( $this->data->customers ) ) {

        $customer = $this->data->customers[0];

      } elseif ( isset( $this->data->customer ) ) {

        $customer = $this->data->customer;

      }

      return $customer;

    }

    /*
    * @function: get_multipass_token
    * @description: Generates access token for multipass
    * @return: Token
    */
    public function get_multipass_token ( $secret, $params ) {

      if( isset($params['email']) ) {

        $multipass = new ShopifyMultipass( $secret );
        $token = $multipass->generate_token( $params );

        return $token;

      }

      return null;

    }

    /*
    * @function: get_inventory
    * @description: Gets inventory of variants
    * @params:
    *   - $all - If true will track all inventory variants...defaults to false
    *   - $track - If true will track quantity of lower than $track_level
    *   - $track_level - Inventory quantity when to confirm its low
    * @return: If track is false - Inventory Quantity - [product_id] => quantity or [product_id] => [ variant_id => 3, ... ] or if true then static
    */
    public function get_inventory ( $all = false, $track = false ) {

      $type = $this->data;

      $this->track = $track;

      if( isset($type->products) ) {

        $this->inventory = [];

        foreach($type->products as $product) {

          if($all) {

            foreach($product->variants as $vIdx => $variant) {

              if( count($product->variants) > 1 ) {

                $this->inventory[$product->id][$variant->id] = $variant->inventory_quantity;

              } else {

                $this->inventory[$product->id] = $product->variants[0]->inventory_quantity;

              }

            }

          } else {

            $this->inventory[$product->id] = $product->variants[0]->inventory_quantity;

          }

        }

      } elseif ( isset($type->product) ) {

        $product = $type->product;

        if($all) {

          foreach($product->variants as $vIdx => $variant) {

            if( count($product->variants) > 1 ) {

              $this->inventory[$product->id][$variant->id] = $variant->inventory_quantity;

            } else {

              $this->inventory[$product->id] = $product->variants[0]->inventory_quantity;

            }

          }

        } else {

          $this->inventory[$product->id] = $product->variants[0]->inventory_quantity;

        }

      } elseif ( isset($type->variants) ) {

        $variants = $type->variants;

        if($all) {

          foreach($variants as $vIdx => $variant) {

            $this->inventory[$variant->id] = $variant->inventory_quantity;

          }

        } else {

          $this->inventory[$variant->id] = $variants[0]->inventory_quantity;

        }

      } elseif ( isset($type->variant) ) {

        $this->inventory[$type->variant->id] = $type->variant->inventory_quantity;

      }

      return ( $track ? $this : $this->inventory );

    }

    /*
    * @function: stock_level
    * @description: Can only be used if get_inventory param $track is true
    * @return: array if low returns low or higher returns normal based off $track_level
    */
    public function stock_level () {

      $return = [];

      if( $this->track ) {

        foreach($this->inventory as $iIdx => $inventory) {

          if( is_array($inventory) ) {

            foreach($inventory as $qIdx => $quantity) {

              if( $quantity == 0 ) {

                $return[$iIdx][$qIdx] = 'none';

              } elseif( $quantity <= $this->track_level ) {

                $return[$iIdx][$qIdx] = 'low';

              } else {

                $return[$iIdx][$qIdx] = 'normal';

              }

            }

          } else {

            if( $inventory == 0 ) {

              $return[$iIdx] = 'none';

            } elseif( $inventory <= $this->track_level ) {

              $return[$iIdx] = 'low';

            } else {

              $return[$iIdx] = 'normal';

            }

          }

        }

      }

      return $return;

    }

    /*
    * @function: search_products
    * @description: Searches products based off - Title, tags, vendor and type
    * @return: Products
    */
    public function search_products ( $search_term ) {

      $query = [];
      $products = $this->get_products();
      $s_query = explode(' ', strtolower($search_term));

      if( !empty($products) ) {

        foreach($products as $product) {

          // Titles
          $words = explode(' ', strtolower($product->title));
          foreach($s_query as $s) {

            if( in_array($s, $words) && !isset($query[$product->id]) ) $query[$product->id] = $product;

          }

          // Tags
          $tags = explode(',', strtolower($product->tags));
          foreach($s_query as $s) {

            if( in_array($s, $tags) && !isset($query[$product->id]) ) $query[$product->id] = $product;

          }

          // Vendor
          $vendor = explode(',', strtolower($product->vendor));
          foreach($s_query as $s) {

            if( in_array($s, $vendor) && !isset($query[$product->id]) ) $query[$product->id] = $product;

          }

          // Type
          $type = explode(',', strtolower($product->product_type));
          foreach($s_query as $s) {

            if( in_array($s, $type) && !isset($query[$product->id]) ) $query[$product->id] = $product;

          }

        }

      }

      return $query;

    }

    /*
    * @function: api_limit_close
    * @description: Checks if api limit is close to exceeding
    * @return: True/False
    */
    public function api_limit_close () {

      $limit_header = 'http_x_shopify_shop_api_call_limit';

      if( isset( $this->headers[$limit_header] ) ) {

        $max = intval( explode('/', $this->headers[$limit_header])[1] );
        $calls = intval( explode('/', $this->headers[$limit_header])[0] );

        if( $calls >= ($max - 1) ) {

          return true;

        }

      }

      return false;

    }

    /*
    * @function: send_get_request
    * @description: Wrapper for wp_remote_get
    * @params:
    *   - $url The url of the resource you need to access
    *   - $args Array of headers to send with request
    *   - $excep Weather to throw exception on error or not
    * @return: Response or null on failure
    */
    private function send_get_request ( $url, $args, $excep = false ) {

      $response = wp_remote_get( $url, $args );

      if( is_array( $response ) ) {

        $this->headers = $response['headers'];
        $encode = json_decode($response['body']);

        if( !isset( $encode->errors ) ) {

          return $encode;

        } else {

          if($excep) {

            throw new Wordpress_Shopify_Api_Exception( 'API Error: '.$encode->errors );

          } else {

            return 'API Error: '.$encode->errors;

          }

        }

      }

      return null;

    }

    /*
    * @function: send_post_request
    * @description: Wrapper for wp_remote_post
    * @params:
    *   - $url The url of the resource you need to access
    *   - $args Array of headers to send with request
    *   - $excep Weather to throw exception on error or not
    * @return: Response or null on failure
    */
    private function send_post_request ( $url, $args, $excep = false ) {

      $response = wp_remote_post( $url, $args );

      if( is_array( $response ) ) {

        $this->headers = $response['headers'];
        $encode = json_decode($response['body']);

        if( !isset( $encode->errors ) ) {

          return $encode;

        } else {

          if($excep) {

            throw new Wordpress_Shopify_Api_Exception( 'API Error: '.$encode->errors );

          } else {

            return 'API Error: '.$encode->errors;

          }

        }

      }

      return null;

    }

    /*
    * @function: get_request_headers
    * @description: Current request headers in response
    * @return: Headers
    */
    public function get_request_headers () {

      return $this->headers;

    }

    /*
    * @function: build_base_url
    * @description: Builds base url for api calls
    * @return: Base url
    */
    public function build_base_url () {

      if( $this->is_options_valid() ) {

        $base_url = [

          'https://',
          get_option( 'prop_shopify' )['api_key'],
          ':',
          get_option( 'prop_shopify' )['pass'],
          '@',
          get_option( 'prop_shopify' )['shop'].'.myshopify.com',

        ];

        return join( '', $base_url );

      }

    }

    /*
    * @function: is_options_valid
    * @description: Checks that wp options are valid and set
    * @return: True or False
    */
    private function is_options_valid () {

      if( !get_option ('prop_shopify')['shop'] ) return false;

      if( !get_option ('prop_shopify')['api_key'] ) return false;

      if( !get_option ('prop_shopify')['pass'] ) return false;

      return true;

    }

    /*
    * @function: get_data
    * @description: Gets data returned from api
    * @return: Data
    */
    public function get_data () {

      return $this->data;

    }

    /*
    * @function: get_url
    * @description: Gets the entire request url
    * @return: Url
    */
    public function get_url () {

      return $this->base_url;

    }

    /*
    * @function: forge
    * @description: Call api to store response
    * @return: New Instance
    */
    public static function forge ( $endpoint, $query = [], $exc_handler = true, $type = 'GET', $args = [] ) {

      return new static( $endpoint, $query, $exc_handler, $type, $args );

    }

  }

?>
