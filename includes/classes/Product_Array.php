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
        $this->products = $payload['data'];
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
        $array[] = Product::forge(['data' => $product]);
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
