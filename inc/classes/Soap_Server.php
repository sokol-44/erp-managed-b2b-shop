<?php
/**
 * Soap_Server.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Soap_Server {
   public $auth = false;
   public $nr_req = 0;
   private $acces_array = array(
         'empty' => 'dfsfi8CTRJHnoOI243NvirtdsHMIU216asiudnfpou',
         '1' => 'dfsfi8CTRJHnoOI243NvirtdsHMIU216asiudnfpou');
   private $worker = false;

   function __auth() {
      $get = Framework::$GET;
      $F = Framework::g_global();
      
      if( Framework::not_null($get['h']) && Framework::not_null($get['s']) ) {
            
         if( Framework::is_null($get['u']) ) $get['u'] = 'empty';
         $secret = $this->acces_array[$get['u']];
         
         if( Framework::is_null($get['hf']) ) $get['hf'] = 'md5';
         
         if ( Framework::not_null($secret) && abs( (int)$get['s']-time() ) < 72000 ) {
            if( ctype_xdigit($get['h']) ) {
               $hash = $get['h'];
            } elseif ( ctype_alnum($get['h']) ) {
               $hash = base64_decode($get['h'], true);
               if( $hash ) {
                  $hash = bin2hex( $hash );
               } else {
                  $hash = false;
               }
            } else {
               $hash = false;
            }
            add_to_fp(var_export($hash ,true));
			
            if( $hash && in_array($get['hf'],hash_algos()) ) {
               $hash_local = hash($get['hf'], $secret . $get['s']);
               add_to_fp("HASH  ");
               if( $hash_local == $hash ) {
                  add_to_fp("AUTH\n");
                  $this->auth = true;
               }
            }
         }
      }
      
      //TODO - remove prom production
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
   
   private function __init_worker() {
      if( !$this->worker || get_class($this->worker) != 'Soap_Server_worker' ) {
         $this->worker = new Soap_Server_worker();
      }
   }

   function __construct() {
      $this->nr_req++;
      add_to_fp("Soap_Server.php\n");
      $this->__init_worker();
      $this->__auth();
   }
  
   
   function call_worker($name, array $arguments) {
      $xml_data = $this->translate_xml($arguments);
      add_to_fp('$xml_data il:'.sizeof($xml_data).'('.sizeof($xml_data['values']).")\nData:".print_r($xml_data, true));
      $response = call_user_func_array( array($this->worker, $name), array($xml_data));
      add_to_fp('$response'.print_r($response, true));
      return $response;
   }
   
   function translate_xml($arguments) {
      global $fp_xml;
      add_to_fp("translate_xml\n");
      if( strlen($arguments[0]) > 0 && $fp_xml && is_resource($fp_xml)  ) {
         fwrite($fp_xml, $arguments[0]);
      }
      if( function_exists('mb_get_info') ) {
         $de =  mb_detect_encoding($arguments[0]);
         if( $de != 'UTF-8' ) {
            add_to_fp("mb_convert_encoding $de \n");
            $arguments[0] = mb_convert_encoding($arguments[0], 'UTF-8', $de);
            $arguments[0] = str_ireplace('encoding="utf-16"', 'encoding="utf-8"', $arguments[0]);
         }
      }
      return ArrayToXML::Xmlto($arguments[0]);
   }
   
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
      
      $res_xml = ArrayToXML::toXml($return_data);
      add_to_fp('$res_xml ' . $res_xml);
      return $res_xml;
   }

}

?>