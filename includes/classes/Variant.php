<?

  /**
  * Class - Variant
  */

  class Variant {

    /*
    * @var payload
    */
    private $payload;

    /*
    * @var variant
    */
    private $variant;

    private function __construct ($payload) {

      $this->payload = $payload;
      $this->variant = null;

      if(!isset($payload['error'])) {
        $this->variant = $payload['data'];
      }

    }

    /*
    * @function get_options
    */
    public function get_options () {

      $array = [];

      if(!$this->variant) return null;

      if($this->variant->option1) $array['option_1'] = $this->variant->option1;
      if($this->variant->option2) $array['option_2'] = $this->variant->option2;
      if($this->variant->option3) $array['option_3'] = $this->variant->option3;

      return $array;
    }

    /*
    * @function get_variant
    */
    public function get_variant () {
      return $this->variant;
    }

    /*
    * @function forge
    */
    public static function forge ($payload) {
      return new static($payload);
    }

  }

?>
