<?php
/**
 * Framework.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Framework
 *
 * Provides a core application container for normalizing global client request streams,
 * rendering secure content representations, creating system URLs, managing contextual dynamic session parameters,
 * and isolating deep state comparisons within array schemas.
 *
 * @designPattern Singleton
 *
 * @todo Declare standard modern explicit visibility attributes (public/protected/private) over static fields.
 * @todo Add concrete native strict type-hinting declarations for input tracking properties and functional scalar outcomes.
 * @todo Deprecate long-lost fallback operations handling ancient configuration states such as `get_magic_quotes_gpc()`.
 * @todo Abstract the static global instance generator (`Framework::g_global()`) with a dependency inversion or PSR container interface standard.
 */
class Framework extends Framework_Data {
   /**
    * @var Framework|bool Cached internal instance pointer containing the active application container context, or false.
    */
   static $class = false;

   /**
    * @var array Standardized input properties array capturing sanitized GET metadata parameters.
    */
   static $POST = array();

   /**
    * @var array Standardized input properties array capturing sanitized POST metadata parameters.
    */
   static $GET = array();

   /**
    * @var array Consolidated input properties mapping containing combined data payload contexts.
    */
   static $REQUEST = array();

   /**
    * @var string Active module component routing address selector indicator.
    */
   static $com = '';

   /**
    * @var string Virtual system pathway mapping reference variable directory key string.
    */
   static $virtualdir = '';

   /**
    * @var bool Operational logic flag indicator tracing structural back-navigation state requests.
    */
   static $going_back = false;

   /**
    * @var array State calculation array containing intermediate dynamic lookup variables fields.
    */
   static $GET_array = array();

   //   static $GET_raw = '', $GET_array = array();

   /**
    * Framework constructor.
    *
    * Normalizes systemic parameters mapping input variables fields across local parameters instances.
    */
   function __construct() {
      $this->GET = array();
      $this->POST = array();
      $this->REQUEST = array();
      $this->RSA = array();
      $this->form = 0;
      $this->virtualdir = false;

      $this->_request_normalize();

      self::$class = $this;
   }

   /**
    * Resolves and supplies the standard active application management singleton configuration instance layer.
    *
    * @return Framework Active centralized system controller instance tracking properties mappings.
    */
   static function g_global() {
      if(self::$class == false) {
         self::$class = new Framework;
      }
      return self::$class;
   }

   /**
    * Packs arbitrary object properties states arrays into clean escaped text streams formats configurations.
    *
    * @param mixed $data Content layout payload context definitions targeted for structural conversion.
    * @return string Escaped JSON notation metadata character stream sequence string format.
    */
   static function json_string($data) {
      return addslashes(json_encode($data));
   }

   /**
    * Standardizes multi-byte prose text strings to fit within safety boundaries while preserving safe HTML character entities templates.
    *
    * @param string $string Raw prose description target character sequence containing unsafe text blocks.
    * @param int|bool $lenght Optional capacity restriction integer limit definition parameter. Defaults to false.
    * @return string Normalized presentation output string format containing encoded safety symbols.
    *
    * @todo Fix the variable designation typo from `$lenght` over to standard standard naming spelling conventions `$length`.
    */
   static function output_string_html($string, $lenght = false) {
      if( $lenght && strlen($string) > $lenght ) {
         $substring = substr($string,0, $lenght);
         $cut_pos = strrpos($substring,' ');
         if( $cut_pos == 0 ) {
             $cut_pos = strrpos($substring,"\n");
             if( $cut_pos == 0 ) {
                $cut_pos = $lenght;
             }
         }
         $substring = substr($substring,0,$cut_pos).'...';
//          return htmlspecialchars($substring);
         return htmlentities($substring,  ENT_COMPAT, 'UTF-8', false );
      } else {
          // return htmlspecialchars($substring);
         return htmlentities($string,  ENT_COMPAT, 'UTF-8', false );
      }
   }

   /**
    * Sanitizes alphanumeric descriptions converting linebreaks to HTML break lines templates variables.
    *
    * @param string $string Target tracking prose text context definition input sequence.
    * @param bool $nl2br Option switch rule enforcing active convert behaviors tracking elements transformations. Defaults to false.
    * @return string Presentation template output containing transformed safe textual data streams components.
    *
    * @todo Remove the unused functional arguments switch tracking configurations (`$nl2br`) or implement its logic structure fully.
    */
   static function output_string($string, $nl2br = false) {
       return nl2br(htmlentities($string,  ENT_COMPAT, 'UTF-8', false ));
     // return htmlspecialchars($string);
   }

   /**
    * Dispatches server relocation headers terminating further instructions streams actions workflows directly.
    *
    * @param string $page Target location address URI mapping pathway string string notation format.
    * @return void
    *
    * @todo Safely resolve crash context defects caused by referencing missing instance method endpoints (`self::put_js_redirect`).
    */
   static function redirect($page) {
      header('Location: ' . $page, true);
      self::put_js_redirect($page);
      exit();
   }

   /**
    * Assesses validation verification routines analyzing variable inputs parameters to detect valid entities data components.
    *
    * @param mixed $input Dynamic tracking model variable configuration properties input checked for validation rules.
    * @return bool Compliance validation verification analysis indicator outcome code.
    */
   static function not_null($input) {
      if ( !$input ) {
         return false;
      } elseif (is_array($input)) {
         if (sizeof($input) > 0) return true;
         else return false;
      } elseif ( is_object($input) ) {
         return true;
      } elseif (($input != '') && (strlen(trim($input)) > 0) && (strtolower($input) != 'null')) {
         return true;
      }
      return false;
   }

   /**
    * Evaluates emptiness tracking indicators against specified evaluation targets.
    *
    * @param mixed $input Object or dynamic target instance parameter evaluated against empty rules definitions.
    * @return bool True if evaluated target is confirmed empty, otherwise false.
    */
   static function is_null($input) {
      return !self::not_null($input);
   }

   /**
    * Compiles complex navigation address locations lines converting dynamic arguments objects parameters lists arrays maps.
    *
    * @param string $component Targeted module context component routing address. Defaults to an empty string.
    * @param array|string $arguments Supplementary metadata parameters choices mapping details array or text list. Defaults to an empty string.
    * @param string $connection Cryptographic target transport rule protocol label standard option context. Defaults to 'NONSSL'.
    * @param bool $add_session_id Structural option enforcing custom parameters session indicators tags injection. Defaults to true.
    * @param bool $seo URL formatting override guidelines switch enabling rewrite patterns. Defaults to false.
    * @return string Assembled relative web address address locator route string definition line format.
    *
    * @todo Clean up and fix code logic crashes caused by calling missing procedural helper parameters targets (`self::__prepare_params`).
    * @todo Eliminate standard unused argument settings variables from system interfaces (`$connection`, `$add_session_id`, `$seo`).
    */
   function make_link($component = '', $arguments = '', $connection = 'NONSSL', $add_session_id = true, $seo = false) {

      if( $this->not_null($component) ) {
         $parameters_array_raw = array('com' => $component);
      } else {
         $parameters_array_raw = array('com' => DEFAULT_COM);
      }

      if ($this->not_null($arguments)) {
         if( is_array($arguments) ) {
            unset($arguments['com']);
            $parameters_array_raw = array_merge($parameters_array_raw, $arguments);
         } else {
            $parameters_array_raw[] = self::__prepare_params($arguments);
         }
      }

      $parameters_array = array();
      foreach($parameters_array_raw as $key => $val ) {
          if( is_array($val) ) {
              foreach($val as $key2 => $val2) {
                  $parameters_array[] = urlencode($key.'['.$key2.']') . '=' . urlencode($val2);
              }
          } else {
             $parameters_array[] = urlencode($key) . '=' . urlencode($val);
          }
      }


      return 'index.php?' . implode('&', $parameters_array);
   }

   /**
    * Re-assembles a mirrored link replica tracking the literal location context processing state parameters maps.
    *
    * @return string System route index link path reference string definition format.
    */
   function self_link() {
      return $this->make_link($this->com, $this->GET);
   }

   /**
    * Generates a filtered subsets matrix capturing active GET properties structures elements.
    *
    * @param array|string $elements Text property checklist containing filter criteria arrays strings markers. Defaults to an empty string.
    * @param bool $mode Inversion switch matching filter parameters arrays criteria evaluation algorithms. Defaults to false.
    * @return array Subset collection array matching specific parameter selections settings layout.
    */
   function make_get ($elements = '', $mode = false) {

      if ( !is_array($elements)) {
         if( $mode ) {
            $elements = array($elements);
         } else {
            $elements = explode(',', $elements);
         }
      }

      $elements = array_merge($elements, array('com'));

      $this->GET_array = array();
      foreach ($this->GET as $key => $val) {
         if ( $mode == in_array($key,$elements)  )
         $this->GET_array[$key] = $val;
      }

      return $this->GET_array;
   }

   /**
    * Deconstructs serialized character string chains from localized dynamic arrays blocks maps.
    *
    * @param string $name Target attribute data lookup index selector text string description code.
    * @param string $delimeter Split point punctuation token rule string parameters map tracking limits. Defaults to ','.
    * @param string $method Global variables collection identification target scope designation string rule. Defaults to 'REQUEST'.
    * @return array Decompiled sequential list matrix tracking separated elements parameters strings vectors.
    *
    * @todo Rectify dynamic global lookup access variables structures (`$this->${method}`) to ensure robust static compilation safety paths.
    * @todo Fix the systematic spelling error inside variable tags parameters mapping from `$delimeter` over to `$delimiter`.
    */
   function request_split_array($name, $delimeter = ',', $method = 'REQUEST') {
      if( $this->check_get($name) && $this->not_null( $delimeter ) &&
      ( $method == 'GET' || $method == 'POST' || $method == 'REQUEST' ) ) {
         $tmp_array = $this->${method};
         $this->RSA[$name] = explode($delimeter, $tmp_array[$name]);
      } else {
         $this->RSA[$name] = array();
      }
      return $this->RSA[$name];
   }

   /**
    * Scans processed text string lists caches to evaluate item values matching defined tracking indexes rules parameters.
    *
    * @param string $name Target mapping data matrix storage verification container selector index text.
    * @param string $value Target string character sequence lookup metric parameter inspected against cache data.
    * @return bool Compliance validation criteria verification tracking confirmation code outcomes.
    */
   function check_request_split_array($name, $value) {
      if( isset($this->RSA[$name]) ) {
         return in_array($value, $this->RSA[$name]);
      } else {
         return false;
      }

   }

   /**
    * Appends key-value parameter pairs onto existing runtime application configuration array buffers matrices.
    *
    * @param array|string $elements Key token descriptor index label string or array properties specifications block. Defaults to an empty string.
    * @param string $value Supplementary property string metadata values definition target variable parameters. Defaults to an empty string.
    * @param array|string $array Optional explicit array context targeting structural append interactions. Defaults to an empty string.
    * @return array Consolidated combined properties dataset collection array matrix context configuration.
    */
   function add_local_get($elements = '', $value = '', $array = '') {
      if ( !is_array($elements)) {
         if( self::not_null($value) ) {
            $elements = array($elements => $value);
         } else {
            $elements = array($elements => $elements);
         }
      }

      if( is_array($array) ) {
         return array_merge($array, $elements);
      } else {
         if( self::not_null($this->GET_array) ) {
            return array_merge($this->GET_array, $elements);
         } else {
            return $elements;
         }
      }
   }

   /**
    * Overwrites existing dynamic global parameter states configurations arrays buffers directly.
    *
    * @param array|string $elements Reference updating property key configuration token array or label text string description. Defaults to an empty string.
    * @param string $value Context details payload text updates variable value parameter definition. Defaults to an empty string.
    * @return array Modified active parameter states collection configuration data.
    */
   function add_get($elements = '', $value = '') {
      $this->GET_array = $this->add_local_get($elements, $value);
      return $this->GET_array;
   }

   /**
    * Resolves floating point variable values from global query arrays templates contexts.
    *
    * @param string $text Target identification text label code string descriptor context parameter token.
    * @param bool $translate Option rule enabling localized language translator library processing passes. Defaults to false.
    * @return float Transformed real number evaluation scaling calculation representation output metric.
    */
   function get_float($text, $translate = false) {
       if($translate) $text = Lang::_($text);

       return (float)str_replace(',', '.', $this->GET[$text]);
   }

   /**
    * Confirms existence metrics verification states looking up parameters within standard GET data caches.
    *
    * @param string $text Query identification tracker string token identifier checked against variables caches index.
    * @param bool $translate Rule option permitting interface localized dialect mapper evaluation adjustments. Defaults to false.
    * @return bool Truth status verification check confirmation metric outcome indicator flag.
    */
   function check_get($text, $translate = false) {
      if($translate) $text = Lang::_($text);

      //      if( isset($this->GET[$text]) ) {
      return self::not_null($this->GET[$text]);
      //      } else {
      //         return false;
      //      }
   }

   /**
    * Confirms existence metrics verification states looking up parameters within standard POST data caches.
    *
    * @param string $text Query identification tracker string token checked against transactional post data structures.
    * @param bool $translate Rule option permitting interface localized dialect mapper evaluation adjustments. Defaults to false.
    * @return bool Truth status verification check confirmation metric outcome indicator flag.
    */
   function check_post($text, $translate = false) {
      if($translate) $text = str_replace(' ', '_', Lang::_($text));

      return self::not_null($this->POST[$text]);
   }

   /**
    * Validates structure rules checking string addresses formats using standard expression filtration patterns.
    *
    * @param string $str Electronic digital transmission routing address sequence checked against regex specifications rules.
    * @return bool Compliance confirmation outcome verification assessment indicator flag code.
    */
   static function check_valid_email( $str ) {
      //if(eregi("^[a-zA-Z0-9]+[_a-zA-Z0-9-]*(\.[_a-z0-9-]+)*@[a-z??????0-9]+(-[a-z??????0-9]+)*(\.[a-z??????0-9-]+)*(\.[a-z]{2,4})$", $str)) {
       if(filter_var($str, FILTER_VALIDATE_EMAIL)) {
         return TRUE;
      }
      return FALSE;
   }

   /**
    * Gauges incoming request login attributes to confirm authorization attempts match explicit permission parameters.
    *
    * @param string|bool $where Isolation portal target authentication code (e.g., 'ADMIN', 'CLIENT'). Defaults to false.
    * @return bool Processing verification assessment authentication state confirmation outcome flag.
    */
   function check_login($where = false) {

      if( !self::not_null($where) ) return false;

      if( $where != 'ADMIN' && $where != 'CLIENT' ) return false;

      if( $this->not_null($this->POST['lgn_' . $where]) &&
      $this->not_null($this->POST['pswrd_' . $where]) ) {
         return true;
      }
      return false;
   }

   //   function get_login_data($type) {
   //      return ($this->POST['lgn_' . $type], )
   //
   //   }

   /**
    * Evaluates structural status flags to determine active virtual request path mapping alignments.
    *
    * @return bool Evaluation validation tracking choice indicator code outcomes.
    */
   function check_virtualdir() {
      if( $this->virtualdir ) return true;
      else return false;
   }

   /**
    * Translates dynamic machine Unix timestamps into formatted calendars datestamps strings.
    *
    * @param int|bool $timestamp Numeric clock seconds cycle epoch marker tracking system timeline. Defaults to false.
    * @return string Formatted calendar date time notation data summary text representation.
    */
   static function get_current_date( $timestamp = false ) {
      if (!$timestamp) $timestamp = time();
      return date($GLOBALS['config']['DATE']['date'], $timestamp);
   }

   /**
    * Translates machine Unix timelines into complex detailed standard calendars time tracking notation text formats.
    *
    * @param int|bool $timestamp Chronological machine process timeline sequence coordinate tracking marker. Defaults to false.
    * @return string Formatted granular chronographical sequence output text specification.
    */
   static function get_current_datetime( $timestamp = false ) {
      if (!$timestamp) $timestamp = time();
      return date($GLOBALS['config']['DATE']['datetime'], $timestamp);
   }

   /**
    * Resolves virtual partition identity parameters matching global context configuration records options arrays.
    *
    * @return mixed Dynamic index location identifier reference code context on matches, or false on mapping faults.
    */
   function return_virtualdir_id() {
      $template_array = $GLOBALS['config']['TEMPLATES'];
      //print_debug($template_array);
      foreach( $template_array as $key => $val ) {
         if( $this->virtualdir == $key ) return $val;
      }
      return false;
   }

   /**
    * Parses, trims, normalizes, and segments incoming runtime application context variables packages (`$_GET`/`$_POST`).
    *
    * @return void
    *
    * @todo Remove dead code.
    */
   function _request_normalize() {
      //$_GET, $_POST

      $res_array_get = self::_request_normalize_rec($_GET);
      if( is_array($res_array_get) ) $this->GET = $res_array_get;
      //$_GET, $_POST
      $res_array_post = self::_request_normalize_rec($_POST, 'POST');
      if( is_array($res_array_post) ) $this->POST = $res_array_post;
      $this->REQUEST = array_merge_recursive($this->POST, $this->GET);

      if( self::not_null($this->GET['com']) ) {
         //FIXME check permisions


//          if( $this->GET['com'] == 'catalog' ) {
            //
//          } else {
            $this->com = $this->GET['com'];
//          }
         //unset($this->GET['com']);
      }

      if( self::not_null($this->GET['virtualdir']) ) {
         $this->virtualdir = $this->GET['virtualdir'];
         unset($this->GET['virtualdir']);
      }

      if( self::not_null($this->GET['going_back']) ) {
         $this->going_back = (($this->GET['going_back']==1)?true:false);
         unset($this->GET['going_back']);
      }
   }

   /**
    * Encodes prose text descriptions to avoid unexpected structural mutations inside JavaScript layout logic blocks code.
    *
    * @param string $str Alphanumeric prose string block sequence targeted for inline script string injection.
    * @param string $str_separator Text marker isolation literal bounding token value character parameter. Defaults to '"'.
    * @return string Transformed escaped text payload safe for compilation executions within client visual layer models.
    */
   function js_escape( $str, $str_separator = '"') {
      $search = array('\\', $str_separator);
      $replace = array('\\\\', '\\' . $str_separator);
      $str = str_replace($search, $replace, $str);
      $str = preg_replace('~(*BSR_ANYCRLF)\R~', chr(92).chr(10), $str);
      return $str;
   }

   /**
    * Recursively executes text parsing behaviors across an array schema to remove syntax elements tags.
    *
    * @param array $array Target collection tracking string values properties.
    * @return array Cleansed uniform properties map containing stripped text components.
    */
   static function array_recursive_strip_tags( $array ) {
      array_walk_recursive($array, 'strip_tags');
      return $array;
   }

   /**
    * Executes deep multi-tier evaluation calculations analyzing variances between deep dimensional array trees specifications models.
    *
    * @param array $array1 Base comparison array target modeling properties states structure indices values checklist.
    * @param array $array2 Evaluated array tracker target checked for variations matching baseline criteria definitions.
    * @return int Aggregated totals tracking isolated variance occurrences detected across compared structural properties fields.
    *
    * @todo Fix logical defects caused by using instance contextual qualifiers (`$this->array_recursive_compare`) within a static method scope.
    */
   static function array_recursive_compare($array1, $array2) {
      //FIXME
      $diff_count = 0;
      foreach($array1 as $key1 => $val1) {
         if( isset($array2[$key1]) ) {
            if( is_array( $val1 ) ) {
               if( count($val1) == 0 ) {
                  $diff_count++;
                  // echo "a1= $val1 != $array2[$key1]\n";
               } else {
                  $diff_count += $this->array_recursive_compare($val1, $array2[$key1]);
                  // echo "b1= $val1 != $array2[$key1]\n";
               }
            } else {
               if( $val1 != $array2[$key1] ) {
                  $diff_count++;
                  // echo "c1= $val1 != $array2[$key1]\n";
               }

            }
         } else {
            if( is_array( $val1 ) ) {
               if( count($val1) == 0 ) {
                  $diff_count++;
                  // echo "d1= $val1 != $array2[$key1]\n";
               } else {
                  $diff_count += $this->array_recursive_compare($val1, array());
                  // echo "e1= $val1 != $array2[$key1]\n";
               }
            } else {
               if( $val1 != $array2[$key1] ) {
                  $diff_count++;
                  // echo "f1= $val1 != $array2[$key1]\n";
               }
            }
         }
      }
      foreach($array2 as $key2 => $val2) {
         if( !isset($array1[$key2]) ) {
            //count all
            if( is_array( $val2 ) ) {
               if( count($val2) == 0 ) {
                  $diff_count++;
                  // echo "a2= $val2 != $array1[$key2]\n";
               } else {
                  $diff_count += $this->array_recursive_compare(array(), $val2);
                  // echo "b2= $val2 != $array1[$key2]\n";
               }
            } else {
               $diff_count++;
               // echo "c2= $val2 != $array1[$key2]\n";
            }
         }
      }
      // echo "$diff_count \n";
      return $diff_count;
      //array_diff
   }

   /**
    * Recursively sweeps through processing vectors arrays to trim padding spacing, clean signatures, and evaluate click metrics elements fields.
    *
    * @param array $array Target raw dynamic array data compilation block extracted from user scope requests tracking.
    * @param string $type Context configuration parameters tracking target group indicator rule mode. Defaults to 'GET'.
    * @return array Sanitized output properties data matrix.
    */
   private function _request_normalize_rec(array $array, $type = 'GET') {
      //$_GET, $_POST

      $ret_array = array();
      foreach( $array as $key => $val ) {
         if( function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() === TRUE ) {
             $key = stripslashes($key);
             $val = stripslashes($val);
         }

         if( is_array($val) ) {
            $ret_array[$key] = self::_request_normalize_rec($val, $type);
         } else {
            $val = (string)trim($val);
            $key_len = strlen($key);
            if( strlen($val) > 0 ) {
               if ( !(($key == 'x' || $key == 'y')) ) {
                    $ret_array[$key] = $val;
               }
               if( $key_len>2 && strpos($key, '_x') == ($key_len-2) &&
                  isset($array[substr($key, 0, -2).'_y']) ) {
                    $ret_array[substr($key, 0, -2)] = substr($key, 0, -2);
               }
            }

         }
      }

      if( sizeof($ret_array) > 0 )  return $ret_array;
      else return array();
   }

}
