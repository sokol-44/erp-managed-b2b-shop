<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

define('RIGHTS_LEVEL_NAME', 'LEVEL_');

/**
 * Class Rights
 *
 * Manages user access rights, roles, and permissions for baskets, invoices, and other entities.
 *v
 *
 * @todo Implement strict typing (declare(strict_types=1)).
 * @todo Use dependency injection instead of static global accessors (e.g., Person::g_global()).
 * @todo Refactor singleton pattern to use modern container-managed dependency injection.
 */
class Rights {
   /**
    * @var Rights|null Holds the singleton instance of the Rights class.
    */
   static $class;

   /**
    * @var bool Flag to determine whether to display/log info messages.
    */
   private $show_info;

   /**
    * @var int|null The maximum rights level allowed for the client.
    */
   private $client_max_level;

   /**
    * Rights constructor.
    *
    * Initializes the singleton instance and retrieves the client's maximum rights level.
    *
    * @return void
    *
    * @todo Avoid calling static Data class directly; inject Data dependency.
    */
   function __construct() {
      self::$class = $this;
      $this->client_max_level=Data::get_client_rights_max_level();
   }

   /**
    * Gets the maximum rights level for the client.
    *
    * @return int|null The maximum client level.
    */
   function get_client_max_level() {
      return $this->client_max_level;
   }

   /**
    * Returns the global singleton instance of the Rights class.
    *
    * @return Rights The singleton instance.
    */
   static function g_global() {
      if( !is_object(self::$class) ) {
         self::$class = new Rights;
      }
      return self::$class;
   }

   /**
    * Runs a method on the global singleton instance.
    *
    * @param string $method The method name to execute.
    * @param array $args The arguments to pass to the method. Defaults to array().
    * @return mixed The result of the method call, or false if the method does not exist.
    * @throws Exception Thrown if the requested method does not exist (currently commented out in logic).
    *
    * @todo Uncomment the Exception throw to adhere to modern error handling standards instead of returning false.
    */
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

   /**
    * Re-initializes the singleton instance upon unserialization.
    *
    * @return void
    */
   function __wakeup() {
      self::$class = $this;
   }

   /**
    * Logs or displays informational messages if show_info is enabled.
    *
    * @param string $method The method name where the info is triggered.
    * @param string $str The information message.
    * @return void
    *
    * @todo Replace Info::g static call with a PSR-3 compliant logging interface.
    */
   private function info($method, $str) {
      if( $this->show_info ) Info::g($method, $str);
   }

   /**
    * Splits a state string into its type and level components.
    *
    * @param string $state The state string (e.g., "TYPE_LEVEL").
    * @return array{state_type: string, state_lvl: string, level_role: string, level_role_plus: string} The split state components.
    *
    * @todo Add validation to ensure the state string contains the expected delimiter.
    */
   static function split_state( $state ) {
      list($state_type, $state_lvl) = explode('_', $state);
      return array('state_type' => $state_type, 'state_lvl' => $state_lvl,
               'level_role' => RIGHTS_LEVEL_NAME . (int)$state_lvl,
               'level_role_plus' => RIGHTS_LEVEL_NAME . (int)($state_lvl+1)
            );
   }

   /**
    * Finds the lowest level role assigned to the current person.
    *
    * @return int|false The lowest level number, or false if none found.
    *
    * @todo Avoid hardcoded loop limit (100) and fetch roles dynamically.
    */
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

   /**
    * Checks if the user has rights to modify basket states.
    *
    * @param array $basket_params Array containing basket parameters, including 'state'.
    * @return array{0: string, 1: string}|null Array containing the level difference and debug info.
    *
    * @todo Refactor the hardcoded loop and fix the "WTF" logic to be more readable and maintainable.
    */
   public function basket_level_rights( $basket_params) {
      $P = Person::g_global();
      $res_debug = '';

      $state = $basket_params['state'];
      $st = self::split_state($state);

      if( $P->check_roles($st['level_role']) ) {
         return array(0, '#DF:0<br>');
      } else {  //FIXME - WTF ?
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

   /**
    * Checks rights for performing actions on invoices.
    *
    * @param array $invoice_params Parameters related to the invoice.
    * @param string $action The action being performed (e.g., 'SHOW').
    * @param bool $show_info Whether to display info messages. Defaults to true.
    * @return array{0: bool, 1: string} Array containing authorization status and debug info.
    *
    * @todo Remove unused $invoice_params parameter or utilize it in the checks.
    */
   public function invoice_rights( $invoice_params, $action, $show_info = true ) {
       $P = Person::g_global();
       $this->show_info = $show_info;
      $res_debug = '';

       switch( $action ) {
           // ---------------------------------------------------------------------------------------------------- //
           case 'SHOW':  //$action
               if ( $P->check_roles('LEVEL_99', 'ADMIN', 'OPERATOR') ) {
                           $res_debug .= 'u1b';
                           return array(true, $res_debug);
               }
               return array(false, $res_debug);
               break;
           default:
               return array(false, $res_debug);
               break;

       }
   }

   /**
    * Checks rights for favorite basket actions.
    *
    * @param array $basket_params Parameters related to the basket.
    * @param string $action The action being performed (e.g., 'USE', 'EDIT').
    * @param bool $show_info Whether to display info messages. Defaults to true.
    * @return array{0: bool, 1: string} Array containing authorization status and debug info.
    *
    * @todo Refactor nested switch-case blocks to reduce complexity and improve readability.
    */
   public function basket_favorite_rights( $basket_params, $action, $show_info = true ) {
       $P = Person::g_global();
       $this->show_info = $show_info;

       $rights_edit = $basket_params['rights_edit'];
       $rights_use = $basket_params['rights_use'];

       $res_debug = 'RCR:<i>' . $action . '</i>:';
       $res_debug .= 'cu:' . $basket_params['id_client_user'] .'c: ' . $basket_params['id_client'] . '<br>';
       $res_debug .= 're:' . $basket_params['rights_edit'] .'ru: ' . $basket_params['rights_use'] . '<br>';

       switch( $action ) {
           // ---------------------------------------------------------------------------------------------------- //
           case 'USE':  //$action
               switch( $rights_use ) {
                   case 'USER':
                  if( $P->id == $basket_params['id_client_user'] ) {
                      $res_debug .= 'u1a';
                      return array(true, $res_debug);
                  } elseif ( $P->data['id_client']  == $basket_params['id_client'] &&
                      $P->check_roles('LEVEL_99', 'ADMIN', 'OPERATOR') ) {
                      $res_debug .= 'u1b';
                      return array(true, $res_debug);
                  }
                 return array(false, $res_debug);
                 break;
                   case 'CLIENT':
                       if ( $P->data['id_client'] == $basket_params['id_client'] ) {
                      $res_debug .= 'c1a';
                           return array(true, $res_debug);
                       }
                       return array(false, $res_debug);
                       break;
                   default:
                       return array(false, $res_debug);
                       break;
               }
               break;
           case 'EDIT':  //$action               switch( $rights_use ) {
               switch( $rights_edit ) {
                   case 'USER':
                  if( $P->id == $basket_params['id_client_user'] ) {
                      $res_debug .= 'u1a';
                      return array(true, $res_debug);
                  } elseif ( $P->data['id_client']  == $basket_params['id_client'] &&
                      $P->check_roles('LEVEL_99', 'ADMIN', 'OPERATOR') ) {
                      $res_debug .= 'u1b';
                      return array(true, $res_debug);
                  }
                 return array(false, $res_debug);
                 break;
                   case 'CLIENT':
                       if ( $P->data['id_client'] == $basket_params['id_client'] ) {
                      $res_debug .= 'c1a';
                           return array(true, $res_debug);
                       }
                       return array(false, $res_debug);
                       break;
                   default:
                       return array(false, $res_debug);
                       break;
               }
               break;
       default:
           return array(false, $res_debug);
           break;

       }
    }

   /**
    * Checks rights for general basket actions based on state and user roles.
    *
    * @param array $basket_params Parameters related to the basket.
    * @param string $action The action being performed (e.g., 'MODIFY_CONTENTS', 'USE', 'FREE', 'LOCK', 'UNLOCK', 'ACC_0_1', 'BCK_1_0', 'ACC_1_2', 'BCK_2_1', 'MAKE_ORDER', 'VIEW', 'LIST').
    * @param bool $show_info Whether to display info messages. Defaults to true.
    * @return array{0: bool, 1: string}|bool Array containing authorization status and debug info, or boolean.
    *
    * @todo Refactor this massive method into smaller, dedicated strategy classes or methods.
    * @todo Avoid using extract() as it makes static analysis difficult and can lead to security issues.
    * @todo Replace die('ERROR') with a proper Exception.
    * @todo Remove dead code.
    */
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
