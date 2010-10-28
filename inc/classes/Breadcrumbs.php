<?php
/**
 * Breadcrumbs.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();


class Breadcrumbs {
   static $class = false;
   static $crumb = array();

   function __construct() {
      $this->reset();
      self::$class = $this;
   }
    
   function __wakeup() {
      self::$class = $this;
   }
    
   static function g_global() {
      if( !is_object(self::$class) ) {
         self::$class = new Breadcrumbs;
      }
      return self::$class;
   }
   
   function reset() {
      $F = Framework::g_global();
      $this->crumb = array( array('name' => Lang::_('TOP_CATEGORY'),
   	'path' => $F->make_link(CFG_COM_CATALOG) ) );
   }
    
   function get_list() {
      return $this->crumb;
   }
    
    
   function add_crumb( array $in_crumb ) {
      $this->crumb[] = $in_crumb;
   }


}
?>