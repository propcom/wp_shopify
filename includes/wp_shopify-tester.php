<?

  /*
  * Class Name: Shopify_Api_Tester
  * Description: Used to test and make sure that Shopify is still live
  */

  class Shopify_Api_Tester {

    public static function test () {

      try {

        $res = Wordpress_Shopify_Api::forge(ENDPOINT_SHOP, [])->get_data();

        if( isset( $res->shop ) ) {

          print (

            '<div class="notice notice-success is-dismissible"><p>Shop Access for '.$res->shop->name.' Granted.</p></div>'

          );

        } else {

          throw new Wordpress_Shopify_Api_Exception('Cannot connect to your shop.');

        }

      } catch (Wordpress_Shopify_Api_Exception $exception) {

        print (

          '<div class="notice notice-error is-dismissible"><p>Shop Access for '.get_option( 'prop_shopify' )['shop'].' denied. '.$exception->getMessage().'</p></div>'

        );

      }

    }

  }

?>
