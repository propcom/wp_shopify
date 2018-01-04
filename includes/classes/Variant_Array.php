<?

  /**
  * Class - Variant_Array
  */

  class Variant_Array {

    /*
    * @var payload
    */
    private $payload;

    /*
    * @var variant
    */
    private $variants;

    private function __construct ($payload) {

      $this->payload = $payload;
      $this->variants = null;

      if(!isset($payload['error'])) {
        $this->variants = $payload['data']->variants;
      }

    }

    /*
    * @function get_variants
    */
    public function get_variants () {

      $array = [];

      if(empty($this->variants)) {
        return null;
      }

      foreach($this->variants as $variant) {
        $array[] = Variant::forge(['data' => $variant]);
      }

      return $array;
    }

    /*
    * @function get_all_options
    */
    public function get_all_options () {

      $array = [];

      if(!$this->variants) return null;

      foreach($this->variants as $variant) {
        $array[] = Variant::forge(['data' => $variant])->get_options();
      }

      return $array;
    }

    /*
    * @function filter_variants
    */
    public function filter_variants ($value, $property = 'id') {

      $array = [];

      if(empty($this->variants)) {
        return null;
      }

      foreach($this->variants as $variant) {
        $vars = get_object_vars($variant);

        if($vars[$property] == $value) {
          $array[] = $variant;
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
