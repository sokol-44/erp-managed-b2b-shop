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
   private $full = false;
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
   public function __construct( $input = false, $type = true ) {
      $this->init($type);
      //var_dump(array($this->new, $this->full));
      if( $input ) {
         if( is_string($input) ) {
            $input = $this->fill_object( $input );
         } elseif (is_array($input) ) {
            //ok -> $input = $input;
            add_to_fp("input_array\n");
         } else {
            $input = array();
         }
      } else {
         $input = array();
      }
      
      $this->load_array( $input );
//       add_to_fp(print_r($this, true)."\n");
      //var_dump($this);
      
   }
   
   public function init( $type ) {
      if( $type === TRUE || $type == 'NEW') {
         $this->new = true;
         $this->full = false;
      } elseif ($type == 'FULL') {
         $this->new = false;
         $this->full = true;
      } else {
         $this->new = false;
         $this->full = false;
      }
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
   
   public function return_list() {
      return compact( $this->list );
   }

   public function return_error() {
      return $this->error;
   }
   public function return_warning() {
      return $this->warning;
   }
       
   public function is_error() {
      if( sizeof( $this->error ) > 0 ) {
         return true;
      }
      return false;
   }

   private function get_local_list() {
      if( $this->full ) {
         return $this->list;
      } elseif( $this->new ) {
         return $this->list_new;
      } else {
         return $this->list_update;
      }
      
   }
   
   private function check_list_need(  ) {
      
      $local_list = $this->get_local_list();
      
      foreach( $local_list as $new_key ) {
         if( isset($this->values[$new_key]) && $this->values[$new_key]!='' /*&& !empty($this->values[$new_key]) */) {
            //
         } else {
            $this->error[] = array('check_list_'.(($this->new)?'new':'update'), $new_key, $this->values[$new_key]);
         }
      }
   }
   
   private function check_list( $input ) {
      var_dump($input);
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
   public $list_update = array('id_category');
}


class OrderData extends BasicSOAPDataMethods {
   public $list = array('id_order', 'id_client', 'date_create', 'date_modified', 'id_order_status',
          'hidden_status', 'description', 'description_basket', 'id_shopping_basket');
   public $list_new = array('id_order', 'id_client');
   public $list_update = array('id_order', 'id_client');
}

class OrderStatus extends BasicSOAPDataMethods {
   public $list = array('id_order_status', 'id_order', 'timestamp', 'description');
   public $list_new = array('id_order_status', 'id_order');
   public $list_update = array('id_order_status', 'id_order');
}


class Product2Category extends BasicSOAPDataMethods {
   public $list = array('id_product', 'id_category');
   public $list_new = array('id_product', 'id_category');
   public $list_update = array('id_product', 'id_category');
}


class ProductData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'name', 'description', 'picture_small_url', 'picture_big_url',
          'picture_id', 'price', 'vat', 'quantity', 'status');
   public $list_new = array('id_product', 'price', 'vat', 'quantity');
   public $list_update = array('id_product');
}


class ProductClientPriceData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'id_client', 'price', 'vat');
   public $list_new = array('id_product', 'id_client', 'price', 'vat');
   public $list_update = array('id_product', 'id_client', 'price', 'vat');
}

class ParamStartLength extends BasicSOAPDataMethods {
   public $list = array('id_start', 'length', 'options');
   public $list_new = array('id_start', 'length');
   public $list_update = array('id_start', 'length');
}


class ParamStartWhereLength extends BasicSOAPDataMethods {
   public $list = array('id_start', 'length', 'where', 'options');
   public $list_new = array('id_start', 'length', 'where');
   public $list_update = array('id_start', 'length', 'where');
}

class ParamDoubleStartLength extends BasicSOAPDataMethods {
   public $list = array('id_start_one', 'id_start_two', 'length', 'options');
   public $list_new = array('id_start_one', 'id_start_two', 'length');
   public $list_update = array('id_start_one', 'id_start_two', 'length');
   
}

class StatusData extends BasicSOAPDataMethods {
   public $list = array('id', 'additional_data', 'status');
   public $list_new = array('id', 'additional_data', 'status');
   public $list_update = array('id', 'additional_data', 'status');
/*
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
   }*/
}

class StatusDoubleData extends BasicSOAPDataMethods {
   public $list = array('id_one', 'id_two', 'additional_data', 'status');
   public $list_new = array('id_one', 'id_two', 'additional_data', 'status');
   public $list_update = array('id_one', 'id_two', 'additional_data', 'status');
}

class PictureData extends BasicSOAPDataMethods {
   public $list = array('id_picture', 'name', 'description', 'data');
   public $list_new = array('id_picture', 'name', 'data');
   public $list_update = array('id_picture', 'data');
}

?>