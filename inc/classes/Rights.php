<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

define('RIGHTS_LEVEL_NAME', 'LEVEL_');

class Rights {
   static $class;
   private $show_info;

   function __construct() {
      self::$class = $this;
   }

   static function g_global() {
      if( !is_object(self::$class) ) {
         self::$class = new Rights;
      }
      return self::$class;
   }
    
   static function run_global( $method, $args = array()) {
      if( !is_object(self::$class) ) {
         self::$class = new Rights;
      }
      if(method_exists(self::$class, $method)) {
         return call_user_func_array(array(self::$class, $method), $args);
      } else {
         return false;
         //throw new Exception(sprintf('The required method "%s" does not exist for %s', $method, get_class($this)));
      }
   }

   function __wakeup() {
      self::$class = $this;
   }
   
   private function info($method, $str) {
      if( $this->show_info ) Info::g($method, $str);
   }
   
   static function split_state( $state ) {
      list($state_type, $state_lvl) = explode('_', $state);
      return array('state_type' => $state_type, 'state_lvl' => $state_lvl,
               'level_role' => RIGHTS_LEVEL_NAME . (int)$state_lvl,
               'level_role_plus' => RIGHTS_LEVEL_NAME . (int)($state_lvl+1)
            );
   }
    
   
   static function get_lowest_level( ) {
      $P = Person::g_global();
      for($nr_level=0; $nr_level<100; $nr_level++) {
         $level_role = RIGHTS_LEVEL_NAME . (int)$nr_level;
         if( $P->check_roles( $level_role ) ) {
            return $nr_level;
         }
      }
      return false;
   }
    
   public function basket_level_rights( $basket_params) {
      $P = Person::g_global();
      $res_debug = '';

      $state = $basket_params['state'];
      $st = self::split_state($state);
      
      if( $P->check_roles($st['level_role']) ) {
         return array(0, '#DF:0<br>');
      } else {
         for( $dif_lvl=1; $dif_lvl<100; $dif_lvl++ ) {
            if( $P->check_roles(RIGHTS_LEVEL_NAME . (int)($st['state_lvl']+$dif_lvl)) ) {
               return array('-'.$dif_lvl, '#DF:-'.$dif_lvl.'<br>');
               break;
            } elseif ( $P->check_roles(RIGHTS_LEVEL_NAME . (int)($st['state_lvl']-$dif_lvl)) ) {
               return array('+'.$dif_lvl, '#DF:+'.$dif_lvl.'<br>');
               break;
            }
         }
      }
      
   }
   
   public function basket_rights( $basket_params, $action, $show_info = true ) {
      $P = Person::g_global();
      $this->show_info = $show_info;

      $state = $basket_params['state'];
      $st = self::split_state($state);
      extract($st);
      
      //       return array('state_type' => $state_type, 'state_lvl' => $state_lvl,
      //             'level_role' => 'LEVEL_' . (int)$state_lvl,
      //             'level_role_plus' => 'LEVEL_' . (int)($state_lvl+1)
      //       );
      
      
      //       'id_client' => 0, 'id_shopping_basket' => 0, 'id_shopping_basket_version' => 0,
      //       'id_nr_shopping_basket' => 0, 'description' => '', 'state' => '',
      //       'date_create' => null, 'date_modified' => null, 'ts_create' => 0, 'ts_modified' => 0,
      //       'using_id_client_user' => 0, 'using_session_id' => 0, 'using_date' => 0,  'ts_using' => 0);

      //$res_debug = 'RCR:<i>' . $action . '</i>:S:' . $state . '=' . state_type . '_' . $state_lvl . '";';
      $res_debug = 'RCR:<i>' . $action . '</i>:';
      $res_debug .= 'u:<b>' . $basket_params['using_id_client_user'] .'</b>: '.$basket_params['using_session_id'] . '<br>';

      switch( $action ) {
// ---------------------------------------------------------------------------------------------------- //
         case 'MODIFY_CONTENTS':  //$action
            $res_debug .= ' :MC:';
            if( !$P->check_roles($level_role) ) {
               $res_debug .= '#'.$level_role.'#';
               $this->info('add', Lang::_('Rights check_roles'));
               return array(false, $res_debug);
            }
            
            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     $res_debug .= 'u1';
                     return array(true, $res_debug);
                  } elseif( session_check_exist( $basket_params['using_session_id'] ) ) {
                     $res_debug .= 'u2';
                     $this->info('add', Lang::_('Rights session_check_exist'));
                     return array(false, $res_debug);
                  } else {
                     $res_debug .= 'u3';
                     return array(true, $res_debug);
                  }
                  break;
               case 'FREE':
                  $res_debug .= 'f1';
                  return array(true, $res_debug);;
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     $res_debug .= 'l1';
                     return array(true, $res_debug);
                  } else {
                     $res_debug .= 'l2';
                     return array(false, $res_debug);
                  }
                  break;
               case 'ORDER':
                  $res_debug .= 'o1';
                  return array(false, $res_debug);;
                  break;
               case '':
                  die('ERROR');
                  break;
               default:
                  $res_debug .= 'd1';
                  return array(false, $res_debug);
                  break;
            }
            
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'USE': //$action
            if( !$P->check_roles($level_role) ) {
               $this->info('add', Lang::_('Rights check_roles'));
               return array(false, $res_debug);
            }
            
            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } elseif( session_check_exist( $basket_params['using_session_id'] ) ) {
                     return array(false, $res_debug);
                  } else {
                     return array(true, $res_debug);
                  }
                  break;
               case 'FREE':
                  return array(true, $res_debug);
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'ORDER':
                  return array(false, $res_debug);;
                  break;
               default:
                  return array(false, $res_debug);
                  break;
            }

            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'FREE': //$action
            if( !$P->check_roles($level_role) ) {
               $this->info('add', Lang::_('Rights check_roles'));
               return array(false, $res_debug);
            }
            
            if( $P->id == $basket_params['using_id_client_user'] ) {
               return array(true, $res_debug);
            } elseif( $P->check_roles($level_role_plus) ) {
               return array(true, $res_debug);
            } else {
               return array(false, $res_debug);
            }
 
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'LOCK': //$action
            if( !$P->check_roles($level_role) ) {
               $this->info('add', Lang::_('Rights check_roles'));
               return array(false, $res_debug);
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
               case 'FREE':
                  return array(true, $res_debug);
                  break;
               case 'LOCK':
                  //TODO Rights error
                  return array(false, $res_debug);
                  break;
               case 'ORDER':
                  return array(false, $res_debug);;
                  break;
               default:
                  return array(false, $res_debug);
                  break;
            }
            
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'UNLOCK': //$action
            switch ( $state_type ) {
               case 'USE':
                  //TODO Rights error
                  return array(false, $res_debug);
               case 'FREE':
                  //TODO array(Rights error
                  return false;
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } elseif( $P->check_roles($level_role_plus) ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'ORDER':
                  return array(false, $res_debug);;
                  break;
               default:
                  return array(false, $res_debug);
                  break;
            }
            
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'ACC_0_1': //$action
            if( !$P->check_roles($level_role) ) {
               $this->info('add', Lang::_('Rights check_roles'));
               return array(false, $res_debug);
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'FREE':
                  //TODO Rights error
                  return array(false, $res_debug);
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'ORDER':
                  return array(false, $res_debug);;
                  break;
               default:
                  return array(false, $res_debug);
                  break;
            }
            
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'BCK_1_0': //$action
            if( !$P->check_roles($level_role) ) {
               $this->info('add', Lang::_('Rights check_roles'));
               return array(false, $res_debug);
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'FREE':
                  if( $P->check_roles($level_role_plus) ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'ORDER':
                  return array(false, $res_debug);;
                  break;
               default:
                  return array(false, $res_debug);
                  break;
            }
            
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'ACC_1_2': //$action
            if( !$P->check_roles($level_role) ) {
               $this->info('add', Lang::_('Rights check_roles'));
               return array(false, $res_debug);
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'FREE':
                  //TODO Rights error
                  return array(false, $res_debug);
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'ORDER':
                  return array(false, $res_debug);;
                  break;
               default:
                  return array(false, $res_debug);
                  break;
            }
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'BCK_2_1': //$action
            if( !$P->check_roles($level_role) ) {
               $this->info('add', Lang::_('Rights check_roles'));
               return array(false, $res_debug);
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'FREE':
                  //TODO Rights error
                  return array(false, $res_debug);
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'ORDER':
                  return array(false, $res_debug);;
                  break;
               default:
                  return array(false, $res_debug);
                  break;
            }
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'MAKE_ORDER': //$action
            if( !$P->check_roles('LEVEL_99') ) {
               $this->info('add', Lang::_('Rights check_roles 99'));
               return array(false, $res_debug);
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'FREE':
                  return array(true, $res_debug);
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return array(true, $res_debug);
                  } else {
                     return array(false, $res_debug);
                  }
                  break;
               case 'ORDER':
                  return array(false, $res_debug);;
                  break;
               default:
                  return array(false, $res_debug);
                  break;
            }
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'VIEW': //$action
            return array(true, $res_debug);
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'LIST': //$action
            return array(true, $res_debug);
            break;
         default:
            return array(false, $res_debug);
            break;
             
      }
   }
    
    
}
?>