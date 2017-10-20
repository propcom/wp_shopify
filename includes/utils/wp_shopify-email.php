<?

  /*
  * Email helper
  */

  class Email_Helper {

    private $email;
    private $mailer;
    private $logged_in;
    private $email_temp = 'abandoned-checkout.php';

    private $user_meta_key = '_ws_user_meta';

    public function __construct ( $logged_in, $email ) {

      $this->logged_in = $logged_in;
      $this->email = $email;
      $this->mailer = plugin_dir_path( dirname( __FILE__ ) ).'mailer/'.$this->email_temp;

    }

    public function send_email ( $subject, $contents ) {

      global $info, $image;

      $info = (object)$contents;
      $image = $this->image_uri( get_template_directory().'/assets/img/logo.png' );

      $email_obj = new stdClass();

      if( $this->email && $this->logged_in && file_exists( $this->mailer ) ) {

        $email_obj->to = $this->email;
        $email_obj->subject = $subject;

        $email_obj->body = $this->get_email_contents( $this->mailer );

        $email_obj->headers = [

          'Content-Type: text/html; charset=UTF-8',
          'From: '.get_option( 'prop_site' )['email']

        ];

        return wp_mail( $email_obj->to, $email_obj->subject, $email_obj->body, $email_obj->headers );

      }

      return false;

    }

    public function set_status ( $status = true, $unique_key = 0 ) {

      $args = [

        'search' => $this->email,
        'search_columns' => [ 'user_email' ]

      ];

      $user = new WP_User_Query( $args );

      if( !empty($user->results) ) {

        $added = add_user_meta( $user->results[0]->ID, $this->user_meta_key.'_'.$unique_key, $status );

        if($added) {

          return true;

        }

      }

      return false;

    }

    public function get_status ( $unique_key = 0 ) {

      $args = [

        'search' => $this->email,
        'search_columns' => [ 'user_email' ]

      ];

      $user = new WP_User_Query( $args );

      if( !empty($user->results) ) {

        return get_user_meta( $user->results[0]->ID, $this->user_meta_key.'_'.$unique_key );

      }

      return false;

    }

    public function get_email_contents ($temp) {

      if( is_file($temp) ) {

        ob_start();
        include_once $temp;
        return ob_get_clean();

      }

      return null;

    }

    public function image_uri ($path) {

      if( ($type = pathinfo($path, PATHINFO_EXTENSION)) ) {

        $data = file_get_contents($path);

        return 'data:image/'.$type.';base64,'.base64_encode($data);

      }

    }

  }

?>
