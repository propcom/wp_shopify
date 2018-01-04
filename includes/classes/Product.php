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
        $this->product = $payload['data']->product;
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
    * @function get_price
    */
    public function get_price () {

      if(!isset($this->product->variants) || empty($this->product->variants)) {
        return null;
      }

      return $this->product->variants[0]->price;
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
    * @function get_tags
    */
    public function get_tags () {

      $tags = [];

      if(!isset($this->product->tags)) {
        return null;
      }

      foreach($tags as $tag) {
        $tags[] = trim($tag);
      }

      return $tags;
    }

    /*
    * @function get_group_tags
    */
    public function get_group_tags ($group = null) {

      $groups = [];

      if(!isset($this->product->tags)) {
        return null;
      }

      $tags = explode(',', $this->product->tags);

      foreach($tags as $tag) {
        $split = explode('_', $tag);

        if($group && isset($split[1]) && $group == $split[0]) {

          $groups[trim($split[0])] = trim($split[1]);
          continue;
        }

        if(isset($split[1])) $groups[trim($split[0])] = trim($split[1]);
      }

      return $groups;
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
