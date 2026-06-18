<?php
/**
 * Page.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 */

if( !defined('_I_INIT') ) die();

class Page {
   static $path_css, $path_js, $path_img;
   static $head_title, $head_keywords, $head_description, $head_js;
   static $masterhead_html, $second_head_html, $component_html, $bottom_html, $footer_html;
   static $com, $com_model_inc, $com_viewer_inc;
   static $target_com;
   static $F;
   static $class;
   static $js_jq_init, $js_jq_body;
   static $js_body, $jq_files;

   public function __construct() {
      self::$class = $this;
      $this->jq_init = false;
      $this->jq_body = array();
      $this->css_array = array();
      $this->jq_files = array();
      $this->js_body = false;
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Page;
      }
      return self::$class;
   }

   public function redirect($page) {
      $this->F->redirect($page);
   }

   public function start() {
      $this->F = Framework::g_global();

      $this->set_raw_paths();

      $this->get_component();
      $this->set_component_paths();
   }

   function get_form_get_string() {

      if( Framework::not_null($this->target_com) ) {
         $target_com = $this->target_com;
      } else {
         $target_com = $this->com;
      }
      return '?com=' . $target_com;
   }


   public function set_raw_paths() {
      $this->path_css = DIR_WWW_CSS;
      $this->path_js = DIR_WWW_JS;
      $this->path_img = DIR_WWW_IMG;
   }

   public function add_css($css) {
      $this->css_array[] = '<link rel="stylesheet" type="text/css" href="' . $this->path_css . $css . '">';
   }

   public function put_css() {
      echo implode("\r", $this->css_array) . "\n";
   }

   public function put_js() {
      if( $this->js_jq_init ) {
         echo '<script type="text/javascript"  src="http://www.google.com/jsapi"></script>' . NL .
		'<script type="text/javascript">google.load("jquery", "1.4");</script>' . NL .
		'<script type="text/javascript">' . NL .
		'$(document).ready(function(){' . NL .
         implode(NL, $this->js_jq_body) . NL .
		'})' . NL .
		'</script>' . NL;
      }
      if( $this->jq_files && count($this->jq_files) > 0 ) {
         foreach($this->jq_files as $js_file) {
            echo '<script type="text/javascript" src="' . $js_file . '"></script>' . NL;
         }
      }
      if( $this->js_body ) {
         echo '<script type="text/javascript">' . NL .
         implode(NL, $this->js_body) . NL .
          '</script>' . NL;
      }
   }

   public function add_jq_init($script) {
      $this->js_jq_init = true;
      $this->js_jq_body[] = $script;
   }

   public function add_js_raw($script) {
      $this->js_body[] = $script;
   }

   public function add_js_file($script, $add_path = true) {
      if($add_path) $this->jq_files[] = $this->path_js . $script;
      else $script;
   }

   //   function set_menus_paths() {
   //      //FIXME - one menu model per whole page, or multiple ?
   //      $this->com_menu_model_inc = Data::get_admin_com_menu_model_inc($this->com);
   //      $this->com_menu_viewer_inc = Data::get_admin_com_menu_view_inc($this->com);
   //   }

   function set_component_paths() {
      $this->com_model_inc = Data::get_admin_com_model_inc($this->com);
   }

   function get_component() {
      $P = Person::g_global();
      //TODO more generic obj
      if( $P->check_session_admin_login() && isset($this->F->GET['com'])) {
         $this->com = Data::check_admin_com_legal($this->F->GET['com']);
      } else {
         $this->com = Data::get_admin_com_login();
         if($this->com != $this->F->GET['com']) {
            $this->redirect( $this->F->make_link($this->com));
         }
      }
   }

   /*
    $Page->put_head_description()
    $Page->put_path_css();
    $Page->put_js();
    $Page->put_head_js();
    $Page->put_masterhead_html();
    $Page->put_component_html();
    $Page->put_bottom_html();
    $Page->put_footer_html();
    $Page->put_head_title();
    */

   function __call($name, array $arguments) {
      if( strstr($name, 'put_') ) {
         $var_name = str_replace('put_', '', $name);
         if( Framework::not_null($this->${var_name}) ) return $this->${var_name};
         else return 'PLACE ' . $var_name;
      } else {
         echo "$name not defined!";
      }
   }
}
?>