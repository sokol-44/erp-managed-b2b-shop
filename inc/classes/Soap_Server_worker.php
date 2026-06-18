<?php
/**
 * Soap_Server_worker.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Soap_Server_worker
 * Handles SOAP server worker tasks, providing various operations for processing
 * client, order, category, and product data, converting structures, and interfacing with data persistence.
 *
 * @designPattern Worker
 *
 * @todo Refactor global state usage and dynamic instantiation (`new $type()`, `this->${method_name}()`).
 * @todo Transition to modern exceptions instead of relying on custom return arrays containing error signals.
 * @todo Implement strict parameter typing, return declarations, and enforce visibility visibility structures.
 * @todo Decouple logging logic (`add_to_fp`) into a standard PSR-3 Logger interface.
 * @todo Refactor schema declarations by relocating them to separate files.
 */
class Soap_Server_worker {
   /**
    * @var string Determines the operational input data schema type.
    */
   public $input_data_type = 'NEW';
   /**
    * @var string Determines the detailed format of output structures returned by the methods.
    */
   public $output_data_type = 'FULL'; //
   /**
    * @var bool Flag determining whether a single parameter format maps to multiple collection returns.
    */
   public $SingleParam_MultipleReturns = false;


   /**
    * Resolves and builds a custom typed class object using indexed single arrays.
    *
    * @param array  $input Input collection mapping parameters.
    * @param string $type  The expected target class name to instantiate dynamically.
    * @return object|bool Returns an instance of the dynamically declared class name, or false on error.
    *
    * @todo Use explicit type-hints and strict conditional tracking instead of open string definitions.
    */
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

   /**
    * Builds standardized error data response payloads.
    *
    * @param string     $method    The execution context method scope name.
    * @param mixed      $data      The processing runtime parameters payload.
    * @param string|int $info      Contextual message metadata description or code.
    * @param bool       $top_array Defines nesting structure formatting wrappers.
    * @return array Prepared key-value dictionary schema holding formatted data, parameters, and debug traces.
    *
    * @todo Standardize return shapes via dedicated Value Objects instead of multi-dimensional arrays.
    */
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

   /**
    * Evaluates and sets basic placeholder payload models for operational results.
    *
    * @param mixed $data Raw evaluation details metadata.
    * @return void
    *
    * @todo Complete implementation of this method or safely drop it if no longer needed.
    */
   public function getReturnStatusData($data) {
      $array_res = array(
                'id' => 0,
                'additional_data' => '',
                'status'  => ''
            ) ;


   }

   /**
    * Analyzes collection arrays to extrapolate minimum and maximum identities tracking indexes.
    *
    * @param array $array Reference target map structure grouping identifier arrays.
    * @return array Normalized grouping tracking the range criteria and mapping initial values.
    *
    * @todo Replace iterative key matching and nested loops with modular array filter mappings or standard definitions.
    */
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

   /**
    * Verifies if a method tracking state response signal resolved with success.
    *
    * @param array $res Context status configuration array.
    * @return bool True if status parameters map to SUCCESS, otherwise false.
    *
    * @todo Swap status string evaluations with strict boolean parameters or strongly typed Enum patterns.
    */
   function _method_didn_return_error( $res ) {
       if( isset($res['status']) ) {
           $status = explode(',', $res['status']);
           if( $status[0] == 'SUCCESS' ) return true;
       }
       return false;
   }

   /**
    * Dispatches electronic mail communication updates routing values through Mail2Send services.
    *
    * @param array $input Parameter dictionary mapping target EmailData.
    * @return void
    *
    * @todo Refactor `call_user_func_array` into object-oriented injected service components.
    * @todo Provide concrete return metrics instead of implicit variable state tracking.
    */
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
//                         $res_array = Mail2Send::{$param_array['method']}($param_array['data'], true);
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


   /**
    * Collects data indexes corresponding to newly tracked clients.
    *
    * @param array $input Payload definition parameter dictionary.
    * @return array Normalized response arrays tracking calculated values or mapping specific error contexts.
    *
    * @todo Remove hardcoded SQL/Where fragments and isolate conditions into data query scopes.
    */
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

   /**
    * Returns pagination segments corresponding to globally captured clients.
    *
    * @param array $input Payload boundaries parameter dictionary mapping pagination targets.
    * @return array Formatted outcome dictionaries or associated execution error arrays.
    *
    * @todo Switch raw int-casting mapping statements into a standardized filter criteria value structure.
    */
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

   /**
    * Generates lists indexing custom configuration criteria metrics mapped onto clients.
    *
    * @param array $input Payload metadata properties mapping pagination inputs.
    * @return array Resolved target metadata schemas or standard error traces.
    *
    * @todo Encapsulate model properties within structured repository layer components.
    */
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

   /**
    * Retreives detailed profiles corresponding to administrative client users.
    *
    * @param array $input Pagination parameters map defining offsets.
    * @return array Standard structured result dictionaries.
    *
    * @todo Rename `ParamDoubleStartLength` to a clear, self-documenting domain model nomenclature.
    */
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


   /**
    * Retreives operational criteria metadata traits assigned onto client user contexts.
    *
    * @param array $input Input pagination details mapping targets.
    * @return array Response schema layout tracking information map.
    *
    * @todo Abstract custom framework logic into independent decoupled interface modules.
    */
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

   /**
    * Returns product prices specific to specific targeted client configurations.
    *
    * @param array $input Offset variables tracking composite conditions mapping references.
    * @return array Compiled price matrix data sets.
    * @todo Avoid using multi-variable generic offset pointers; pass descriptive context variables.
    */
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

   /**
    * Lists global product items relative to standard customer price catalogs.
    *
    * @param array $input Structure criteria holding bounds definitions.
    * @return array Extracted details mapping definitions.
    *
    * @todo Refactor data extraction methods to return consistent data structures.
    */
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

   /**
    * Fetches organizational store category items boundaries data.
    *
    * @param array $input Offset indexes bounding pagination requirements.
    * @return array Output structured results data dictionary.
    *
    * @todo Implement standardized caching protocols for stable structural metadata loops.
    */
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


   /**
    * Returns paginated collections indexing general products catalogs metadata details.
    *
    * @param array $input Pagination parameters metadata inputs dictionary.
    * @return array Data collection response structure.
    *
    * @todo Implement Type Hinting matching modern syntax structures.
    */
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

   /**
    * Dispatches relational lookup fetching products mapping specifically inside structured categories.
    *
    * @param array $input Query configuration mapping limits.
    * @return array Resolved target metadata models arrays.
    *
    * @todo Decouple raw string indexes mapping array variables into separate business logic classes.
    */
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

   /**
    * Pulls transactions details marked with an active unhidden new transactional identity status.
    *
    * @param array $input Object variables properties tracking offset bounds.
    * @return array Target response tracking array elements.
    *
    * @todo Extract SQL fragment constraints safely out of transactional core modules.
    */
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

   /**
    * Executes deep data collection dumps across tables boundaries structures.
    *
    * @param array $input Request processing parameters bounds information mapping parameters.
    * @return array General dictionary mapping database entities.
    *
    * @todo Restrict massive data dumps; use scoped limits to avoid service memory consumption crashes.
    */
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

   /**
    * Builds summaries describing transactions histories and items lists constraints.
    *
    * @param array $input Offsets configuration structure dictionary.
    * @return array Merged outcome components configuration maps.
    *
    * @todo Eliminate dynamic calculations based on size parameters; substitute explicit structures logic.
    */
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

   /**
    * Queries specific custom configurations assigned corresponding to base order operations.
    *
    * @param array $input Boundaries tracking structures payload mapping references.
    * @return array Outcome components configuration arrays.
    *
    * @todo Establish continuous data validation parameters matching precise domain specifications.
    */
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

   /**
    * Evaluates orders information maps filtered by specific underlying status structures.
    *
    * @param array $input Property constraints grouping configurations variables.
    * @return array Output datasets mappings maps.
    *
    * @todo Refactor conditional variables sorting checking bounds logic into typed model behaviors.
    */
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

   /**
    * Adjusts core client identities specifications inside persisting datasets models.
    *
    * @param array $input Dictionary carrying the target metrics modifications arrays.
    * @return array Transaction validation processing logs metrics.
    *
    * @todo Migrate custom data mapping entities to native object structures matching standard DTO definitions.
    */
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


   /**
    * Executes primary tracking keys updates workflows aligning client values sequences variables.
    *
    * @param array $input Identity structures matching configurations parameters.
    * @return array Output operational metrics summaries mappings maps.
    *
    * @todo Deprecate dynamic internal component execution calls matching `${method_name}` to enforce code security.
    */
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


   /**
    * Executes target mapping reference sequencing updates matching Client User constraints.
    *
    * @param array $input Identity boundaries criteria mapping profiles.
    * @return array Resolution array mapping outcome codes.
    *
    * @todo Refactor string manipulation mechanisms like `explode` on description strings into separate attributes properties.
    */
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

   /**
    * Records newly declared client models into backing systems storages.
    *
    * @param array $input Payload information arrays describing client properties.
    * @return array Standard process layout dictionary mapping outcomes.
    *
    * @todo Introduce proper validation chains before handling core persistence methods.
    */
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

   /**
    * Destroys target profiles completely eliminating related references databases links.
    *
    * @param array $input Identifiers maps outlining client models targeting erasure.
    * @return array Result summary tracking transaction properties.
    *
    * @todo Enforce high protection constraints and structural foreign identity evaluations before purge execution.
    */
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


   /**
    * Sets or adjusts operational customer dynamic properties metadata assignments.
    *
    * @param array $input Content parameters dictionary mapping metadata details.
    * @return array Transaction execution summaries variables.
    *
    * @todo Refactor procedural loops by moving metadata modifications workflows into separate domain services.
    */
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

   /**
    * Appends or maps properties specifications matching internal users contexts models.
    *
    * @param array $input Parameter data payload structures dictionary.
    * @return array Final calculation collection summary map.
    *
    * @todo Replace iterative array sizing evaluations with specific validation helper classes.
    */
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


   /**
    * Inserts or recalculates account data identities allocated for single user definitions.
    *
    * @param array $input Core specifications dictionary values variables.
    * @return array Operation response parameters values arrays.
    *
    * @todo Standardize the overlapping user save routines (`doClientUserAddOrUpdate` vs `doClientUserAdd`).
    */
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


   /**
    * Executes nested child entity cleanups before committing comprehensive structural record synchronization updates.
    *
    * @param array $input Identity schemas dictionary context references.
    * @return array Output operational metrics summaries mappings maps.
    *
    * @todo Refactor variable method invocation syntax strings (`$this->${method_name}`) with specific polymorphic actions classes.
    */
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


   /**
    * Allocates new systemic context users profiles within databases systems.
    *
    * @param array $input Payload metadata details matching customer contexts.
    * @return array Normalized dictionary mapping execution outputs.
    *
    * @todo Refactor identical duplicate handling routes to achieve better compliance with DRY design standards.
    */
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

   /**
    * Mutates properties elements assigned under specified client user targets.
    *
    * @param array $input Modification criteria maps tracking records adjustments.
    * @return array Compiled summary log maps matching transactions.
    *
    * @todo Replace arbitrary type changes flag properties (`input_data_type`) with target context mappings.
    */
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

   /**
    * Marks or filters designated customer interface profiles as deactivated/removed.
    *
    * @param array $input Identity tracking parameters dictionary.
    * @return array Structured operation validation mapping metrics.
    *
    * @todo Enforce clean logging mechanisms using formal domain structures instead of raw printing tracking.
    */
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

   /**
    * Enforces new security keys credentials definitions over targeted customer user entries.
    *
    * @param array $input Configuration variables details tracking cryptographic passwords changes.
    * @return array Metrics confirmation dictionary schema tracking context variables.
    *
    * @todo Enforce strict cryptographically secure hashing validations prior to running database overrides.
    */
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

   /**
    * Appends or aligns secondary location shipping/billing variables associated with system users.
    *
    * @param array $input Parameter data fields structures collection dictionary.
    * @return array Output operational metrics summaries mappings maps.
    *
    * @todo Create concrete object mappings to represent structural addresses objects.
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

   /**
    * Erases a targeted client user location identity data matching precise constraints specifications.
    *
    * @param array $input Identity metrics records definitions tracking erasure coordinates.
    * @return array Final action summary properties parameters tracking context mappings.
    *
    * @todo Leverage standardized logical state soft deletions tags instead of destroying dataset elements directly.
    */
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

   /**
    * Lists global address coordinates assigned relative to system user profiles.
    *
    * @param array $input Request query boundary options fields properties dictionary.
    * @return array Normalized response arrays tracking calculated values.
    *
    * @todo Fix structural mismatch names in tracking error feedback configurations mapping variables.
    */
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

   /**
    * Resolves comprehensive base billing location indexes for customer business entries.
    *
    * @param array $input Context metrics values parameters dictionary tracking constraints options.
    * @return array Standard structured result dictionaries.
    *
    * @todo Refactor raw variable index conversions matching generic integer declarations `(int)`.
    */
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

   /**
    * Retreives raw master addresses segments parameters profiles.
    *
    * @param array $input Operational tracking parameters limits metadata details.
    * @return array Normalized response metrics properties tracking parameters context mapping variables.
    *
    * @todo Correct code tracking runtime exceptions references pointing onto undefined elements `$ParamDoubleStartLength`.
    */
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

   /**
    * Registers standard stock product models properties elements inside metadata storage systems.
    *
    * @param array $input Parameters array details matching newly tracked inventory items specifications.
    * @return array Operation process metrics results mapping parameters configurations.
    *
    * @todo Decouple functional properties verification processes outside iterative loops blocks.
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

   /**
    * Mutates specific catalog product data fields or specifications maps elements.
    *
    * @param array $input Modification dictionary holding adjustments inputs profiles.
    * @return array Output operational metrics summaries mappings maps.
    *
    * @todo Standardize product data access components across uniform domain models templates.
    */
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

   /**
    * Processes structural upsert transactions tracking catalog product definitions.
    *
    * @param array $input Processing information mappings parameters.
    * @return array Output process metrics tracking statuses variables.
    *
    * @todo Refactor native upsert execution protocols into core transactional services operations.
    */
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

   /**
    * Flushes related nested metadata records before writing primary database records values updates matching single items.
    *
    * @param array $input Identity tracking parameters dictionary.
    * @return array Outcome verification parameters collection logs data metrics.
    *
    * @todo Wrap relational multi-query logic execution in strict ACID SQL transactional contexts.
    */
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

   /**
    * Executes transactional cascade cleanups tracking base client profile updates.
    *
    * @param array $input Customer fields parameters dictionary.
    * @return array Output process metrics tracking statuses variables.
    *
    * @todo Normalize log messaging statements formatting strings details across distinct routines.
    */
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

   /**
    * Assigns specific override prices tracking singular product objects for customer configurations.
    *
    * @param array $input Identity and monetary details variables parameters dictionary payload.
    * @return array Standard response shape definition dictionaries.
    *
    * @todo Refactor input array data mapping values into an independent business rules validator wrapper.
    */
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

   /**
    * Routes direct price sheet changes mapping specific clients updates indicators flags.
    *
    * @param array $input Inventory items and prices configurations.
    * @return array Transaction verification data results maps parameters.
    *
    * @todo Separate proxy structural shortcuts methods out from implementation class frameworks.
    */
   function setClientProductPriceListUpdate( $input ) {
      return $this->doClientProductPriceListAddOrUpdate( $input, true);
   }

   /**
    * Establishes initial customer targeted catalog prices matching multi-item records mappings.
    *
    * @param array $input Collection records maps specifying target customer profiles details.
    * @return array Standard structured result dictionaries.
    *
    * @todo Standardize boolean routing parameter signatures with explicit distinct execution scopes.
    */
   function setClientProductPriceList( $input ) {
      return $this->doClientProductPriceListAddOrUpdate( $input, false);
   }

   /**
    * Manages structural price adjustments matrices assigned corresponding to client profiles.
    *
    * @param array $input Parameter dictionary data array holding price metadata elements.
    * @param bool  $update Flags whether execution context processes incremental sync changes or full sheet overrides.
    * @return array Metrics summary confirmation variables dictionary.
    *
    * @todo Eliminate implicit accumulation runtime pointers tracking `$del_count` and `$ins_count` values.
    */
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

   /**
    * Appends clean structural categorization components models inside persistence.
    *
    * @param array $input Payload information describing category attributes.
    * @return array Standard structured result dictionaries.
    *
    * @todo Refactor type indicators variables structures handling validation conditions layers.
    */
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

   /**
    * Executes transactional store category structural updates or additions mappings configurations.
    *
    * @param array $input Properties metrics criteria parameters collection mappings fields variables.
    * @return array Standard process layout dictionary mapping outcomes.
    *
    * @todo Unify validation error responses formatting behaviors logic patterns.
    */
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

   /**
    * Adjusts text definitions or parameters identifiers describing store operational categories.
    *
    * @param array $input Input dataset parameters dictionary mapping targets specifications.
    * @return array Result summaries payload configurations mappings maps.
    *
    * @todo Shift hardcoded strings checks into constant configuration dictionaries tags properties.
    */
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


   /**
    * Completely removes structural target organizational categories.
    *
    * @param array $input Target identity fields parameters dictionary context mappings.
    * @return array Standard structured result dictionaries.
    *
    * @todo Define strict node validations checking tree dependency relationships to protect integrity.
    */
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


   /**
    * Maps product references into specific inventory organization categories structures.
    *
    * @param array $input Relational coordinates parameters collection mappings fields variables.
    * @return array Result configuration maps checking system indicators properties.
    *
    * @todo Fix logical typo bugs tracking undeclared context variables reference inputs `$param_array`.
    */
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

   /**
    * Redirects process data updates to the binary media storage handling systems.
    *
    * @param array $input Content parameters tracking image data objects payload configurations.
    * @return array Matrix result details mappings maps.
    *
    * @todo Substitute static class method shortcuts invocations `self::` with descriptive runtime interfaces.
    */
   function doPictureAddOrUpdate( $input ) {
       return self::setPicture( $input );
   }

   /**
    * Binds incoming binary media information records onto systemic visual components profiles.
    *
    * @param array $input Core data buffer tracking visual media properties parameters.
    * @return array Data tracking outputs map tracking execution results.
    *
    * @todo Implement binary stream management interfaces to decouple high-overhead media fields from standard arrays processing.
    */
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

   /**
    * Sets or handles structural customer order properties updates.
    *
    * @param array $input Configuration metadata data dictionaries tracking order properties variables.
    * @return array Process logs dictionary maps matching transactions metadata.
    *
    * @todo Refactor arbitrary iterative structures parsing arrays configurations out of workflow controllers.
    */
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

   /**
    * Modifies visibility status fields maps metrics on single transaction orders records entries.
    *
    * @param array $input Transaction properties information structures collection dictionary details.
    * @return array Standard structured result dictionaries.
    *
    * @todo Standardize properties identities naming keys formats across distinct database tables records formats.
    */
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

   /**
    * Adjusts hidden operational states assigned under specific active client transactions records models.
    *
    * @param array $input Parameters array metrics describing target statuses parameters.
    * @return array Operational validation logs data collection.
    *
    * @todo Replace generic positional array values extractions loops with clean model schema structures.
    */
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

   /**
    * Lists generic financial ledger invoices elements data maps records models.
    *
    * @param array $input Input bounds parameters pagination variables properties tracking offsets.
    * @return array Output structured parameters maps properties.
    *
    * @todo Fix string logging concatenation logic metrics (`$param_array`) which risks generating execution errors under array metrics contexts.
    */
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

   /**
    * Returns ledger financial lists data details assigned mapping targeted system customers contexts.
    *
    * @param array $input Configuration options structure criteria mapping offsets variables tracking limits.
    * @return array Formatted result metrics models mapping properties.
    *
    * @todo Align names configurations errors tags descriptive keys across similar invoice lookup scopes.
    */
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


   /**
    * Lists financial invoice profiles mapped into targeted store order profiles.
    *
    * @param array $input Parameter bounds configuration dictionary criteria mapping variables.
    * @return array Data collection response structure.
    *
    * @todo Decouple functional boundaries checks workflows outside core service interface class blocks.
    */
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


   /**
    * Processes standard insertions or modifications mapping customer transaction order invoices metrics models.
    *
    * @param array $input Content parameters records defining invoice tracking variables payload configurations.
    * @return array Response schema layout tracking status confirmation dictionary.
    *
    * @todo Establish continuous data validation parameters matching precise accounting domains specifications.
    */
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


   /**
    * Overrides processing tracking codes states describing ledger financial invoices documents.
    *
    * @param array $input Data modifications dictionary tracking validation changes metadata details.
    * @return array Standard structured result dictionaries.
    *
    * @todo Swap status dynamic configurations string evaluations tracking properties arrays with typed Enum schemas.
    */
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

   /**
    * Assigns strategic administrative account managers mappings relations onto targeted clients setups.
    *
    * @param array $input Content data fields structures properties dictionary payload tracking entities linkages.
    * @return array Matrix result details mappings maps.
    *
    * @todo Refactor raw print outputs tracking metrics components logs data variables inside production environments.
    */
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

   /**
    * Pulls administrative associations mapping strategic account managers across global customer definitions.
    *
    * @param array $input Query parameters metadata bounds specifications metrics arrays profiles.
    * @return array Data collection response structure.
    *
    * @todo Align named class parameters matching context variations (e.g. resolve mismatch definitions usage `ParamDoubleStartLength` vs `ParamStartLength`).
    */
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

   /**
    * Returns list collections linking product attributes grouped structural parameters maps configurations.
    *
    * @param array $input Offsets configuration structure dictionary criteria.
    * @return array Final calculation collection summary map.
    *
    * @todo Establish proper pagination interfaces passing custom criteria criteria parameters safely.
    */
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

   /**
    * Adds or updates features configurations grouping elements related onto item definitions catalog setups.
    *
    * @param array $input Core parameters details variables parameters dictionary mapping metadata metrics fields.
    * @return array Standard structured result dictionaries.
    *
    * @todo Refactor loops logic mappings into modular repository transactional controllers operations.
    */
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


   /**
    * Fetches explicit characteristics identifiers records maps belonging onto catalog items listings templates variables.
    *
    * @param array $input Limits settings parameter variables configurations details mapping metrics options.
    * @return array Output process metrics tracking statuses variables.
    *
    * @todo Replace overlapping named target references loops methods mappings (`getShopProductAttributeList` names definitions).
    */
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

   /**
    * Sets configurations attributes definitions mapped down onto precise e-commerce store items models.
    *
    * @param array $input Value descriptors array mapping inputs fields details.
    * @return array Metrics summary confirmation variables dictionary.
    *
    * @todo Transition custom parameter mappings logic workflows over onto typed collection models schemas.
    */
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


   /**
    * Returns paginated system attribute traits schemas applied across core digital storefront systems platforms configurations.
    *
    * @param array $input Offsets metadata criteria definitions mapping structures targets variables.
    * @return array Standard structured result dictionaries.
    *
    * @todo Leverage robust object hydration protocols to enforce secure structures validation checking loops.
    */
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

   /**
    * Modifies global platform customization properties elements inside storefront setup settings.
    *
    * @param array $input Payload information arrays describing attribute elements properties.
    * @return array Operational execution logs verification data arrays profiles.
    *
    * @todo Isolate dynamic system variable tracking fields configurations into structured schema layers modules.
    */
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

   /**
    * Triggers external logistics fulfillment metric audits or shipping calculation actions mapping UPS identifiers details variables.
    *
    * @param array $input Identity structures matching configurations parameters dictionary maps.
    * @return array Normalized response arrays tracking calculated values.
    *
    * @todo Decouple raw string logging variables usage maps matching context arrays from interface handlers blocks.
    */
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

   /**
    * Returns static parameters mocks outlining mock pagination boundary configurations.
    *
    * @param array $input Processing information metrics configuration parameters options metadata details fields context variables.
    * @return array Mock configuration keys mapping sample limits parameters values details.
    *
    * @todo Safely deprecate this fallback dummy helper or substitute it with concrete test assertions setups.
    */
   function getParamStartLength( $input ) {
      $in_o = array(
            'start' => 1,
            'length' => 2,
            'options' => 'asasa'
      );
      return $in_o;
   }

   /**
    * Unused skeleton controller pipeline structure shortcut pointer mapping targets parameters.
    *
    * @param mixed  $input      Data details parameters elements tracking profiles metrics models.
    * @param string $methodname Method structural tracking descriptor context key value.
    * @return void
    *
    * @todo Complete implementation matching clear operational specifications, or remove if obsolete.
    */
   function _response( $input, $methodname) {


   }

   /**
    * Transforms deep multi-dimensional array sets structures into explicit XML strings properties buffers data structures.
    *
    * @param array  $input      Content metrics details tracking conversion values parameters contexts mappings options arrays.
    * @param string $classname  Dynamic system class string context reference tag variable used for model instantiations properties.
    * @param string $string_add Context modifiers criteria properties append flags variables metadata.
    * @return string Serialized raw XML string buffer content layout mapping target models configurations data outputs.
    *
    * @todo Refactor global state references variables usage templates (`global $fp_xml`) to enforce standard dependency injection.
    */
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
