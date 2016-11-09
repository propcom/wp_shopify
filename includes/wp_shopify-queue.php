<?

  /*
  * Call stacking helper class, for use of queuing requests to avoid overlow exception on shopify side
  */

  class Wordpress_Shopify_Queue {

    public $stack;
    public $stackIdx;
    public $urls;
    public $query;
    public $res_type;

    public function __construct ( $urls, $query = [], $res_type = ['get_product'] ) {

      $this->urls = $urls;
      $this->query = $query;
      $this->res_type = $res_type;
      $this->stackIdx = 0;
      $this->stack = [];

    }

    public function queue ( $callback ) {

      if( !empty($this->urls) ) {

        $resource = Wordpress_Shopify_Api::forge( $this->urls[$this->stackIdx], $this->query[$this->stackIdx] );

        $this->doNext( call_user_func( [$resource, $this->res_type[$this->stackIdx] ] ), $callback );

      } else {

        call_user_func($callback, $this->stack);

      }

    }

    protected function doNext ( $res, $callback ) {

      if( isset($res->id) || isset($res[0]) ) {

        if( is_array($res) ) {

          $this->stack = $res;

        } else {

          $this->stack[] = $res;

        }

        if( count($this->urls) > 0 ) {

          array_shift($this->urls);
          array_shift($this->query);

          $this->queue($callback);

        }

      }

    }

  }

?>
