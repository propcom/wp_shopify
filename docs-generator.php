<?

  /*
  * Docs api generator for use of generating a doc for your api
  */

  class DocsGenerator {


    private $path;
    private $save_as;

    private $tags = [

      'var',
      'class',
      'function',
      'description',
      'params',
      'return',

    ];

    private $html = [

      'var' => 'p',
      'class' => 'h1',
      'function' => 'h3',
      'description' => 'p',
      'return' => 'p',

    ];

    private $objInc = 0;
    private $doc_object = [];
    private $finalMarkup = null;

    public function __construct ( $path, $save_as ) {

      $this->path = ( dirname( __FILE__ ).$path );
      $this->save_as = $save_as;

    }

    public function generate_doc () {

      $this->dlog( 'Reading script', $this->path );

      try {

        $script = $this->get_phpFile();
        if( $script ) {

          $this->get_parsed( $script );

          if( !empty($this->doc_object) ) {

            $this->dlog( 'Document', 'generating docs file' );

            $this->finalMarkup = '<div class="doc">';

            foreach($this->doc_object as $object) {

              $markup = $this->set_docs_file( $object );
              if( !empty($markup) ) $this->finalMarkup .= $markup;

            }

            $this->finalMarkup .= '</div>';

          }

        }

      } catch (Exception $ex) {

        die( $ex->getMessage() );

      }

    }

    public function get_doc_html () {

      if( $this->finalMarkup ) {

        return $this->finalMarkup;

      }

      return '';

    }

    private function get_parsed ( $script ) {

      $comments = [];
      $readComments = false;
      $current = 0;

      $lines = explode( PHP_EOL, $script );

      if( !empty($lines) ) {

        foreach($lines as $idx => $line) {

          if( strrpos($line, '/*') ) $readComments = true;
          if( strrpos($line, '*/') ) {

            $current++;
            $readComments = false;

          }

          if( $readComments ) {

            $comments[$current][$idx] = $line;
            $this->dlog( 'Parsing line '.$idx, 'creating comment blocks' );

          }

        }

      }

      if( !empty($comments) ) {

        foreach($comments as $comment) {

          $this->parse_tags( $comment );

        }

      }

    }

    private function parse_tags ( $comments ) {

      $paramsComment = [];
      $isParamTag = false;

      $current = 0;

      if( !empty($comments) ) {

        // Blocks of commenting
        foreach($comments as $line => $comment) {

          $regex = preg_match( '/@\w+\:(.*)|\-(.*)/i', $comment, $match );
          if( $regex && isset($match[0]) ) {

            // Below handles params comments
            if( strrpos($match[0], '@params:') > -1 ) $isParamTag = true;
            if( strrpos($match[0], '@') ) {

              $current++;
              $isParamTag = false;

            }

            if( $isParamTag ) {

              $paramsComment[$current][] = $match[0];
              $this->dlog( 'Extracting', 'building params comments' );

            }

            // Below handles rest of comments
            preg_match( '/@(.*?)\:/i', $match[0], $tag );
            if( isset($tag[1]) && in_array( $tag[1], $this->tags ) ) {

              $text = explode(':', $match[0])[1];

              if( $text ) {

                $this->doc_object[ $this->objInc ][ $tag[1] ] = trim($text);
                $this->dlog( 'Object', 'building docs object' );

              }

            }

          }

        }

      }

      $this->objInc++;

    }

    private function set_docs_file ( $docs_object ) {

      $return = [];

      if( !empty($docs_object) ) {

        $return[] = '<div class="doc__block">';

        foreach($docs_object as $key => $value) {

          if( isset($this->html[$key]) ) $return[] = ( '<'.$this->html[$key].'><b>'.ucfirst($key).':</b> '.$value.'</'.$this->html[$key].'>' );

        }

        $return[] = '</div>';

      }

      return join('', $return);

    }

    private function get_phpFile () {

      if( file_exists( $this->path ) ) {

        $contents = file_get_contents( $this->path );
        if( !$contents ) {

          throw new Exception ('Error: Could read php script');

        }

        return $contents;

      }

      return null;

    }

    private function dlog ( $procc_name, $message ) {

      if( $procc_name ) {

        echo $procc_name.': '.$message.PHP_EOL;

      }

    }

  }

  $gen = new DocsGenerator( '/admin/wp_shopify-api.php', 'api-docs.html' );
  $gen->generate_doc();

  var_dump($gen->get_doc_html());

?>
