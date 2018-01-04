<?

  /**
  * Class - Collection
  */

  class Collection {

    /*
    * @var payload
    */
    private $payload;

    /*
    * @var collection
    */
    private $collection;

    private function __construct ($payload) {

      $this->payload = $payload;
      $this->collection = null;

      if(!isset($payload['error'])) {
        $this->collection = $payload['data']->custom_collection;
      }

    }

    /*
    * @function get_image
    */
    public function get_image () {

      if(!isset($this->collection->image)) {
        return null;
      }

      return $this->collection->image->src;
    }

    /*
    * @function get_body
    */
    public function get_body () {
      return $this->collection->body_html;
    }

    /*
    * @function get_collection
    */
    public function get_collection () {
      return $this->collection;
    }

    /*
    * @function forge
    */
    public static function forge ($payload) {
      return new static($payload);
    }
  }

?>
