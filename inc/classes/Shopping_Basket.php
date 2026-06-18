<?php
/**
 * Shopping_Basket.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Shopping_Basket
 *
 * Manages operations, historical versions, contents tracking, and access validation for shopping baskets.
 *
 * @designPattern Entity / State
 *
 * @todo Add declare(strict_types=1); at the top of the file to enforce type safety.
 * @todo Refactor the hardcoded static class tracking pattern ($class registry) into a robust Repository or Session container.
 * @todo Define proper access visibility modifiers (public, protected, private) for all methods and class attributes.
 * @todo Migrate properties and parameter structures to use explicit modern PHP native type declarations.
 */
class Shopping_Basket {
    const TIME2FREE = 1800; // 30min
   static $class = false;
   public $contents = array();
   public $product_info_array = array();
   public $version = array();
   public $history = array();
   public $id_shopping_basket = 0;
   public $id_shopping_basket_version = 0;
   public $id_shopping_basket_history = 0;
   public $res_debug;
   public $params = array(
         'id_client' => 0, 'id_shopping_basket' => 0, 'id_shopping_basket_version' => 0, 'id_shopping_basket_history' => 0,
         'id_nr_shopping_basket' => 0, 'description' => '', 'state' => '',
         'date_create' => null, 'date_modified' => null, 'ts_create' => 0, 'ts_modified' => 0,
         'using_id_client_user' => 0, 'using_session_id' => 0, 'using_name' => '', 'using_date' => 0,  'ts_using' => 0);

   /**
    * Shopping_Basket constructor.
    *
    * Instantiates the shopping basket entity, matching existing records or preparing a fresh db layout.
    *
    * @param array|bool $params Optional initialization array data profile fields. Defaults to false.
    * @param bool $create Determines whether a new row instance should be registered directly in storage. Defaults to false.
    * @param array|bool $contents Collection of items parsed to pre-seed properties during instantiation. Defaults to false.
    * @return void
    */
   function __construct($params = false, $create = false, $contents = false) {
      $this->reset();
      self::$class = $this;
      if( $params ) {
         $this->params = $params;
         if( $create ) {
            $new_params = $this->db_create_basket();
            $this->params['id_shopping_basket'] = $new_params['id_shopping_basket'];
            $this->params['id_shopping_basket_version'] = $new_params['id_shopping_basket_version'];
            if( $contents ) {
                $this->check_contents( $contents );
            }
            $this->id_shopping_basket=$this->params['id_shopping_basket'];
            $this->db_save_contents();
         } else {
            $this->id_shopping_basket=$this->params['id_shopping_basket'];
            $this->db_restore_contents();
            $this->calculate_total();
         }
      }
      $this->id_shopping_basket = $this->params['id_shopping_basket'];
   }

   /**
    * Normalizes, validates, and adds an item mapping collection structure into the current execution payload.
    *
    * @param array $contents Collection of multi-dimensional items data payloads to validate.
    * @return void
    *
    * @todo Replace references to Data_Products static calls with injected dynamic catalog models.
    */
   public function check_contents( $contents ) {

       foreach( $contents as $product ) {
           $key = Data_Products::get_key_from_product_params( $product );
           if( (int)$product['id_product'] > 0 && (int)$product['quantity'] > 0 ) {
               $this->contents[$key] = array(
                   'quantity' => (int)$product['quantity'],
                   'id_product' => (int)$product['id_product'],
                   'id_product_subtype' => (int)$product['id_product_subtype'],
                   'id_shopping_basket_version' => (int)$this->params['id_shopping_basket_version']
               );
           }
       }
   }

   /**
    * Static checkout handler to extract an active, frozen order record matching user structural credentials.
    *
    * @param int $id_shopping_basket Unique lookup key identifier for the targeted records database table.
    * @return Shopping_Basket|bool Populated basket wrapper layout object instance, or false on restriction failures.
    *
    * @todo Refactor global state access (Person class) to use localized service locator references.
    */
   static function get_order_data($id_shopping_basket) {
         $P = Person::g_global();

         if( $P->logged_in ) {
             $params = array('id_shopping_basket' => (int)$id_shopping_basket, 'id_client' => $P->data['id_client']);
           $params = Data::get_basket_data($params);
           if( $P->data['id_client'] == $params['id_client'] && $params['state'] == 'ORDER' ) {
               return new Shopping_Basket($params);
           }
         }
         return false;
   }

   /**
    * Standard memory caching utility designed to return the tracked entity class state wrapper context.
    *
    * @return Shopping_Basket Core tracker singleton context array instance pointer.
    */
   static function g_global() {
      if(self::$class == false) {
         self::$class = new Shopping_Basket();
      }
      return self::$class;
   }

   /**
    * Asserts object implementation type profiles match constraints.
    *
    * @param mixed $Shopping_Basket Target implementation variable context checklist.
    * @return bool True if evaluated target variable matching parameters comply with instance bounds.
    */
   static function _check_valid_basket( $Shopping_Basket ) {
      if ( is_a($Shopping_Basket, 'Shopping_Basket') ) {
         return true;
      } else {
         return false;
      }
   }

   /**
    * Flags lock attributes and context indicators onto data store attributes matching current user configurations.
    *
    * @return mixed Operation update return schema payloads from the persistent storage tier.
    *
    * @todo Standardize the split expression patterns from global layer components to decouple dynamic dependencies.
    */
   function state_using_get() {
      $P = Person::g_global();
      $st = Rights::split_state( $this->params['state'] );



      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         $this->params['using_id_client_user'] = (int)$P->id;
         $this->params['using_session_id'] = $P->session_id;
         if( $st['state_type'] == 'LOCK' ) $this->params['state'] = 'LOCK_'  . $st['state_lvl'];
          else $this->params['state'] = 'USE_'  . $st['state_lvl'];
      } elseif( $this->params['state'] == 'ORDER' ) {
         $this->params['using_id_client_user'] = NULL;
         $this->params['using_session_id'] = NULL;
      } else {
         $this->params['using_id_client_user'] = NULL;
         $this->params['using_session_id'] = NULL;
      }

      return Data::put_basket_update_use_data( $this->params );
   }

   /**
    * Normalizes basket state properties into structural authorization boundaries matching specific indexes.
    *
    * @param int|string $new_lvl Target hierarchy rank indicator to update properties against.
    * @return mixed DB execution result mapping dataset array values.
    */
   function state_change_level($new_lvl) {
      $this->params['using_id_client_user'] = NULL;
      $this->params['using_session_id'] = NULL;
      $this->params['state'] = 'FREE_'  . $new_lvl;

      return Data::put_basket_update_use_data( $this->params );
   }

   /**
    * Resets internal tracking locks and handles clearing current user token ownership variables.
    *
    * @return mixed Data operation tracking records feedback metadata array.
    */
   function state_using_clear() {
      $st = Rights::split_state( $this->params['state'] );

      $this->params['using_id_client_user'] = NULL;
      $this->params['using_session_id'] = NULL;
      $this->params['state'] = 'FREE_'  . $st['state_lvl'];

      return Data::put_basket_update_use_data( $this->params );
   }

   /**
    * Configures strict operational freeze parameters onto structural schema parameters.
    *
    * @return mixed Structural confirmation dataset arrays derived from database operations.
    */
   function state_lock_set() {
      $st = Rights::split_state( $this->params['state'] );

      $this->params['state'] = 'LOCK_'  . $st['state_lvl'];

      return Data::put_basket_update_lock_data( $this->params );
   }

   /**
    * Clears frozen constraint parameters on the database model record profiles.
    *
    * @param int $id_basket_default Identity index flag tracking global defaults. Defaults to 0.
    * @return mixed Data operation tracking records response.
    */
   function state_lock_unset( $id_basket_default = 0 ) {
      $P = Person::g_global();
      $st = Rights::split_state( $this->params['state'] );

      if( $id_basket_default == $this->id_shopping_basket ) { //$this->params['id_client'] = (int)$P->id;
         $this->params['state'] = 'USE_'  . $st['state_lvl'];
      } else {
         $this->params['state'] = 'FREE_' . $st['state_lvl'];
      }

      return Data::put_basket_update_lock_data( $this->params );
   }

   /**
    * Archives the record metrics tracking layout data fields permanently onto specialized log configurations.
    *
    * @return mixed Persistence operation summary context map array, or false on right mismatch.
    */
   function state_archive_order() {
      if( $this->check_rights('MAKE_ORDER') ) {

         $this->params['using_id_client_user'] = NULL;
         $this->params['using_session_id'] = NULL;
         $this->params['state'] = 'ORDER';

         return Data::put_basket_update_use_data( $this->params );
      } else {
         return false;
      }
   }

   /**
    * Merges items listed from an external object entity cleanly into the current class instance definitions.
    *
    * @param Shopping_Basket $Shopping_Basket Secondary instantiation model container tracking source elements.
    * @return bool True if operations successfully process items compilation configurations.
    */
   function add_from_basket( $Shopping_Basket ) {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for ADD FROM BASKET to basket: ' . $this->id_shopping_basket));
         return false;
      }

      if( $this->_check_valid_basket($Shopping_Basket) ) {
         $in_contents = $Shopping_Basket->contents;
         foreach( $in_contents as $key_product => $product_array ) {
            $this->add_to_basket($product_array, $product_array['quantity']);
         }
         $in_description = $Shopping_Basket->params['description'];
         if( Framework::not_null($in_description) ) {
            $this->params['description'] .= "\n" . $in_description;
         }
         self::db_save_contents();
         return true;
      } else {
         return false;
      }
   }

   /**
    * Synchronizes core client account mapping values down to matching entity property parameters.
    *
    * @param int $id_nr_shopping_basket Tracking profile record tag number. Defaults to 0.
    * @param bool $save Enforces instantaneous file layout updates down to local storage configurations. Defaults to true.
    * @return void
    */
   function update_person( $id_nr_shopping_basket = 0 , $save = true) {
      $P = Person::g_global();
      $this->params['id_client'] = (int)$P->data['id_client'];
      if( $id_nr_shopping_basket > 0 ) {
         $this->params['id_nr_shopping_basket'] = (int)$id_nr_shopping_basket;
      }
      if( $save ) self::db_save_contents();
   }

   /**
    * Escalates or rolls back authorization access level rankings for the records tier tracking logs.
    *
    * @param string $direction Ranking transition target vector ('UP' or 'DOWN').
    * @param array $param_in Optional dynamic logging variables block tracker dataset parameters. Defaults to array().
    * @return mixed Generated sequence layout primary index from transaction log rows, or false on check failure.
    */
   function basket_level_change($direction, $param_in = array() ) {
       $P = Person::g_global();
      if( ( $direction == 'UP'   && $this->check_move('UP') ) ||
          ( $direction == 'DOWN' && $this->check_move('DOWN') ) ) {

         $param_in['now'] = $this->basket_level_nr();
         if( $direction == 'UP' ) {
            $param_in['new'] = $param_in['now'] + 1;
         } else {
            $param_in['new'] = $param_in['now'] - 1;
         }

         $this->params['using_id_client_user'] = (int)$P->id;

         $new_id = Data::put_basket_history($this->params, $param_in);
         $this->state_change_level($param_in['new']);
         return $new_id;
      } else {
         return false;
      }
   }

   /**
    * Extracts current authorization state taxonomy names.
    *
    * @return string Normalized state label text.
    */
   function basket_level_text( ) {
      $st = Rights::split_state($this->params['state']);
      return $st['state_type'];
   }

   /**
    * Fetches the current rank sequence indexing code properties.
    *
    * @return int Computed configuration rank context levels.
    */
   function basket_level_nr( ) {
      $st = Rights::split_state($this->params['state']);
      return (int)$st['state_lvl'];
   }

   /**
    * Compares user permission schemas to compute rank displacement vectors.
    *
    * @return string|int Quantified rank clearance variation indicators.
    */
   function basket_level_rights( ) {
      $Rights = Rights::g_global();
      list($level_diff, $res_debug) = $Rights->basket_level_rights( $this->params );

      $this->res_debug .= $res_debug;

      return $level_diff;
   }

   /**
    * Asserts whether a discrete external session holds edit boundaries over this collection structure.
    *
    * @param array $params Optional operational context variables. Defaults to array().
    * @return bool True if conflict matches exist against external lock flags.
    */
   function currently_other_locked( $params = array() ) {
      $P = Person::g_global();

      if( $this->params['using_id_client_user'] != $P->id && $this->basket_level_text() == 'LOCK' ) {
         return true;
      }

      return false;
   }

   /**
    * Asserts if the current session scope uniquely retains operational restrictions over records properties.
    *
    * @param array $params Optional parameter array. Defaults to array().
    * @return bool True if matches meet local instance configuration bounds.
    */
   function currently_user_locked( $params = array() ) {
      $P = Person::g_global();

      if( $this->params['using_id_client_user'] == $P->id && $this->basket_level_text() == 'LOCK' ) {
         return true;
      }

      return false;
   }

   /**
    * Evaluates ownership constraints tracking current active interaction paths.
    *
    * @param array $params Optional check properties. Defaults to array().
    * @return bool True if identity tokens correlate directly with instance metadata fields.
    */
   function currently_user_using( $params = array() ) {
       $P = Person::g_global();

       if( $this->params['using_id_client_user'] == $P->id ) {
           return true;
       }

       return false;
   }

   /**
    * Tests timeout constraints to assert whether external processes are currently engaging data layers.
    *
    * @param array $params Parameter schema flags dictionary array. Defaults to array().
    * @return bool True if live lease tokens exist indicating active external edits.
    */
   function currently_other_using( $params = array() ) {
      $P = Person::g_global();
      //global param
      $time_diff_ok = time()-self::TIME2FREE;

      if(  $this->params['using_id_client_user'] == $P->id ) {
//           echo 'Y';
          return false;
      } elseif( (int)$this->params['using_id_client_user'] == 0 || Framework::is_null($this->params['using_session_id']) )  {
//           echo 'EM';
          return false;
      } elseif ( $this->params['ts_using'] < $time_diff_ok ) {
//           echo 'TO';
          return false;
      }
// echo 'OU';
      return true;
   }

   /**
    * Verifies structural parameters layout metrics map cleanly onto hierarchical transitions constraints.
    *
    * @param string $direction Operational tracking shift bounds vector string ('UP' or 'DOWN'). Defaults to 'UP'.
    * @return bool True if system properties meet target boundaries safely.
    */
   function check_move( $direction = 'UP') {
      $Rights = Rights::g_global();

      if( !$this->check_rights('MODIFY_CONTENTS') ) return false;

      if( $direction == 'UP' ) {
         return ($Rights->get_client_max_level() > $this->basket_level_nr());
      } elseif ( $direction == 'DOWN' ) {
         return ($this->basket_level_nr()>0);
      } else {
         return false;
      }
   }

   /**
    * Evaluates standard security contexts against targeted operations requests parameters.
    *
    * @param string $action Domain logic token representation configuration parameter check.
    * @param bool $show_info Controls notification propagation parameters during runtime block check. Defaults to true.
    * @return bool True if operational validation conditions resolve positively.
    */
   function check_rights( $action, $show_info = true) {
      $P = Person::g_global();
      $Rights = Rights::g_global();

      list($res, $res_debug) = $Rights->basket_rights($this->params, $action, $show_info);

      if( !$res && $show_info ) {
         Info::g('add', Lang::_('You dont have rights for (' . $action . ') basket: ' . $this->id_shopping_basket));
      }

      $this->res_debug .= $res_debug;

      return $res;
   }

   /**
    * Populates data models from baseline persistent store row configurations schemas.
    *
    * @return void
    *
    * @todo Standardize file structures and replace FIXME comments with optimized dataset map loaders.
    */
   function db_restore_contents() {
      //FIXME
      //DB stuff and merge with DB
      $F = Framework::g_global();
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         $this->params = Data::get_basket_data($this->params);

         if( (int)$this->params['using_id_client_user'] > 0 ) {
             if( (int)$this->params['using_id_client_user'] == $P->id ) {
                 $this->params['using_name'] = Person::get_client_user_short_text($P->data);
             } else {
                 $client_user_data = Person::get_client_user_data( (int)$this->params['using_id_client_user'], 'CLIENT');
                 $this->params['using_name'] = Person::get_client_user_short_text($client_user_data);
             }
         }

         //VERSIONS
         $version_list = Data::get_basket_version_list($this->params);
         $last = 0;
         foreach( $version_list as $key => $basket_version ) {
            if( $basket_version['ts_created'] > $last ) {
               $last = $basket_version['ts_created'];
               $this->id_shopping_basket_version = $basket_version['id_shopping_basket_version'];
               $this->params['id_shopping_basket_version'] = $this->id_shopping_basket_version;
            }
            if( $basket_version['id_client_user'] == $P->id ) {
               $basket_version['client_user_name'] = Person::get_client_user_short_text($P->data);
            } else {
               $client_user_data = Person::get_client_user_data( (int)$basket_version['id_client_user'], 'CLIENT');
               $basket_version['client_user_name'] = Person::get_client_user_short_text($client_user_data);
            }
            $this->version[$basket_version['id_shopping_basket_version']] = $basket_version;
         }
         //CONTENTS OF "NEWEST" VER.
         if( Framework::not_null($this->id_shopping_basket_version) )
            $this->contents = Data::get_basket_version_product_list( $this->params );

         //HISTORY
         $history_list = Data::get_basket_history_list($this->params);

         $last = 0;
         foreach( $history_list as $basket_history ) {
             if( $basket_history['ts_date'] > $last ) {
                 $last = $basket_history['ts_date'];
                 $this->id_shopping_basket_history = $basket_history['id_shopping_basket_history'];
                 $this->params['id_shopping_basket_history'] = $this->id_shopping_basket_history;
             }
             $this->history[$basket_history['id_shopping_basket_history']] = $basket_history;
         }
      }
   }

   /**
    * Registers initialization dataset sequences down to fundamental backend infrastructure layers.
    *
    * @return array Identification token pair values tracking the generated records structures.
    */
   function db_create_basket() {
      //DB CREATE
      $F = Framework::g_global();
      $P = Person::g_global();
      $Rights = Rights::g_global();

      $res = array('id_shopping_basket' => 0, 'id_shopping_basket_version' => 0);
      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         $this->params['state'] = 'USE_' . $Rights->get_lowest_level();
         $this->state_using_get();
         $res = Data::create_new_basket($this->params);
      }
      return $res;
   }

   /**
    * Saves temporary item allocation ownership indicators into backend tables.
    *
    * @return void
    */
   function db_save_using() {
      //DB SAVE
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         Data::put_basket_version_product_list($this->contents, $this->params);
      }
   }

   /**
    * Commits total collection array configurations down to persistence parameters.
    *
    * @return void
    */
   function db_save_contents() {
      //DB SAVE
      $F = Framework::g_global();
      $P = Person::g_global();
      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         Data::put_basket_version_product_list($this->contents, $this->params);
         //$history_last = Data::get_basket_history_last($this->params);
         Data::put_basket_info($this->params, (int)$this->id_shopping_basket_history);
      }
   }

   /**
    * Drops targeted catalog associations directly off specific tables rows parameters.
    *
    * @param string|int $product_key Composite index string representation pattern tracking database row fields.
    * @return void
    */
   function db_remove_product( $product_key ) {
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client'] && Framework::not_null($product_key) ) {
         Data::remove_basket_product( $product_key, $this->params);
      }
   }

   /**
    * Purges obsolete or detached entity records parameters out from storage scopes.
    *
    * @return void
    */
   function db_clean_contents() {

   }

   /**
    * Flushes memory allocations or wipes backend configurations to start clean runtime states.
    *
    * @param bool $reset_database Controls if relational model row collections undergo explicit drops. Defaults to false.
    * @return void
    */
   function reset($reset_database = false) {
      global $customer_id;

      $this->contents = array();
      $this->total = array('product_total' => 0, 'product_types' => 0, 'sum_gross' => 0, 'sum_netto' => 0);

      if ( $reset_database == true ) {
         //remove from DB
      }
   }

   /**
    * Analyzes revision timelines and flags updates or spins new tracking histories if contexts shift.
    *
    * @return bool True if data discrepancies trigger unique baseline allocation copies.
    */
   function add_check_version() {
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         $current_client_user_id = (int)$this->version[(int)$this->id_shopping_basket_version]['id_client_user'];
         if( (int)$P->data['id_client_user'] != (int)$current_client_user_id &&
               Framework::not_null($this->contents) ) {

            $basket_new_version = Data::add_basket_new_version($this->params, (int)$P->data['id_client_user']);

            $this->id_shopping_basket_version = (int)$basket_new_version['id_shopping_basket_version'];
            $this->version[$this->id_shopping_basket_version] = $basket_new_version;
            $this->params['id_shopping_basket_version'] = $this->id_shopping_basket_version;

            foreach( $this->contents as $key_product => $product_params ) {
               $this->contents[$key_product]['id_shopping_basket_version'] = $this->id_shopping_basket_version;
            }
            $FD = File_Debug::g_global();
            $FD->s('ADD');
            $FD->s(array('$this->version' => $this->version, '$this->id_shopping_basket_version' => $this->id_shopping_basket_version));

            return true;
         }
      }
      $FD = File_Debug::g_global();
      $FD->s('NOT');
      $FD->s(array('$this->version' => $this->version, '$this->id_shopping_basket_version' => $this->id_shopping_basket_version));

      return false;
   }

   /**
    * Increments product record quantities tracking localized key values.
    *
    * @param array $product_params Baseline parameter fields configuration variables array for mapping elements.
    * @param int $quantity Quantified volumetric adjustment values to map. Defaults to 1.
    * @param bool $save Directs engine to commit modifications to persistent layers. Defaults to true.
    * @return int|bool Aggregated final unit metrics total counted on successful parsing, or false on rejection.
    */
   function add_to_basket($product_params, $quantity = 1, $save = true) {
      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for ADD to basket: ' . $this->id_shopping_basket));
         return false;
      }

      $this->add_check_version();

      $id_product = (int)$product_params['id_product'];
      $id_product_subtype = (int)$product_params['id_product_subtype'];
      $key_product = Data::get_key_from_product_params($product_params);

      if( isset($this->contents[$key_product]) ) {
         $this->contents[$key_product]['quantity'] = $this->contents[$key_product]['quantity'] + (int)$quantity;
      } else {
         $this->contents[$key_product] = array(
               'quantity' => (int)$quantity,
               'id_product' => $id_product,
               'id_product_subtype' => $id_product_subtype,
               'id_shopping_basket_version' => (int)$this->id_shopping_basket_version
         );
      }

      if( $save ) self::db_save_contents();

      return $this->contents[$key_product]['quantity'];
   }

   /**
    * Updates massive collections data definitions parameters efficiently in singular iterations.
    *
    * @param array $product_quantity_array Matrix dataset map arrays indexing quantities against distinct target codes.
    * @param string|bool $description Inline comments summary content tracking. Defaults to false.
    * @return void|bool Returns false if authorization fails.
    */
   function update_basket_quantity_list($product_quantity_array, $description = false) {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for UPDATE QUANTITY LIST to basket: ' . $this->id_shopping_basket));
         return false;
      }

      foreach($product_quantity_array as $key_product => $quantity) {
         $this->update_product_quantity($key_product, (int)$quantity, false);
      }
      if( $description ) $this->params['description'] = $description;
      //TODO
      //only update in DB
      self::db_save_contents();
   }

   /**
    * Overwrites local count records tracking unique product mapping arrays.
    *
    * @param string $key_product Targeted target record layout representation identity phrase.
    * @param int $quantity Overwrite quantity metrics bounds parameter. Defaults to 0.
    * @param bool $save Directs database transaction execution tracking updates. Defaults to true.
    * @return int Updated volume count details mapping internal properties.
    */
   function update_product_quantity($key_product, $quantity = 0, $save = true) {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for UPDATE QUANTITY to basket: ' . $this->id_shopping_basket));
         return false;
      }

      if( isset($this->contents[$key_product]) ) {
         $this->contents[$key_product]['quantity'] = (int)$quantity;
         if( $save ) self::db_save_contents();
         return $this->contents[$key_product]['quantity'];
      } else {
         return 0;
      }
   }

   /**
    * Returns active numeric volumetric records for specific query matches.
    *
    * @param array $product_params Lookup structural property metadata components.
    * @return int Sum counts tracked, otherwise zero.
    */
   function get_product_quantity($product_params) {
      $key_product = Data::get_key_from_product_params($product_params);
      if( isset($this->contents[$key_product]) ) {
         return $this->contents[$key_product]['quantity'];
      } else {
         return 0;
      }
   }

   /**
    * Reads client data identification indicators.
    *
    * @return int Client configuration tracking key references.
    */
   function get_id_client() {
      return $this->params['id_client'];
   }

   /**
    * Extracts transaction tracking tracking sequence label details.
    *
    * @return int Indexed code assignments.
    */
   function get_id_nr_shopping_basket() {
      return $this->params['id_nr_shopping_basket'];
   }

   /**
    * Confirms presence profiles of explicit target identifiers inside local memory definitions.
    *
    * @param string $key_product Catalog item tracking identification parameters text.
    * @return bool True if records match existing instances fields.
    */
   function check_product_in($key_product) {
      if (isset($this->contents[$key_product])) {
         return true;
      } else {
         return false;
      }
   }

   /**
    * Drops explicit records lines out from active compilation mapping profiles.
    *
    * @param string $product_key Catalog reference representation identity text.
    * @return mixed Transaction validation parameters response feedback from db layer.
    */
   function remove_from_basket( $product_key ) {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for REMOVE to basket: ' . $this->id_shopping_basket));
         return false;
      }

      if( isset($this->contents[$product_key]) ) {
         unset( $this->contents[$product_key] );
      }

      if( $this->add_check_version() ) {
         $this->db_save_contents();
      }

      return $this->db_remove_product( $product_key );
   }

   /**
    * Wipes items lists down to clean definitions without impacting storage metrics directly.
    *
    * @return void
    */
   function remove_all_product() {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for REMOVE ALL to basket: ' . $this->id_shopping_basket));
         return false;
      }

      $this->reset();
      //TODO
      //only delete
      $this->db_save_contents();
   }

   /**
    * Lists indexed revision historical tag elements.
    *
    * @return array Identification keys collection array.
    */
   function get_version_list() {
      return array_keys($this->version);
   }

   /**
    * Reads matching keys collection mapped sequentially inside structural item sets.
    *
    * @param array|bool $contents Secondary target data map to analyze, or false to default to internal arrays. Defaults to false.
    * @return array Structured identification tags list.
    */
   function get_product_key_list( $contents = false ) {
      if( $contents === FALSE ) return array_keys($this->contents);
      else return array_keys($contents);
   }

   /**
    * Reads product reference identification codes explicitly.
    *
    * @return array Primary data key sequence tracking fields.
    */
   function get_product_id_list() {
      return array_keys($this->contents);
   }

   /**
    * Collects quantities variables recorded under custom item schema descriptors.
    *
    * @param array $product_params Metric data attributes.
    * @return int Unit counts extracted.
    */
   function get_quantity( $product_params ) {
      $key = Data::get_key_from_product_params($product_params);
      if( isset($this->contents[$key]) ) {
         return $this->contents[$key]['quantity'];
      } else {
         return 0;
      }
   }

   /**
    * Returns historical structural configurations collections explicitly.
    *
    * @return array Total version dataset profile parameters array.
    */
   function get_all_version() {
      return $this->version;
   }

   /**
    * Extracts total log tracking row configurations collections safely.
    *
    * @return array Collection array listing history parameters.
    */
   function get_all_history() {
      return $this->history;
   }

   /**
    * Pulls out the immediate preceding timeline event tracking data array.
    *
    * @return array Last chronological event context profile parameters map.
    */
   function get_last_history() {
      return $this->history[$this->id_shopping_basket_history];
   }

   /**
    * Compiles detailed commercial structural metadata array maps linking localized inventory values.
    *
    * @return array Comprehensive catalog item data elements block.
    */
   function get_all_product() {
      $F = Framework::g_global();

      $this->product_info_array = array();

      if ( is_array($this->contents) && $F->not_null($this->contents) ) {
         $product_id_array = $this->get_product_key_list();
         $product_info_array = Data::get_product_info_list( $product_id_array );

         foreach ($product_info_array  as $key => $product_info) {
             $product_info_array[$key]['db_quantity'] =  $product_info_array[$key]['quantity'];
            $product_info_array[$key]['quantity'] = $this->contents[$key]['quantity'];
         }

         $this->product_info_array = $product_info_array;
      }

      return $this->product_info_array;
   }

   /**
    * Re-evaluates item matrix parameters from historical state logs matching static checkpoints.
    *
    * @param int $id_shopping_basket_version Targeted revision marker profile key indicator. Defaults to 0.
    * @return array Isolated collection profile parameters map.
    */
   function get_all_product_version( $id_shopping_basket_version = 0 ) {
      $F = Framework::g_global();

      $product_info_array = array();

      $basket_params = $this->params;
      $basket_params['id_shopping_basket_version'] = (int)$id_shopping_basket_version;

      $contents = Data::get_basket_version_product_list( $basket_params );

      if ( is_array($contents) && $F->not_null($contents) ) {
         $product_id_array = $this->get_product_key_list( $contents );
         $product_info_array = Data::get_product_info_list( $product_id_array );

         foreach ($product_info_array  as $key => $product_info) {
            $product_info_array[$key]['quantity'] = $contents[$key]['quantity'];
         }
      }

      return $product_info_array;
   }

   /**
    * Deletes localized collection rows directly off permanent storage structures.
    *
    * @return mixed Operation feedback status matrix from core data utilities, or false on right failure.
    */
   function remove_basket() {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::sadd(Lang::_('You don\'t have rights for REMOVE BASKET to basket: ' . $this->id_shopping_basket));
         return false;
      }

      return Data::remove_basket( $this->params );
   }

   /**
    * Sums numerical aggregates and parses monetary totals including dynamic tax layouts calculation rules.
    *
    * @return array Structural financial parameters tracking counts and values context schemas.
    */
   function calculate_total() {
      $this->total = array('product_total' => 0, 'product_types' => 0,
            'sum_gross' => 0, 'sum_gross_split' => array(), 'sum_netto' => 0);

      if ( Framework::not_null($this->contents) && $this->total['product_total'] == 0 ) {
         $this->get_all_product();

         foreach($this->product_info_array as $key_product => $product ) {
            $this->total['product_total'] += $product['quantity'];
            $this->total['product_types'] ++;
            $this->total['sum_gross_split'][$product['vat']] += Price::add_vat($product['price'], $product['vat'], $product['quantity']);
            $this->total['sum_netto'] += ($product['price'] * $product['quantity']);
         }
         foreach( $this->total['sum_gross_split'] as $vat => $vat_value ) {
            $this->total['sum_gross_split'][$vat] = Price::rount_tax($vat_value);
            $this->total['sum_gross'] += Price::rount_tax($vat_value);
         }
      }
      return $this->total;
   }

   /**
    * Resolves total values tracking the current computational fields layout array maps.
    *
    * @return array Final financial calculation context profiles.
    */
   function get_total() {
      $this->calculate_total();

      return $this->total;
   }

   /**
    * Native serialization clean hook to streamline dynamic engine references before sleep.
    *
    * @return array Property names tracking variable layouts to serialize natively.
    */
   function __sleep() {
      unset($this->product_array);
      return( array_keys( get_object_vars( $this ) ) );
   }

   /**
    * Magic lifecycle deserialization reconstruction reference hook.
    *
    * @return void
    */
   function __wakeup() {
      self::$class = $this;
   }

}
