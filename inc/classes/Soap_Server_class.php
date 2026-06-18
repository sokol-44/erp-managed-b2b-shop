<?php
/**
 * Soap_Server_class.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if (!defined('_I_INIT')) die();

/**
 * Class BasicSOAPDataMethods
 *
 * Base validation and data mapping class for handling structured data layouts
 * traversing a custom SOAP-XML engine context.
 *
 * @todo Add strict type declarations (declare(strict_types=1)).
 * @todo Map typed properties explicitly to replace generic array declarations (PHP 7.4+ / 8.0+).
 * @todo Modernize all generic lists to bracket array syntax short form `[]`.
 * @todo Refactor schema declarations by relocating them to separate files.
 */
class BasicSOAPDataMethods { /* implements ArrayAccess */
   /**
    * @var array Sanitized internal value dictionary mapping.
    */
   private $values = array();

   /**
    * @var string Execution configuration operational layout schema modifier (e.g., 'NEW', 'FULL').
    */
   private $type = 'NEW';

   /**
    * @var bool Tracking status specifying if a new unique entry record lifecycle is being addressed.
    */
   private $new = false;

   /**
    * @var bool Flags if validation structures must target the full complete attribute lists.
    */
   private $full = false;

   /**
    * @var array Transaction outcome status dictionary context.
    */
   private $status = array('NR' => -1, 'TXT' => '');

   /**
    * @var array Collection structural logs indicating parsing execution validation failures.
    */
   private $error = array();

   /**
    * @var array System logs outlining partial warnings found inside structural checks.
    */
   private $warning = array();

   /**
    * @var array Permissible elements permitted for the current entity context execution.
    */
   public $list = array();

   /**
    * @var array Mandated attributes checked when a record action handles item creations.
    */
   public $list_new = array();

   /**
    * @var array Mandated attributes checked when a record action updates existing items.
    */
   public $list_update = array();

   /**
    * @var array Attribute typing rules map (e.g., INT, INT+, FLOAT, TXT, ARRAYOBJ, OBJ).
    */
   public $list_type = array();

   /**
    * @var array Outbound procedural routine execution identifiers mapped against properties.
    */
   public $list_method = array();

   /**
    * @var array List of properties defined as core unique keys for the data structural block.
    */
   public $list_primary_key = array();
   /*
    * Classes:
    * - Checks for existence, including whether it must exist
    * list, list_new, list_update
    * - Checks for predefined types (int, email, ito)
    * - returning attributes as an array (for XML)
    * - reading attributes from an array
    * - returning attributes to SQL (with escaping) and accounting for insert(new)/update
    * - general error handling (invalid elements
    */

   /**
    * BasicSOAPDataMethods constructor.
    *
    * Evaluates initial inputs and safely delegates raw dictionaries to internal parsers.
    *
    * @param mixed $input Raw input payload structure (string, array, or similar object context). Defaults to false.
    * @param mixed $type Structural state configuration mode context string or boolean. Defaults to true.
    * @return void
    *
    * @todo Fix variable scope references in variable-variable instantiation calls (`${key}`).
    */
   public function __construct( $input = false, $type = true ) {
      $this->type = $type;
      $this->init();
      //var_dump(array($this->new, $this->full));
      if( $input ) {
         if( is_string($input) ) {
            add_to_fp("input_string\n".print_r($input, true));
            $input = $this->fill_object( $input );
              $this->load_array( $input );
         } elseif (is_array($input) ) {
            add_to_fp("input_array\n".print_r($input, true));
            $this->load_array( $input );
         } elseif ( is_object($input) && get_class($input) == get_class($this) ) {
            add_to_fp("input_object\n".print_r($input, true));
            $this->load_array( $input->return_array() );
//            add_to_fp("input_object END\n".print_r($input, true));
         } else {
             add_to_fp("input_unknow\n".print_r($input, true));
            $this->load_array( array() );
         }
      } else {
         add_to_fp("input_false\n");
         $this->load_array( array() );
      }

   }

   /**
    * Configures the internal state execution profile trackers based on specified data processing types.
    *
    * @return void
    */
   public function init( ) {
      if( $this->type === TRUE || $this->type == 'NEW') {
         $this->new = true;
         $this->full = false;
      } elseif ($this->type == 'FULL') {
         $this->new = false;
         $this->full = true;
      } else {
         $this->new = false;
         $this->full = false;
      }
   }

   /**
    * Triggers full array field evaluation passes and enforces presence requirements.
    *
    * @param array $input Raw data payload parameters extracted for verification routines.
    * @return bool True if operations passed structural requirements without errors, false otherwise.
    */
   public function load_array( array $input ) {
      $this->check_list($input);
      $this->check_list_need();

      //TODO somethinig with error
      if( $this->is_error() ) {
         return false;
      }
      return true;
   }

   /**
    * Flattens internal multi-dimensional structures down into safe serialized array maps.
    * Intercepts child collection contexts to extract arrays from dynamic custom nested objects.
    *
    * @return array Multi-dimensional array tracking validated information models.
    *
    * @todo Resolve nested indexing defects and review potential infinity tracking patterns during recursion steps.
    */
   public function return_array() {
      add_to_fp('return_array()');
      if( $this->is_error() ) {
         return array();
      } else {

          $res = $this->values;

          $res_obj = array_keys($this->list_type, 'OBJ');
          foreach($res_obj as $key => $val) {
              if( isset($this->values[$key]) && is_object($this->values[$key]) )
                  $res[$key]->return_array();
          }

          $res_arrobj = array_keys($this->list_type, 'ARRAYOBJ');
          foreach($res_arrobj as $key => $val) {
              if( isset($this->values[$val]) && is_array($this->values[$val]) ) {
                    foreach($this->values[$val] as $key2 => $val2 ) {
                        if( is_object($val2) ) {
                            unset($res[$val][$key2]);
                            if( is_numeric($key2) )
                                $res[$val]['value_'.$key2] = $this->values[$val][$key2]->return_array();
                            else
                                $res[$val][$key2] = $this->values[$val][$key2]->return_array();
                        }
                    }
              }
          }

          add_to_fp('return_array:"'.print_r($res,true).'"');
         return $res;
      }
   }

   /**
    * Isolated structural subroutine assisting recursive array extraction workflows.
    *
    * @param mixed $in Expected targeted sub-class data object tracking methods.
    * @return array Extracted property mapping information parameters dictionary.
    */
   private function _return_array($in) {
      add_to_fp('_return_array()');
      if( !is_object($in) ) {
         return array();
      } else {
         return $in->return_array();
      }
   }

   /**
    * Packages object properties matching the main class schema definition framework.
    *
    * @return array Extracted parameters structure.
    * @todo Refactor usage of legacy `compact()` routines to eliminate unintended dynamic scope visibility bugs.
    */
   public function return_list() {
      return compact( $this->list );
   }

   /**
    * Provides captured parsing validation data failure details.
    *
    * @return array Structural log trace mapping information errors.
    */
   public function return_error() {
      return $this->error;
   }

   /**
    * Provides execution trace metric warning updates.
    *
    * @return array Log trace mapping updates.
    */
   public function return_warning() {
      return $this->warning;
   }

   /**
    * Validates whether tracking mechanisms contain active processing exception constraints.
    *
    * @return bool True if parsing problems exist, false otherwise.
    */
   public function is_error() {
      if( sizeof( $this->error ) > 0 ) {
         return true;
      }
      return false;
   }

   /**
    * Selects the correct validation list criteria tracking framework based on structural run types.
    *
    * @return array Validation key list context.
    */
   private function get_local_list() {
      if( $this->full ) {
         return $this->list;
      } elseif( $this->new ) {
         return $this->list_new;
      } else {
         return $this->list_update;
      }

   }

   /**
    * Scans processed properties to ensure all mandatory keys are populated.
    * Appends structural logs inside internal collections when validations fail.
    *
    * @return void
    *
    * @todo Resolve notice generation conditions where unset keys invoke array offset lookup errors.
    */
   private function check_list_need(  ) {

      $local_list = $this->get_local_list();
      //add_to_fp( print_r() )
      foreach( $local_list as $new_key ) {
         if( isset($this->values[$new_key]) && (string)$this->values[$new_key]!='' ) {
            //
         } else {
            $this->error[] = array(0 => 'check_list_'.(($this->new)?'new':'update'), 1 => $new_key, 2=>$this->values[$new_key]);
         }
      }
   }

   /**
    * Cycles across a raw tracking array map, parsing items found inside class parameter configurations.
    *
    * @param array $input Raw transaction input parameters array stack.
    * @return void
    */
   private function check_list( $input ) {
      foreach ($input as $key => $val ) {
        if( in_array($key, $this->list) ) {
          list($val_check, $status) = $this->check_val($key, $val);
          if( $val_check !== FALSE ) {
             $this->values[$key] = $val_check;
          } else {
             $this->warning[] = array('check_val', $status.': '.$key.'=>'.$val);
          }
        } else {
                add_to_fp('check_list NF "'.$key.'";"'.$val.'"');
            $this->warning[] = array('check_list', $key.'=>'.$val);
        }
      }
   }

   /**
    * Sanitizes and verifies scalar data parameters according to specified data configuration templates.
    * Supports checks for explicit integers, bounds, emails, markup elements, paths, and nested classes.
    *
    * @param string $key Attribute parameter name target.
    * @param mixed $val Target string, numeric, or object property instance context.
    * @return array{0: mixed, 1: string} Tuple array tracking converted output data and error execution messages.
    *
    * @todo Fix variable-variable injection bug risks inside dynamic class checking routines (`new ${key}`).
    * @todo Resolve runtime notice exceptions where loop contexts leverage uninitialized trackers (e.g., `$element`).
    * @todo Update standard regex validation formulas to handle variable layouts safely.
    */
   private function check_val($key, $val) {
      $status = '';
      $val_out = false;
//      add_to_fp('check_val '.$key.';'.$this->list_type[$key].';'.$val);
      if( isset($this->list_type[$key]) ) {
         /* types: INT,INT+ (>zero),FLOAT,FLOAT+ (>zero),PATH,TXT,HTML,DATE,EMAIL,ARRAY,OBJ, ARRAYOBJ   */
         switch ($this->list_type[$key]) {
            case 'INT':
               if( is_numeric($val) ) $val_out = (int)$val;
               break;
            case 'INT+':
                //add_to_fp('INT+:"'.print_r(array(is_numeric($val)?'t':'n', ((int)$val>0)?'t':'n'), true).'"');
               if( is_numeric($val) && (int)$val>0 ) $val_out = (int)$val;
               break;
            case 'FLOAT':
               $val = str_replace(',', '.', $val);
               if( preg_replace('/[0-9\.]+/', '', $val) == '' ) $val_out = (float)$val;
               break;
            case 'FLOAT+':
               $val = str_replace(',', '.', $val);
               if( preg_replace('/[0-9\.]+/', '', $val) == '' &&  (float)$val>0 ) $val_out = (float)$val;
               break;
            case 'PATH':
               $path_parts = pathinfo($val);
               if( ctype_print($val) && $path_parts ) $val_out = $path_parts['dirname'] . DS . $path_parts['basename'];
               break;
            case 'UUID':
                if( preg_replace('/[a-f0-9\-]+/i', '', $val) == '' ) $val_out = (int)$val;
               break;
            case 'TEXT':
            case 'TXT':
               $val_out = strip_tags($val);
               break;
            case 'HTML':
               $val_out = trim(
                    preg_replace('/(id|class|on([a-z])*)="(.|\s)*?"/i', '',
                    strip_tags(
                    $val,
                    '<a><p><b><i><u><br><span><strike><blockquote><ol><ul><li><strong><font>')));
               break;
            case 'DATE':
               if( ctype_print($val) && preg_replace('/[0-9\.\-\ T:,_]+/', '', $val) == '' ) $val_out = $val;
               break;
            case 'EMAIL':
               if( filter_var($val,FILTER_VALIDATE_EMAIL) ) $val_out = $val;
               break;
            case 'ARRAYOBJ':
               if( is_array($val) && class_exists($key) ) {
                   foreach($val as $element)
                      $val_out[] = new ${key}($element, $this->type);
               }
               break;
            case 'OBJ':
               if( class_exists($key) ) {
                  $val_out[] = new ${key}($element, $this->type);
               }
               break;
            case 'ARRAY':
               if( is_array($val) ) {
                  $val_out = $val;
               }
               break;
         }
         if( $val_out === false ) $status = 'NOT ' . $key;
      } else {
         $val_out = $val;
      }
//      add_to_fp('ret:"'.print_r($val_out, true).'"');
      return array($val_out, $status);
   }

   /**
    * Validation route testing if dynamic properties match standard class configuration expectations.
    *
    * @param string $key Attribute property mapping identifier.
    * @param mixed $val Raw metric variable testing value target.
    * @return array{0: mixed, 1: string} Verification results tuple.
    */
   public function data_exist( $key, $val ) {
       //add_to_fp('data_exist:'.$key."\n".print_r($this->values[$key], true));
       return $this->check_val($key, $val);
   }

   /**
    * Mock placeholder utility generating dummy data parameters for interface testing profiles.
    *
    * @param mixed $add Dynamic initialization modifier string fallback parameter. Defaults to 'null'.
    * @return array Synthesized placeholder configurations array map.
    */
   public function fill_object($add = 'null') {
      if( !is_object($add) || !method_exists($add,'return_array') ) {
          $local_list = $this->get_local_list();
          $first_id_name = false;

          foreach( $local_list as $new_key ) {
             if( substr($new_key, 0, 2) == 'id' ) {
                if( !$first_id_name && $new_key != $first_id_name) {
                   $first_id_name = $new_key;

                   $values[$new_key] = strlen($add) + rand(10,100);
                } else {
                   $values[$new_key] = strlen($add) + rand(1010,1100);
                }
                continue;
             }
             $values[$new_key] = $add . ' ' . time() . ' ' . $new_key;
          }
      } else {
         $values = $add->return_array();
      }
      return $values;
   }

   /**
    * Extracts key parameter mapping values designated as structural primary elements.
    *
    * @return mixed Single index content, key-value dictionary set, or false if undefined.
    */
   public function get_primary_key() {
       if( sizeof($this->list_primary_key) == 1 )
            return $this->values[ reset($this->list_primary_key) ];
       elseif( sizeof($this->list_primary_key) == 0 )
           return false;
       else {
           $ret = array();
           foreach($this->list_primary_key as $pkey) {
               $ret[$pkey] = $this->values[$pkey];
           }
           return $ret;
       }
   }

   /**
    * Generates unescaped SQL statement assignment clause arrays for inserts or updates.
    *
    * Filters non-scalar fields like arrays or nested objects from the returned collections.
    *
    * @return array{insert_array: array, update_array: array} Prepared configuration SQL segments.
    *
    * @todo Replace bad parameter references in logger steps (`this` used instead of tracking variable `$this`).
    * @todo Migrate dynamic manual assignment serialization patterns towards real parameterized SQL Prepared Statements.
    */
   public function get_inst_upd_arr() {

       $insert_array = array();
       $update_array = array();

       foreach( $this->values as $key => $val ) {
           if( $this->list_type[$key] != 'ARRAY' &&
                   $this->list_type[$key] != 'OBJ' &&
                   $this->list_type[$key] != 'ARRAYOBJ' ) {
                list($key_db, $val_db) = $this->__db_escape_w_type($key, $val);
               if( !in_array($key, $this->list_primary_key ) ) {
                   $update_array[] = '`' . $key_db . '`="' . $val_db . '"';
               }
               $insert_array[] = '`' . $key_db . '`="' . $val_db . '"';
           }
       }

       add_to_fp('$values: '. print_r($this->values, this));
       add_to_fp('$update_array: '. print_r($update_array, this));
       add_to_fp('$insert_array: '. print_r($insert_array, this));
       $res = compact('insert_array', 'update_array');
       add_to_fp('get_inst_upd_arr: '.print_r($res, this));
       return $res;
   }

   /**
    * Dispatches database escape routines depending on structural formatting properties.
    *
    * @param string $key Property target column identification key name.
    * @param mixed $val Property variable targeting clean filtration adjustments.
    * @return array{0: string, 1: mixed} Safe database column name and formatted data item.
    */
   private function __db_escape_w_type($key, $val) {

       $val_out = '';
       if( isset($this->list_type[$key]) ) {
         /* types: INT,INT+ (>zero),FLOAT,FLOAT+ (>zero),PATH,TXT,HTML,DATE,EMAIL,ARRAY,OBJ, ARRAYOBJ   */
           switch ($this->list_type[$key]) {
               case 'INT':
               case 'INT+':
                   $val_out = db_int($val);
                   break;
               case 'FLOAT':
               case 'FLOAT+':
                   $val_out = db_float($val);
                   break;
               case 'PATH':
               case 'UUID':
               case 'TEXT':
               case 'TXT':
               case 'HTML':
               case 'DATE':
               case 'EMAIL':
                   $val_out = db_escape($val);
                   break;
               case 'ARRAYOBJ':
               case 'OBJ':
               case 'ARRAY':
               default:
                   break;
           }
       } else {
           $val_out = db_escape($val);
       }
       return ( array(db_escape($key), $val_out) ) ;
   }

}

/**
 * Class EmailData
 *
 * Outlines properties and verification logic boundaries for electronic mail delivery blocks.
 */
class EmailData extends BasicSOAPDataMethods {
    public $list = array('mode', 'method', 'data');
    public $list_type = array('mode' => 'TEXT', 'method' => 'TEXT', 'data' => 'ARRAY');
    public $list_new = array('mode', 'method', 'data');
    public $list_update = array('mode', 'method', 'data');
}

/**
 * Class ClientData
 *
 * Formulates schema layouts defining complete profile structures tracking consumer client accounts.
 *
 * @package Soap
 * @subpackage Data
 */
class ClientData extends BasicSOAPDataMethods {
    public $list = array('id_client', 'name', 'description', 'email', 'phone', 'state', 'guid',
             'ClientPriceListData', 'ProductClientPriceData',
             'ClientAttributeData', 'ClientUserData',);
    public $list_type = array('id_client' => 'INT+', 'name' => 'TEXT', 'description' => 'TEXT',
             'email' => 'EMAIL', 'phone' => 'TEXT', 'state' => 'TEXT', 'guid' => 'UUID',
            'ClientPriceListData' => 'ARRAYOBJ', 'ProductClientPriceData' => 'ARRAYOBJ',
             'ClientAttributeData' => 'ARRAYOBJ', 'ClientUserData' => 'ARRAYOBJ',
             'EmailData' => 'ARRAYOBJ');
    public $list_new = array('id_client', 'name');
    public $list_update = array('id_client');
    public $list_primary_key = array('id_client');
   public $list_method = array(
               'ClientProductPriceListData' => 'setClientProductPriceList',
               'ProductClientPriceData' => 'setProductClientPrice',
              'ClientAttributeData' => 'doClientAttributeAddOrUpdate',
               'ClientUserData' => 'doClientUserCleanAddOrUpdate',
               'EmailData' => 'doEmailSend');
}

/**
 * Class ClientAttributeData
 *
 * Manages custom ancillary metadata mappings attached explicitly onto consumer accounts.
 */
class ClientAttributeData extends BasicSOAPDataMethods {
   public $list = array('id_client', 'type', 'val');
   public $list_type = array('id_client' => 'INT+', 'type' => 'TEXT', 'val' => 'TEXT');
   public $list_new = array('id_client', 'type', 'val');
   public $list_update = array('id_client', 'type', 'val');
}

/**
 * Class ClientUserData
 *
 * Maps credential details, tracking variables, and associations for sub-user profiles under clients.
 */
class ClientUserData extends BasicSOAPDataMethods {
   public $list = array('id_client_user', 'id_client', 'login', 'password', 'password_salt', 'guid',
         'description', 'name', 'email', 'phone', 'phone_cell', 'created', 'last_login', 'state',
           'ClientUserAddressData', 'ClientUserAttributeData', 'AccountManagerData',
           'ClientUserPasswordData');
   public $list_type = array('id_client_user' => 'INT+', 'id_client' => 'INT+', 'login' => 'TEXT',
            'password' => 'TEXT', 'password_salt' => 'TEXT', 'description' => 'TEXT', 'name' => 'TEXT',
         'email' => 'EMAIL', 'phone' => 'TEXT', 'phone_cell' => 'TEXT',
         'created' => 'DATE', 'last_login' => 'DATE', 'state' => 'TEXT', 'guid' => 'UUID',
           'ClientUserAddressData' => 'ARRAYOBJ', 'ClientUserAttributeData' => 'ARRAYOBJ',
           'AccountManagerData' => 'ARRAYOBJ',
           'ClientUserPasswordData' => 'OBJ', 'EmailData' => 'OBJ');
   public $list_new = array('id_client_user', 'id_client', 'login', 'password', 'name');
   public $list_update = array('id_client_user', 'id_client');
    public $list_primary_key = array('id_client_user');
   public $list_method = array(
               'ClientUserAddressData' => 'doClientUserAddressAddOrUpdate',
               'ClientUserAttributeData' => 'doClientUserAttributeAddOrUpdate',
               'AccountManagerData' => 'doClientAccountManagerAddOrUpdate',
              'ClientUserPasswordData' => 'doClientUserSetPassword',
               'EmailData' => 'doEmailSend');
}

/**
 * Class ClientUserAttributeData
 *
 * Represents localized dynamic meta configurations declared for specified user accounts.
 */
class ClientUserAttributeData extends BasicSOAPDataMethods {
    public $list = array('id_client', 'id_client_user', 'type', 'val');
    public $list_type = array('id_client' => 'INT+', 'id_client_user' => 'INT+', 'type' => 'TEXT', 'val' => 'TEXT');
    public $list_new = array('id_client', 'id_client_user', 'type', 'val');
    public $list_update = array('id_client', 'id_client_user', 'type', 'val');
}

/**
 * Class ClientUserPasswordData
 *
 * Isolates cryptographic hash parameters during credential modification procedures.
 */
class ClientUserPasswordData extends BasicSOAPDataMethods {
   public $list = array('id_client_user', 'id_client', 'password', 'password_salt');
   public $list_type = array('id_client_user' => 'INT+', 'id_client' => 'INT+',
          'password' => 'TEXT', 'password_salt' => 'TEXT');
   public $list_new = array('id_client_user', 'id_client', 'password');
   public $list_update = array('id_client_user', 'id_client', 'password');
}

/**
 * Class ClientUserAddressData
 *
 * Encapsulates formal shipping, dispatching, and physical billing address profiles.
 */
class ClientUserAddressData extends BasicSOAPDataMethods {
    public $list = array('id_address', 'id_client_user', 'id_client', 'description', 'name',
            'street', 'city', 'zip_code', 'country', 'state');
    public $list_type = array('id_address' => 'INT+', 'id_client_user' => 'INT+', 'id_client' => 'INT+',
            'street' => 'TEXT', 'city' => 'TEXT', 'zip_code' => 'TEXT', 'country' => 'TEXT', 'state' => 'TEXT');
    public $list_new = array('id_address', 'id_client_user', 'id_client', 'description', 'name',
            'street', 'city', 'zip_code', 'country', 'state');
    public $list_update = array('id_address', 'id_client_user', 'id_client', 'description', 'name',
            'street', 'city', 'zip_code', 'country', 'state');
}

/**
 * Class AccountManagerData
 *
 * Coordinates specific corporate sales advisors or account managers allocated to a client.
 */
class AccountManagerData extends BasicSOAPDataMethods {
   public $list = array('id_account_manager', 'id_client_user', 'id_client', 'account_manager_name',
           'fullname', 'phone1', 'phone2', 'email', 'state');
   public $list_type = array('id_account_manager' => 'INT', 'id_client_user' => 'INT+', 'id_client' => 'INT+',
         'account_manager_name' => 'TEXT', 'fullname' => 'TEXT', 'phone1' => 'TEXT', 'phone2' => 'TEXT',
            'email' => 'TEXT', 'state' => 'TEXT');
   public $list_new = array('id_account_manager', 'id_client_user', 'id_client', 'account_manager_name',
           'fullname', 'phone1', 'phone2', 'email', 'state');
   public $list_update = array('id_account_manager', 'id_client_user', 'id_client', 'account_manager_name', 'state');
}

/**
 * Class CategoryData
 *
 * Structures store categorization hierarchies, root index trees, and descriptive headings.
 */
class CategoryData extends BasicSOAPDataMethods {
   public $list = array('id_category', 'id_category_parent', 'sort_order', 'root_number', 'name', 'description',
         'date_added', 'date_modified');
   public $list_type = array('id_category' => 'INT+', 'id_category_parent' => 'INT', 'sort_order' => 'INT',
         'root_number' => 'INT', 'name' => 'TEXT', 'description' => 'TEXT', 'date_added' => 'DATE', 'date_modified' => 'DATE');
   public $list_new = array('id_category', 'id_category_parent', 'name');
   public $list_update = array('id_category');
}

/**
 * Class OrderData
 *
 * Identifies primary transactional parameters defining shopping purchases made inside systems.
 */
class OrderData extends BasicSOAPDataMethods {
   public $list = array('id_order', 'id_client', 'date_create', 'date_modified', 'id_order_status', 'guid',
          'hidden_status', 'description', 'description_basket', 'id_shopping_basket', 'id_address');
   public $list_type = array('id_order' => 'INT+', 'id_client' => 'INT+', 'date_create' => 'DATE',
          'date_modified' => 'DATE', 'id_order_status' => 'INT', 'hidden_status' => 'TEXT', 'guid' => 'UUID',
          'description' => 'TEXT', 'description_basket' => 'TEXT', 'id_shopping_basket' => 'INT+',
            'id_address' => 'INT', 'id_account_manager' => 'INT', 'OrderAttributeData' => 'OBJ');
   public $list_new = array('id_order', 'id_client');
   public $list_update = array('id_order', 'id_client');
}

/**
 * Class OrderAttributeData
 *
 * Maps flexible contextual meta elements onto processed transactions.
 */
class OrderAttributeData extends BasicSOAPDataMethods {
    public $list = array('id_order', 'type', 'val');
    public $list_type = array('id_order' => 'INT+', 'type' => 'TEXT', 'val' => 'TEXT');
    public $list_new = array('id_order', 'type', 'val');
    public $list_update = array('id_order', 'type', 'val');
}

/**
 * Class OrderStatusData
 *
 * Logs chronological workflow adjustments tracking order execution states over time.
 */
class OrderStatusData extends BasicSOAPDataMethods {
   public $list = array('id_order_status', 'id_order', 'timestamp', 'description');
   public $list_type = array('id_order_status' => 'INT+', 'id_order' => 'INT+',
          'timestamp' => 'DATE', 'description' => 'TEXT');
   public $list_new = array('id_order_status', 'id_order');
   public $list_update = array('id_order_status', 'id_order', 'description');
}

/**
 * Class Product2CategoryData
 *
 * Formulates relational links intersecting inventory items into category layout positions.
 */
class Product2CategoryData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'id_category');
   public $list_type = array('id_product' => 'INT+', 'id_category' => 'INT+');
   public $list_new = array('id_product', 'id_category');
   public $list_update = array('id_product', 'id_category');
}

/**
 * Class ProductSubtypeData
 *
 * Governs variance options, catalog identifiers, and custom imagery links mapped under a parent product.
 */
class ProductSubtypeData extends BasicSOAPDataMethods {
   public $list = array('id_product_subtype', 'id_product', 'description', 'catalog_index',
          'picture_small_url', 'picture_big_url', 'picture_id', 'price_diff', 'status');
   public $list_type = array('id_product_subtype' => 'INT+', 'id_product' => 'INT+',
            'description' => 'TEXT', 'catalog_index' => 'TEXT',
          'picture_small_url' => 'PATH', 'picture_big_url' => 'PATH', 'picture_id' => 'INT+',
          'price_diff' => 'FLOAT+', 'status' => 'TEXT'
           );
    public $list_new = array('id_product_subtype', 'id_product', 'description', 'catalog_index');
    public $list_update = array('id_product_subtype', 'id_product');

}

/**
 * Class ProductData
 *
 * Core structural model encapsulating standard retail fields, pricing metrics, VAT configurations, and stock states.
 */
class ProductData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'name', 'description', 'producer', 'catalog_index',
          'picture_small_url', 'picture_big_url', 'picture_id', 'price', 'vat',
            'promotion_price', 'promotion_date_start', 'promotion_date_end',
            'quantity', 'status', 'guid',
            'Product2CategoryData', 'ProductClientPriceData', 'ProductAttributeData',
            'ProductAttributeWGroupData');
   public $list_primary_key = array('id_product');
   public $list_type = array('id_product' => 'INT+', 'name' => 'TEXT', 'description' => 'TEXT',
          'producer' => 'TEXT', 'catalog_index' => 'TEXT',
          'picture_small_url' => 'PATH', 'picture_big_url' => 'PATH', 'picture_id' => 'INT+',
          'price' => 'FLOAT+', 'vat' => 'FLOAT',
            'promotion_price' => 'FLOAT', 'promotion_date_start' => 'TEXT', 'promotion_date_end' => 'TEXT',
            'quantity' => 'INT', 'status' => 'TEXT', 'guid' => 'UUID',
            'ProductSubtypeData' => 'ARRAYOBJ',
          'Product2CategoryData' => 'ARRAYOBJ', 'ProductClientPriceData' => 'ARRAYOBJ',
          'ProductAttributeData' => 'ARRAYOBJ', 'ProductAttributeWGroupData' => 'ARRAYOBJ'
           );
   public $list_new = array('id_product', 'name', 'price', 'vat', 'quantity');
   public $list_update = array('id_product');
   public $list_method = array(
               'ProductSubtypeData' => 'doProductSubtypeAddOrUpdate',
               'Product2CategoryData' => 'setProduct2Category',
               'ProductClientPriceData' => 'setProductClientPrice',
              'ProductAttributeData' => 'doProductAttributeAddOrUpdate',
               'ProductAttributeWGroupData' => 'doProductAttributeWGroupAddOrUpdate');
}

/**
 * Class ClientProductPriceListData
 *
 * Aggregates targeted unique item price collections tailored to individual buyers.
 */
class ClientProductPriceListData extends BasicSOAPDataMethods {
   public $list = array('id_client', 'ProductPriceData');
   public $list_type = array('id_client' => 'INT+', 'ProductPriceData' => 'ARRAYOBJ');
   public $list_new = array('id_client', 'ProductPriceData');
   public $list_update = array('id_client', 'ProductPriceData');
}

/**
 * Class ProductPriceData
 *
 * Defines explicit cost records bound against specific single items.
 */
class ProductPriceData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'price');
   public $list_type = array('id_product' => 'INT+', 'price' => 'FLOAT+');
   public $list_new = array('id_product', 'price');
   public $list_update = array('id_product', 'price');
}

/**
 * Class ClientPriceListData
 *
 * Maps custom corporate account price index records.
 */
class ClientPriceListData extends BasicSOAPDataMethods {
   public $list = array('id_client', 'price');
   public $list_type = array('id_client' => 'INT+', 'price' => 'FLOAT+');
   public $list_new = array('id_client', 'price');
   public $list_update = array('id_client', 'price');
}

/**
 * Class CategoryListData
 *
 * Wraps collections of unique category node reference keys.
 */
class CategoryListData extends BasicSOAPDataMethods {
   public $list = array('id_category');
   public $list_type = array('id_category' => 'INT+');
   public $list_new = array('id_category');
   public $list_update = array('id_category');
}

/**
 * Class ProductClientPriceData
 *
 * Explicit pricing cross-reference mapping records connecting products to single client IDs.
 */
class ProductClientPriceData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'id_client', 'price');
   public $list_type = array('id_product' => 'INT+', 'id_client' => 'INT+',
          'price' => 'FLOAT+');
   public $list_new = array('id_product', 'id_client', 'price');
   public $list_update = array('id_product', 'id_client', 'price');
}

/**
 * Class ParamStartLength
 *
 * Handles database pagination window queries containing offsets, counts, and modifiers.
 */
class ParamStartLength extends BasicSOAPDataMethods {
   public $list = array('id_start', 'length', 'options');
   public $list_type = array('id_start' => 'INT', 'length' => 'INT+', 'options' => 'TEXT');
   public $list_new = array('id_start', 'length');
   public $list_update = array('id_start', 'length');
}

/**
 * Class ParamStartWhereLength
 *
 * Structures complex filter constraints supporting target SQL filtering constraints alongside limits.
 */
class ParamStartWhereLength extends BasicSOAPDataMethods {
   public $list = array('id_start', 'length', 'where', 'options');
   public $list_type = array('id_start' => 'INT', 'length' => 'INT+',
          'where' => 'TEXT', 'options' => 'TEXT');
   public $list_new = array('id_start', 'length', 'where');
   public $list_update = array('id_start', 'length', 'where');
}

/**
 * Class ParamDoubleStartLength
 *
 * Combines multi-dimensional point indicators alongside pagination restrictions.
 */
class ParamDoubleStartLength extends BasicSOAPDataMethods {
   public $list = array('id_start_one', 'id_start_two', 'length', 'options');
   public $list_type = array('id_start_one' => 'INT', 'id_start_two' => 'INT',
          'length' => 'INT+', 'options' => 'TEXT');
   public $list_new = array('id_start_one', 'id_start_two', 'length');
   public $list_update = array('id_start_one', 'id_start_two', 'length');
}

/**
 * Class StatusData
 *
 * Tracks individual status messages returned along with supplementary trace information payloads.
 */
class StatusData extends BasicSOAPDataMethods {
   public $list = array('id', 'additional_data', 'status');
   public $list_type = array('id' => 'INT', 'additional_data' => 'TEXT', 'status' => 'TEXT');
   public $list_new = array('id', 'additional_data', 'status');
   public $list_update = array('id', 'additional_data', 'status');
}

/**
 * Class StatusDoubleData
 *
 * Tracks complex multi-indexed response status indicators across integration bridges.
 */
class StatusDoubleData extends BasicSOAPDataMethods {
   public $list = array('id_one', 'id_two', 'additional_data', 'status');
   public $list_type = array('id_one' => 'INT', 'id_two' => 'INT',
          'additional_data' => 'TEXT', 'status' => 'TEXT');
   public $list_new = array('id_one', 'id_two', 'additional_data', 'status');
   public $list_update = array('id_one', 'id_two', 'additional_data', 'status');
}

/**
 * Class PictureData
 *
 * Maps multimedia metadata properties along with structural asset strings or base64 data payloads.
 */
class PictureData extends BasicSOAPDataMethods {
   public $list = array('id_picture', 'name', 'description', 'data');
   public $list_type = array('id_picture' => 'INT', 'name' => 'TEXT',
          'description' => 'TEXT', 'data' => 'TEXT');
   public $list_new = array('id_picture', 'name', 'data');
   public $list_update = array('id_picture', 'data');
}

/**
 * Class OrderInvoiceData
 *
 * Manages financial billing documents, balances, dates, and related transactional images.
 *
 * @todo Fix duplicate key entries found in the internal `$list_type` structural declaration configuration map (`net_value`).
 */
class OrderInvoiceData extends BasicSOAPDataMethods {
    public $list = array('id_invoice', 'id_order', 'id_client', 'invoice_number', 'state',
            'net_value', 'gross_value', 'date_issue', 'date_pay', 'invoice_image', 'description');
    public $list_type = array('id_invoice' => 'INT+', 'id_order' => 'INT+', 'id_client' => 'INT+',
            'invoice_number' => 'TEXT', 'state' => 'TEXT', 'net_value' => 'FLOAT+', 'net_value' => 'FLOAT+',
            'date_issue' => 'DATE', 'date_pay' => 'DATE', 'invoice_image' => 'TEXT', 'description' => 'TEXT');
    public $list_new = array('id_invoice', 'id_order', 'id_client', 'invoice_number', 'state',
            'net_value', 'gross_value', 'date_issue', 'date_pay');
    public $list_update = array('id_invoice', 'id_order');
}

/**
 * Class ShopAttributeData
 *
 * Standardizes key-value attributes globally applicable across the storefront instance.
 */
class ShopAttributeData extends BasicSOAPDataMethods {
    public $list = array('type', 'val');
    public $list_type = array('type' => 'TEXT', 'val' => 'TEXT');
    public $list_new = array('type', 'val');
    public $list_update = array('type', 'val');
}

/**
 * Class ProductAttributeData
 *
 * Handles core feature flags or technical metadata variables bound directly to inventory profiles.
 */
class ProductAttributeData extends BasicSOAPDataMethods {
    public $list = array('id_product', 'type', 'val');
    public $list_type = array('id_product' => 'INT+', 'type' => 'TEXT', 'val' => 'TEXT');
    public $list_new = array('id_product', 'type', 'val');
    public $list_update = array('id_product', 'type', 'val');
}

/**
 * Class ProductAttributeWGroupData
 *
 * Structures grouped descriptive parameters matching formatted presentation blocks in client themes.
 */
class ProductAttributeWGroupData extends BasicSOAPDataMethods {
    public $list = array('id_product', 'id_attribute', 'attribute_name', 'attribute_value', 'attribute_order',
                'id_group', 'group_name', 'group_order');
    public $list_type = array('id_product' => 'INT+', 'id_group' => 'INT', 'id_attribute' => 'INT',
                'attribute_name' => 'TEXT', 'attribute_value' => 'TEXT', 'attribute_order' => 'INT',
                'group_name' => 'TEXT', 'group_order' => 'TEXT',);
    public $list_new =  array('id_product', 'id_attribute', 'attribute_name', 'attribute_value', 'attribute_order',
                'id_group', 'group_name', 'group_order');
    public $list_update =  array('id_product', 'id_attribute', 'attribute_name', 'attribute_value', 'attribute_order',
                'id_group', 'group_name', 'group_order');
}

/**
 * Class RomiUPSData
 *
 * Custom industrial schema defining physical properties of Uninterruptible Power Supply (UPS) inventory items.
 */
class RomiUPSData extends BasicSOAPDataMethods {
    public $list = array('id_product', 'maker', 'model', 'output_power', 'output_power_w', 'cabinet',
             'internal_count', 'internal_capacity', 'external_count', 'external_capacity',
             'box', 'typology', 'phase');
    public $list_type = array('id_product' =>  'INT+', 'maker' => 'TEXT', 'model' => 'TEXT',
             'output_power' => 'INT+', 'output_power_w' => 'INT', 'cabinet' => 'TEXT',
             'internal_count' => 'INT+', 'internal_capacity' => 'INT+',
             'external_count' => 'INT', 'external_capacity' => 'INT',
             'box' => 'TEXT', 'typology' => 'TEXT', 'phase' => 'TEXT');
    public $list_new = array('id_product', 'maker', 'model', 'output_power', 'output_power_w', 'cabinet',
             'internal_count', 'internal_capacity', 'external_count', 'external_capacity',
             'box', 'typology', 'phase');
    public $list_update = array('id_product', 'maker', 'model', 'output_power', 'output_power_w', 'cabinet',
             'internal_count', 'internal_capacity', 'external_count', 'external_capacity',
             'box', 'typology', 'phase');
}
