<?php
//class ClientDataArray {
//   public $values = array();
//
//   function __construct($add = 'null') {
//      $this->values = array(0 => new ClientData('auto'));
//   }
//
//}

class BasicSOAPDataMethods { /* implements ArrayAccess */
   private $values = array();
   private $new = false;
   private $status = array('NR' => -1, 'TXT' => '');
   private $error = array();
   private $warning = array();
   // placeholder for upper class
   public $list = array();
   public $list_new = array();
   public $list_update = array();
   /*
    * klasy:
    * - sprawdzajaca instnienie, wraz z tym czy musi istniec
    *    list, list_new, list_update
    * - sprawdzajaca predefiniowane typy (int, email, ito)
    * - zwracająca atrybuty jako tablica (do XML)
    * - wczytująca atrybuty z tablicy
    * - zwracajaca atrybury do sql (z escape) oraz uwzglednieniem intert(new)/update
    * - ogolna obsługa błedów (nieprawidłowych elementów
    */
   public function __construct( $input = false, $new = true ) {
      $this->new = (bool)$new;
      if( $input ) {
         if( is_string($input) ) {
            $input = $this->fill_object( $input );
         } else {
            $input = array();
         }
      } else {
         $input = array();
      }
      
      $this->load_array( $input );
      //var_dump($this);
   }
   
   public function init( $new = false ) {
      $this->new = (bool)$new;
   }
   
   public function load_array( array $input ) {
      $this->check_list($input);
      $this->check_list_need();
      
      //TODO somethinig with error
      if( $this->is_error() ) {
         return false;
      }
      return true;
   }
   
   public function return_array() {
      if( $this->is_error() ) {
         return array();
      } else {
         return $this->values;
      }
   }
   
   
   private function is_error() {
      if( sizeof( $this->error ) > 0 ) {
         return true;
      }
      return false;
   }

   private function get_local_list() {
      if( $this->new ) {
         return $this->list_new;
      } else {
         return $this->list_update;
      }
      
   }
   
   private function check_list_need(  ) {
      
      $local_list = $this->get_local_list();
      
      foreach( $local_list as $new_key ) {
         if( isset($this->values[$new_key]) && !empty($this->values[$new_key]) ) {
            //
         } else {
            $this->error[] = array('check_list_'.(($this->new)?'new':'update'), $new_key);
         }
      }
   }
   
   private function check_list( $input ) {
      foreach ($input as $key => $val ) {
       if( !empty($val) && in_array($key, $this->list) ) {
            $this->values[$key] = $val;
         } else {
            $this->warning[] = array('check_list', $key.'=>'.$val);
         }
      }
   }
   
   public function fill_object($add = 'null') {
      if( !is_object($add) || !method_exists($add,'return_array') ) {
          $local_list = $this->get_local_list();
          $first_id_name = false;
          
          foreach( $local_list as $new_key ) {
             if( substr($new_key, 0, 2) == 'id' ) {
                if( !$first_id_name && $new_key != $first_id_name) {
                   $first_id_name = $new_key;

                   echo "#$new_key#1!";
                   $values[$new_key] = strlen($add) + rand(10,100);
                } else {
                   echo "#$new_key#2!";
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

   /*
   public function offsetSet($offset, $value) {
      if (is_null($offset)) {
         $this->values[] = $value;
      } else {
         $this->values[$offset] = $value;
      }
   }
   
   public function offsetExists($offset) {
      return isset($this->container[$offset]);
   }
   
   public function offsetUnset($offset) {
      unset($this->values[$offset]);
   }
   
   public function offsetGet($offset) {
      return isset($this->values[$offset]) ? $this->values[$offset] : null;
   }
   */
}
/*
 * atrybuty w tablicach:
 * - lista elementów
 * - wymagane elementy dla nowy/update
 */

class ClientData extends BasicSOAPDataMethods {
   public $list = array('id_client', 'name', 'description', 'email', 'phone', 'state');
   public $list_new = array('id_client', 'name');
   public $list_update = array('id_client');

}


class ClientUserData extends BasicSOAPDataMethods {
   public $list = array('id_client_user', 'id_client', 'login', 'password', 'password_salt',
         'description', 'name', 'email', 'created', 'last_login', 'state');
   public $list_new = array('id_client_user', 'id_client', 'login', 'password', 'name', 'email');
   public $list_update = array('id_client_user', 'id_client');

}

class ClientUserPassword extends BasicSOAPDataMethods {
   public $list = array('id_client_user', 'id_client', 'password', 'password_salt');
   public $list_new = array('id_client_user', 'id_client', 'password');
   public $list_update = array('id_client_user', 'id_client', 'password');
}


class CategoryData extends BasicSOAPDataMethods {
   public $list = array('id_category', 'id_category_parent', 'sort_order', 'root_number', 'name', 'description',
         'date_added', 'date_modified');
   public $list_new = array('id_category', 'id_category_parent', 'name');
   public $list_update = array('id_category', 'id_category_parent');
}


class OrderData {
   public $id_order = '';
   public $id_client = '';
   public $date_create = '';
   public $date_modified = '';
   public $id_order_status = '';
   public $hidden_status = '';
   public $description = '';
   public $description_basket = '';


   function __construct($add = 'null', $string_add = '') {
      $this->id_order = strlen($add) + 10000;
      $this->id_client = strlen($add) + 20000;
      $this->date_create = data_data();
      $this->date_modified = data_data();
      $this->id_order_status = strlen($add) + 30000;
      $this->hidden_status = strlen($add)%2;
      $this->description = $add . '$description' . $string_add;
      $this->description_basket = $add . '$description_basket';
   }
}

class OrderStatus {
   public $id_order_status = '';
   public $id_order = '';
   public $timestamp = '';
   public $description = '';


   function __construct($add = 'null', $string_add = '') {
      $this->id_order_status = strlen($add) + 10000;
      $this->id_order = strlen($add) + 20000;
      $this->timestamp = data_data();
      $this->description = data_data() . $string_add;
   }
}


class Product2Category {
   public $id_product = '';
   public $id_category = '';

   function __construct($add = 'null', $string_add = '') {
      $this->id_product = strlen($add) + 10000;
      $this->id_category = strlen($add) + 20000;
   }
}


class ProductData {
   public $id_product = '';
   public $name = '';
   public $description = '';
   public $picture_small_url = '';
   public $picture_big_url = '';
   public $picture_id = '';
   public $price = '';
   public $vat = '';
   public $quantity = '';
   public $status= '';

   function __construct($add = 'null', $string_add = '') {
      $this->id_product = strlen($add) + 10000;
      $this->name = $add . '$name';
      $this->description = $add . '$description';
      $this->picture_small_url = $add . '$picture_small_url';
      $this->picture_big_url = $add . '$picture_big_url';
      $this->picture_id = 0;
      $this->price = strlen($add) + 10000;
      $this->vat = strlen($add)%23;
      $this->quantity = strlen($add)%3;
      $this->status = $add . '$picture_big_url' . $string_add;
   }
}


class ProductClientPriceData {
   public $id_product = '';
   public $id_client = '';
   public $price = '';
   public $vat = '';

   function __construct($add = 'null', $string_add = '') {
      $this->id_product = strlen($add) + 10000;
      $this->id_client = strlen($add) + 20000;
      $this->price = strlen($add) + 10000 + strlen($string_add);
      $this->vat = strlen($add)%23;
   }
}

class ParamStartLength  {
   public $id_start = '';
   public $length = '';
   public $options = '';

   function __construct($add = 'null', $string_add = '') {
      $this->id_start = 10;
      $this->length = 2;
      $this->options = 'dupa';
   }
}


class ParamStartWhereLength {
   public $id_start = '';
   public $length = '';
   public $where = '';
   public $options = '';

   function __construct($add = 'null', $string_add = '') {
      $this->id_start = strlen($add) + 10;
      $this->length = strlen($add) + 20;
      $this->where = $add . '$where';
      $this->options = $add . '$options';
   }
}

class ParamDoubleStartLength {
   public $id_start_one = '';
   public $id_start_two = '';
   public $length = '';
   public $options = '';

   function __construct($add = 'null', $string_add = '') {
      $this->id_start_one = strlen($add) + 10;
      $this->id_start_two = strlen($add) + 10;
      $this->length = strlen($add) + 20;
      $this->options = $add . '$options';
   }
}


class StatusData {
   public $id = '';
   public $additional_data = '';
   public $status = '';

   function __construct($add = 'null', $string_add = '') {
      if( is_object($add) || is_array($add) ) {
      add_to_fp( 'StatusData _in ' . var_export($add, true) );
         $this->_fill_response($add, $string_add);
         //add_to_fp( print_r($this, true) );
      } else {
         $this->id = strlen($add) + 10;
         $this->additional_data = $add . '$additional_data';
         $this->status = $add . '$status';
      }
   }

   function _fill_response( $obj, $string_add = '') {
      $this->id = false;
      $this->status = 'TIMEOUT' . $string_add;
      foreach( $obj as $key => $variable ) {
         if( substr($key, 0, 2) == 'id' ) {
            $this->id = $variable;
            break;
         }
      }
      return $this;
   }
}


class StatusDoubleData {
   public $id_one = '';
   public $id_two = '';
   public $additional_data = '';
   public $status = '';

   function __construct($add = 'null', $string_add = '') {
      if( is_object($add) || is_array($add) ) {
         $this->_fill_response($add, $string_add);
      } else {
         $this->id_one = strlen($add) + 10;
         $this->id_two = strlen($add) + 20;
         $this->additional_data = $add . '$additional_data';
         $this->status = $add . '$status';
      }
   }

   function _fill_response( $obj, $string_add = '' ) {
      $this->id_one = false;
      $this->id_two = false;
      $this->status = 'TIMEOUT' . $string_add;
      $first_id_name = false;
      foreach( $obj as $key => $variable ) {
         if( substr($key, 0, 2) == 'id' ) {
            if( !$first_id_name && $key != $first_id_name) {
               $first_id_name = $key;
               $this->id_one = $variable;
            } else {
               $this->id_two = $variable;
               break;
            }
         }
      }
      return $this;
   }
}




?>