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
      'params' => 'ul',
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

    public function write_out ( $out ) {

      $html_out = file_put_contents( $this->save_as, $out );
      if( $html_out ) {

        $this->dlog( 'Saved doc', 'successfully saved docs file at '.$this->save_as );

      } else {

        $this->dlog( 'Writing issue', 'unable to write docs file.' );

      }

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

              $paramItem = trim( str_replace( '@params:', '', $match[0] ) );
              if( $paramItem != '' && substr($paramItem, 0, 1) != '@' ) {

                $paramsComment[$current][] = $paramItem;

              }

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

        $this->doc_object[ $this->objInc ][ 'params' ] = $paramsComment;

      }

      $this->objInc++;

    }

    private function set_docs_file ( $docs_object ) {

      $return = [];

      if( !empty($docs_object) ) {

        $return[] = '<div class="doc__block">';

        foreach($docs_object as $key => $value) {

          if( isset($this->html[$key]) ) {

            if( $key == 'params' && !empty($value) ) {

              $return[] = ( '<p><b>'.ucfirst($key).':</b></p>' );
              $return[] = '<'.$this->html[$key].'>';

              foreach($value[0] as $param) {

                $return[] = ( '<li>'.$param.'</li>' );

              }

              $return[] = '</'.$this->html[$key].'>';

            } else if(!is_array($value)) {

              $return[] = ( '<'.$this->html[$key].'><b>'.ucfirst($key).':</b> '.$value.'</'.$this->html[$key].'>' );

            }

          }

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

  $api_path = ( isset($argv[1]) ? $argv[1] : null );
  $doc_path = ( isset($argv[2]) ? $argv[2] : null );

  if( $api_path && $doc_path ) {

    $gen = new DocsGenerator( $api_path, $doc_path );
    $gen->generate_doc();

    $gen->write_out( $gen->get_doc_html() );

  } else {

    echo 'Error: Unable to execute, missing args - api_path or doc_path.'.PHP_EOL;

  }

?>
