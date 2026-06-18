<?php
/**
 * Data_Order.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 *
 * @todo Eliminate legacy conditional file guard definitions (`_I_INIT`) and shift architecture toward PSR-4 namespaces.
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Data_Order
 *
 * Implements data orchestration and persistence workflows for customer orders, status changes,
 * invoice items, financial accounting adjustments, and metadata attributes tracking.
 *
 * @todo Declare visibility modifiers explicitly (e.g., `public`, `protected`) across all methods to comply with clean PSR standards.
 * @todo Migrate low-level direct raw SQL functions (`db_query`, `db_fetch_array`) into isolated repository layers.
 */
class Data_Order extends Data_Shop {
    /**
     * @var array Static directory mapping parameter expectations and primitive configurations for order attributes.
     */
    static $Data_order_params = array(
            'PAYMENT_METHOD' => array(
                    'CASH_TRANSFER', 'CASH_ON_DELIVERY', 'CASH_IN_PERSON'
                    ),
            'DELIVERY_PARTIAL' => 'BOOL',
            'DELIVERY_DATE'    => 'DATE'
            );

   /**
    * Data_Order constructor.
    *
    * Instantiates the core order persistence layer, resolving dependencies against parent store handlers.
    */
   function __construct() {
        //echo get_class();
      parent::__construct();

   }


   /**
    * Pulls an array compilation of customer orders, parsing associated tracking metrics and sub-record objects.
    *
    * @param int $id_order_start Numeric primary record key targeting offset conditions.
    * @param int $length Scalar bounding limit constraint tracking max items to draw.
    * @param string $where Supplementary SQL criteria clauses used to filter results further.
    * @return array Matrix breakdown storing individual order data frames alongside product metadata arrays.
    *
    * @todo Add proper input argument signature and scalar return typing (`int`, `string`, `array`).
    * @todo Transition internal global logging tracers like `add_to_fp()` over to PSR-3 Logger implementations.
    * @todo Fix spelling.
    */
   static function getOrderList( $id_order_start = 0, $length = 1, $where = '' ) {
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

      $query = 'select o.id_order, o.id_client, o.date_create, o.date_modified, o.id_order_status,
      o.description, o.description_basket, o.id_shopping_basket, o.id_address, os.name as status_name,
      o.id_account_manager, o.guid, cuam.account_manager_name
      from ' . TBL_SHOP_ORDER . ' o left join ' . TBL_SHOP_ORDER_STATUS . ' os
      on (o.id_order_status = os.id_order_status)
      left outer join ' . TBL_GLOBAL_CLIENT_USER_ACCOUNT_MANAGER . ' cuam
      on (o.id_account_manager = cuam.id_account_manager)
      where o.id_order ' . $comparision_dir . db_int($id_order_start) . $where . '
      ORDER BY o.id_order ' . $order_dir . ' LIMIT '. db_int($length);
      add_to_fp($query);

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

   /**
    * Returns specialized attribute configurations mapping custom field elements tracking individual orders.
    *
    * Normalizes array output indexing keys using incremental index notation labels.
    *
    * @param int $id_order Target order system identifier primary key.
    * @return array Formatted tracking dictionary array housing parsed order metadata.
    */
   static function getOrderAttributeData( $id_order ) {
       $ret_tmp = self::getOrderAttributeList((int)$id_order);

       $ret_array = array();
       $idx=0;
       foreach($ret_tmp as $order ) {
           $ret_array['value_'.$idx++] = $order;
       }
       return $ret_array;
   }

   /**
    * Retrieves all physical line item product records purchased under a specific order envelope.
    *
    * @param int $id_order Primary parent order verification lookup index.
    * @return array Matrix collecting product arrays indexed by individual item identity tokens.
    */
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

   /**
    * Counts the remaining volume length of orders trailing past specified boundary settings.
    *
    * @param int $id_order_start Numeric primary record offset index pointer.
    * @param int $length Scalar configuration maximum constraint (passed to math calculations but bypassed globally).
    * @param string $where Supplementary filtering SQL query parameter string.
    * @return int Computed remaining row inventory calculations.
    */
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

   /**
    * Triggers internal store procedure scripts to apply hidden status modifications safely.
    *
    * @param int $id_order Target order identity code.
    * @param int $id_client Reference customer corporate key pointer.
    * @param string $hidden_status Specialized internal tracking state visibility keyword flag.
    * @return array Evaluation metadata parameters collection tracking execution confirmation status.
    */
   static function setOrderHiddenStatus( $id_order, $id_client, $hidden_status) {
      $query = 'select "' . db_int($id_order) . '" as id_one, "' . db_int($id_client) . '" as id_two,
       "" as additional_data,
       b_func_order_hidden_status_change("' . db_int($id_order) . '", "' . db_int($id_client) . '", "' . db_escape($hidden_status) . '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Public entry point used to shift status values assigned onto active customer invoices.
    *
    * Normalizes systemic confirmation labels depending on underlying database response codes.
    *
    * @param int $id_order Core target identifier primary key tracking order records.
    * @param int $id_order_status Unique status node catalog positioning configuration code.
    * @param string $status Custom annotation comment details logged along transition tracking timelines.
    * @return array Evaluation metrics map capturing outcome statements.
    *
    * @todo Remove dead code.
    */
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

   /**
    * Returns an indexed mapping cataloging all configured order status codes altered via prefix formatting labels.
    *
    * @return array Dictionary array matching identifier indexes to transformed validation string text labels.
    */
   static function get_order_history_list() {

       $query = 'select os.id_order_status, concat("OSH_", os.name) as name from ' . TBL_SHOP_ORDER_STATUS . ' os';
       $res = db_result_array( db_query( $query ) );
       $ret_array = array();
       foreach( $res as $osh ) {
           $ret_array[$osh['id_order_status']] = $osh['name'];
       }

       return $ret_array;
   }

   /**
    * Draws client invoice order sets from storage tables, processing configurations across core segmentation helper functions.
    *
    * @param int $id_client Target customer filtering pointer flag index. Defaults to 0.
    * @param string $where Supplementary inline conditions statement text (shadowed internally by local overrides).
    * @return array Multi-dimensional collection containing matched order items records.
    */
   static function get_order_list( $id_client = 0, $where = '' ) {
      $SP = SplitPage::g_global();

      if( $id_client > 0 ) {
         $where = ' where o.id_client = ' . db_int($id_client);
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

   /**
    * Extracts comprehensive profile rows capturing distinct information metrics for an individual order entry.
    *
    * @param int $id_order Target order validation unique identity index.
    * @return array Profile data row elements tracking timestamps and operational structural codes.
    */
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

   /**
    * Returns product detail breakdowns and metadata links assigned to an operational order configuration.
    *
    * @param int $id_order Root order data container key identifier.
    * @return array Collection array storing inventory item specifics mapped by product identifier.
    */
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

   /**
    * Gathers historical logs tracking transitional status shifts executed over singular order envelopes.
    *
    * @param int $id_order Target reference lookup constraint index.
    * @return array Matrix set sequencing modifications chronologically.
    */
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

   /**
    * Inserts record definitions inside baseline tables to convert active shopper baskets into processing orders.
    *
    * @param int $id_client Customer identification primary key mapping owner records.
    * @param array $basket_params Parent snapshot variable maps describing the originating shopper container element.
    * @param array $params_in Input data parameter dictionary defining shipping locations and corporate account manager tags.
    * @return int Returns the auto-increment primary identifier tracker indexing the new order entry.
    */
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

      // print_debug($query);

      db_query( $query );
      $id_order = db_insert_id();
      //$id_order = 1;
      return $id_order;
   }


   /**
    * Core transaction mechanism logging updates inside tracking history structures before shifting order parent states.
    *
    * Checks order profile existence constraints prior to updating state records.
    *
    * @param int $id_order Reference unique tracking data key.
    * @param int $id_order_status Target state catalog position code alignment flag.
    * @param string $description Annotation comment logged along with the historical transition snapshots.
    * @return int|bool absolute affected operations counter results tracking logging executions, or false if order row missing.
    */
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

   /**
    * Appends multi-item attribute meta settings safely inside secondary configuration lookup tables.
    *
    * Compiles inline value collections prior to executing single unified insert queries.
    *
    * @param int $id_order Primary parent order container identifier code.
    * @param array $attributes Dictionary array collection mapping custom metadata fields onto specific value constraints.
    * @return mixed Programmatic return output statement evaluations tracing core operations metrics, or false.
    */
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

   /**
    * Populates distinct purchased product item variables mapping financial values inside order line-item storage maps.
    *
    * Enclosed within transaction safety blocks to guarantee absolute item tracking synchronization.
    *
    * @param int $id_order Root identity pointer code aligning records onto the parent order container.
    * @param array $product_list Multi-dimensional dataset gathering product details, cost indices, and total weights.
    * @return int Affected database rows count detailing completion totals.
    */
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
         db_query( $insert_query );
      }
      $res = db_affected_rows();
      db_transaction_end();

      return $res;
   }

   /**
    * Isolates custom metadata variables matching explicit attribute filters assigned onto order items.
    *
    * Supports fetching singular field properties or fallback multi-item dataset configurations.
    *
    * @param int $id_order Reference unique tracking data key targeting specific records.
    * @param string|bool $attribute_type Targeting context code parameter identifying the metadata field configuration. Defaults to false.
    * @return mixed Extracted string parameter result, full data multi-row dictionary collection, or boolean false.
    */
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
       } elseif( $nrow > 0 && $F->is_null($attribute_type)) {
           return db_result_array_full($result);
       } else {
           return false;
       }

   }


   /**
    * Restores absolute accounting balances and parameter dimensions from targeted database invoice record tokens.
    *
    * @param int $id_invoice Unique ledger invoice identification primary key code.
    * @return array Profile snapshot tracking row items mapping billing fields, or empty array fallback.
    */
   static function get_invoice_data($id_invoice) {
       $query = 'select `id_invoice`, `id_client`, `id_order`, `invoice_number`,
           `state`, `net_value`, `gross_value`, `description`,
           `date_issue`, `date_pay`, invoice_image,
           UNIX_TIMESTAMP(date_issue) as ts_issue, UNIX_TIMESTAMP(date_pay) as ts_pay
           from ' . TBL_SHOP_ORDER_INVOICE . '
          where id_invoice = ' . db_int($id_invoice) . '';
       $result = db_query( $query );

       if( db_rows($result) > 0 ) return db_fetch_array($result);
       else return array();

   }

   /**
    * Returns the complete financial overview summary list tracking invoices assigned to customer accounts.
    *
    * Ordered descending based on billing release timelines.
    *
    * @param array $params Context tracker parameters mapping the targeted `id_client` configuration property.
    * @return array Multi-row collection matrix capturing billing line records.
    */
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

   /**
    * Gathers financial accounting sheets linked against structural parent order nodes.
    *
    * @param array $params Identifier wrapper mapping key targets (improperly links `id_order` onto query value parameters internally).
    * @return array Matrix set collecting document summaries.
    *
    * @todo Fix logical query mismatch constraint where `where id_client = ` checks parameters matching `id_order` keys.
    */
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


   /**
    * Gathers records matching authorized sales contacts assigned onto corporate accounts pools.
    *
    * @param int $id_client Corporate account identity lookup pointer index.
    * @param int $length Scalar bounding framework limit specifying results volume properties.
    * @return array Collection array storing manager information maps.
    *
    * @todo Fix spelling.
    */
   static function getClientAccountManagerList($id_client, $length) {
       list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

       $query = 'select `id_account_manager`, `id_client`, `id_client_user`, `account_manager_name`,
               `fullname`, `phone1`, `phone2`, `email`, `date_created`, `date_modified`, `state`
               from ' . TBL_GLOBAL_CLIENT_USER_ACCOUNT_MANAGER . '
               where id_client ' . $comparision_dir . db_int($id_client) . '
          ORDER BY id_client ' . $order_dir . ' LIMIT '. db_int($length);
       $result = db_query( $query );

       if( db_rows($result) > 0 ) return db_result_array_full($result);
       else return array();
   }

   /**
    * Synchronizes administrative staff properties via store procedures routines tracking sales representative links.
    *
    * @param array $param Profile context array containing name indicators, phone fields, and mailbox paths.
    * @return array Evaluation metrics map tracking procedure confirmation statements.
    */
   static function doClientAccountManagerAddOrUpdate($param) {
       $F = Framework::g_global();


        $query = 'select "' . db_int($param['id_account_manager']) . '" as id_start,
               "' . db_escape($param['account_manager_name'].','.$param['id_client']
               .','.$param['id_client_user']) . '" as additional_data,
          b_func_account_manager_set("' . db_int($param['id_account_manager']) . '",
          "' . db_int($param['id_client']) . '", "' . db_int($param['id_client_user']) . '",
          "' . db_escape($param['account_manager_name']) . '", "' . db_escape($param['fullname']) . '",
          "' . db_escape($param['phone1']) . '", "' . db_escape($param['phone2']) . '",
          "' . db_escape($param['email']) . '", "' . db_escape($param['state']) . '") as status';

       add_to_fp($query);
       $result = db_query( $query );
       return db_fetch_array($result);
   }

   /**
    * Iterates financial ledger indexes using segment calculations constraints to fetch comprehensive billing logs.
    *
    * @param int $id_invoice Unique ledger record primary tracking index.
    * @param int $length Operational total bounding max inventory records to return.
    * @return array Sequenced compilation of invoice data details rows.
    *
    * @todo Fix spelling.
    */
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

       if( db_rows($result) > 0 ) return db_result_array_full($result);
       else return array();
   }


   /**
    * Invokes core catalog procedures to shift state fields assigned to financial balance records.
    *
    * @param array $param Core dictionary parameters parsing `id_invoice`, parent `id_order`, and the target `state` string.
    * @return array Output operational mapping tracking evaluation responses.
    */
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

   /**
    * Processes structural updates or registers brand new ledger entries utilizing catalog procedural calls.
    *
    * Automatically handles base64 binary or file system PDF blob updates if valid images exist.
    *
    * @param array $param Rich parameters dictionary containing net pricing values, date values, and invoice numbering schemas.
    * @return array Tracking results capturing confirmation metrics.
    */
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

   /**
    * Isolates invoice logs limited by company indicators and primary validation offsets.
    *
    * @param int $id_client Corporate profile registration tracker index.
    * @param int $id_invoice Reference lookup pagination identifier context.
    * @param int $length Operational constraint limiting maximal items to return.
    * @return array Multi-row collection tracking financial rows.
    *
    * @todo Fix spelling.
    */
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

       if( db_rows($result) > 0 ) return db_result_array_full($result);
       else return array();
   }

   /**
    * Retrieves a paginated list of invoices associated with a specific order.
    *
    * Queries the billing ledger to locate invoices matching the parent order identifier,
    * filtering the records based on the provided pagination cursor offsets.
    *
    * @param int|string $id_order Core primary validation target index tracking the parent order.
    * @param int|string $id_invoice Pagination cursor index specifying the starting invoice identifier.
    * @param int $length Operational maximum limit of records to return within the dataset view window.
    * @return array Matrix compilation containing the verified invoice records hashes list.
    *
    * @todo Declare explicit native visibility modifiers (public/protected/private) for this method.
    * @todo Add concrete type declarations for parameters and define a structural union/native array return hint.
    * @todo Refactor the raw inline SQL statement to use a decoupled Data Mapper or parameterized PDO adapter.
    * @todo Fix spelling.
    */
   static function getOrderInvoiceList($id_order, $id_invoice, $length) {
       list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
   //           `date_issue`, `date_pay`, invoice_image,
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

   /**
    * Pulls flat rows capturing order metadata values attributes linked to a chosen order tracking ID.
    *
    * @param int|string $id_order Reference target primary index criteria matching the order scope. Defaults to 0.
    * @param int $length Volumetric pull capacity ceiling definition setting constraints parameter. Defaults to 1.
    * @return array Collected matrix rows list capturing parsed order specifications fields.
    *
    * @todo Remove the redundant $length argument since it is completely ignored within the compiled query structure.
    * @todo Enforce explicit data types validation layers and modern scalar return values type hinting.
    * @todo Eliminate the manual multi-table implicit join syntax (`from A, B`) in favor of clear standard ANSI SQL JOIN phrases.
    */
   static function getOrderAttributeList( $id_order = 0, $length = 1 ) {

       $query = 'select oa.`id_order`, `type`, `val`
             from ' . TBL_SHOP_ORDER_ATTRIBUTES . ' oa ,
             ' . TBL_SHOP_ORDER . ' o
             where oa.id_order = o.id_order and oa.id_order = "' . db_int($id_order) . '"';
       add_to_fp('$query ' . $query);
       $result = db_query( $query );
       return db_result_array_full($result);
   }

   /**
    * Saves or revises metadata parameters logs invoking structural backend stored functions routines.
    *
    * @param array $param Target data criteria config array detailing updates instructions and text attributes.
    * @return array Database procedural feedback log hash detailing transactional results status models.
    *
    * @todo Avoid using unstructured parameters arrays; migrate to a dedicated value object or strongly typed parameter definitions.
    * @todo Implement modern try-catch error safety architectures around the query execution engine calls block.
    */
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
