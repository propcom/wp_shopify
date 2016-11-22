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
    private static $http_args = [

      'timeout' => 30,
      'headers' => [

        'Content-Type' => 'application/json'

      ],
      'sslverify' => false,

    ];

    /*
    * @var: data
    * @description: Holder for returned data
    */
    private static $data = '';

    /*
    * @var: headers
    * @description: Holder for response headers
    */
    private static $headers = [];

    /*
    * @var: endpoint
    * @description: Holder for current endpoint being used
    */
    private static $endpoint = '';

    /*
    * @var: base_url
    * @description: Holder for base url
    */
    private static $base_url = '';

    /*
    * @var: query
    * @description: Holder for current query
    */
    private static $query = [];

    /*
    * @var: inventory
    * @description: Holder for inventory quantity
    */
    private static $inventory = [];

    /*
    * @var: track
    * @description: Track low stock on inventory quantity
    */
    private static $track = false;

    /*
    * @var: track_level
    * @description: Track stock level on inventory quantity
    */
    private static $track_level = 0;

    /*
    * @function: forge
    * @description: Call api to store response
    * @return: New Instance
    */
    public static function forge ( $endpoint, $query = [], $exc_handler = true ) {

      self::$inventory = [];
      self::$track_level = ( get_option('prop_shopify')['inventory_level'] ? get_option('prop_shopify')['inventory_level'] : 0 );

      self::$query = $query;
      self::$endpoint = $endpoint;

      if( !empty($query) ) {

        self::$base_url = self::build_base_url().$endpoint.'?'.urldecode(http_build_query($query));

      } else {

        self::$base_url = self::build_base_url().$endpoint;

      }

      if( self::is_options_valid() ) {

        if( $exc_handler ) {

          try {

            self::$data = self::send_get_request( self::$base_url, self::$http_args, true );

          } catch (Wordpress_Shopify_Api_Exception $exception) {

            self::$data = $exception->getMessage();

          }

        } else {

          self::$data = self::send_get_request( self::$base_url, self::$http_args );

        }

      }

      return new static();

    }

    /*
    * @function: get_products
    * @description: Gets all products from shop
    * @return: Products
    */
    public static function get_products () {

      return ( isset( self::$data->products ) ? self::$data->products : null );

    }

    /*
    * @function: get_product
    * @description: Gets single product from shop
    * @return: Product
    */
    public static function get_product () {

      return ( isset( self::$data->product ) ? self::$data->product : null );

    }

    /*
    * @function: get_variant
    * @description: Gets single variant from shop
    * @return: Variant
    */
    public static function get_variant () {

      return ( isset( self::$data->variant ) ? self::$data->variant : null );

    }

    /*
    * @function: get_collections
    * @description: Gets all collections from shop
    * @return: Collections
    */
    public static function get_collections () {

      return ( isset( self::$data->custom_collections ) ? self::$data->custom_collections : null );

    }

    /*
    * @function: get_collection
    * @description: Gets single collections from shop
    * @return: Collection
    */
    public static function get_collection () {

      return ( isset( self::$data->custom_collection ) ? self::$data->custom_collection : null );

    }

    /*
    * @function: get_orders
    * @description: Gets orders for single customer from shop
    * @return: Orders
    */
    public static function get_orders () {

      return ( isset( self::$data->orders ) ? self::$data->orders : null );

    }

    /*
    * @function: get_ab_checkouts
    * @description: Gets list of abandoned checkouts
    * @return: Abandoned Checkouts
    */
    public static function get_ab_checkouts () {

      return ( isset( self::$data->checkouts ) ? self::$data->checkouts : null );

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
    public static function get_inventory ( $all = false, $track = false ) {

      $type = self::$data;

      self::$track = $track;

      if( isset($type->products) ) {

        self::$inventory = [];

        foreach($type->products as $product) {

          if($all) {

            foreach($product->variants as $vIdx => $variant) {

              if( count($product->variants) > 1 ) {

                self::$inventory[$product->id][$variant->id] = $variant->inventory_quantity;

              } else {

                self::$inventory[$product->id] = $product->variants[0]->inventory_quantity;

              }

            }

          } else {

            self::$inventory[$product->id] = $product->variants[0]->inventory_quantity;

          }

        }

      } elseif ( isset($type->product) ) {

        $product = $type->product;

        if($all) {

          foreach($product->variants as $vIdx => $variant) {

            if( count($product->variants) > 1 ) {

              self::$inventory[$product->id][$variant->id] = $variant->inventory_quantity;

            } else {

              self::$inventory[$product->id] = $product->variants[0]->inventory_quantity;

            }

          }

        } else {

          self::$inventory[$product->id] = $product->variants[0]->inventory_quantity;

        }

      } elseif ( isset($type->variants) ) {

        $variants = $type->variants;

        if($all) {

          foreach($variants as $vIdx => $variant) {

            self::$inventory[$variant->id] = $variant->inventory_quantity;

          }

        } else {

          self::$inventory[$variant->id] = $variants[0]->inventory_quantity;

        }

      } elseif ( isset($type->variant) ) {

        self::$inventory[$type->variant->id] = $variant->inventory_quantity;

      }

      return ( $track ? new static() : self::$inventory );

    }

    /*
    * @function: stock_level
    * @description: Can only be used if get_inventory param $track is true
    * @return: array if low returns low or higher returns normal based off $track_level
    */
    public static function stock_level () {

      $return = [];

      if( self::$track ) {

        foreach(self::$inventory as $iIdx => $inventory) {

          if( is_array($inventory) ) {

            foreach($inventory as $qIdx => $quantity) {

              if( $quantity == 0 ) {

                $return[$iIdx][$qIdx] = 'none';

              } elseif( $quantity <= self::$track_level ) {

                $return[$iIdx][$qIdx] = 'low';

              } else {

                $return[$iIdx][$qIdx] = 'normal';

              }

            }

          } else {

            if( $inventory == 0 ) {

              $return[$iIdx] = 'none';

            } elseif( $inventory <= self::$track_level ) {

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
    * @function: api_limit_close
    * @description: Checks if api limit is close to exceeding
    * @return: True/False
    */
    public static function api_limit_close () {

      $limit_header = 'http_x_shopify_shop_api_call_limit';

      if( isset( self::$headers[$limit_header] ) ) {

        $max = intval( explode('/', self::$headers[$limit_header])[1] );
        $calls = intval( explode('/', self::$headers[$limit_header])[0] );

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
    private static function send_get_request ( $url, $args, $excep = false ) {

      $response = wp_remote_get( $url, $args );

      if( is_array( $response ) ) {

        self::$headers = $response['headers'];
        $encode = json_decode($response['body']);

        if( !isset( $encode->errors ) ) {

          return $encode;

        } else {

          if($excep) {

            throw new Wordpress_Shopify_Api_Exception( 'API Error: '.implode($encode->errors) );

          } else {

            return 'API Error: '.implode($encode->errors);

          }

        }

      }

      return null;

    }

    /*
    * @function: build_base_url
    * @description: Builds base url for api calls
    * @return: Base url
    */
    public static function build_base_url () {

      if( self::is_options_valid() ) {

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
    private static function is_options_valid () {

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
    public static function get_data () {

      return self::$data;

    }

    /*
    * @function: get_url
    * @description: Gets the entire request url
    * @return: Url
    */
    public static function get_url () {

      return self::$base_url;

    }

  }

?>
