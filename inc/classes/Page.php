<?php
/**
 * Page.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Page {
   //   static $path_css, $path_js, $path_img;
   //   static $head_title, $head_keywords, $head_description, $head_js;
   //   static $masterhead_html, $second_head_html, $component_html, $bottom_html, $footer_html;
   //   static $com, $com_model_inc, $com_viewer_inc;
   //   static $target_com;v
   //   static $js_jq_init, $js_jq_body;
   //   static $js_body, $jq_files;
   static $F;
   static $class;

   public function __construct() {
      self::$class = $this;
      $this->jq_init = false;
      $this->jq_body = array();
      $this->css_array = array();
      $this->jq_files = array();
      $this->js_body = false;
      $this->list_places = array();
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
      $this->list_places = $this->get_components();
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


   function get_components() {
      $P = Person::g_global();
      $F = Framework::g_global();
      //TODO more generic obj

      $list_places = array(
         'masterhead_html' => array('script' => '', 'type' => 'MOD'),
         'second_head_html' => array('script' => '', 'type' => 'MOD'),
         'left_column_html' => array('script' => 'categories_list', 'type' => 'MOD'),
         'component_html' => array('script' => '', 'type' => 'COM'),
         'right_column_html' => array('script' => '', 'type' => 'MOD'),
         'bottom_html' => array('script' => '', 'type' => 'MOD'),
         'footer_html' => array('script' => '', 'type' => 'MOD'),
      );

      $list_places['component_html']['script'] = $this->com;

      //$list_places['component_html']['script'] = 'sasa';

      if( $F->check_get('com') ) {
         $list_places['component_html']['script'] = $F->com;
      } else {
         $list_places['component_html']['script'] = DEFAULT_COM;
      }

      return $list_places;
   }

   function render_places() {
      foreach($this->list_places as $place_name => $place) {
         if( Framework::not_null($place['script']) ) {
            echo 'saa' . $place['script'];
            ob_start();
            $this->include_element($place['script'], $place['type']);
            $this->${place_name} = ob_get_clean();
         }
      }
   }

   function include_element($name, $type = 'COM') {
      //TODO check if exist
      $F = Framework::g_global();
      $P = Person::g_global();
      $Page = Page::g_global();
      $Lang = Lang::g_global();
      switch( $type ) {
         case 'COM': include(DIR_INC_COMPONENTS . DS . $name . '.php');
         break;
         case 'MOD': include(DIR_INC_MODULES . DS . $name . '.php');
         break;
         default: break;
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