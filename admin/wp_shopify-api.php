<?

  /*
  * Class Name: Wordpress_Shopify_Api
  * Description: Shopify api class for returning json data from shopify api
  */

  class Wordpress_Shopify_Api {

    /*
    * Var: http_args
    * Description: WP remote get args
    */
    private static $http_args = [

      'timeout' => 30,
      'headers' => [

        'Content-Type' => 'application/json'

      ],
      'sslverify' => false,

    ];

    /*
    * Var: data
    * Description: Holder for returned data
    */
    private static $data = '';

    /*
    * Var: headers
    * Description: Holder for response headers
    */
    private static $headers = [];

    /*
    * Var: endpoint
    * Description: Holder for current endpoint being used
    */
    private static $endpoint = '';

    /*
    * Var: base_url
    * Description: Holder for base url
    */
    private static $base_url = '';

    /*
    * Var: query
    * Description: Holder for current query
    */
    private static $query = [];

    /*
    * Var: inventory
    * Description: Holder for inventory quantity
    */
    private static $inventory = [];

    /*
    * Var: track
    * Description: Track low stock on inventory quantity
    */
    private static $track = false;

    /*
    * Var: track_level
    * Description: Track stock level on inventory quantity
    */
    private static $track_level = 0;

    /*
    * Function: forge
    * Description: Call api to store response
    * Return: New Instance
    */
    public static function forge ( $endpoint, $query = [], $exc_handler = true ) {

      self::$inventory = [];
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
    * Function: get_products
    * Description: Gets all products from shop
    * Return: Products
    */
    public static function get_products () {

      return ( isset( self::$data->products ) ? self::$data->products : null );

    }

    /*
    * Function: get_product
    * Description: Gets single product from shop
    * Return: Product
    */
    public static function get_product () {

      return ( isset( self::$data->product ) ? self::$data->product : null );

    }

    /*
    * Function: get_variant
    * Description: Gets single variant from shop
    * Return: Variant
    */
    public static function get_variant () {

      return ( isset( self::$data->variant ) ? self::$data->variant : null );

    }

    /*
    * Function: get_collections
    * Description: Gets all collections from shop
    * Return: Collections
    */
    public static function get_collections () {

      return ( isset( self::$data->custom_collections ) ? self::$data->custom_collections : null );

    }

    /*
    * Function: get_collection
    * Description: Gets single collections from shop
    * Return: Collection
    */
    public static function get_collection () {

      return ( isset( self::$data->custom_collection ) ? self::$data->custom_collection : null );

    }

    /*
    * Function: get_orders
    * Description: Gets orders for single customer from shop
    * Return: Orders
    */
    public static function get_orders () {

      return ( isset( self::$data->orders ) ? self::$data->orders : null );

    }

    /*
    * Function: get_ab_checkouts
    * Description: Gets list of abandoned checkouts
    * Return: Abandoned Checkouts
    */
    public static function get_ab_checkouts () {

      return ( isset( self::$data->checkouts ) ? self::$data->checkouts : null );

    }

    /*
    * Function: get_inventory
    * Description: Gets inventory of variants
    * Params:
    *   - $all - If true will track all inventory variants...defaults to false
    *   - $track - If true will track quantity of lower than $track_level
    *   - $track_level - Inventory quantity when to confirm its low
    * Return: If track is false - Inventory Quantity - [product_id] => quantity or [product_id] => [ variant_id => 3, ... ] or if true then static
    */
    public static function get_inventory ( $all = false, $track = false, $track_level = 10 ) {

      $type = self::$data;

      self::$track = $track;
      self::$track_level = $track_level;

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
    * Function: stock_level
    * Description: Can only be used if get_inventory param $track is true
    * Return: array if low returns low or higher returns normal based off $track_level
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
    * Function: api_limit_close
    * Description: Checks if api limit is close to exceeding
    * Return: True/False
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
    * Function: send_get_request
    * Description: Wrapper for wp_remote_get
    * Return: Response or null on failure
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
    * Function: build_base_url
    * Description: Builds base url for api calls
    * Return: Base url
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
    * Function: is_options_valid
    * Description: Checks that wp options are valid and set
    * Return: True or False
    */
    private static function is_options_valid () {

      if( !get_option ('prop_shopify')['shop'] ) return false;

      if( !get_option ('prop_shopify')['api_key'] ) return false;

      if( !get_option ('prop_shopify')['pass'] ) return false;

      return true;

    }

    /*
    * Function: get_data
    * Description: Gets data returned from api
    * Return: Data
    */
    public static function get_data () {

      return self::$data;

    }

    /*
    * Function: get_url
    * Description: Gets the entire request url
    * Return: Url
    */
    public static function get_url () {

      return self::$base_url;

    }

  }

?>
