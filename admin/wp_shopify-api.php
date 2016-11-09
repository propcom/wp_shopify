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
    * Function: forge
    * Description: Call api to store response
    * Return: New Instance
    */
    public static function forge ( $endpoint, $query = [], $exc_handler = true ) {

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

            self::$data = self::send_get_request( self::$base_url, self::$http_args );

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
    * Function: send_get_request
    * Description: Wrapper for wp_remote_get
    * Return: Response or null on failure
    */
    private static function send_get_request ( $url, $args ) {

      $response = wp_remote_get( $url, $args );

      if( is_array( $response ) ) {

        $encode = json_decode($response['body']);

        if( !isset( $encode->errors ) ) {

          return $encode;

        } else {

          throw new Wordpress_Shopify_Api_Exception( 'API Error: '.$encode->errors );

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
