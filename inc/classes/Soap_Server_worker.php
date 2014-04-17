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
   
   public function getReturnStatusData($data) {
      $array_res = array(
				'id' => 0,
				'additional_data' => '',
				'status'  => ''
            ) ;
      
      
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
   
   function _method_didn_return_error( $res ) {
   	if( isset($res['status']) ) {
   		$status = explode(',', $res['status']);
   		if( $status[0] == 'SUCCESS' ) return true;
   	}
   	return false;
   }
   
   
   function doEmailSend( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;	
   	
   	add_to_fp('-------- doEmailSend');
   
	   if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
	   	$response_tmp = array();
	   	foreach($input['values'] as $key => $val) {
	   		$SingleValueClass = new EmailData($val);
	   	   if( !$SingleValueClass->is_error() ) {
	            $param_array = $SingleValueClass->return_array();
	            add_to_fp('$param_array:'. print_r($param_array, true) );
	            if( strtolower($param_array['mode']) == 'auto' ) {
		            if( method_exists('Mail2Send', $param_array['method']) ) {
		            	add_to_fp("param_array['method']".$param_array['method']);
// 			            $res_array = Mail2Send::{$param_array['method']}($param_array['data'], true);
		            	$res_array = call_user_func_array( 
		            			array('Mail2Send', $param_array['method']), array($param_array['data'], true)
		            	);
			            add_to_fp('$res_array:'. print_r($res_array, true) );
			            $response_tmp[] = $res_array;
		            } else {
		            	$response_tmp[] = $this->getReturnError('doEmailSend', $input, 'METHOD: NOT IMPLEMENTED');
		            }
	            } else {
	            	$response_tmp[] = $this->getReturnError('doEmailSend', $input, 'MODE: NOT IMPLEMENTED');
	            }
	         } else {
	            $response_tmp[] = $this->getReturnError('doEmailSend', $input, $SingleValueClass->return_error() );
	         }
	   	}
	   	add_to_fp(print_r($response_tmp, true));
	   	$response = $this->_addArrayValues($response_tmp);
	   } else {
	   	$response = $this->getReturnError('doClientUserAdd', '', 'EMPTY_LIST');
	   }
   }
   

   function getClientNewList( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = true;
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');

   	$where['h.state'] ='NEW';
   	
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
            $param_array = $ParamStartLength->return_array();
            add_to_fp('$param_array:'. print_r($param_array, true) );
            $res_array = Data::getClientList((int)$param_array['id_start'], (int)$param_array['length'], $where);
            add_to_fp('$res_array:'. print_r($res_array, true) );
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getClientNewList', $input, $ParamStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getClientNewList', $input, 'WRONG CLASS');
      }

      return($response);
   }
   
   function getClientList( $input ) {//ParamStartLength, ClientData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
      
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
            $param_array = $ParamStartLength->return_array();
            add_to_fp('$param_array:'. print_r($param_array, true) );
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
   
   function getClientAttributeList( $input ) {//ParamStartLength, ClientData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
      
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
            $param_array = $ParamStartLength->return_array();
            //add_to_fp('$param_array:'. print_r($param_array, true) );
            $res_array = Data::getClientAttributeList((int)$param_array['id_start'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getClientAttributeList', $input, $ParamStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getClientAttributeList', $input, 'WRONG CLASS');
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
   

   function getClientUserAttributeList( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = true;
   
   	$ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
   
   	if( is_object($ParamStartLength) ) {
   		if( !$ParamStartLength->is_error() ) {
   			$param_array = $ParamStartLength->return_array();
   			//add_to_fp('$param_array:'. print_r($param_array, true) );
   			$res_array = Data::getClientUserAttributeList((int)$param_array['id_start'], (int)$param_array['length']);
   			$response = $this->_addArrayValues($res_array);
   		} else {
   			$response = $this->getReturnError('getClientUserAttributeList', $input, $ParamStartLength->return_error() );
   		}
   	} else {
   		$response = $this->getReturnError('getClientUserAttributeList', $input, 'WRONG CLASS');
   	}
   
   	return($response);
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
   
   function getProductClientPriceList( $input ) { //ParamDoubleStartLength, ProductData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;

      $ParamDoubleStartLength = $this->_getSingleValue($input, 'ParamDoubleStartLength');
      
      if( is_object($ParamDoubleStartLength) ) {
         if( !$ParamDoubleStartLength->is_error() ) {
            $param_array = $ParamDoubleStartLength->return_array();
            $res_array = Data::getProductClientPriceList((int)$param_array['id_start_one'], (int)$param_array['id_start_two'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getProductClientPriceList', $input, $ParamDoubleStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getProductClientPriceList', $input, 'WRONG CLASS');
      }
      
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
    
   function getAllDatabaseData( $input ) { //ParamStartLength, OrderData
      $this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = false;
      
      add_to_fp('in: getAllDatabaseData');
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
         	$param_array = $ParamStartLength->return_array();
         	add_to_fp('$param_array:'. print_r($param_array, true) );
				$response = Data::getAllDatabaseData( $param_array ) ;
         } else {
            $response = $this->getReturnError('getAllDatabaseData', $input, $ParamStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getAllDatabaseData', $input, 'WRONG CLASS');
      }
      add_to_fp('$response:'. print_r($response, true) );
      
      return($response);
   }
    
   function getOrderList( $input ) { //ParamStartLength, OrderData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
            $param_array = $ParamStartLength->return_array();
            $res_array = Data::getOrderList((int)$param_array['id_start'], (int)$param_array['length']);
            $response = array();
            
            if( sizeof($res_array) > 0 ) {
               $response = $this->_addArrayValues($res_array);
               if( (int)$param_array['length'] > 0  ) $id_chk = (int)$response['Info']['DataInfo']['id_one_max'];
               else $id_chk = (int)$response['Info']['DataInfo']['id_one_min'];
               
               $response['Info']['Rest'] = Data::getOrderListRest((int)$id_chk, (int)$param_array['length']);
            }
         } else {
            $response = $this->getReturnError('getOrderList', $input, $ParamStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getOrderList', $input, 'WRONG CLASS');
      }

      return($response);
   }
   
   function getOrderAttributeList( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = true;
   
   	$ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
   
   	if( is_object($ParamStartLength) ) {
   		if( !$ParamStartLength->is_error() ) {
   			$param_array = $ParamStartLength->return_array();
   			//add_to_fp('$param_array:'. print_r($param_array, true) );
   			$res_array = Data::getOrderAttributeList((int)$param_array['id_start'], (int)$param_array['length']);
   			$response = $this->_addArrayValues($res_array);
   		} else {
   			$response = $this->getReturnError('getOrderAttributeList', $input, $ParamStartLength->return_error() );
   		}
   	} else {
   		$response = $this->getReturnError('getOrderAttributeList', $input, 'WRONG CLASS');
   	}
   
   	return($response);
   }
       
   function getOrderHiddenStatusList( $input ) { //ParamStartLength, OrderData
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamStartWhereLength = $this->_getSingleValue($input, 'ParamStartWhereLength');
      if( is_object($ParamStartWhereLength) ) {
         if( !$ParamStartWhereLength->is_error() ) {
            $param_array = $ParamStartWhereLength->return_array();
            $res_array = Data::getOrderList((int)$param_array['id_start'], (int)$param_array['length'], $param_array['where']);
            $response = array();
            
            if( sizeof($res_array) > 0 ) {
               $response = $this->_addArrayValues($res_array);
               if( (int)$param_array['length'] > 0  ) $id_chk = (int)$response['Info']['DataInfo']['id_one_max'];
               else $id_chk = (int)$response['Info']['DataInfo']['id_one_min'];
               
               $response['Info']['Rest'] = Data::getOrderListRest((int)$id_chk, (int)$param_array['length'], $param_array['where']);
            }
         } else {
            $response = $this->getReturnError('getOrderHiddenStatusList', $input, $ParamStartWhereLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getOrderHiddenStatusList', $input, 'WRONG CLASS');
      }

      return($response);
   }
       
   function doClientChange ( $input ) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;
      
      add_to_fp('-------- doClientChange');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doClientAdd($SingleValueClass, false);
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


   function doClientNewIdUpdateList ( $input ) {
      $this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = false;
      
      add_to_fp('-------- doClientNewIdUpdateList');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               list($mstr, $id_client_new) = explode(':', $param_array['description']);
               if( ($mstr == 'new_id' || $mstr == 'new_id_client') && (int)$id_client_new > 0 ) {
   					$res_additional_data = array();
   				
   					$res_additional_data['doClientNewIdUpdateStart'] = Data::doBatchStart($param_array);
               	
   					//id_one, additional_data, status
   					$res = Data::doClientNewIdUpdateList((int)$param_array['id_client'], (int)$id_client_new);
   					add_to_fp('$res:'. print_r($res, true) );
   					$res_additional_data['doClientNewIdUpdateList'] = $res['additional_data'];
   					
   					if( $this->_method_didn_return_error($res) ) {
   						
	   					foreach( $SingleValueClass->list_method as $key => $method_name ) {
	   						add_to_fp('list_method: '."$key => $method_name\n".print_r( $param_array[$key],true) );
	   						list($val_out, $status) = $SingleValueClass->data_exist($key, $param_array[$key]);
	   						if( Framework::not_null($val_out) && Framework::not_null($param_array[$key]) ) {
	   							$input_val['values'] = $param_array[$key];
	   							add_to_fp('list_method $input_val: '.print_r($input_val,true));
	   							$res_additional_data_tmp = Data::doClientUserCleanMethodData( $key, $param_array );
	   							$res_additional_data[$key] = $this->${method_name}( $input_val );
	   							$res_additional_data[$key]['Info']['Remove'] = $res_additional_data_tmp;
	   						}
   						}
   						
   					}
   				
   					$res_additional_data['doClientNewIdUpdateStop'] = Data::doBatchStop($param_array);
   					$res['additional_data'] = $res_additional_data;
   				
   					$response_tmp[] = $res;
               } else {
               	$response_tmp[] = $this->getReturnError('doClientNewIdUpdateList', $val, 'WRONG_NEW_ID');
               }
            } else {
               $response_tmp[] = $this->getReturnError('doClientNewIdUpdateList', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doClientNewIdUpdateList', '', 'EMPTY_LIST');
      }
      
      return($response);
   }
   

   function doClientUserNewIdUpdateList ( $input ) {
   	$this->input_data_type = 'UPDATE';
   	$this->SingleParam_MultipleReturns = false;
   
   	add_to_fp('-------- doClientUserNewIdUpdateList');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ClientUserData($val, $this->input_data_type);
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				$new_param_array = explode(':', $param_array['description']);
   				if( sizeof($new_param_array) > 0 && in_array('new_id_client_user', $new_param_array) !== FALSE ) {
   					$new_ids_array['id_client_user'] = (int)$new_param_array[1];
   					if( sizeof($new_param_array) > 2 ) $new_ids_array['id_client'] = (int)$new_param_array[3];
   					else $new_ids_array['id_client'] = (int)$param_array['id_client'];
   					
   					$response_tmp[] = Data::doClientUserNewIdUpdateList($param_array, $new_ids_array);
   				} else {
   					$response_tmp[] = $this->getReturnError('doClientUserNewIdUpdateList', $val, 'WRONG_NEW_ID');
   				}
   			} else {
   				$response_tmp[] = $this->getReturnError('doClientUserNewIdUpdateList', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doClientUserNewIdUpdateList', '', 'EMPTY_LIST');
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
               $response_tmp[] = Data::doClientAdd($SingleValueClass, true);
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
   
   function doClientRemovePermanently ( $input ) {
   	$this->input_data_type = 'UPDATE';
   	$this->SingleParam_MultipleReturns = false;
   
   	add_to_fp('-------- doClientRemovePermanently');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ClientData($val, $this->input_data_type);
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				$response_tmp[] = Data::doClientRemovePermanently($param_array, true);
   			} else {
   				$response_tmp[] = $this->getReturnError('doClientRemovePermanently', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doClientRemovePermanently', '', 'EMPTY_LIST');
   	}
   
   	return($response);
   }
   

   function doClientAttributeAddOrUpdate( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;
   
		add_to_fp('-------- doClientAttributeAddOrUpdate');
		
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientAttributeData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doClientAttributeAddOrUpdate($param_array);
            } else {
               $response_tmp[] = $this->getReturnError('doClientAttributeAddOrUpdate', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doClientAttributeAddOrUpdate', '', 'EMPTY_LIST');
      }
      
      return($response);
   }

   function doClientUserAttributeAddOrUpdate( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;
   	 
   	add_to_fp('-------- doClientUserAttributeAddOrUpdate');
   
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ClientUserAttributeData($val, $this->input_data_type);
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				$response_tmp[] = Data::doClientUserAttributeAddOrUpdate($param_array);
   			} else {
   				$response_tmp[] = $this->getReturnError('doClientUserAttributeAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doClientUserAttributeAddOrUpdate', '', 'EMPTY_LIST');
   	}
   
   	return($response);
   }
    
      
   function doClientUserAddOrUpdate( $input ) {
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
               $response_tmp[] = Data::doClientUserAdd($SingleValueClass, true);
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
   

   function doClientUserCleanAddOrUpdate( $input ) {
   	$this->input_data_type = 'UPDATE';
   	$this->SingleParam_MultipleReturns = false;
   	
   	add_to_fp('-------- doClientUserCleanAddOrUpdate');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ClientUserData($val, $this->input_data_type);
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				if( !$SingleValueClass->is_error() ) {
   					$param_array = $SingleValueClass->return_array();
   					add_to_fp('$param_array:'. print_r($param_array, true) );
   					//$res = array('id_one' => '', 'additional_data' => '', 'status' => '');
   				
   					$res_additional_data = array();
   				
   					$res_additional_data['doClientUserCleanStart'] = Data::doBatchStart($param_array);
   					//id_one, additional_data, status
   					$res = Data::doClientUserAddOrUpdate($param_array);
   					add_to_fp('$res:'. print_r($res, true) );
   					$res_additional_data['doClientUserAddOrUpdate'] = $res['additional_data'];
   					
   					if( $this->_method_didn_return_error($res) ) {
   						
	   					foreach( $SingleValueClass->list_method as $key => $method_name ) {
	   						add_to_fp('list_method: '."$key => $method_name\n".print_r( $param_array[$key],true) );
	   						list($val_out, $status) = $SingleValueClass->data_exist($key, $param_array[$key]);
	   						if( Framework::not_null($val_out) && Framework::not_null($param_array[$key]) ) {
	   							$input_val['values'] = $param_array[$key];
	   							add_to_fp('list_method $input_val: '.print_r($input_val,true));
	   							$res_additional_data_tmp = Data::doClientUserCleanMethodData( $key, $param_array );
	   							$res_additional_data[$key] = $this->${method_name}( $input_val );
	   							$res_additional_data[$key]['Info']['Remove'] = $res_additional_data_tmp;
	   						}
   						}
   						
   					}
   				
   					$res_additional_data['doClientUserCleanStop'] = Data::doBatchStop($param_array);
   					$res['additional_data'] = $res_additional_data;
   				
   					$response_tmp[] = $res;
   				} else {
   					$response_tmp[] = $this->getReturnError('doClientUserCleanAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   				}
   			} else {
   				$response_tmp[] = $this->getReturnError('doClientUserCleanAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r('$response_tmp:'.$response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doClientUserCleanAddOrUpdate', '', 'EMPTY_LIST');
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
               $response_tmp[] = Data::doClientUserAdd($SingleValueClass, true);
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

      add_to_fp('-------- doClientUserChange');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientUserData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doClientUserAdd($SingleValueClass, false);
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
   
/*
 * New start
 */
   function doClientUserAddressAddOrUpdate( $input ) {
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;
   
   	add_to_fp('-------- doClientUserAddressAddOrUpdate');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ClientUserAddressData($val, $this->input_data_type);
   			add_to_fp(print_r($SingleValueClass, true));
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				$response_tmp[] = Data::doClientUserAddressAddOrUpdate($param_array);
   			} else {
   				$response_tmp[] = $this->getReturnError('doClientUserAddressAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doClientUserAddressAddOrUpdate', '', 'EMPTY_LIST');
   	}
   
   	return($response);
   }   
   
   function doClientUserAddressDelete( $input ) {
   	$this->input_data_type = 'UPDATE';
   	$this->SingleParam_MultipleReturns = false;
   
   	add_to_fp('-------- doClientUserAddressDelete');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ClientUserAddressData($val, $this->input_data_type);
   			add_to_fp(print_r($SingleValueClass, true));
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				$response_tmp[] = Data::doClientUserAddressDelete($param_array);
   			} else {
   				$response_tmp[] = $this->getReturnError('doClientUserAddressDelete', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doClientUserAddressDelete', '', 'EMPTY_LIST');
   	}
   
   	return($response);
   }

   function getClientUserAddressList( $input ) {
   	$this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamDoubleStartLength = $this->_getSingleValue($input, 'ParamDoubleStartLength');
      
   	add_to_fp('-------- getClientUserAddress');
      if( is_object($ParamDoubleStartLength) ) {
         if( !$ParamDoubleStartLength->is_error() ) {
            $param_array = $ParamDoubleStartLength->return_array();
            add_to_fp(print_r($param_array, true));
            $res_array = Data::getClientUserAddressList((int)$param_array['id_start_one'], (int)$param_array['id_start_two'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getClientUserAddress', $input, $ParamDoubleStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getClientUserAddress', $input, 'WRONG CLASS');
      }
   	 
   	return($response);
   }
   
   function getClientAddressList( $input ) {
   	$this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamDoubleStartLength = $this->_getSingleValue($input, 'ParamDoubleStartLength');
      
   	add_to_fp('-------- getClientAddress');
      if( is_object($ParamDoubleStartLength) ) {
         if( !$ParamDoubleStartLength->is_error() ) {
            $param_array = $ParamDoubleStartLength->return_array();
            $res_array = Data::getClientAddressList((int)$param_array['id_start_one'], (int)$param_array['id_start_two'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getClientAddress', $input, $ParamDoubleStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getClientAddress', $input, 'WRONG CLASS');
      }
   	 
   	return($response);
   }
   
   function getAddressList( $input ) {
   	$this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = true;
      
      $ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
      
   	add_to_fp('-------- getAddressList');
      if( is_object($ParamStartLength) ) {
         if( !$ParamStartLength->is_error() ) {
            $param_array = $ParamStartLength->return_array();
            $res_array = Data::getAddressList((int)$param_array['id_start'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getAddressList', $input, $ParamDoubleStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getAddressList', $input, 'WRONG CLASS');
      }
   	 
   	return($response);
   }
   /*
    * New end
   */
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
               add_to_fp('$param_array:'. print_r($SingleValueClass->return_array(), true) );
               $response_tmp[] = Data::doProductChange( $SingleValueClass );
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
   
   function doProductAddOrUpdate( $input ) {
      $this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = false;

      add_to_fp('-------- doProductAddOrUpdate');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ProductData($val, $this->input_data_type);
            add_to_fp(print_r($SingleValueClass, true));
            if( !$SingleValueClass->is_error() ) {
               add_to_fp('$param_array:'. print_r($SingleValueClass->return_array(), true) );
               $response_tmp[] = Data::doProductAddOrUpdate( $SingleValueClass );
            } else {
               $response_tmp[] = $this->getReturnError('doProductAddOrUpdate', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doProductAddOrUpdate', '', 'EMPTY_LIST');
      }
      
      return($response);
   }
   
   function doProductCleanAddOrUpdate( $input ) {
   	$this->input_data_type = 'UPDATE';
   	$this->SingleParam_MultipleReturns = false;
   
   	add_to_fp('-------- doProductCleanAddOrUpdate');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ProductData($val, $this->input_data_type);
   			add_to_fp(print_r($SingleValueClass, true));
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				//$res = array('id_one' => '', 'additional_data' => '', 'status' => '');
   				
   				$res_additional_data = array();
   				
   				$res_additional_data['doProductCleanStart'] = Data::doBatchStart($param_array);				
   				//id_one, additional_data, status
   				$res = Data::doProductAddOrUpdate( $SingleValueClass );
   				$res_additional_data['doProductAddOrUpdate'] = $res['additional_data'];

   				if( $this->_method_didn_return_error($res) ) {
   					
	   				foreach( $SingleValueClass->list_method as $key => $method_name ) {
	   					add_to_fp('list_method: '."$key => $method_name\n".print_r( $param_array[$key],true) );
	   					list($val_out, $status) = $SingleValueClass->data_exist($key, $param_array[$key]);
	   				   if( Framework::not_null($val_out) && Framework::not_null($param_array[$key]) ) {
	   						$input_val['values'] = $param_array[$key];
	   						add_to_fp('list_method $input_val: '.print_r($input_val,true));
	   						$res_additional_data_tmp = Data::doProductCleanMethodData( $key, (int)$param_array['id_product'] );
	   						$res_additional_data[$key] = $this->${method_name}( $input_val );
	   						$res_additional_data[$key]['Info']['Remove'] = $res_additional_data_tmp;
	   					}
	   				}
	   				
   				}
   				
   				$res_additional_data['doProductCleanStop'] = Data::doBatchStop($param_array);
   				$res['additional_data'] = $res_additional_data;
   				
   				$response_tmp[] = $res;
   			} else {
   				$response_tmp[] = $this->getReturnError('doProductCleanAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp('before return:'.print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doProductCleanAddOrUpdate', '', 'EMPTY_LIST');
   	}
   
   	return($response);
   }

   function doClientCleanAddOrUpdate( $input ) {
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;
   	 
   	add_to_fp('-------- doClientUserCleanAddOrUpdate');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ClientData($val, $this->input_data_type);
   			add_to_fp(print_r($SingleValueClass, true));
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				//$res = array('id_one' => '', 'additional_data' => '', 'status' => '');
   					
   				$res_additional_data = array();
   					
   				$res_additional_data['doClientCleanStart'] = Data::doBatchStart($param_array);
   				//id_one, additional_data, status
   				$res = Data::doClientAddOrUpdate( $SingleValueClass );
   				$res_additional_data['doClientAddOrUpdate'] = $res['additional_data'];
   				
   				if( $this->_method_didn_return_error($res) ) {
   		
	   				foreach( $SingleValueClass->list_method as $key => $method_name ) {
	   					add_to_fp('list_method: '."$key => $method_name\n".print_r( $param_array[$key],true) );
	   					list($val_out, $status) = $SingleValueClass->data_exist($key, $param_array[$key]);
	   					if( Framework::not_null($val_out) && Framework::not_null($param_array[$key]) ) {
	   						$input_val['values'] = $param_array[$key];
	   						add_to_fp('list_method $input_val: '.print_r($input_val,true));
	   						$res_additional_data_tmp = Data::doClientCleanMethodData( $key, (int)$param_array['id_client'] );
	   						$res_additional_data[$key] = $this->${method_name}( $input_val );
	   						$res_additional_data[$key]['Info']['Remove'] = $res_additional_data_tmp;
	   					}
   					}
   				
   				}
   					
   				$res_additional_data['doClientCleanStop'] = Data::doBatchStop($param_array);
   				$res['additional_data'] = $res_additional_data;
   					
   				$response_tmp[] = $res;
   			} else {
   				$response_tmp[] = $this->getReturnError('doClientCleanAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}

   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doClientCleanAddOrUpdate', '', 'EMPTY_LIST');
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
               $response_tmp[] = Data::setProductClientPrice($pa['id_product'], $pa['id_client'], $pa['price']);
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

   function setClientProductPriceListUpdate( $input ) {
      return $this->doClientProductPriceListAddOrUpdate( $input, true);
   }
  
   function setClientProductPriceList( $input ) {
      return $this->doClientProductPriceListAddOrUpdate( $input, false);
   }
   
   function doClientProductPriceListAddOrUpdate( $input, $update = false) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;
      add_to_fp('-------- doClientProductPriceListAddOrUpdate ');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         $del_count = 0;
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new ClientProductPriceListData($val, $this->input_data_type);
            add_to_fp('$param_array:'. print_r($SingleValueClass, true) );
            if( !$SingleValueClass->is_error() ) {
               $pa = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($pa, true) );
               if( !$update ) $del_count = Data::doProductClientPriceClean($pa['id_client']);
               $ins_count = Data::setClientProductPriceList($pa['id_client'],  $pa['ProductPriceData']);
               add_to_fp('$ins_count:'. print_r($ins_count, true) );
               if( $ins_count > 0 ) $status_res = 'SUCCESS';
               else $status_res = 'ERROR';
               $additional_data = "DEL: $del_count, INS: $ins_count";
               $response_tmp[] = array('id' => $pa['id_client'], 'additional_data' => $additional_data,  'status' => $status_res );
            } else {
               $response_tmp[] = $this->getReturnError('doClientProductPriceListAddOrUpdate', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
         $response['Info']['Deleted'] = $del_count;
         $response['Info']['Inserted'] = $ins_count;
      } else {
         $response = $this->getReturnError('doClientProductPriceListAddOrUpdate ', '', 'EMPTY_LIST');
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

   function doCategoryAddOrUpdate( $input ) {
      $this->input_data_type = 'NEW';
      $this->SingleParam_MultipleReturns = false;
   
      add_to_fp('-------- doCategoryAddOrUpdate');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new CategoryData($val, $this->input_data_type);
            add_to_fp(print_r($SingleValueClass, true));
            if( !$SingleValueClass->is_error() ) {
               $param_array = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
               $response_tmp[] = Data::doCategoryAddOrUpdate($param_array);
            } else {
               $response_tmp[] = $this->getReturnError('doCategoryAddOrUpdate', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('doCategoryAddOrUpdate', '', 'EMPTY_LIST');
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
            $SingleValueClass = new Product2CategoryData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $pa = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($param_array, true) );
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
   
   function doPictureAddOrUpdate( $input ) {
   	return self::setPicture( $input );
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
   
   function doOrderAttributeAddOrUpdate( $input ) {
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;
   	 
   	add_to_fp('-------- doOrderAttributeAddOrUpdate');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new OrderAttributeData($val, $this->input_data_type);
   			add_to_fp(print_r($SingleValueClass, true));
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				$response_tmp[] = Data::doOrderAttributeAddOrUpdate($param_array);
   			} else {
   				$response_tmp[] = $this->getReturnError('doOrderAttributeAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doOrderAttributeAddOrUpdate', '', 'EMPTY_LIST');
   	}
   	 
   	return($response);
   }
   
   function setOrderStatus( $input ) {
	  $this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = false;
      
      add_to_fp('-------- setOrderStatus');
      if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
         $response_tmp = array();
         foreach($input['values'] as $key => $val) {
            $SingleValueClass = new OrderData($val, $this->input_data_type);
            if( !$SingleValueClass->is_error() ) {
               $pa = $SingleValueClass->return_array();
               add_to_fp('$param_array:'. print_r($pa, true) );
               $response_tmp[] = Data::setOrderStatus($pa['id_order'], $pa['id_order_status'], $pa['description']);
            } else {
               $response_tmp[] = $this->getReturnError('setOrderStatus', $val, $SingleValueClass->return_error(), false);
            }
         }
         add_to_fp(print_r($response_tmp, true));
         $response = $this->_addArrayValues($response_tmp);
      } else {
         $response = $this->getReturnError('setOrderStatus', '', 'EMPTY_LIST');
      }
      
      return($response);
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
// ##############################
   function getInvoiceList( $input ) {
   	$this->input_data_type = 'UPDATE';
   	$this->SingleParam_MultipleReturns = true;
   	 
   	$ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
   	 
   	add_to_fp('-------- getInvoiceList');
   	add_to_fp('$ParamStartLength: '.var_export($ParamStartLength, true));
   	
   	if( is_object($ParamStartLength) ) {
   		if( !$ParamStartLength->is_error() ) {
   			$param_array = $ParamStartLength->return_array();
   			add_to_fp('$param_array: '.$param_array);
   			$res_array = Data::getInvoiceList((int)$param_array['id_start'], (int)$param_array['length']);
   			add_to_fp('$res_array: '.$res_array);
   			$response = $this->_addArrayValues($res_array);
   		} else {
   			$response = $this->getReturnError('getInvoiceList', $input, $ParamStartLength->return_error() );
   		}
   	} else {
   		$response = $this->getReturnError('getInvoiceList', $input, 'WRONG CLASS');
   	}
   	 
   	return($response);
   }
    
   function getClientInvoiceList( $input ) {
   	$this->input_data_type = 'UPDATE';
      $this->SingleParam_MultipleReturns = true;

      $ParamDoubleStartLength = $this->_getSingleValue($input, 'ParamDoubleStartLength');
      
   	add_to_fp('-------- getInvoiceList');
      if( is_object($ParamDoubleStartLength) ) {
         if( !$ParamDoubleStartLength->is_error() ) {
            $param_array = $ParamDoubleStartLength->return_array();
            $res_array = Data::getClientInvoiceList((int)$param_array['id_start_one'], (int)$param_array['id_start_two'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
         } else {
            $response = $this->getReturnError('getInvoiceList', $input, $ParamDoubleStartLength->return_error() );
         }
      } else {
         $response = $this->getReturnError('getInvoiceList', $input, 'WRONG CLASS');
      }
   	 
   	return($response);
   }  
   

   function getOrderInvoiceList( $input ) {
   	$this->input_data_type = 'UPDATE';
   	$this->SingleParam_MultipleReturns = true;
   
   	$ParamDoubleStartLength = $this->_getSingleValue($input, 'ParamDoubleStartLength');
     
   	add_to_fp('-------- getOrderInvoiceList');
   	if( is_object($ParamDoubleStartLength) ) {
   		if( !$ParamDoubleStartLength->is_error() ) {
   			$param_array = $ParamDoubleStartLength->return_array();
   			$res_array = Data::getOrderInvoiceList((int)$param_array['id_start_one'], (int)$param_array['id_start_two'], (int)$param_array['length']);
            $response = $this->_addArrayValues($res_array);
   		} else {
   			$response = $this->getReturnError('getOrderInvoiceList', $input, $ParamDoubleStartLength->return_error() );
   		}
   	} else {
   		$response = $this->getReturnError('getOrderInvoiceList', $input, 'WRONG CLASS');
   	}
   	 
   	return($response);
   }

   
   function doInvoiceAddOrUpdate( $input ) {
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;
   	
   	add_to_fp('-------- doInvoiceAddOrUpdate');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new OrderInvoiceData($val, $this->input_data_type);
   			if( !$SingleValueClass->is_error() ) {
   				$param = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param, true) );
   				$response_tmp[] = Data::doInvoiceAddOrUpdate($param);
   			} else {
   				$response_tmp[] = $this->getReturnError('doInvoiceAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doInvoiceAddOrUpdate', '', 'EMPTY_LIST');
   	}
   	
   	return($response);
   }
    

   function setInvoiceStatus( $input ) {
   	$this->input_data_type = 'UPDATE';
   	$this->SingleParam_MultipleReturns = false;
   
   	add_to_fp('-------- setInvoiceStatus');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new OrderInvoiceData($val, $this->input_data_type);
   			add_to_fp('$$SingleValueClass:'. print_r($SingleValueClass, true) );
   			if( !$SingleValueClass->is_error() ) {
   				$param = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param, true) );
   				$response_tmp[] = Data::setInvoiceStatus($param);
   			} else {
   				$response_tmp[] = $this->getReturnError('setInvoiceStatus', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('setInvoiceStatus', '', 'EMPTY_LIST');
   	}
   
   	return($response);
   }   
//--------------
   
   
   function doClientAccountManagerAddOrUpdate( $input ) {
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;
   
   	add_to_fp('-------- doClientAccountManagerAddOrUpdate');
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new AccountManagerData($val, $this->input_data_type);
   				add_to_fp('$$SingleValueClass:'. print_r($SingleValueClass, true) );
   			if( !$SingleValueClass->is_error() ) {
   				$param = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param, true) );
   				$response_tmp[] = Data::doClientAccountManagerAddOrUpdate($param);
   			} else {
   				$response_tmp[] = $this->getReturnError('doClientAccountManagerAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doClientAccountManagerAddOrUpdate', '', 'EMPTY_LIST');
   	}
   
   	return($response);
   }  
   
   function getClientAccountManagerList( $input ) {
   	$this->input_data_type = 'UPDATE';
   	$this->SingleParam_MultipleReturns = true;
   	 
   	$ParamDoubleStartLength = $this->_getSingleValue($input, 'ParamStartLength');
   	 
   	add_to_fp('-------- getClientAccountManagerList');
   	if( is_object($ParamDoubleStartLength) ) {
   		if( !$ParamDoubleStartLength->is_error() ) {
   			$param_array = $ParamDoubleStartLength->return_array();
   			$res_array = Data::getClientAccountManagerList((int)$param_array['id_start'], (int)$param_array['length']);
   			$response = $this->_addArrayValues($res_array);
   		} else {
   			$response = $this->getReturnError('getClientAccountManagerList', $input, $ParamDoubleStartLength->return_error() );
   		}
   	} else {
   		$response = $this->getReturnError('getClientAccountManagerList', $input, 'WRONG CLASS');
   	}
   	 
   	return($response);
   }   
   
   function getProductAttributeWGroupList( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = true;

   	$ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
   	 
   	if( is_object($ParamStartLength) ) {
   		if( !$ParamStartLength->is_error() ) {
   			$param_array = $ParamStartLength->return_array();
   			//add_to_fp('$param_array:'. print_r($param_array, true) );
   			$res_array = Data::getProductAttributeWGroupList((int)$param_array['id_start'], (int)$param_array['length']);
   			$response = $this->_addArrayValues($res_array);
   		} else {
   			$response = $this->getReturnError('getProductAttributeWGroupList', $input, $ParamStartLength->return_error() );
   		}
   	} else {
   		$response = $this->getReturnError('getProductAttributeWGroupList', $input, 'WRONG CLASS');
   	}
   	 
   	return($response);
   }
   
   function doProductAttributeWGroupAddOrUpdate( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;
   	 
   	add_to_fp('-------- doProductAttributeWGroupAddOrUpdate');
   	 
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ProductAttributeWGroupData($val, $this->input_data_type);
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				$response_tmp[] = Data::doProductAttributeWGroupAddOrUpdate($param_array);
   			} else {
   				$response_tmp[] = $this->getReturnError('doProductAttributeWGroupAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doProductAttributeWGroupAddOrUpdate', '', 'EMPTY_LIST');
   	}
   	 
   	return($response);
   }


   function getProductAttributeList( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = true;
   	 
   	$ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
   	 
   	if( is_object($ParamStartLength) ) {
   		if( !$ParamStartLength->is_error() ) {
   			$param_array = $ParamStartLength->return_array();
   			//add_to_fp('$param_array:'. print_r($param_array, true) );
   			$res_array = Data::getShopProductAttributeList((int)$param_array['id_start'], (int)$param_array['length']);
   			$response = $this->_addArrayValues($res_array);
   		} else {
   			$response = $this->getReturnError('getShopProductAttributeList', $input, $ParamStartLength->return_error() );
   		}
   	} else {
   		$response = $this->getReturnError('getShopProductAttributeList', $input, 'WRONG CLASS');
   	}
   	 
   	return($response);
   }
    
   function doProductAttributeAddOrUpdate( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;
   	 
   	add_to_fp('-------- doProductAttributeAddOrUpdate');
   	 
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ProductAttributeData($val, $this->input_data_type);
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				$response_tmp[] = Data::doProductAttributeAddOrUpdate($param_array);
   			} else {
   				$response_tmp[] = $this->getReturnError('doProductAttributeAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doProductAttributeAddOrUpdate', '', 'EMPTY_LIST');
   	}
   	 
   	return($response);
   }
   
   
   function getShopAttributeList( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = true;
   
   	$ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
   
   	if( is_object($ParamStartLength) ) {
   		if( !$ParamStartLength->is_error() ) {
   			$param_array = $ParamStartLength->return_array();
   			//add_to_fp('$param_array:'. print_r($param_array, true) );
   			$res_array = Data::getShopAttributeList((int)$param_array['id_start'], (int)$param_array['length']);
   			$response = $this->_addArrayValues($res_array);
   		} else {
   			$response = $this->getReturnError('getShopAttributeList', $input, $ParamStartLength->return_error() );
   		}
   	} else {
   		$response = $this->getReturnError('getShopAttributeList', $input, 'WRONG CLASS');
   	}
   
   	return($response);
   }
   
   function doShopAttributeAddOrUpdate( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = false;
   	 
   	add_to_fp('-------- doShopAttributeAddOrUpdate');
   	 
   	if( isset($input['values']) && is_array($input['values']) && sizeof($input['values']) > 0) {
   		$response_tmp = array();
   		foreach($input['values'] as $key => $val) {
   			$SingleValueClass = new ShopAttributeData($val, $this->input_data_type);
   			if( !$SingleValueClass->is_error() ) {
   				$param_array = $SingleValueClass->return_array();
   				add_to_fp('$param_array:'. print_r($param_array, true) );
   				$response_tmp[] = Data::doShopAttributeAddOrUpdate($param_array);
   			} else {
   				$response_tmp[] = $this->getReturnError('doShopAttributeAddOrUpdate', $val, $SingleValueClass->return_error(), false);
   			}
   		}
   		add_to_fp(print_r($response_tmp, true));
   		$response = $this->_addArrayValues($response_tmp);
   	} else {
   		$response = $this->getReturnError('doShopAttributeAddOrUpdate', '', 'EMPTY_LIST');
   	}
   	 
   	return($response);
   }
   
   function doRecalculateUPSData( $input ) {//ParamStartLength, ClientData
   	$this->input_data_type = 'NEW';
   	$this->SingleParam_MultipleReturns = true;
   
   	$ParamStartLength = $this->_getSingleValue($input, 'ParamStartLength');
   
   	if( is_object($ParamStartLength) ) {
   		if( !$ParamStartLength->is_error() ) {
   			$param_array = $ParamStartLength->return_array();
   			//add_to_fp('$param_array:'. print_r($param_array, true) );
   			$res_array = Data::doRecalculateUPSData((int)$param_array['id_start']);
   			$response = $this->_addArrayValues($res_array);
   		} else {
   			$response = $this->getReturnError('doRecalculateUPSData', $input, $ParamStartLength->return_error() );
   		}
   	} else {
   		$response = $this->getReturnError('doRecalculateUPSData', $input, 'WRONG CLASS');
   	}
   
   	return($response);
   }
   
// -------------------------   
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