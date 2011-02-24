<?php
class ClientData {
   public $id_client = '';
   public $name = '';
   public $description = '';
   public $email = '';
   public $phone = '';
   public $state = '';

   function __construct($add = 'null') {
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
      $this->id_client = false;
   }
}

class ClientDataArray {
   public $values = array();

    
   function __construct($add = 'null') {
      $this->values = array(0 => new ClientData('auto'));
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

    
   function __construct($add = 'null') {
      $this->id_category = strlen($add) + 10000;
      $this->id_category_parent = strlen($add) + 20000;
      $this->sort_order = strlen($add) + 30000;
      $this->root_number = strlen($add)%2;
      $this->description = $add . '$description';
      $this->date_added = data_data();
      $this->date_modified = data_data();
   }
}

class ClientUserData {
   public $id_client_user = '';
   public $id_client = '';
   public $name = '';
   public $description = '';
   public $login = '';
   public $password = '';
   public $password_salt = '';
   public $email = '';
   public $created = '';
   public $last_login = '';
   public $state = '';


   function __construct($add = 'null') {
      $this->id_client_user = strlen($add) + 10000;
      $this->id_client = strlen($add) + 20000;
      $this->name = $add . '$name';
      $this->description = $add . '$description';
      $this->login = $add . '$login';
      $this->password = $add . '$password';
      $this->password_salt = $add . '$password_salt';
      $this->email = $add . '$email';
      $this->created = data_data();
      $this->last_login = data_data();
      $this->state = $add . '$state';
   }
}

class ClientUserPassword {
   public $id_client_user = '';
   public $id_client = '';
   public $password = '';
   public $password_salt = '';


   function __construct($add = 'null') {
      $this->id_client_user = strlen($add) + 10000;
      $this->id_client = strlen($add) + 20000;
      $this->password = $add . '$password';
      $this->password_salt = $add . '$password_salt';
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


   function __construct($add = 'null') {
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


   function __construct($add = 'null') {
      $this->id_order_status = strlen($add) + 10000;
      $this->id_order = strlen($add) + 20000;
      $this->timestamp = data_data();
      $this->description = data_data();
   }
}


class Product2Category {
   public $id_product = '';
   public $id_category = '';

   function __construct($add = 'null') {
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

   function __construct($add = 'null') {
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

   function __construct($add = 'null') {
      $this->id_product = strlen($add) + 10000;
      $this->id_client = strlen($add) + 20000;
      $this->price = strlen($add) + 10000;
      $this->vat = strlen($add)%23;
   }
}

class ParamStartLength  {
   public $start = '';
   public $length = '';
   public $options = '';

   function __construct($add = 'null') {
      $this->start = 10;
      $this->length = 2;
      $this->options = 'dupa';
   }
}


class ParamStartWhereLength {
   public $start = '';
   public $lenght = '';
   public $where = '';
   public $options = '';

   function __construct($add = 'null') {
      $this->start = strlen($add) + 10;
      $this->lenght = strlen($add) + 20;
      $this->where = $add . '$where';
      $this->options = $add . '$options';
   }
}

class ParamDoubleStartLength {
   public $start_one = '';
   public $start_two = '';
   public $lenght = '';
   public $options = '';

   function __construct($add = 'null') {
      $this->start_one = strlen($add) + 10;
      $this->start_two = strlen($add) + 10;
      $this->lenght = strlen($add) + 20;
      $this->options = $add . '$options';
   }
}


class StatusData {
   public $id = '';
   public $additional_data = '';
   public $status = '';
    
   function __construct($add = 'null') {
      if( !is_object($add) ) {
         $this->id = strlen($add) + 10;
         $this->additional_data = $add . '$additional_data';
         $this->status = $add . '$status';
      } else {
         add_to_fp( print_r($add, true) );
         $this->_fill_response($add);
         add_to_fp( print_r($this, true) );
      }
   }
    
   function _fill_response( $obj ) {
      $this->id = false;
      $this->status = 'TIMEOUT';
      foreach( $obj as $key => $variable ) {
         if( substr($key, 0, 2) ) {
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

   function __construct($add = 'null') {
      $this->id_one = strlen($add) + 10;
      $this->id_two = strlen($add) + 20;
      $this->additional_data = $add . '$additional_data';
      $this->status = $add . '$status';
   }
}




?>