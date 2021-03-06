<?

  /**
  * Class - Product_Array
  */

  class Product_Array {

    /*
    * @var payload
    */
    private $payload;

    /*
    * @var products
    */
    private $products;

    private function __construct ($payload) {

      $this->payload = $payload;
      $this->products = null;

      if(!isset($payload['error'])) {
        $this->products = $payload['data']->products;
      }

    }

    /*
    * @function get_products
    */
    public function get_products () {

      $array = [];

      if(empty($this->products)) {
        return null;
      }

      foreach($this->products as $product) {
        $object = new stdClass();
        $object->product = $product;

        $array[] = Product::forge(['data' => $object]);
      }

      return $array;
    }

    /*
    * @function filter_products
    */
    public function filter_products ($value, $property = 'id') {

      $array = [];

      if(empty($this->products)) {
        return null;
      }

      foreach($this->products as $product) {
        $vars = get_object_vars($product);

        if($vars[$property] == $value) {
          $array[] = $product;
        }
      }

      return $array;

    }

    /*
    * @function forge
    */
    public static function forge ($payload) {
      return new static($payload);
    }
  }

?>
