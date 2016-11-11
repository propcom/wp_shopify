<?

  /*
  * Class Name: Wordpress_Shopify_Api_Exception
  * Description: Custom exception handler
  */

  class Wordpress_Shopify_Api_Exception extends Exception {

    public function __construct ($message, $code = 0, Exception $previous = null) {

      parent::__construct($message, $code, $previous);

    }

    public function __toString () {

      return __CLASS__.': [{$this->code}]: {$this->message}\n';

    }

    public function scriptAlert () {

      $script = [

        '<script>',
        'alert(Error code: {$this->code} : {$this->message});',
        '</script>'

      ];

      return join(' ', $script);

    }

    public function consoleLog () {

      $script = [

        '<script>',
        'console.error(Error code: {$this->code} : {$this->message});',
        '</script>'

      ];

      return join(' ', $script);

    }

  }

?>
