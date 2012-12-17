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
   private static $worker = false;

   function Authenticate($login) {

      // Authenticate the user
      //if ($login->username === "a.single.sign.on.user@your.organization.org" && $login->password === "the-password-of-that-user") {
      //return array('Authenticated'=>true);
      // } else {
      return array('Authenticated'=>false);
      // }
   }
   
   private function __init_worker() {
      if( !$this->worker || get_class($this->worker) != 'Soap_Server_worker' ) {
         $this->worker = new Soap_Server_worker();
      }
   }

   function __construct() {
      $this->nr_req++;
      add_to_fp("Soap_Server.php\n");
      add_to_fp(print_r($this, true));
      $this->__init_worker();
   }
  
   
   function call_worker($name, array $arguments) {
      //$arguments = array( $input ) ; $input->values = array();
      $xml_data = $this->translate_xml($arguments);
      //add_to_fp(print_r($xml_data, true));
      $response = call_user_func_array( array($this->worker, $name), array($xml_data));
      add_to_fp(print_r($response, true));
      return $response;
   }
   
   function translate_xml($arguments) {
      global $fp_xml;
      if( isset($fp_xml) && is_resource($fp_xml) ) {
         fwrite($fp_xml, $arguments[0]);
      }
      
      return ArrayToXML::Xmlto($arguments[0]);
   }
   
   function __call($name, array $arguments) {
      if(method_exists($this->worker,$name)) {
         return $this->call_worker($name, $arguments);//Request
         ///call_user_func_array( array($this->worker, $name), $arguments);
         //$this->worker->${var_name}();
      } else {
         
      }
   }

}

?>