<?php

  /*
  * @class: WP_Shopify
  * @description: Front end class used to communicate to api
  */

  class WP_Shopify {

    /*
    * @var endpoint
    */
    private $endpoint;

    /*
    * @var params
    */
    private $params;

    /*
    * @var segments
    */
    private $segments;

    /*
    * @var instance
    */
    private $instance;

    protected function __construct ($endpoint = ENDPOINT_SHOP, $params = []) {

      $this->endpoint = $endpoint;
      $this->params = $params;
      $this->segments = explode('/', $this->endpoint);

      $this->instance = Wordpress_Shopify_Api::forge($endpoint, $params);
    }

    /*
    * @return Object instance
    */
    public function instance () {
      if(empty($this->segments)) return null;

      if(in_array('shop.json', $this->segments)) return $this->instance->get_data();

      if(in_array('products', $this->segments)) return $this->instance->product();
      if(in_array('products.json', $this->segments)) return $this->instance->products();

      if(in_array('variants', $this->segments)) return $this->instance->variant();
      if(in_array('variants.json', $this->segments)) return $this->instance->variants();

      if(in_array('custom_collections', $this->segments)) return $this->instance->collection();
      if(in_array('custom_collections.json', $this->segments)) return $this->instance->collections();

      return null;
    }

    /*
    * @return filters
    */
    public function get_filters () {
      if(empty($this->segments) || !in_array('products.json', $this->segments)) return null;

      $filters = new Filters($this->instance->products());
    }

    /*
    * @return product_id
    */
    public static function get_product_from_uri () {
      $product_id = null;
      $url_segments = explode('/', $_SERVER['REQUEST_URI']);

      if(empty($url_segments)) return null;

      // filter segments
      foreach($url_segments as $segment) {
        if($segment == '') continue;

        if(is_numeric($segment)) {

          $product_id = intval($segment);
          break;
        }
      }

      return $product_id;
    }

    /*
    * @return Standard object
    */
    public function get_data () {
      return $this->instance->get_data();
    }

    /*
    * @return static instance
    */
    public static function forge ($endpoint, $params = []) {
      return new static($endpoint, $params);
    }
  }

?>
