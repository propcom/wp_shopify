<?

  /**
  * Class - Product
  */

  class Product {

    /*
    * @var payload
    */
    private $payload;

    /*
    * @var product
    */
    private $product;

    private function __construct ($payload) {

      $this->payload = $payload;
      $this->product = null;

      if(!isset($payload['error'])) {
        $this->product = $payload['data'];
      }

    }

    /*
    * @function get_variants
    */
    public function get_variants () {

      if(!isset($this->product->variants) || empty($this->product->variants)) {
        return null;
      }

      return $this->product->variants;
    }

    /*
    * @function get_options
    */
    public function get_options () {

      if(!isset($this->product->options) || empty($this->product->options)) {
        return null;
      }

      return $this->product->options;
    }

    /*
    * @function get_images
    */
    public function get_images () {

      if(!isset($this->product->images) || empty($this->product->images)) {
        return null;
      }

      return $this->product->images;
    }

    /*
    * @function get_main_image
    */
    public function get_main_image () {

      if(!isset($this->product->image)) {
        return null;
      }

      return $this->product->image;
    }

    /*
    * @function get_product
    */
    public function get_product () {
      return $this->product;
    }

    /*
    * @function forge
    */
    public static function forge ($payload) {
      return new static($payload);
    }
  }

?>
