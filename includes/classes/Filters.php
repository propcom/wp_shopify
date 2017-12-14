<?

  /**
  * Class - Filters
  */

  class Filters {

    /*
    * @var payload
    */
    private $payload;

    /*
    * @var options
    */
    private $options;

    /*
    * @var filters
    */
    private $filters;

    public function __construct (Product_Array $products) {

      $this->payload = $products;

      foreach($this->payload->get_products() as $product) {

        foreach($product->get_options() as $option) {
          if($option->name === 'Title') continue;

          $this->options[$option->name] = $option;
        }
      }

      var_dump($this->options);
    }

    /*
    * @function get_variants
    */
    public function get_variants () {
    }
  }

?>
