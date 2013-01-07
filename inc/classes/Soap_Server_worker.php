<?php
/**
 * Soap_Server.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Soap_Server_worker {
   public $input_data_type = 'NEW';
   public $output_data_type = 'FULL'; //
   public $SingleParam_MultipleReturns = false;

   
   private function _getSingleValue( $input, $type ) {
 
      if( class_exists($type) ) {
         add_to_fp("\t _getSingleValu $type <- OK");
         if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) == 1) {
           $val = reset($input['values']);
           $SingleValueClass = new $type($val, $this->input_data_type);
           return $SingleValueClass;
         }
      }
      return false;
   }
   
   public function getReturnError($method, $data, $info, $top_array = true) {
      $array_res = array(
               'method' => $method,
               'data' => $data,
               'info' => $info,
               'debug' => ''
            ) ;
      
      if ( is_array($data) ) {
         $array_res['data'] =  ArrayToXML::toXml( $data );
      }
      if ( is_array($info) ) {
         $array_res['info'] =  ArrayToXML::toXml( $info );
      }
      if (defined('DEBUG_XML_QUERIES') && (DEBUG_XML_QUERIES == 'true')) {
         $array_res['debug'] =  ArrayToXML::toXml( debug_backtrace() );
      }
      
      if( $top_array ) return array('value' => $array_res);
      else return $array_res;
   }
   
   private function _addArrayValues( $array ) {
      $res = array();
      foreach( $array as $key => $val ) {
         $res['value_' . $key] = $val;
      }
      return $res;
   }



   function getClientList( $input ) {//ParamStartLength, ClientData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
      
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
            $param_array = $ParamStartLength->return_array();
            //add_to_fp('$param_array:'. print_r($param_array, true) );
            $res_array = Data::getClientList((int)$param_array['id_start'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getClientList', $input, $ParamStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getClientList', $input, 'WRONG CLASS');
      }

      return($response);
   }
    
   function getClientUserList( $input ) {//ParamStartLength, ClientUserData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamDoubleStartLength = $this->_getSingleValue($input, 'ParamDoubleStartLength');
      
      if( is_object($ParamDoubleStartLength) ) {
         if( !$ParamDoubleStartLength->is_error() ) {
            $param_array = $ParamDoubleStartLength->return_array();
            $res_array = Data::getClientUserList((int)$param_array['id_start_one'], (int)$param_array['id_start_two'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getClientUserList', $input, $ParamDoubleStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getClientUserList', $input, 'WRONG CLASS');
      }
      
      return($response);
   }

   function getClientPriceList( $input ) {
      $response = $this->_fill_response( $input, 'ProductData', 'getProductListFromCategory');
      // $response = array( new ProductClientPriceData('xyz', 'getProductClientPriceList') );
      //$response = array();
       
      return $response;
   }
   

   function getClientPriceProductList( $input ) { //ParamDoubleStartLength, ProductData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;

      $ParamDoubleStartLength = $this->_getSingleValue($input, 'ParamDoubleStartLength');
      
      if( is_object($ParamDoubleStartLength) ) {
         if( !$ParamDoubleStartLength->is_error() ) {
            $param_array = $ParamDoubleStartLength->return_array();
            $res_array = Data::getClientPriceProductList((int)$param_array['id_start_one'], (int)$param_array['id_start_two'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getClientPriceProductList', $input, $ParamDoubleStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getClientPriceProductList', $input, 'WRONG CLASS');
      }
      
      return($response);
   }
   
   function getProductClientPriceList( $input ) {
      $response = $this->_fill_response( $input, 'ProductData', 'getProductListFromCategory');
      //$response = array( new ProductClientPriceData('xyz', 'getProductClientPriceList') );
      //$response = array();
       
      return $response;
   }
    
   function getCategoryList( $input ) {//ParamStartLength, ProductData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
      
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
            $param_array = $ParamStartLength->return_array();
            //add_to_fp('$param_array:'. print_r($param_array, true) );
            $res_array = Data::getCategoryList((int)$param_array['id_start'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getCategoryList', $input, $ParamStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getCategoryList', $input, 'WRONG CLASS');
      }

      return($response);
   }


   function getProductList( $input ) { //ParamStartLength, ProductData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
      
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
            $param_array = $ParamStartLength->return_array();
            //add_to_fp('$param_array:'. print_r($param_array, true) );
            $res_array = Data::getProductList((int)$param_array['id_start'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getProductList', $input, $ParamStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getProductList', $input, 'WRONG CLASS');
      }

      return($response);
   }

   function getProductListFromCategory( $input ) { //ParamDoubleStartLength, ProductData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamDoubleStartLength = $this->_getSingleValue($input, 'ParamDoubleStartLength');
      
      if( is_object($ParamDoubleStartLength) ) {
         if( !$ParamDoubleStartLength->is_error() ) {
            $param_array = $ParamDoubleStartLength->return_array();
            $res_array = Data::getProductListFromCategory((int)$param_array['id_start_one'], (int)$param_array['id_start_two'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getProductListFromCategory', $input, $ParamDoubleStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getProductListFromCategory', $input, 'WRONG CLASS');
      }
      
      return($response);
   }

   function getOrderListNew( $input ) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
      
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
            $param_array = $ParamStartLength->return_array();
            add_to_fp('$param_array:'. print_r($param_array, true) );
            $res_array = Data::getOrderList((int)$param_array['id_start'], (int)$param_array['length'], ' and `hidden_status` IS NULL ');
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getOrderListNew', $input, $ParamStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getOrderListNew', $input, 'WRONG CLASS');
      }

      return($response);
   }
    
   function getOrderList( $input ) { //ParamStartLength, OrderData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
      
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
            $param_array = $ParamStartLength->return_array();
            //add_to_fp('$param_array:'. print_r($param_array, true) );
            $res_array = Data::getOrderList((int)$param_array['id_start'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getOrderList', $input, $ParamStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getOrderList', $input, 'WRONG CLASS');
      }

      return($response);
   }
       
   function doClientChange ( $input ) {
      // return serialize($input);
      //add_to_fp(print_r($input, true));

      $response = $this->_fill_response( $input, 'StatusData');

      return $response;
      //		`id_client_in` INT,
      //		`name_in` TINYTEXT,
      //		`description_in` TEXT,
      //		`email_in` TINYTEXT,
      //		`phone_in` TINYTEXT,
      //		`state_in` TINYTEXT
   }

   function doClientAdd ( $input ) {
      // return serialize($input);

      $response = $this->_fill_response( $input, 'StatusData');

      return $response;
      //		`id_client_in` INT,
      //		`name_in` TINYTEXT,
      //		`description_in` TEXT,
      //		`email_in` TINYTEXT,
      //		`phone_in` TINYTEXT,
      //		`state_in` TINYTEXT
   }

   function doClientUserAdd ( $input ) {
      $response = $this->_fill_response( $input, 'StatusDoubleData');

      return $response;
   }

   function doClientUserChange ( $input ) {
      $response = $this->_fill_response( $input, 'StatusDoubleData');

      return $response;
   }

   function doClientUserDelete ( $input ) {
      $response = $this->_fill_response( $input, 'StatusDoubleData');

      return $response;
   }

   function doClientUserSetPassword( $input ) {
      $response = $this->_fill_response( $input, 'StatusDoubleData');

      return $response;
   }

   function doProductAdd( $input ) {
      $response = $this->_fill_response( $input, 'StatusData', 'doProductAdd');
      //$response = array( new StatusData('xyz') );
      //$response = array();
      return $response;
   }

   function doProductChange( $input ) {
      $response = $this->_fill_response( $input, 'StatusData', 'doProductChange');
      //$response = array( new StatusData('xyz') );
      //$response = array();
      return $response;
   }


   function setProductClientPrice( $input ) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;
      add_to_fp('-------- setProductClientPrice');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ProductClientPriceData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $pa = $SingleValueClass->return_array();
               //add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::setProductClientPrice($pa['id_product'], $pa['id_client'], $pa['price'] , $pa['vat']);
            } else {
               $response_tmp[] = $this->getReturnError('setProductClientPrice', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('setProductClientPrice', '', 'EMPTY_LIST');
      }
      
      return($response);
   }


   function doCategoryAdd( $input ) {
      //$response = 'setProductClientPrice';
      $response = $this->_fill_response( $input, 'StatusData', 'doCategoryAdd');
      // $response = array( new StatusDoubleData('xyz') );
      //$response = array();

      return $response;
   }

   function doCategoryEdit( $input ) {
      $response = $this->_fill_response( $input, 'StatusData', 'doCategoryEdit');
      // $response = array( new StatusDoubleData('xyz') );
      //$response = array();

      return $response;
   }


   function doCategoryDelete( $input ) {
      $response = $this->_fill_response( $input, 'StatusData', 'doCategoryDelete');
      // $response = array( new StatusDoubleData('xyz') );
      //$response = array();

      return $response;
   }


   function setProduct2Category( $input ) {
      $response = $this->_fill_response( $input, 'StatusData', 'setProduct2Category');
      // $response = array( new StatusDoubleData('xyz') );
      //$response = array();

      return $response;
   }

   function setOrderStatus( $input ) {
      $response = $this->_fill_response( $input, 'StatusData', 'setOrderStatus');
      //$response = array();

      return $response;
   }

   function setOrderHiddenStatus( $input ) {
      $response = $this->_fill_response( $input, 'StatusData', 'setOrderHiddenStatus');
      //$response = array();

      return $response;
   }

   //   function _fill_response_param( $input, $classname, $string_add = '') {
   //      //      $count = sizeof($input);
   //      add_to_fp('START _fill_response_param');
   //      add_to_fp(print_r($input, true));
   //      add_to_fp($classname);
   //      $response = array();
   //      for($input->values as $key => $val) {
   //         //eval('$response["' . $key . '"] = ' . $classname . '::_fill_response();');
   //         add_to_fp(print_r($val, true));
   //         $response[$key] = new $classname($val, $string_add);
   //      }
   //      add_to_fp(print_r($response, true));
   //      add_to_fp('END _fill_response');
   //      return $response;
   //   }

   function getParamStartLength( $input ) {
      $in_o = array(
            'start' => 1,
            'length' => 2,
            'options' => 'asasa'
      );
      return $in_o;
   }

   function _response( $input, $methodname) {


   }

   function _fill_response( $input, $classname, $string_add = '') {
      //      $count = sizeof($input);
      global $fp_xml;

      add_to_fp("START _fill_response\n" .
            print_r($input, true) . "\n" .
            "CLASSNAME: $classname");
      $response = array();

      
      if( isset($input['values']) ) {
         add_to_fp('ITERATE');
         foreach( $input['values'] as $key => $val ) {
            //add_to_fp($key." => ".print_r($val,true)."\n");
            //add_to_fp( print_r($val, true) );
            //if( $classname == 'StatusData' || $classname == 'StatusDoubleData' ) {
               $c_tmp = new $classname($classname . ' ' . $string_add, 'FULL');
               $c_tmp->init(false, true);
               $response[$key] =  $c_tmp->return_array();
            //} else {
             //  $c_tmp = new $classname($val, $string_add);
            //   $response[$key] = $c_tmp->return_array();
            //}
         }
      } else {
         $response['wew_ms_foo'] = new $classname($input, $string_add);
         $response['wew_ms_bar'] = new $classname($input, $string_add);
      }

      add_to_fp("-RES:\n" . print_r($response, true) . "\nEND _fill_response");

      $res_xml = ArrayToXML::toXml($response);
      add_to_fp("-RES XML:\n" . print_r($res_xml, true));

      return $res_xml;
   }

   //Client_Add, client_change, client_user_add, client_user_change, client_user_delete, client_user_set_password
}


?>