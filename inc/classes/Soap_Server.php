<?php
/**
 * Soap_Server.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Soap_Server
 *
 * Handles incoming SOAP requests, manages basic cryptographic authentication,
 * and delegates XML workload conversions to a dedicated worker engine.
 *
 * @designPattern Strategy / Singleton
 *
 * @todo Add strict type declarations (declare(strict_types=1)).
 * @todo Explicitly define visibility modifiers and types for all properties (PHP 7.4+ / 8.0+).
 * @todo Migrate to standardized PSR-3 logging framework instead of global `add_to_fp` routines.
 * @todo Remove hardcoded fallback auth bypasses ($this->auth = true) completely before deployment.
 */
class Soap_Server {
   /**
    * @var bool Authentication status of the current request payload context.
    */
   public $auth = false;

   /**
    * @var int Cumulative count of request cycles initialized.
    */
   public $nr_req = 0;

   /**
    * @var array<string, string> Internal secure lookup matrix mapping credentials to static API secrets.
    * @todo Extract sensitive static token components out of codebase and map into protected environment variables.
    */
   private $acces_array = array(
         'empty' => 'dfsfi8CTRJHnoOI243NvirtdsHMIU216asiudnfpou',
         '1' => 'dfsfi8CTRJHnoOI243NvirtdsHMIU216asiudnfpou');

   /**
    * @var \Soap_Server_worker|false The delegated back-end execution engine instance layer, or false if unassigned.
    */
   private $worker = false;

   /**
    * Authenticates incoming connections against URL parameters using hash signatures or HMAC checks.
    *
    * Parses cryptographic tokens and time window limits to evaluate authorization states.
    *
    * @return void
    *
    * @todo Refactor global state usage ($F->GET) towards a clean PSR-7 HTTP ServerRequest abstraction.
    * @todo Enforce a strict constant-time string comparison (`hash_equals`) to mitigate timing attack vectors.
    * @todo Replace the massive nested conditionally branch structure with clean, readable guard clauses.
    * @todo Fix spelling.
    */
   function __auth() {
      $F = Framework::g_global();
      $get = $F->GET;
      //add_to_fp('Soap_Server __auth ' . print_r($get, true) . ';');
      if( Framework::not_null($get['h']) && Framework::not_null($get['s']) ) {

         if( Framework::is_null($get['u']) ) $get['u'] = 'empty';
         $secret = $this->acces_array[$get['u']];

         if( Framework::is_null($get['hf']) ) $get['hf'] = 'md5';
         $get['hf'] = trim(strtolower($get['hf']));

         if( strpos($get['hf'], 'hmac') === false ) {
             $hmac = false;
         } else {
             $get['hf'] = str_replace('hmac', '', $get['hf']);
             $hmac = true;
         }

         $s = strstr($get['s'], '.', true);
         if( $s !== false ) {
             $stime = (int)$s;
         } else {
             $stime = (int)$get['s'];
         }

         if ( Framework::not_null($secret) && abs( (int)$stime-time() ) < 72000 ) {

            if( ctype_xdigit($get['h']) ) {
               $hash = $get['h'];
            } elseif ( ctype_alnum(str_replace('=', '', $get['h'])) ) {
               $hash = base64_decode($get['h'], true);
               if( $hash ) {
                  $hash = bin2hex( $hash );
               } else {
                  $hash = false;
               }
            } else {
               $hash = false;
            }
            //add_to_fp(var_export($hash ,true));

            if( $hash && in_array($get['hf'],hash_algos()) ) {

                if( $hmac ) $hash_local = hash_hmac($get['hf'], $secret, $get['s']);
               else $hash_local = hash($get['hf'], $secret . $get['s']);

               //add_to_fp("HASH sec: '$secret': $hash_local == $hash ");
               if( $hash_local == $hash ) {
                  //add_to_fp("AUTH\n");
                  $this->auth = true;
               }
            }
         }
      }
      add_to_fp('Soap_Server __auth '.var_export($this->auth ,true));
      //TODO - remove from production
      $this->auth = true;
       /*Array
      (
            [u] => 1
            [h] => aJhJrVjAntZZVwmZfLdeW929RZwxMzYzNjEyMjU5
            [s] => 1363612259
            [hf] => sha1
      )
      */
   }

   /**
    * Lazily constructs and registers the internal operational worker node.
    *
    * @return void
    *
    * @todo Use explicit dependency injection to pass standard workers via constructor layers instead of tightly coupling dependencies.
    */
   private function __init_worker() {
      if( !$this->worker || get_class($this->worker) != 'Soap_Server_worker' ) {
         $this->worker = new Soap_Server_worker();
      }
   }

   /**
    * Soap_Server constructor.
    *
    * Increments the processing counter metric, provisions core workers, and challenges incoming authentication requests.
    *
    * @return void
    */
   function __construct() {
      $this->nr_req++;
      add_to_fp("Soap_Server __construct\n");
      $this->__init_worker();
      $this->__auth();
   }

   /**
    * Parses raw internal arguments, updates records, and forwards execution streams into the internal worker.
    *
    * @param string $name Name of target method context requested on the worker target.
    * @param array $arguments Indexed execution arguments structure provided.
    * @return mixed The serialized or standard operational output returned from target execution worker routines.
    *
    * @todo Enforce explicit strict scalar parameter definitions (`string $name`) and clean up typing requirements.
    */
   function call_worker($name, array $arguments) {
      $xml_data = $this->translate_xml($arguments);
      add_to_fp('$xml_data il:'.sizeof($xml_data).'('.sizeof($xml_data['values']).")\nData:".print_r($xml_data, true));
      $response = call_user_func_array( array($this->worker, $name), array($xml_data));
      add_to_fp('$response'.print_r($response, true));
      return $response;
   }

   /**
    * Evaluates context configurations, standardizes encoding sets to UTF-8, and writes log cache footprints.
    *
    * @param array $arguments Encapsulated incoming data packets where element 0 holds payload contents.
    * @return array Multi-dimensional configuration mapping array compiled from structural XML records.
    *
    * @todo Refactor the side-effect global reference context `$fp_xml` towards an isolated tracking stream wrapper.
    * @todo Check array index existence securely (`isset($arguments[0])`) before attempting operations.
    */
   function translate_xml($arguments) {
      global $fp_xml;
      add_to_fp("translate_xml\n");
      if( function_exists('mb_get_info') ) {
         $de =  mb_detect_encoding($arguments[0]);
         if( $de != 'UTF-8' ) {
            add_to_fp("mb_convert_encoding $de \n");
            $arguments[0] = mb_convert_encoding($arguments[0], 'UTF-8', $de);
            $arguments[0] = str_ireplace('encoding="utf-16"', 'encoding="utf-8"', $arguments[0]);
         }
      }
      if( strlen($arguments[0]) > 0 && $fp_xml && is_resource($fp_xml)  ) {
         fwrite($fp_xml, $arguments[0]);
      }
      return ArrayToXML::Xmlto($arguments[0]);
   }

   /**
    * Magic intercepter routing unregistered methods towards authenticated internal worker nodes.
    *
    * Monitors operational resource metrics and appends supplementary payload file readings onto the outbound structures.
    *
    * @param string $name Targeted intercept call method identity.
    * @param array $arguments Structural parameter data mapping elements.
    * @return string Compiled outgoing transaction configuration structural XML information string.
    *
    * @todo Fix logical checking anomalies (`strpos(...) == 0` versus strict types checks `=== 0`).
    * @todo Safeguard tracking indices via array keys validation (`isset($return_data['status'])`) to prevent unexpected error scenarios.
    * @todo Abstract filesystem read loops away into decoupled storage handler systems rather than binding methods inside routing layers.
    */
   function __call($name, array $arguments) {
      $utime = microtime(true);
      if ( !$this->auth ) {
         add_to_fp(' not auth ' . $name);
         $return_data = $this->worker->getReturnError('AUTH', '', 'WRONG USER PASSWORD');
      } elseif(strstr($name, '_') === FALSE && method_exists($this->worker,$name)) {
         add_to_fp('     auth & method ' . $name);
         $return_data = $this->call_worker($name, $arguments);
         //Request
         ///call_user_func_array( array($this->worker, $name), $arguments);
         //$this->worker->${var_name}();
      } else {
         add_to_fp('     auth & not method ' . $name);
         $return_data = $this->worker->getReturnError($name, '', 'WRONG METHOD');
      }
      $elements = (isset($return_data['Info'])?(sizeof($return_data)-1):sizeof($return_data));
      $return_data['Info'] = array_merge($return_data['Info'], array(
            'Elements' => $elements,
            'Method' => $name,
            'RunningTime' => round(microtime(true)-$utime, 2),
            'DateTime' => date("Y-m-d\TH:i:sP")
            ) );

      if(  strpos($return_data['status'], 'FILE:') == 0 &&
            strpos($return_data['status'], 'FILE:') !== FALSE ) {


          $dta = strpos($return_data['status'], ':');
          if( $dta > 0 ) $fn=substr($return_data['status'], $dta+1);

          add_to_fp('$dta ;' . $dta . '; ' . print_r($return_data,true).' ;fn:'.$fn);

          add_to_fp(' $is_file='.(is_file($fn)?'t':'n').' $is_readable='.(is_readable($fn)?'t':'n'));

          if( is_file($fn) && is_readable($fn) ) {
//               sleep(1);
              clearstatcache(true, $fn);
              $stat_ar = stat($fn);
              add_to_fp('file - add from ' . print_r($fn, true) . ' stat:' . print_r($stat_ar, true));
              $return_data['additional_data'] = file_get_contents($fn);
              //$return_data['additional_data'] = '<![CDATA['.file_get_contents($fn).']]>';
              //return file_get_contents($fn);
          } else {
              add_to_fp('file - error ' . print_r($fn, true));
              $return_data['additional_data'] = $return_data['status'];
              $return_data['status'] = 'ERROR,WRONG_FILE';
          }

          $res_xml = ArrayToXML::toXml($return_data);
          add_to_fp('$res_xml ' . $res_xml);
          return $res_xml;

      } else {

          $res_xml = ArrayToXML::toXml($return_data);
          add_to_fp('$res_xml ' . $res_xml);
          return $res_xml;
      }

   }

}
