<?

  /*
  * Call stacking helper class, for use of queuing requests to avoid overlow exception on shopify side
  */

  class Wordpress_Shopify_Queue {

    public $stack;
    public $stackIdx;
    public $shopify;
    public $urls;
    public $currentStack;

    public function __construct ( $urls ) {

      $this->urls = $urls;
      $this->stackIdx = 0;
      $this->stack = [];

    }

    public function queue ( $callback ) {

      if( !empty($this->urls) ) {

        $resource = Wordpress_Shopify_Api::forge( $this->urls[$this->stackIdx] )->get_product();
        $this->doNext( $resource, $callback );

      }

    }

    protected function doNext ( $res, $callback ) {

      if($res && isset($res->id)) {

        $this->stack[] = $res;
        array_shift($this->urls);

        if( count($this->urls) > 0 ) {

          $this->queue($callback);

        } else {

          call_user_func($callback, $this->stack);

        }

      }

    }

  }

?>
