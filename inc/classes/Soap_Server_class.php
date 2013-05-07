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
   /* types: INT,INT+ (>zero),FLOAT,FLOAT+ (>zero),PATH,TXT,HTML,DATE,EMAIL   */
   public $list_type = array();
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
            add_to_fp("input_array\n".print_r($input, true));
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
      //add_to_fp( print_r() )
      foreach( $local_list as $new_key ) {
         if( isset($this->values[$new_key]) && $this->values[$new_key]!='' ) {
            //
         } else {
            $this->error[] = array(0 => 'check_list_'.(($this->new)?'new':'update'), 1 => $new_key, 2=>$this->values[$new_key]);
         }
      }
   }
   
   private function check_list( $input ) {
      foreach ($input as $key => $val ) {
       if( in_array($key, $this->list) && $val!='' ) {
          list($val_check, $status) = $this->check_val($key, $val);
          if( $val_check !== FALSE ) {
             $this->values[$key] = $val_check;
          } else {
             $this->warning[] = array('check_val', $status.': '.$key.'=>'.$val);
          }
         } else {
            $this->warning[] = array('check_list', $key.'=>'.$val);
         }
      }
   }
   
   private function check_val($key, $val) {
      $status = '';
      $val_out = false;
      
      if( in_array($key, $this->list_type) ) {
         /* types: INT,INT+ (>zero),FLOAT,FLOAT+ (>zero),PATH,TXT,HTML,DATE,EMAIL   */
         switch ($this->list_type[$key]) {
            case 'INT':
               if( ctype_digit($val) ) $val_out = (int)$val;
               break;
            case 'INT+':
               if( ctype_digit($val) && (int)$val>0 ) $val_out = (int)$val;
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
         }
         if( $val_out === false ) $status = 'NOT ' . $key;
      } else {
         $val_out = $val;
      }
      
      return array($val_out, $status);
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
   public $list_type = array('id_client' => 'INT+', 'name' => 'TEXT', 'description' => 'TEXT',
          'email' => 'EMAIL', 'phone' => 'TEXT', 'state' => 'TEXT');
   public $list_new = array('id_client', 'name');
   public $list_update = array('id_client');

}


class ClientUserData extends BasicSOAPDataMethods {
   public $list = array('id_client_user', 'id_client', 'login', 'password', 'password_salt',
         'description', 'name', 'email', 'created', 'last_login', 'state');
   public $list_type = array('id_client_user' => 'INT+', 'id_client' => 'INT+', 'login' => 'TEXT',
         'password' => 'TEXT', 'password_salt' => 'TEXT', 'description' => 'TEXT', 'name' => 'TEXT',
          'email' => 'EMAIL', 'created' => 'DATE', 'last_login' => 'DATE', 'state' => 'TEXT');
   public $list_new = array('id_client_user', 'id_client', 'login', 'password', 'name', 'email');
   public $list_update = array('id_client_user', 'id_client');

}

class ClientUserPassword extends BasicSOAPDataMethods {
   public $list = array('id_client_user', 'id_client', 'password', 'password_salt');
   public $list_type = array('id_client_user' => 'INT+', 'id_client' => 'INT+',
          'password' => 'TEXT', 'password_salt' => 'TEXT');
   public $list_new = array('id_client_user', 'id_client', 'password');
   public $list_update = array('id_client_user', 'id_client', 'password');
}


class CategoryData extends BasicSOAPDataMethods {
   public $list = array('id_category', 'id_category_parent', 'sort_order', 'root_number', 'name', 'description',
         'date_added', 'date_modified');
   public $list_type = array('id_category' => 'INT+', 'id_category_parent' => 'INT', 'sort_order' => 'INT',
         'root_number' => 'INT', 'name' => 'TEXT', 'description' => 'TEXT', 'date_added' => 'DATE', 'date_modified' => 'DATE');
   public $list_new = array('id_category', 'id_category_parent', 'name');
   public $list_update = array('id_category');
}


class OrderData extends BasicSOAPDataMethods {
   public $list = array('id_order', 'id_client', 'date_create', 'date_modified', 'id_order_status',
          'hidden_status', 'description', 'description_basket', 'id_shopping_basket');
   public $list_type = array('id_order' => 'INT+', 'id_client' => 'INT+', 'date_create' => 'DATE',
          'date_modified' => 'DATE', 'id_order_status' => 'INT', 'hidden_status' => 'TEXT',
          'description' => 'TEXT', 'description_basket' => 'TEXT', 'id_shopping_basket' => 'INT+');
   public $list_new = array('id_order', 'id_client');
   public $list_update = array('id_order', 'id_client');
}

class OrderStatus extends BasicSOAPDataMethods {
   public $list = array('id_order_status', 'id_order', 'timestamp', 'description');
   public $list_type = array('id_order_status' => 'INT+', 'id_order' => 'INT+',
          'timestamp' => 'DATE', 'description' => 'TEXT');
   public $list_new = array('id_order_status', 'id_order');
   public $list_update = array('id_order_status', 'id_order');
}


class Product2Category extends BasicSOAPDataMethods {
   public $list = array('id_product', 'id_category');
   public $list_type = array('id_product' => 'INT+', 'id_category' => 'INT+');
   public $list_new = array('id_product', 'id_category');
   public $list_update = array('id_product', 'id_category');
}


class ProductData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'name', 'description', 'picture_small_url', 'picture_big_url',
          'picture_id', 'price', 'vat', 'quantity', 'status');
   public $list_type = array('id_product' => 'INT+', 'name' => 'TEXT', 'description' => 'TEXT',
          'picture_small_url' => 'PATH', 'picture_big_url' => 'PATH', 'picture_id' => 'INT+',
          'price' => 'FLOAT+', 'vat' => 'FLOAT', 'quantity' => 'INT+', 'status' => 'TEXT');
   public $list_new = array('id_product', 'price', 'vat', 'quantity');
   public $list_update = array('id_product');
}


class ProductClientPriceData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'id_client', 'price', 'vat');
   public $list_type = array('id_product' => 'INT+', 'id_client' => 'INT+',
          'price' => 'FLOAT+', 'vat' => 'FLOAT');
   public $list_new = array('id_product', 'id_client', 'price', 'vat');
   public $list_update = array('id_product', 'id_client', 'price', 'vat');
}

class ParamStartLength extends BasicSOAPDataMethods {
   public $list = array('id_start', 'length', 'options');
   public $list_type = array('id_start' => 'INT', 'length' => 'INT+', 'options' => 'TEXT');
   public $list_new = array('id_start', 'length');
   public $list_update = array('id_start', 'length');
}


class ParamStartWhereLength extends BasicSOAPDataMethods {
   public $list = array('id_start', 'length', 'where', 'options');
   public $list_type = array('id_start' => 'INT', 'length' => 'INT+',
          'where' => 'TEXT', 'options' => 'TEXT');
   public $list_new = array('id_start', 'length', 'where');
   public $list_update = array('id_start', 'length', 'where');
}

class ParamDoubleStartLength extends BasicSOAPDataMethods {
   public $list = array('id_start_one', 'id_start_two', 'length', 'options');
   public $list_type = array('id_start_one' => 'INT', 'id_start_two' => 'INT',
          'length' => 'INT+', 'options' => 'TEXT');
   public $list_new = array('id_start_one', 'id_start_two', 'length');
   public $list_update = array('id_start_one', 'id_start_two', 'length');
   
}

class StatusData extends BasicSOAPDataMethods {
   public $list = array('id', 'additional_data', 'status');
   public $list_type = array('id' => 'INT', 'additional_data' => 'TEXT', 'status' => 'TEXT');
   public $list_new = array('id', 'additional_data', 'status');
   public $list_update = array('id', 'additional_data', 'status');
}

class StatusDoubleData extends BasicSOAPDataMethods {
   public $list = array('id_one', 'id_two', 'additional_data', 'status');
   public $list_type = array('id_one' => 'INT', 'id_two' => 'INT',
          'additional_data' => 'TEXT', 'status' => 'TEXT');
   public $list_new = array('id_one', 'id_two', 'additional_data', 'status');
   public $list_update = array('id_one', 'id_two', 'additional_data', 'status');
}

class PictureData extends BasicSOAPDataMethods {
   public $list = array('id_picture', 'name', 'description', 'data');
   public $list_type = array('id_picture' => 'INT', 'name' => 'TEXT',
          'description' => 'TEXT', 'data' => 'TEXT');
   public $list_new = array('id_picture', 'name', 'data');
   public $list_update = array('id_picture', 'data');
}

?>