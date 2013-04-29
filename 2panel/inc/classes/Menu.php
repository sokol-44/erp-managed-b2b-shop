<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Menu {
   static $menu;

   function __construct() {
      //nothing
      $this->menu = array();
   }

   function create() {
      $P = Page::g_global();
      $F = Framework::g_global();


      //TODO do actual menu contraction
      if( $P->com != 'login' )
      $this->menu = array(
      array('href' => '#', 'name' => 'Użytkownicy',
         'submenu' => array(
               array('href' => $F->make_link('user_management', array('type' => 'ADMIN')), 'name' => 'Administratorzy'),
               array('href' => $F->make_link('user_management', array('type' => 'CLIENT') ), 'name' => 'Klienci'),
               array('href' => $F->make_link('user_management', array('type' => 'CLIENT_USER') ), 'name' => 'Uzytkownicy')
            )
         ),
      array('href' => '#', 'name' => 'Transakcje',
         'submenu' => array(
               array('href' => $F->make_link('transaction', array('type' => 'P_SPECIAL_FREE_TYPES')), 'name' => 'Lista Transakcji'),
               array('href' => $F->make_link('p_rooms_types', array('type' => 'P_ROOMS_TYPES') ), 'name' => 'Rooms Types')
            )
         ),
      array('href' => $F->make_link('logout'), 'name' => 'Logout')
      );

   }



   function make_html () {
      return $this->menu_html_r($this->menu);
   }

   private function menu_html_r(array $menu, $level = 0) {
      $F = Framework::g_global();

      $tab = str_repeat("\t", $level*2);
      $ret_str = '';
      if( $level == 0 )  $ret_str .= "$tab<ul id=\"nav\">\r\n";
      else  $ret_str .= "$tab<ul>\r\n";

      foreach ($menu as $item) {
         if( is_array($item['submenu']) ) {
            $ret_str .= $tab .  "\t" . '<li>';
            if( $F->not_null($item['href']) )
               $ret_str .= '<a href="' . $item['href'] . '">' . $item['name'] . '</a>';
            else
               $ret_str .= $item['name'];

            $ret_str .= "\r\n";
            $ret_str .= $this->menu_html_r($item['submenu'], $level+1);
            $ret_str .= "$tab\t</li>\r\n";
         } else {
            $ret_str .= $tab .  "\t" . '<li>';
            if( $F->not_null($item['href']) )
               $ret_str .= '<a href="' . $item['href'] . '">' . $item['name'] . '</a>';
            else
               $ret_str .= $item['name'];
            $ret_str .= "\r\n";
         }

      }
      $ret_str .= "$tab</ul>\r\n";
      return $ret_str;
   }


   function add_to_page() {
      $P = Page::g_global();

      $P->masterhead_html = $this->make_html();
      $P->add_jq_init(self::script_jq_init());
      $P->add_js_raw(self::script_source());
   }


   function script_source() {
      return '
      function mainmenu(){
      $(" #nav ul ").css({display: "none"}); // Opera Fix
      $(" #nav li").hover(function(){
      		$(this).find(\'ul:first\').css({visibility: "visible",display: "none"}).show(400);
      		},function(){
      		$(this).find(\'ul:first\').css({visibility: "hidden"});
      		});
      }
      ';

   }

   function script_jq_init() {
      return 'mainmenu();';
   }
}
?>