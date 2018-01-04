<?

  /**
  * Class - Collection_Array
  */

  class Collection_Array {

    /*
    * @var payload
    */
    private $payload;

    /*
    * @var collections
    */
    private $collections;

    private function __construct ($payload) {

      $this->payload = $payload;
      $this->collections = null;

      if(!isset($payload['error'])) {
        $this->collections = $payload['data']->custom_collections;
      }

    }

    /*
    * @function get_collections
    */
    public function get_collections () {

      $array = [];

      if(empty($this->collections)) {
        return null;
      }

      foreach($this->collections as $collection) {
        $object = new stdClass();
        $object->custom_collection = $collection;

        $array[] = Collection::forge(['data' => $object]);
      }

      return $array;
    }

    /*
    * @function filter_collections
    */
    public function filter_collections ($value, $property = 'id') {

      $array = [];

      if(empty($this->collections)) {
        return null;
      }

      foreach($this->collections as $collection) {
        $vars = get_object_vars($collection);

        if($vars[$property] == $value) {
          $array[] = $collection;
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
