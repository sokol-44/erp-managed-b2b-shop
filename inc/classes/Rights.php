<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();


class Rights {
   static $class;

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
    
    
   function basket_rights( $basket_params, $action ) {
      $P = Person::g_global();

      $state = $basket_params['state'];
      list($state_type, $state_lvl) = explode('_', $state);

      //       'id_client' => 0, 'id_shopping_basket' => 0, 'id_shopping_basket_version' => 0,
      //       'id_nr_shopping_basket' => 0, 'description' => '', 'state' => '',
      //       'date_create' => null, 'date_modified' => null, 'ts_create' => 0, 'ts_modified' => 0,
      //       'using_id_client_user' => 0, 'using_session_id' => 0, 'using_date' => 0,  'ts_using' => 0);

      $level_role = 'LEVEL_' . $state_lvl;
      $level_role_plus = 'LEVEL_' . ($state_lvl+1);


      switch( $action ) {
// ---------------------------------------------------------------------------------------------------- //
         case 'MODIFY_CONTENTS':  //$action
            if( !$P->check_roles($level_role) ) {
               Info::g('add', Lang::_('Rights check_roles'));
               return false;
            }
            
            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } elseif( session_check_exist( $basket_params['using_session_id'] ) ) {
                     Info::g('add', Lang::_('Rights session_check_exist'));
                     return false;
                  } else {
                     return true;
                  }
                  break;
               case 'FREE':
                  return true;
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               default:
                  return false;
                  break;
            }
            
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'USE': //$action
            if( !$P->check_roles($level_role) ) {
               Info::g('add', Lang::_('Rights check_roles'));
               return false;
            }
            
            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } elseif( session_check_exist( $basket_params['using_session_id'] ) ) {
                     return false;
                  } else {
                     return true;
                  }
                  break;
               case 'FREE':
                  return true;
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               default:
                  return false;
                  break;
            }

            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'FREE': //$action
            if( !$P->check_roles($level_role) ) {
               Info::g('add', Lang::_('Rights check_roles'));
               return false;
            }
            
            if( $P->id == $basket_params['using_id_client_user'] ) {
               return true;
            } elseif( $P->check_roles($level_role_plus) ) {
               return true;
            } else {
               return false;
            }
 
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'LOCK': //$action
            if( !$P->check_roles($level_role) ) {
               Info::g('add', Lang::_('Rights check_roles'));
               return false;
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
               case 'FREE':
                  return true;
                  break;
               case 'LOCK':
                  //TODO Rights error
                  return false;
                  break;
               default:
                  return false;
                  break;
            }
            
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'UNLOCK': //$action
            switch ( $state_type ) {
               case 'USE':
                  //TODO Rights error
                  return false;
               case 'FREE':
                  //TODO Rights error
                  return false;
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } elseif( $P->check_roles($level_role_plus) ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               default:
                  return false;
                  break;
            }
            
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'ACC_0_1': //$action
            if( !$P->check_roles($level_role) ) {
               Info::g('add', Lang::_('Rights check_roles'));
               return false;
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               case 'FREE':
                  //TODO Rights error
                  return false;
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               default:
                  return false;
                  break;
            }
            
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'BCK_1_0': //$action
            if( !$P->check_roles($level_role) ) {
               Info::g('add', Lang::_('Rights check_roles'));
               return false;
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               case 'FREE':
                  if( $P->check_roles($level_role_plus) ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               default:
                  return false;
                  break;
            }
            
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'ACC_1_2': //$action
            if( !$P->check_roles($level_role) ) {
               Info::g('add', Lang::_('Rights check_roles'));
               return false;
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               case 'FREE':
                  //TODO Rights error
                  return false;
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               default:
                  return false;
                  break;
            }
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'BCK_2_1': //$action
            if( !$P->check_roles($level_role) ) {
               Info::g('add', Lang::_('Rights check_roles'));
               return false;
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               case 'FREE':
                  //TODO Rights error
                  return false;
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               default:
                  return false;
                  break;
            }
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'MAKE_ORDER': //$action
            if( !$P->check_roles('LEVEL_99') ) {
               Info::g('add', Lang::_('Rights check_roles 99'));
               return false;
            }

            switch ( $state_type ) {
               case 'USE':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               case 'FREE':
                  return true;
                  break;
               case 'LOCK':
                  if( $P->id == $basket_params['using_id_client_user'] ) {
                     return true;
                  } else {
                     return false;
                  }
                  break;
               default:
                  return false;
                  break;
            }
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'VIEW': //$action
            return true;
            break;
// ---------------------------------------------------------------------------------------------------- //
         case 'LIST': //$action
            return true;
            break;
         default:
            return false;
            break;
             
      }
   }
    
    
}
?>