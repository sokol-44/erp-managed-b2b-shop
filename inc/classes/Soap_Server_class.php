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
   private $type = 'NEW';
   private $new = false;
   private $full = false;
   private $status = array('NR' => -1, 'TXT' => '');
   private $error = array();
   private $warning = array();
   // placeholder for upper class
   public $list = array();
   public $list_new = array();
   public $list_update = array();
   /* types: INT,INT+ (>zero),FLOAT,FLOAT+ (>zero),PATH,TXT,HTML,DATE,EMAIL,ARRAY,ARRAYOBJ,OBJ */
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
      $this->type = $type;
      $this->init();
      //var_dump(array($this->new, $this->full));
      if( $input ) {
         if( is_string($input) ) {
            $input = $this->fill_object( $input );
      		$this->load_array( $input );
         } elseif (is_array($input) ) {
            add_to_fp("input_array\n".print_r($input, true));
            $this->load_array( $input );
         } elseif ( is_object($input) && get_class($input) == get_class($this) ) {
            add_to_fp("input_object START\n".print_r($input, true));
            $this->load_array( $input->return_array() );
            add_to_fp("input_object END\n".print_r($input, true));
            
         } else {
            $this->load_array( array() );
         }
      } else {
         $this->load_array( array() );
      }
      
   }
   
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
   
   public function load_array( array $input ) {
      $this->check_list($input);
      $this->check_list_need();
      
      //TODO somethinig with error
      if( $this->is_error() ) {
         return false;
      }
      return true;
   }
   
   
   //FIXME - rekurencja !
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
   
   private function _return_array($in) {
      add_to_fp('_return_array()');
      if( !is_object($in) ) {
         return array();
      } else {
         return $in->return_array();
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
         if( isset($this->values[$new_key]) && (string)$this->values[$new_key]!='' ) {
            //
         } else {
            $this->error[] = array(0 => 'check_list_'.(($this->new)?'new':'update'), 1 => $new_key, 2=>$this->values[$new_key]);
         }
      }
   }
   
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
   
   private function check_val($key, $val) {
      $status = '';
      $val_out = false;
      add_to_fp('check_val '.$key.';'.$this->list_type[$key].';'.$val);
      if( isset($this->list_type[$key]) ) {
         /* types: INT,INT+ (>zero),FLOAT,FLOAT+ (>zero),PATH,TXT,HTML,DATE,EMAIL,ARRAY,OBJ   */
         switch ($this->list_type[$key]) {
            case 'INT':
               if( is_numeric($val) ) $val_out = (int)$val;
               break;
            case 'INT+':
            	add_to_fp('INT+:"'.print_r(array(is_numeric($val)?'t':'n', ((int)$val>0)?'t':'n'), true).'"');
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
      add_to_fp('ret:"'.print_r($val_out, true).'"');
      return array($val_out, $status);
   }
   
   public function data_exist( $key, $val ) {
   	add_to_fp('data_exist:'.$key."\n".print_r($this->values[$key], true));
   	return $this->check_val($key, $val);
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

class ClientAttributeData extends BasicSOAPDataMethods {
   public $list = array('id_client', 'type', 'val');
   public $list_type = array('id_client' => 'INT+', 'type' => 'TEXT', 'val' => 'TEXT');
   public $list_new = array('id_client', 'type', 'val');
   public $list_update = array('id_client', 'type', 'val');
}

class ClientUserData extends BasicSOAPDataMethods {
   public $list = array('id_client_user', 'id_client', 'login', 'password', 'password_salt',
         'description', 'name', 'email', 'phone', 'phone_cell', 'created', 'last_login', 'state');
   public $list_type = array('id_client_user' => 'INT+', 'id_client' => 'INT+', 'login' => 'TEXT',
         'password' => 'TEXT', 'password_salt' => 'TEXT', 'description' => 'TEXT', 'name' => 'TEXT',
          'email' => 'EMAIL', 'phone' => 'TEXT', 'phone_cell' => 'TEXT',
          'created' => 'DATE', 'last_login' => 'DATE', 'state' => 'TEXT');
   public $list_new = array('id_client_user', 'id_client', 'login', 'password', 'name');
   public $list_update = array('id_client_user', 'id_client');
}

class ClientUserAttributeData extends BasicSOAPDataMethods {
	public $list = array('id_client', 'id_client_user', 'type', 'val');
	public $list_type = array('id_client' => 'INT+', 'id_client_user' => 'INT+', 'type' => 'TEXT', 'val' => 'TEXT');
	public $list_new = array('id_client', 'id_client_user', 'type', 'val');
	public $list_update = array('id_client', 'id_client_user', 'type', 'val');
}

class ClientUserPasswordData extends BasicSOAPDataMethods {
   public $list = array('id_client_user', 'id_client', 'password', 'password_salt');
   public $list_type = array('id_client_user' => 'INT+', 'id_client' => 'INT+',
          'password' => 'TEXT', 'password_salt' => 'TEXT');
   public $list_new = array('id_client_user', 'id_client', 'password');
   public $list_update = array('id_client_user', 'id_client', 'password');
}

class ClientUserAddressData extends BasicSOAPDataMethods {
	public $list = array('id_address', 'id_client_user', 'id_client', 'description', 'name',
			'street', 'city', 'zip_code', 'country');
	public $list_type = array('id_address' => 'INT+', 'id_client_user' => 'INT+', 'id_client' => 'INT+',
			'street' => 'TEXT', 'city' => 'TEXT', 'zip_code' => 'TEXT', 'country' => 'TEXT', 'state' => 'TEXT');
	public $list_new = array('id_address', 'id_client_user', 'id_client', 'description', 'name',
			'street', 'city', 'zip_code', 'country', 'state');
	public $list_update = array('id_address', 'id_client_user', 'id_client', 'description', 'name',
			'street', 'city', 'zip_code', 'country', 'state');
}

class AccountManagerData extends BasicSOAPDataMethods {
   public $list = array('id_account_manager', 'id_client_user', 'id_client', 'account_manager_name',
   		'fullname', 'phone1', 'phone2', 'email');
   public $list_type = array('id_account_manager' => 'INT', 'id_client_user' => 'INT+', 'id_client' => 'INT+',
         'account_manager_name' => 'TEXT', 'fullname' => 'TEXT', 'phone1' => 'TEXT', 'phone2' => 'TEXT',
   		 'email' => 'TEXT', 'state' => 'TEXT');
   public $list_new = array('id_account_manager', 'id_client_user', 'id_client', 'account_manager_name',
   		'fullname', 'phone1', 'phone2', 'email', 'state');
   public $list_update = array('id_account_manager', 'id_client_user', 'id_client', 'account_manager_name');
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
          'hidden_status', 'description', 'description_basket', 'id_shopping_basket', 'id_address');
   public $list_type = array('id_order' => 'INT+', 'id_client' => 'INT+', 'date_create' => 'DATE',
          'date_modified' => 'DATE', 'id_order_status' => 'INT', 'hidden_status' => 'TEXT',
          'description' => 'TEXT', 'description_basket' => 'TEXT', 'id_shopping_basket' => 'INT+',
   		 'id_address' => 'INT', 'id_account_manager' => 'INT', 'OrderAttributeData' => 'OBJ');
   public $list_new = array('id_order', 'id_client');
   public $list_update = array('id_order', 'id_client');
}

class OrderAttributeData extends BasicSOAPDataMethods {
	public $list = array('id_order', 'type', 'val');
	public $list_type = array('id_order' => 'INT+', 'type' => 'TEXT', 'val' => 'TEXT');
	public $list_new = array('id_order', 'type', 'val');
	public $list_update = array('id_order', 'type', 'val');
}

class OrderStatusData extends BasicSOAPDataMethods {
   public $list = array('id_order_status', 'id_order', 'timestamp', 'description');
   public $list_type = array('id_order_status' => 'INT+', 'id_order' => 'INT+',
          'timestamp' => 'DATE', 'description' => 'TEXT');
   public $list_new = array('id_order_status', 'id_order');
   public $list_update = array('id_order_status', 'id_order', 'description');
}

class Product2CategoryData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'id_category');
   public $list_type = array('id_product' => 'INT+', 'id_category' => 'INT+');
   public $list_new = array('id_product', 'id_category');
   public $list_update = array('id_product', 'id_category');
}

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

class ProductData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'name', 'description', 'producer', 'catalog_index',
          'picture_small_url', 'picture_big_url', 'picture_id', 'price', 'vat', 'quantity',
          'status', 'Product2CategoryData', 'ProductClientPriceData', 'ProductAttributeData',
   		 'ProductAttributeWGroupData');
   public $list_type = array('id_product' => 'INT+', 'name' => 'TEXT', 'description' => 'TEXT',
          'producer' => 'TEXT', 'catalog_index' => 'TEXT',
          'picture_small_url' => 'PATH', 'picture_big_url' => 'PATH', 'picture_id' => 'INT+',
          'price' => 'FLOAT+', 'vat' => 'FLOAT', 'quantity' => 'INT+', 'status' => 'TEXT',
   		 'ProductSubtypeData' => 'ARRAYOBJ',
          'Product2CategoryData' => 'ARRAYOBJ', 'ProductClientPriceData' => 'ARRAYOBJ',
          'ProductAttributeData' => 'ARRAYOBJ', 'ProductAttributeWGroupData' => 'ARRAYOBJ'
   		);
   public $list_new = array('id_product', 'price', 'vat', 'quantity');
   public $list_update = array('id_product');
   public $list_method = array(
   			'ProductSubtypeData' => 'doProductSubtypeAddOrUpdate', 
   			'Product2CategoryData' => 'setProduct2Category', 
   			'ProductClientPriceData' => 'setProductClientPrice',
          	'ProductAttributeData' => 'doProductAttributeAddOrUpdate',  
   			'ProductAttributeWGroupData' => 'doProductAttributeWGroupAddOrUpdate');
}

class ClientProductPriceListData extends BasicSOAPDataMethods {
   public $list = array('id_client', 'ProductPriceData');
   public $list_type = array('id_client' => 'INT+', 'ProductPriceData' => 'OBJ');
   public $list_new = array('id_client', 'ProductPriceData');
   public $list_update = array('id_client', 'ProductPriceData');
}

class ProductPriceData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'price');
   public $list_type = array('id_product' => 'INT+', 'price' => 'FLOAT+');
   public $list_new = array('id_product', 'price');
   public $list_update = array('id_product', 'price');
}

class ClientPriceListData extends BasicSOAPDataMethods {
   public $list = array('id_client', 'price');
   public $list_type = array('id_client' => 'INT+', 'price' => 'FLOAT+');
   public $list_new = array('id_client', 'price');
   public $list_update = array('id_client', 'price');
}

class CategoryListData extends BasicSOAPDataMethods {
   public $list = array('id_category');
   public $list_type = array('id_category' => 'INT+');
   public $list_new = array('id_category');
   public $list_update = array('id_category');
}

class ProductClientPriceData extends BasicSOAPDataMethods {
   public $list = array('id_product', 'id_client', 'price');
   public $list_type = array('id_product' => 'INT+', 'id_client' => 'INT+',
          'price' => 'FLOAT+');
   public $list_new = array('id_product', 'id_client', 'price');
   public $list_update = array('id_product', 'id_client', 'price');
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

class ShopAttributeData extends BasicSOAPDataMethods {
	public $list = array('type', 'val');
	public $list_type = array('type' => 'TEXT', 'val' => 'TEXT');
	public $list_new = array('type', 'val');
	public $list_update = array('type', 'val');
}

class ProductAttributeData extends BasicSOAPDataMethods {
	public $list = array('id_product', 'type', 'val');
	public $list_type = array('id_product' => 'INT+', 'type' => 'TEXT', 'val' => 'TEXT');
	public $list_new = array('id_product', 'type', 'val');
	public $list_update = array('id_product', 'type', 'val');
}

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

/// UPS seller 
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
?>