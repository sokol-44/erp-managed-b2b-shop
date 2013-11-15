<?php
/**
 * Data_Order.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/*
 * order attribute
 * 
 * sposób płatności, czy częściowa realizacja jest dopuszczalna
 * przy zamówieniu ma być możliwość podania życzonego terminu dostawy
 * przy składaniu zamówienia opcja: odbiór osobisty
 * 
 * PAYMENT_METHOD
 * DELIVERY_PARTIAL
 * DELIVERY_DATE
 * DELIVERY_PERSONAL
 * 
 */


class Data_Order extends Data_Picture {
	static $Data_order_params = array( 
			'PAYMENT_METHOD' => array(
					'CASH_TRANSFER', 'CASH_ON_DELIVERY', 'CASH_IN_PERSON'
					),
			'DELIVERY_PARTIAL' => 'BOOL',
			'DELIVERY_DATE'	=> 'DATE'		
			);
	
   function __construct() {
      //echo 'Data_Order';
      parent::__construct();

   }


   /*
    $this->data = Data::get_order_data( $id_order );
    if( $this->data ) {
    $this->product_list = Data::get_order_product_list( $id_order );
    $this->status_history = Data::get_order_status_history_list( $id_order );
     
    */
   static function getOrderList( $id_order_start = 0, $length = 1, $where = '' ) {
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
      
      $query = 'select o.id_order, o.id_client, o.date_create, o.date_modified, o.id_order_status,
      o.description, o.description_basket, o.id_shopping_basket, o.id_address, os.name as status_name,
      o.id_account_manager, cuam.account_manager_name
      from ' . TBL_SHOP_ORDER . ' o left join ' . TBL_SHOP_ORDER_STATUS . ' os
      on (o.id_order_status = os.id_order_status)
      left outer join ' . TBL_GLOBAL_CLIENT_USER_ACCOUNT_MANAGER . ' cuam 
      on (o.id_account_manager = cuam.id_account_manager)
      where o.id_order ' . $comparision_dir . db_int($id_order_start) . $where . '
      ORDER BY o.id_order ' . $order_dir . ' LIMIT '. db_int($length);
      
      $result = db_query( $query );
      $ret_tmp = db_result_array_full($result);
      
      $ret_array = array();
      foreach($ret_tmp as $order ) {
         $order['ProductOrder'] = self::getProductOrder((int)$order['id_order']);
         $order['OrderAttributeData'] = self::getOrderAttributeData((int)$order['id_order']);
         $ret_array[] = $order;
      }
      return $ret_array;
   }
   
   static function getOrderAttributeData( $id_order ) {
   	$ret_tmp =  self::getOrderAttributeList((int)$id_order);
   	
   	$ret_array = array();
   	$idx=0;
   	foreach($ret_tmp as $order ) {
   		$ret_array['value_'.$idx++] = $order;
   	}
   	return $ret_array;
   }
   
   static function getProductOrder( $id_order ) {
      $query = 'select op.id_product, op.name, op.price, op.vat, op.quantity
      from ' . TBL_SHOP_ORDER_PRODUCT . ' op where op.id_order = ' . db_int($id_order);
      
      $result = db_query( $query );
      $ret_tmp = db_result_array_full($result);
      
      $ret_array = array();
      foreach($ret_tmp as $product ) {
         $ret_array['value_'.$product['id_product']] = $product;
      }
      return $ret_array;
   }
   
   static function getOrderListRest( $id_order_start = 0, $length = 1, $where = '' ) {
      add_to_fp("getOrderListRest:");
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
      
      $query = 'select o.id_order, o.id_client, o.date_create, o.date_modified, o.id_order_status,
      o.description, o.description_basket, o.id_shopping_basket, o.id_address
      from ' . TBL_SHOP_ORDER . ' o
      where o.id_order ' . $comparision_dir . db_int($id_order_start) . $where . '
      ORDER BY o.id_order ' . $order_dir;
      add_to_fp($query);
      $result = db_query( $query );
      return db_rows($result)-1;
   }
   
   static function setOrderHiddenStatus( $id_order, $id_client, $hidden_status) {
      $query = 'select "' . db_int($id_order) . '" as id_one, "' . db_int($id_client) . '" as id_two,
       "" as additional_data,
       b_func_order_hidden_status_change("' . db_int($id_order) . '", "' . db_int($id_client) . '", "' . db_escape($hidden_status) . '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }
   
   static function setOrderStatus( $id_order, $id_order_status, $status) {
   	  $res = self::put_order_status((int)$id_order, (int)$id_order_status, $status);
   	  switch($res) {
   	  	case 1:
   	  		$status = 'SUCCESS,NEW_STATUS';
   	  		break;
   	  	case 2:
   	  		$status = 'SUCCESS,UPDATE_STATUS';
   	  		break;
   	  	//case false:
   	  	//case 0:
   	  	default:
   	  		$status = 'ERROR,WRONG_ORDER_ID';
   	  		break;
   	  }
      return array( 'id' => $id_order, 
      				'additional_data' => 'id_order_status=' . $id_order_status,
      				'status' => $status );
   }
   
   static function get_order_history_list() {

   	$query = 'select os.id_order_status, concat("OSH_", os.name) as name from ' . TBL_SHOP_ORDER_STATUS . ' os';
   	$res = db_result_array( db_query( $query ) );
   	$ret_array = array();
   	foreach( $res as $osh ) {
   		$ret_array[$osh['id_order_status']] = $osh['name'];
   	}
   	
   	return $ret_array;
   }
   
   static function get_order_list( $id_client = 0, $where = '' ) {
      $SP = SplitPage::g_global();
      
      if( $id_client > 0 ) {
         $where =  ' where o.id_client = ' . db_int($id_client);
      }

      $query = 'select o.id_order, o.id_client, o.date_create, o.date_modified, o.id_order_status,
      o.description, o.description_basket, o.id_shopping_basket, o.id_address, concat("OSH_", os.name) as name,
     	UNIX_TIMESTAMP(o.date_create) as ts_create, UNIX_TIMESTAMP(o.date_modified) as ts_modified,
      o.id_account_manager, cuam.account_manager_name
      from ' . TBL_SHOP_ORDER . ' o left join ' . TBL_SHOP_ORDER_STATUS . ' os
      on (o.id_order_status = os.id_order_status)
      left outer join ' . TBL_GLOBAL_CLIENT_USER_ACCOUNT_MANAGER . ' cuam 
      on (o.id_account_manager = cuam.id_account_manager)' . $where;
      $query_fast = 'select count(o.id_order) as total from ' . TBL_SHOP_ORDER . ' o ' . $where;
      $sp_query = $SP->prepare_sql( $query, $query_fast);
      $res = db_query( $sp_query );
      $ret_tmp = db_result_array( $res );
      $ret_array = array();
      foreach($ret_tmp as $order ) {
         $ret_array[$order['id_order']] = $order;
      }
      return $ret_array;
   }



   static function get_order_data( $id_order ) {
      $query = 'select o.id_order, o.id_client, o.date_create, o.date_modified, o.id_order_status,
      o.description, o.description_basket, o.id_shopping_basket, o.id_address, concat("OSH_", os.name) as name,
     	UNIX_TIMESTAMP(o.date_create) as ts_create, UNIX_TIMESTAMP(o.date_modified) as ts_modified, o.id_address,
      o.id_account_manager, cuam.account_manager_name
      from ' . TBL_SHOP_ORDER . ' o left join ' . TBL_SHOP_ORDER_STATUS . ' os
      on (o.id_order_status = os.id_order_status)
      left outer join ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . ' cua 
      on (o.id_address = cua.id_address)
      left outer join ' . TBL_GLOBAL_CLIENT_USER_ACCOUNT_MANAGER . ' cuam 
      on (o.id_account_manager = cuam.id_account_manager)
      where id_order = ' . db_int($id_order);
      return db_fetch_array( db_query( $query ) );
   }

   static function get_order_product_list( $id_order ) {
      $query = 'select op.id_product, op.name, op.price, op.vat, op.quantity,
      p.name as p_name, p.description, p.picture_small_url, p.picture_big_url, p.picture_id
      from ' . TBL_SHOP_ORDER_PRODUCT . ' op left outer join ' . TBL_SHOP_PRODUCT . ' p
      on (op.id_product = p.id_product and p.status = "ACTIVE")
      where id_order = ' . db_int($id_order);
      $ret_tmp = db_result_array( db_query( $query ) );
      $ret_array = array();
      foreach($ret_tmp as $product ) {
         $ret_array[$product['id_product']] = $product;
      }
      return $ret_array;
   }

   static function get_order_status_history_list( $id_order ) {
      $query = 'select osh.id_order_status, osh.timestamp, osh.description, concat("OSH_", os.name) as name
      from ' . TBL_SHOP_ORDER_STATUS_HISTORY . ' osh left join ' . TBL_SHOP_ORDER_STATUS . ' os
      on (osh.id_order_status = os.id_order_status)
      where id_order = ' . db_int($id_order) . ' order by osh.timestamp asc';
      $ret_tmp = db_result_array( db_query( $query ) );
      $ret_array = array();
      foreach($ret_tmp as $status ) {
         $ret_array[$status['id_order_status']] = $status;
      }
      return $ret_array;
   }



   static function put_order_data($id_client, $basket_params, $params_in) {
      $query = 'insert into ' . TBL_SHOP_ORDER . '
      	set id_client = ' . db_int($id_client) . ',
      	description = "' . db_escape($params_in['order_description']) . '",
      	description_basket = "' . db_escape($basket_params['description']) . '",
      	id_shopping_basket = "' . db_int($basket_params['id_shopping_basket']) . '",
      	id_address = "' . db_int($params_in['id_address']) . '",
      	id_account_manager = "' . db_int($params_in['id_account_manager']) . '",
      	date_create = now(), date_modified = NULL,
      	id_order_status = 1';

      //      print_debug($query);
       
      db_query( $query );
      $id_order = db_insert_id();
      //$id_order = 1;
      return $id_order;
   }


   static function put_order_status($id_order, $id_order_status, $description) {
   	$query_chk = 'select `id_order` from ' . TBL_SHOP_ORDER . '
      	where id_order = ' . db_int($id_order);
   	
   	  if( db_rows( db_query($query_chk) ) == 1 ) {
	   	$query = 'insert into ' . TBL_SHOP_ORDER_STATUS_HISTORY . '
	      	set id_order = ' . db_int($id_order) . ',
	   		id_order_status = ' . db_int($id_order_status) . ',
	      	description = "' . db_escape($description) . '"';
	      $res = db_query( $query );
	      $af_rows = db_affected_rows();
	      $query = 'update ' . TBL_SHOP_ORDER . ' 
	        set id_order_status = ' . db_int($id_order_status) . '
	      	where id_order = ' . db_int($id_order);	
	      $res = db_query( $query );
	      return  $af_rows;
      } else {
      	return false;
      }
      
   }
   
   static function put_order_attributes_list($id_order, $attributes) {
   
   	$insert_query = 'insert into ' . TBL_SHOP_ORDER_ATTRIBUTES . ' 
   			(`id_order`, `type`, `val` ) values ';
   	$insert_query_arr = array();
   	$id_order = db_int($id_order);
   	foreach( $attributes as $name => $val ) {
   		$insert_query_arr[] = 
   		'(' . $id_order . ', "' . db_escape($name) . '", "' . db_escape($val) . '")'; 
   	}
   	
   	if( sizeof($insert_query_arr) > 0 ) {
   		return db_query($insert_query . implode(',',$insert_query_arr ));
   	} else {
   		return false;
   	}
   }
   
   static function put_order_product_list($id_order, $product_list) {

      db_transaction_start();
      foreach( $product_list as $id_product => $details ) {
         $insert_query = 'insert into ' . TBL_SHOP_ORDER_PRODUCT . '
      	set id_order = ' . db_int($id_order) . ',
      	id_product = ' . db_int($id_product) . ',
      	name = "' . db_escape($details['name']) . '",
      	price = "' . db_escape($details['price']) . '",
      	vat = "' . db_int($details['vat']) . '",
      	quantity = ' . db_int($details['quantity']) . '';
         //         print_debug($insert_query);
         db_query( $insert_query );
      }
      //      print_debug($basket_params);
      //      print_debug($basket_contents);
      $res = db_affected_rows();
      db_transaction_end();

      return $res;
   }

   static function get_order_attribute( $id_order, $attribute_type = false) {
   	$F = Framework::g_global();
   
   	$where_add = '';
   
   	if( $F->not_null($attribute_type) ) {
   		$where_add = ' and oa.type = "' . db_escape($attribute_type) . '"';
   	}
   
   	$query = 'select type, val
         	from ' . TBL_SHOP_ORDER_ATTRIBUTES . ' oa ,
         	' . TBL_SHOP_ORDER . ' o
         	where oa.id_order = o.id_order and oa.id_order = "' . db_int($id_order) . '"' . $where_add;
   	$result = db_query( $query );
   	$nrow = db_rows( $result );
   	if( $nrow == 1 && $F->not_null($attribute_type) ) {
   		return db_fetch_result('val', $result);
   	} elseif( $nrow > 1 ) {
   		return db_result_array_full($result);
   	} else {
   		return false;
   	}   

   }
   
   
   static function get_invoice_data($id_invoice) {
   	$query = 'select `id_invoice`, `id_client`, `id_order`, `invoice_number`,
   		`state`, `net_value`, `gross_value`, `description`,
   		`date_issue`, `date_pay`, invoice_image,
   		UNIX_TIMESTAMP(date_issue) as ts_issue, UNIX_TIMESTAMP(date_pay) as ts_pay
   		from ' . TBL_SHOP_ORDER_INVOICE . '
      	where id_invoice = ' . db_int($id_invoice) . '';
   	$result = db_query( $query );
   
   	if( db_rows($result) > 0 ) return  db_fetch_array($result);
   	else return array();
   
   }
   
   static function get_invoice_client_list($params) {
   	$query = 'select `id_invoice`, `id_client`, `id_order`, `invoice_number`,
   		`state`, `net_value`, `gross_value`, `description`,
   		`date_issue`, `date_pay`, invoice_image,
   		UNIX_TIMESTAMP(date_issue) as ts_issue, UNIX_TIMESTAMP(date_pay) as ts_pay
   		from ' . TBL_SHOP_ORDER_INVOICE . '
      	where id_client = ' . db_int($params['id_client']) . ' order by date_issue desc';
   	$result = db_query( $query );
   	
   	if( db_rows($result) > 0 ) return db_result_array_full( $result );
   	else return array();
   }
    
   static function get_invoice_order_list($params) {
   	$query = 'select `id_invoice`, `id_client`, `id_order`, `invoice_number`,
   		`state`, `net_value`, `gross_value`, `description`,
   		`date_issue`, `date_pay`, invoice_image,
   		UNIX_TIMESTAMP(date_issue) as ts_issue, UNIX_TIMESTAMP(date_pay) as ts_pay
   		from ' . TBL_SHOP_ORDER_INVOICE . '
      	where id_client = ' . db_int($params['id_order']) . ' order by date_issue desc';
   	$result = db_query( $query );
   	
   	if( db_rows($result) > 0 ) return db_result_array_full( $result );
   	else return array();
   }
   

   static function getClientAccountManagerList($id_client, $length) {
   	list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
   
   	$query = 'select `id_account_manager`, `id_client`, `id_client_user`, `account_manager_name`,
   			`fullname`, `phone1`, `phone2`, `email`, `date_created`, `date_modified`
   			from ' . TBL_GLOBAL_CLIENT_USER_ACCOUNT_MANAGER . '
   			where id_client ' . $comparision_dir . db_int($id_client) . '
      	ORDER BY id_client ' . $order_dir . ' LIMIT '. db_int($length);
   	$result = db_query( $query );
   
   	if( db_rows($result) > 0 ) return  db_result_array_full($result);
   	else return array();
   }
      
   static function doClientAccountManagerAddOrUpdate($param) {
   	$F = Framework::g_global();

  
   	 $query = 'select "' . db_int($param['id_account_manager']) . '" as id_start,
   			"' . db_escape($param['account_manager_name'].','.$param['id_client']
   			.','.$param['id_client_user']) . '" as additional_data,
          b_func_account_manager_set("' . db_int($param['id_account_manager']) . '",
          "' . db_int($param['id_client']) . '", "' . db_int($param['id_client_user']) . '",
          "' . db_escape($param['account_manager_name']) . '", "' . db_escape($param['fullname']) . '",
          "' . db_escape($param['phone1']) . '", "' . db_escape($param['phone2']) . '",
          "' . db_escape($param['email']) . '") as status';
   
   	add_to_fp($query);
   	$result = db_query( $query );
   	return db_fetch_array($result);
   }
   
   static function getInvoiceList($id_invoice, $length) {
   	list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
   	
   	$query = 'select `id_invoice`, `id_client`, `id_order`, `invoice_number`,
   		`state`, `net_value`, `gross_value`, `description`,
   		`date_issue`, `date_pay`,
   		UNIX_TIMESTAMP(date_issue) as ts_issue, UNIX_TIMESTAMP(date_pay) as ts_pay
   		from ' . TBL_SHOP_ORDER_INVOICE . '
      	where id_invoice ' . $comparision_dir . db_int($id_invoice) . '
      	ORDER BY id_invoice ' . $order_dir . ' LIMIT '. db_int($length);
   	$result = db_query( $query );
   
   	if( db_rows($result) > 0 ) return  db_result_array_full($result);
   	else return array();
   }
   
   
   static function setInvoiceStatus($param) {
   	
   	$query = 'select "' . db_int($param['id_invoice']) . '" as id_one,
   			"' . db_int($param['id_order']) . '" as id_two,
   			"' . db_escape($param['state']) . '" as additional_data,
          b_func_order_invoice_state_set("' . db_int($param['id_invoice']) . '", "' . db_int($param['id_order']) . '", 
          "' . db_escape($param['state']) . '") as status';
   	
   	add_to_fp($query);
   	$result = db_query( $query );
   	return db_fetch_array($result);
   }
   
   static function doInvoiceAddOrUpdate($param) {
   	$F = Framework::g_global();
   	
   	$query = 'select "' . db_int($param['id_invoice']) . '" as id_one,
   			"' . db_int($param['id_order']) . '" as id_two,
   			"' . db_escape($param['id_order'].','.$param['invoice_number']) . '" as additional_data,
          b_func_order_invoice_set("' . db_int($param['id_invoice']) . '", 
          "' . db_int($param['id_order']) . '", "' . db_int($param['id_client']) . '",
          "' . db_escape($param['invoice_number']) . '", "' . db_escape($param['state']) . '",
          "' . db_float($param['net_value']) . '", "' . db_float($param['gross_value']) . '",
          "' . db_escape($param['date_issue']) . '", "' . db_escape($param['date_pay']) . '",		
          "' . db_escape($param['description']) . '") as status';
   	
   	add_to_fp($query);
   	$result = db_query( $query );
   	$res_array = db_fetch_array($result);
   	
   	if( $F->not_null($param['invoice_image']) && strstr($res_array['status'], 'SUCCESS,')) {
   		add_to_fp('PDF BLOB');
   		$query_ii = 'UPDATE shop_order_invoice set
   		`invoice_image` = "' . db_escape($param['invoice_image']) . '"
   		WHERE id_invoice = "' . db_int($param['id_invoice']) . '"';
   		db_query( $query_ii );
   		$res_array['status'] .= ',INVOICE_IMAGE';
   	}
   	
   	return $res_array;
   }
   
   static function getClientInvoiceList($id_client, $id_invoice, $length) {
   	list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
   	
   	$query = 'select `id_invoice`, `id_client`, `id_order`, `invoice_number`,
   		`state`, `net_value`, `gross_value`, `description`,
   		`date_issue`, `date_pay`,
   		UNIX_TIMESTAMP(date_issue) as ts_issue, UNIX_TIMESTAMP(date_pay) as ts_pay
   		from ' . TBL_SHOP_ORDER_INVOICE . '
      	where id_client = ' . db_int($id_client) . '  and 
      			id_invoice ' . $comparision_dir . db_int($id_invoice) . '
      	ORDER BY id_invoice ' . $order_dir . ' LIMIT '. db_int($length);
   	$result = db_query( $query );
   
   	if( db_rows($result) > 0 ) return  db_result_array_full($result);
   	else return array();
   }

   static function getOrderInvoiceList($id_order, $id_invoice, $length) {
   	list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
   //   		`date_issue`, `date_pay`, invoice_image,
   	$query = 'select `id_invoice`, `id_client`, `id_order`, `invoice_number`,
   		`state`, `net_value`, `gross_value`, `description`,
   		`date_issue`, `date_pay`,
   		UNIX_TIMESTAMP(date_issue) as ts_issue, UNIX_TIMESTAMP(date_pay) as ts_pay
   		from ' . TBL_SHOP_ORDER_INVOICE . '
      	where id_order = ' . db_int($id_order) . '  and
      			id_invoice ' . $comparision_dir . db_int($id_invoice) . '
      	ORDER BY id_invoice ' . $order_dir . ' LIMIT '. db_int($length);
   	$result = db_query( $query );
   	 
   	if( db_rows($result) > 0 ) return  db_result_array_full($result);
   	else return array();
   }
   
   static function getOrderAttributeList( $id_order = 0, $length = 1 ) {
   
   	$query = 'select oa.`id_order`, `type`, `val`
         	from ' . TBL_SHOP_ORDER_ATTRIBUTES . ' oa ,
         	' . TBL_SHOP_ORDER . ' o
         	where oa.id_order = o.id_order and oa.id_order = "' . db_int($id_order) . '"';
   	add_to_fp('$query ' . $query);
   	$result = db_query( $query );
   	return db_result_array_full($result);
   }
    
   static function doOrderAttributeAddOrUpdate( $param ) {
   
   	$query = 'select "' . db_int($param['id_order']) . '" as id,
   			"' . db_escape($param['type'].','.$param['val']) . '" as additional_data,
          b_func_order_attribute_set("' . db_int($param['id_order']) . '", "' . db_escape($param['type']) . '",
          "' . db_escape($param['val']) . '") as status';
   
   	add_to_fp($query);
   	$result = db_query( $query );
   	return db_fetch_array($result);
   }

}


?>