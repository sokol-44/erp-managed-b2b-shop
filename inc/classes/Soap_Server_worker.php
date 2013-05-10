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
      $res['Info'] = array();
      $ainfo = array('id_one_name'=>'', 'id_one_min'=>false, 'id_one_max'=>false,
                     'id_two_name'=>'', 'id_two_min'=>false, 'id_two_max'=>false);
      foreach( $array as $key => $val ) {
         if( $ainfo['id_one_name'] === false ) {
         } elseif( $ainfo['id_one_name'] === '' ) {
            foreach($val as $kname => $kval) {
               if( strpos($kname, 'id_') == 0 && strpos($kname, 'id_') !== FALSE ) {
                  $ainfo['id_one_name'] = $kname;
                  $ainfo['id_one_max'] = $kval;
                  $ainfo['id_one_min'] = $kval;
                  break;
               }
            }
            if( $ainfo['id_one_name'] == '' ) $ainfo['id_one_name'] = false;
         } else {
            if( $val[$ainfo['id_one_name']] > $ainfo['id_one_max'] ) $ainfo['id_one_max'] = $val[$ainfo['id_one_name']];
            if( $val[$ainfo['id_one_name']] < $ainfo['id_one_min'] ) $ainfo['id_one_min'] = $val[$ainfo['id_one_name']];
         }
         
         if( $ainfo['id_two_name'] === false ) {
         } elseif( $ainfo['id_two_name'] === '' ) {
            foreach($val as $kname => $kval) {
               if( strpos($kname, 'id_') == 0 && strpos($kname, 'id_') !== FALSE && $kname != $ainfo['id_one_name'] ) {
                  $ainfo['id_two_name'] = $kname;
                  $ainfo['id_two_max'] = $kval;
                  $ainfo['id_two_min'] = $kval;
                  break;
               }
            }
            if( $ainfo['id_two_name'] == '' ) $ainfo['id_two_name'] = false;
         } else {
            if( $val[$ainfo['id_two_name']] > $ainfo['id_two_max'] ) $ainfo['id_two_max'] = $val[$ainfo['id_two_name']];
            if( $val[$ainfo['id_two_name']] < $ainfo['id_two_min'] ) $ainfo['id_two_min'] = $val[$ainfo['id_two_name']];
         }
         
         $res['value_' . $key] = $val;
         $res['Info']['DataInfo'] = $ainfo;
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
            add_to_fp('$param_array:'. print_r($param_array, true) );
            $res_array = Data::getOrderList((int)$param_array['id_start'], (int)$param_array['length']);
            $response = array();
            
            if( sizeof($res_array) > 0 ) {
               $response = $this->_addArrayValues($res_array);
               add_to_fp('$response:'.print_r($response, true) );
               if( (int)$param_array['length'] > 0  ) $id_chk = (int)$response['Info']['DataInfo']['id_one_max'];
               else $id_chk = (int)$response['Info']['DataInfo']['id_one_min'];
               add_to_fp('rest in');
               $response['Info']['Rest'] = Data::getOrderListRest((int)$id_chk, (int)$param_array['length']);
               add_to_fp('rest out');
            }
         } else {
            $response = $this->getReturnError('getOrderList', $input, $ParamStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getOrderList', $input, 'WRONG CLASS');
      }

      add_to_fp('return out');
      return($response);
   }
       
   function doClientChange ( $input ) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;
      
      add_to_fp('-------- doClientAdd');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doClientAdd($param_array, false);
            } else {
               $response_tmp[] = $this->getReturnError('doClientChange', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doClientChange', '', 'EMPTY_LIST');
      }
      
      return($response);
   }

   function doClientAdd ( $input ) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doClientAdd');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doClientAdd($param_array, true);
            } else {
               $response_tmp[] = $this->getReturnError('doClientAdd', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doClientAdd', '', 'EMPTY_LIST');
      }
      
      return($response);
   }

   function doClientUserAdd ( $input ) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doClientUserAdd');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientUserData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doClientUserAdd($param_array, true);
            } else {
               $response_tmp[] = $this->getReturnError('doClientUserAdd', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doClientUserAdd', '', 'EMPTY_LIST');
      }
      
      return($response);
   }

   function doClientUserChange ( $input ) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doClientUserAdd');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientUserData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doClientUserAdd($param_array, false);
            } else {
               $response_tmp[] = $this->getReturnError('doClientUserChange', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doClientUserChange', '', 'EMPTY_LIST');
      }
      
      return($response);
   }

   function doClientUserDelete ( $input ) {
      $this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doClientUserDelete');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientUserData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doClientUserDelete($param_array);
            } else {
               $response_tmp[] = $this->getReturnError('doClientUserDelete', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doClientUserDelete', '', 'EMPTY_LIST');
      }
      
      return($response);
   }

   function doClientUserSetPassword( $input ) {
      $this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doClientUserSetPassword');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientUserData($val, $this->input_data_type);
            add_to_fp(print_r($SingleValueClass, true));
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doClientUserSetPassword($param_array);
            } else {
               $response_tmp[] = $this->getReturnError('doClientUserSetPassword', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doClientUserSetPassword', '', 'EMPTY_LIST');
      }
      
      return($response);
   }

   function doProductAdd( $input ) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doProductAdd');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ProductData($val, $this->input_data_type);
            add_to_fp(print_r($SingleValueClass, true));
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doProductAdd($param_array);
            } else {
               $response_tmp[] = $this->getReturnError('doProductAdd', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doProductAdd', '', 'EMPTY_LIST');
      }
      
      return($response);
   }

   function doProductChange( $input ) {
      $this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doProductChange');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ProductData($val, $this->input_data_type);
            add_to_fp(print_r($SingleValueClass, true));
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doProductChange($param_array);
            } else {
               $response_tmp[] = $this->getReturnError('doProductChange', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doProductChange', '', 'EMPTY_LIST');
      }
      
      return($response);
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
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doCategoryAdd');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new CategoryData($val, $this->input_data_type);
            add_to_fp(print_r($SingleValueClass, true));
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doCategoryAdd($param_array);
            } else {
               $response_tmp[] = $this->getReturnError('doCategoryAdd', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doCategoryAdd', '', 'EMPTY_LIST');
      }
      
      return($response);
   }

   function doCategoryEdit( $input ) {
      $this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doCategoryEdit');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new CategoryData($val, $this->input_data_type);
            add_to_fp(print_r($SingleValueClass, true));
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doCategoryEdit($param_array);
            } else {
               $response_tmp[] = $this->getReturnError('doCategoryEdit', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doCategoryEdit', '', 'EMPTY_LIST');
      }
      
      return($response);
   }


   function doCategoryDelete( $input ) {
      $this->input_data_type = 'DELETE';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doCategoryDelete');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new CategoryData($val, $this->input_data_type);
            add_to_fp(print_r($SingleValueClass, true));
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doCategoryDelete($param_array);
            } else {
               $response_tmp[] = $this->getReturnError('doCategoryDelete', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doCategoryDelete', '', 'EMPTY_LIST');
      }
      
      return($response);
   }


   function setProduct2Category( $input ) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;
      
      add_to_fp('-------- setProduct2Category');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new Product2Category($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $pa = $SingleValueClass->return_array();
               //add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::setProduct2Category($pa['id_product'], $pa['id_category']);
            } else {
               $response_tmp[] = $this->getReturnError('setProduct2Category', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('setProduct2Category', '', 'EMPTY_LIST');
      }
      
      return($response);
   }
   
   function setPicture( $input ) {
      $this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = false;
   
      add_to_fp('-------- setPicture');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new PictureData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $pa = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. $pa['id_picture'] . ',' . strlen($pa['data']) );
               $response_tmp[] = Data::setPicture($pa);
            } else {
               $response_tmp[] = $this->getReturnError('setPicture', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('setPicture', '', 'EMPTY_LIST');
      }
   
      return($response);
   }

   function setOrderStatus( $input ) {
      $response = $this->_fill_response( $input, 'StatusData', 'setOrderStatus');
      //$response = array();
   
      return $response;
   }
       
   function setOrderHiddenStatus( $input ) {
      $this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = false;
      
      add_to_fp('-------- setOrderHiddenStatus');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new OrderData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $pa = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($pa, true) );
               $response_tmp[] = Data::setOrderHiddenStatus($pa['id_order'], $pa['id_client'], $pa['hidden_status']);
            } else {
               $response_tmp[] = $this->getReturnError('setOrderHiddenStatus', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('setOrderHiddenStatus', '', 'EMPTY_LIST');
      }
      
      return($response);
   }

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