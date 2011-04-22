<?php
//class ClientDataArray {
//   public $values = array();
//
//   function __construct($add = 'null') {
//      $this->values = array(0 => new ClientData('auto'));
//   }
//
//}

class ClientData {
   public $id_client = '';
   public $name = '';
   public $description = '';
   public $email = '';
   public $phone = '';
   public $state = '';

   function __construct($add = 'null', $string_add = '') {
      if( !is_object($add) ) {
         $this->id_client = strlen($add) + 10000;
         $this->name = $add . '$name';
         $this->description = $add . '$description';
         $this->email = $add . '$email';
         $this->phone = $add . '$phone';
         $this->state = $add . '$state';
      } else {
         $this->_fill_response($add);
      }
   }


   function _fill_response( $obj ) {
      $this->id_client = 0;
      $this->name = '';
      $this->description = '';
      $this->email = '';
      $this->phone = '';
      $this->state = '';
      foreach( $obj as $key => $variable ) {
         if( substr($key, 0, 2) == 'id' ) {
            $this->id_client = $variable;
            break;
         }
      }
      return $this;
   }
}


class ClientUserData {
   public $id_client_user = '';
   public $id_client = '';
   public $login = '';
   public $password = '';
   public $password_salt = '';
   public $description = '';
   public $name = '';
   public $email = '';
   public $created = '';
   public $last_login = '';
   public $state = '';


   function __construct($add = 'null', $string_add = '') {
      if( !is_object($add) ) {
         $this->id_client_user = strlen($add) + 10000;
         $this->id_client = strlen($add) + 20000;
         $this->login = $add . '$login';
         $this->password = $add . '$password';
         $this->password_salt = $add . '$password_salt';
         $this->description = $add . '$description';
         $this->name = $add . '$name';
         $this->email = $add . '$email';
         $this->created = data_data();
         $this->last_login = data_data();
         $this->state = $add . '$state';
      } else {
         $this->_fill_response($add);
      }
   }

   function _fill_response( $obj ) {
      $this->id_client_user = 0;
      $this->id_client = 0;
      $this->login = '';
      $this->password = '';
      $this->password_salt = '';
      $this->description = '';
      $this->name = '';
      $this->email = '';
      $this->created = '';
      $this->last_login = '';
      $this->state = '';
      foreach( $obj as $key => $variable ) {
         if( substr($key, 0, 2) == 'id' ) {
            if( !$first_id_name && $key != $first_id_name) {
               $first_id_name = $key;
               $this->id_client_user = $variable;
            } else {
               $this->id_client = $variable;
               break;
            }
         }
      }
      return $this;
   }


}

class ClientUserPassword {
   public $id_client_user = '';
   public $id_client = '';
   public $password = '';
   public $password_salt = '';


   function __construct($add = 'null', $string_add = '') {
      $this->id_client_user = strlen($add) + 10000;
      $this->id_client = strlen($add) + 20000;
      $this->password = $add . '$password';
      $this->password_salt = $add . '$password_salt';
   }
}


class CategoryData {
   public $id_category = '';
   public $id_category_parent = '';
   public $sort_order = '';
   public $root_number = '';
   public $name = '';
   public $description = '';
   public $date_added = '';
   public $date_modified = '';


   function __construct($add = 'null', $string_add = '') {
      $this->id_category = strlen($add) + 10000;
      $this->id_category_parent = strlen($add) + 20000;
      $this->sort_order = strlen($add) + 30000;
      $this->root_number = strlen($add)%2;
      $this->name = $add . '$name';
      $this->description = $add . '$description';
      $this->date_added = data_data();
      $this->date_modified = data_data();
   }

   function _fill_response( $obj ) {
      $this->id_category = 0;
      $this->id_category_parent = 0;
      $this->sort_order = 0;
      $this->root_number = 0;
      $this->name = '';
      $this->description = '';
      $this->date_added = '';
      $this->date_modified = '';
      foreach( $obj as $key => $variable ) {
         if( substr($key, 0, 2) == 'id' ) {
            $this->id_category = $variable;
            break;
         }
      }
      return $this;
   }

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
      $this->description = $add . '$description';
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
      $this->description = data_data();
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
      $this->status = $add . '$picture_big_url';
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
      $this->price = strlen($add) + 10000;
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
      if( !is_object($add) ) {
         $this->id = strlen($add) + 10;
         $this->additional_data = $add . '$additional_data';
         $this->status = $add . '$status';
      } else {
         //add_to_fp( print_r($add, true) );
         $this->_fill_response($add, $string_add);
         //add_to_fp( print_r($this, true) );
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
      if( !is_object($add) ) {
         $this->id_one = strlen($add) + 10;
         $this->id_two = strlen($add) + 20;
         $this->additional_data = $add . '$additional_data';
         $this->status = $add . '$status';
      } else {
         $this->_fill_response($add, $string_add);
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