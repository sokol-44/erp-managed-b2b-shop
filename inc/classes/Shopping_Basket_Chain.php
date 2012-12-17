<?php
/**
 * Shopping_Basket_Chain.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Shopping_Basket_Chain {
   static $class = false;
   static $id_client = 0, $id_client_user = 0;
   static $id_basket_current = 0;
   static $id_basket_set = false;
   static $id_nr_shopping_basket = 0;
   static $nr2id = array();
   static $Basket_List = array();
   static $nr_basket = 0;
   private static $max_basket = 24;

   public function __construct() {
      self::$class = $this;

      $this->_reload_data();

   }

   private function _reload_data() {
      $F = Framework::g_global();
      $P = Person::g_global();

      $this->id_client = (int)$P->data['id_client'];
      $this->id_client_user = (int)$P->id;
      $this->Basket_List = array();

      //FIXME
      //for not logged users
      if( $P->logged_in ) {
         $Basket_List = Data::get_basket_chain_basket_list( $this->id_client );
         if( $F->not_null($Basket_List) ) {
            $this->set_basket_list($Basket_List);
            //$this->set_default_basket_by_date();
         } else {
            $this->init_basket();
         }
      } else {
         $this->init_basket();
      }
   }

   //add basket to mainbasket
   public function add_to_mainbasket( $id_shopping_basket = 0) {

      if( $this->_check_valid_basket( $id_shopping_basket ) && $this->_check_valid_basket( $this->id_basket_current ) ) {
         
         $right_basket_add  = $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS');
         $right_basket_main = $this->Basket_List[ $this->id_basket_current ]->check_rights('MODIFY_CONTENTS');
         
         if( $right_basket_add && $right_basket_main ) {
            $status = $this->Basket_List[$this->id_basket_current]->add_from_basket($this->Basket_List[$id_shopping_basket]);
         } else {
            return false;
         }
         if( $status ) {
            $this->remove_basket($id_shopping_basket);
            unset( $this->Basket_List[$id_shopping_basket] );
            return $status;
         } else {
            return false;
         }
         
      } else {
         return false;
      }
   }

   public function add_basket( $force = false ) {
      echo 'xbc:<b>' . sizeof($this->Basket_List) . '</b>,mb<b>' . self::$max_basket . '</b>,' . ($force?'t':'f') . 'x<br>';
      if( sizeof($this->Basket_List) < self::$max_basket ) {
         for( $id_sb = 1; $id_sb <= self::$max_basket ; $id_sb++ ){
            if( !$this->_check_valid_basket($id_sb) ) {
               $this->init_basket( $id_sb );
               return true;
            }
         }
         return false;
      } elseif ( $force ) {
         
         $this->init_basket(self::$max_basket+1, true);
         return true;
      } else {
         return false;
      }
   }

   public function remove_basket( $id_shopping_basket = 0) {
      if( $this->_check_valid_basket($id_shopping_basket) &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') ) {
         $this->Basket_List[$id_shopping_basket]->remove_basket();
         unset($this->Basket_List[$id_shopping_basket]);
         if( (int)$id_shopping_basket == (int)$this->id_basket_current ) {
            $this->id_basket_set = false;
            $this->set_default_basket_by_date();
         }
         return true;
      } else {
         return false;
      }
   }

   public function _check_valid_basket( $id_shopping_basket ) {
      if ($id_shopping_basket > 0 &&
            isset($this->Basket_List[$id_shopping_basket]) &&
            is_object($this->Basket_List[$id_shopping_basket]) ) {
         return true;
      } else {
         return false;
      }
   }

   public function get_can_add_basket() {
      if( $this->get_basket_list_count() < self::$max_basket ) {
         return true;
      }
      else { return false;
      }
   }

   public function get_basket_list_count() {
      //FIXME
      //params to get list of baskets belonging to specific ID
      return count($this->Basket_List);
   }

   public function init_basket( $number = 1, $create = true ) {
      $P = Person::g_global();
      $params = array(
            'id_client' => $this->id_client, 'id_shopping_basket' => 0, 'id_shopping_basket_version' => 0,
            'id_nr_shopping_basket' => $number, 'description' => '',
            'date_create' => date('Y-m-d H:i:s'), 'date_modified' => '', 'ts_create' => time(), 'ts_modified' => '',
            'using_id_client_user' => $this->id_client, 'using_session_id' => $P->session_id, 'using_date' => 0);
      $new_basket = new Shopping_Basket( $params, $create );
      $this->Basket_List[$new_basket->id_shopping_basket] = $new_basket;
//echo 'INIT'.$number.($create?'Ct':'Cf') . print_r($params);
      if( $create ) {
         $this->id_basket_set = false;
         $this->id_basket_current = $number;
      }
   }

   public function clean_basket( $id_shopping_basket = 0 ) {

      if( $this->_check_valid_basket($id_shopping_basket) ) {
         $this->Basket_List[ $id_shopping_basket ]->remove_all_product();
         return true;
      } else {
         return false;
      }
   }

   public function set_basket_list( $Basket_List ) {
      $ts_modified = 0;
      $id_nr_shopping_basket = 1;

      foreach( $Basket_List as $id_shopping_basket => $param) {
         if( !$this->id_basket_set && ($ts_modified == 0 || $param['ts_modified'] > $ts_modified) ) {
            $ts_modified = $param['ts_modified'];
            $this->id_basket_current = $id_shopping_basket;
            $param['id_nr_shopping_basket'] = $id_nr_shopping_basket;
         }
         $this->Basket_List[$id_shopping_basket] = new Shopping_Basket( $param );
         $this->nr2id[$id_nr_shopping_basket] = $id_shopping_basket;
         $id_nr_shopping_basket++;
         //LOAD Basket
         // var_dump($param);
      }
   }

   public function set_lock_basket( $id_shopping_basket = 0 ) {
       
      if( $this->_check_valid_basket($id_shopping_basket) &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('LOCK') ) {
         //$this->id_basket_set = true;
         $this->Basket_List[ $id_shopping_basket ]->state_lock_set();
         return true;
      } else {
         return false;
      }
   }

   public function set_unlock_basket( $id_shopping_basket = 0 ) {
       
      if( $this->_check_valid_basket($id_shopping_basket) &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('UNLOCK') ) {
         //$this->id_basket_set = true;
         $this->Basket_List[ $id_shopping_basket ]->state_lock_unset( $this->id_basket_current );
         return true;
      } else {
         return false;
      }
   }

   public function set_default_basket( $id_shopping_basket = 0 ) {
       
      if( $this->_check_valid_basket($id_shopping_basket) &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') ) {
         $this->id_basket_set = true;
         $this->_switch_default_basket( $id_shopping_basket );
         return true;
      } else {
         $this->id_basket_set = false;
         $this->set_default_basket_by_date();
         return false;
      }
   }
   
   private function _switch_default_basket( $id_shopping_basket ) {
      if( $this->_check_valid_basket($this->id_basket_current) ) {
         $this->Basket_List[ $this->id_basket_current ]->state_using_clear();
      }
      $this->id_basket_current = $id_shopping_basket;
      $this->Basket_List[ $this->id_basket_current ]->state_using_get();
   }
   

   public function set_default_basket_by_date( $recurrence = false  ) {
      $ts_modified = 0;
      $this->id_basket_set = false;
      $this->id_basket_current = 0;
      //echo ' a'.($recurrence?'Rt':'Rf');
      foreach( $this->Basket_List as $id_shopping_basket => $Basket) {
//          echo "<br> ^$id_shopping_basket:";
//          echo (!$this->id_basket_set?'t':'f');
//          echo ((empty($Basket->param['ts_modified']))?':t':':f');
//          echo '#'.$Basket->param['ts_modified'] .'>'.$ts_modified.'#.';
//          echo '<b>'.($Basket->check_rights('MODIFY_CONTENTS', false)?'t':'f') . '</b>';
//          echo ':^';
         if( ($Basket->param['ts_modified'] > $ts_modified || empty($Basket->param['ts_modified'])) &&
            $Basket->check_rights('MODIFY_CONTENTS', false)  ) {
            $this->_switch_default_basket( $id_shopping_basket );
            if( empty($Basket->param['ts_modified']) ) {
               $ts_modified = time();
               break;
            } else {
               $ts_modified = $Basket->param['ts_modified'];
            }
         }
      }
      //dsadasdas();
      //echo 'def:'.$this->id_basket_current;
      if( !$this->_check_valid_basket($this->id_basket_current) && !$recurrence ) {
         $this->add_basket( true );
         $this->set_default_basket_by_date( true );
      }
   }

   public function return_default_basket_modify() {

      if( $this->_check_valid_basket($this->id_basket_current) ) {
         if( $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') ) {
            return $this->Basket_List[ $id_shopping_basket ];
         } else {
            $this->id_basket_set = false;
            $this->set_default_basket_by_date();
            return $this->Basket_List[ $this->id_basket_current ];
         }
      } else {
         // var_dump($this);
         $this->id_basket_set = false;
         $this->set_default_basket_by_date();
         if( $this->_check_valid_basket($this->id_basket_current) ){
            return $this->Basket_List[ $this->id_basket_current ];
         } else {
            return false;
         }
      }
   }

   public function return_default_basket() {

      if( $this->_check_valid_basket($this->id_basket_current) ) {
         return $this->Basket_List[ $this->id_basket_current ];
      } else {
         // var_dump($this);
         $this->id_basket_set = false;
         $this->set_default_basket_by_date( false );
         return $this->Basket_List[ $this->id_basket_current ];
      }
   }

   public function return_basket_modify( $id_shopping_basket = 0 ) {

      if( $this->_check_valid_basket($id_shopping_basket) ) {
         if( $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') ) {
            return $this->Basket_List[ $id_shopping_basket ];
         } else {
            Info::g('add', Lang::_('SBC err'));
            return false;
         }
      } else {
         return false;
      }
   }


   public function return_basket( $id_shopping_basket = 0 ) {
      if( $this->_check_valid_basket($id_shopping_basket) ) {
         return $this->Basket_List[ $id_shopping_basket ];
      } else {
         return false;
      }
   }

   public function reset_basket_list() {
      reset($this->Basket_List);
   }

   public function return_basket_next( $skip_current = false ) {
      $Shopping_Basket = current($this->Basket_List);
      if( $this->id_basket_current == $Shopping_Basket->id_shopping_basket ) {
         next($this->Basket_List);
         $Shopping_Basket = current($this->Basket_List);
      }

      next($this->Basket_List);
      return $Shopping_Basket;
   }


   public function login_user() {
      $Shopping_Basket_tmp = reset($this->Basket_List);
      $Shopping_Basket = clone $Shopping_Basket_tmp;

      $this->_reload_data();

      //adding nonlogin basket - upt to 2 times baskets
      if( Shopping_Basket::_check_valid_basket($Shopping_Basket) &&
            sizeof($Shopping_Basket->contents) > 0 ) {
         //FIXME - new ID for basket!
         for( $id_sb = 1; $id_sb <= (self::$max_basket*2-1) ; $id_sb++ ){
            if( !$this->_check_valid_basket($id_sb) ) {
               $Shopping_Basket->add_person_save( $id_sb );
               die('FIXME');
                
               $this->Basket_List[$id_shopping_basket] = $Shopping_Basket;
               return true;
            }
         }
      }
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Shopping_Basket_Chain();
      }
      return self::$class;
   }

   function reset() {

   }

   function __sleep() {
      if ( $this->id_client > 0 ) {
         unset($this->Basket_List);
      }
      return( array_keys( get_object_vars( $this ) ) );
   }

   function __wakeup() {
      if ( $this->id_client > 0 ) {
         $this->_reload_data();
      }
      self::$class = $this;
   }

}

?>