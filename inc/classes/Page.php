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
   //   static $target_com;
   //   static $js_jq_init, $js_jq_body;
   //   static $js_body, $jq_files;
   static $F;
   static $class;
   private $PLACE, $TEMPLATE_places, $jq_init, $jq_body, $css_array, $jq_files, $js_body, $list_places;

   public function __construct() {
      self::$class = $this;
      $this->PLACE = array();
      $this->TEMPLATE_places = false;
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
      $this->get_template_places();
      $this->get_component();
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
   
   public function put_head_title() {
   	  return $this->head_title;
   }

   public function put_head_keywords() {
   	  return $this->head_keywords;
   }   
   public function put_head_description() {
   	  return $this->head_description;
   }

   public function put_css() {
      echo implode("\r", $this->css_array) . "\n";
   }

   public function put_js() {
      global $config;

      if( $this->js_jq_init ) {
         echo '<script type="text/javascript" src="http://www.google.com/jsapi"></script>' . NL .
         '<script type="text/javascript">' . NL .
         'if ( window[\'google\'] && window[\'google\'][\'loader\']) {' . NL .
         '  google.load("jquery", "1"); ' . NL .
         '} else {' . NL .
         '  document.write(\'<script type="text/javascript" src="' . $this->path_js . $config['TEMPLATES']['jquery'] . '"><\/script>\');' . NL .
         '}' . NL .
         '</script>' . NL .
         '<script type="text/javascript">' . NL .
         '$(document).ready(function(){' . NL .
         implode(NL, $this->js_jq_body) . NL .
         '})' . NL .
         '</script>' . NL;
      }      
      if( $this->jq_files && count($this->jq_files) > 0 ) {
      	$jq_files = array_unique($this->jq_files);
         foreach($jq_files as $js_file) {
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
      else $this->jq_files[] = $script;
   }

   //   function set_menus_paths() {
   //      //FIXME - one menu model per whole page, or multiple ?
   //      $this->com_menu_model_inc = Data::get_admin_com_menu_model_inc($this->com);
   //      $this->com_menu_viewer_inc = Data::get_admin_com_menu_view_inc($this->com);
   //   }

   function get_template_places() {

      $this->TEMPLATE_places = array(
            'component_html' => array('script' => '', 'type' => 'COM'),
            'info_html' => array('script' => 'info', 'type' => 'MOD'),
            'masterhead_html' => array('script' => 'empty', 'type' => 'MOD'),
            'mastermenu_html' => array('script' => 'master_menu', 'type' => 'MOD'),
            'bottomhead_html' => array(
                  array('script' => 'vertical_menu', 'type' => 'MOD'),
                  array('script' => 'search_mini', 'type' => 'MOD'),
                  array('script' => 'person_data', 'type' => 'MOD')
            ),
            'second_head_html' => array('script' => 'breadcrumbs', 'type' => 'MOD'),
            'left_column_html' => array(
                  array('script' => 'menu_left', 'type' => 'MOD', 'enabled' => false),
                  array('script' => 'categories_list', 'type' => 'MOD', 'logged' => 'YES'),
                  array('script' => 'banner_left', 'type' => 'MOD', 'logged' => 'NO')
            ),
            'right_column_html' => array(
                  array('script' => 'basket_list', 'type' => 'MOD', 'logged' => 'YES'),
                  array('script' => 'banner_right', 'type' => 'MOD', 'logged' => 'NO')
            ),
            'bottom_html' => array('script' => 'empty', 'type' => 'MOD'),
            'footer_html' => array('script' => 'empty', 'type' => 'MOD'),
      );
      
      if( defined('TEMPLATE_conf') && defined('TEMPLATE_places') && substr_count(constant('TEMPLATE_places'), ':')>0 ) {
         $template_places = explode(':', constant('TEMPLATE_places'));
         $this->TEMPLATE_places = Data::get_page_places($template_places);
      }
      return $this->TEMPLATE_places;
   }

   function get_component() {
      $F = Framework::g_global();
      $P = Person::g_global();

      $this->list_places = $this->TEMPLATE_places;
   
      //FIXME
      //add rights to coponent
      if( $F->check_get('com') ) {
         if ( $this->check_component_rights($F->com) ) {
            $this->list_places['component_html'][0]['script'] = $F->com;
         } else {
            $this->redirect( $F->make_link(DEFAULT_COM) );
         }
      } else {
         $this->list_places['component_html'][0]['script'] = DEFAULT_COM;
      }

   }
   
   function check_component_rights( $component_name ) {
      $F = Framework::g_global();
      $P = Person::g_global();
      
      if( $F->not_null($component_name) ) {
         $component_name_info = Data::get_component_rights( $component_name );
         if( sizeof($component_name_info) == 1 ) {
            $place = $component_name_info[0];
            if( !isset($place['logged']) ) $display_logged = true;
            elseif ( $place['logged'] == 'BOTH' ) $display_logged = true;
            elseif ( $place['logged'] == 'YES' && $P->logged_in ) $display_logged = true;
            elseif ( $place['logged'] == 'NO' && !$P->logged_in ) $display_logged = true;
            else $display_logged = false;
            
            return $display_logged;
         }
      } else {
         return false;
      }

   }

   function render_places() {
      foreach($this->list_places as $place_name => $place) {
         $content = '';
         if( Framework::not_null($place['script']) ) {
            $content = $this->_render_place($place);
         } elseif(Framework::not_null($place[0]) && is_array($place[0]) ) {
            foreach($place as $idx_place => $place_arr) {
               if( Framework::not_null($place_arr['script']) ) {
                  $content .= $this->_render_place($place_arr, $idx_place);
               }
            }
         }
         $this->PLACE[${place_name}] = $content;
         //echo ${place_name} . strlen($content) . "<br>\n";
      }
   }
    
   private function _render_place($place, $idx_place = 0) {
      $F = Framework::g_global();
      $P = Person::g_global();
      $Page = Page::g_global();
      $BC = Breadcrumbs::g_global();
      //echo $place['type'].': '.$place['script'].' ; '.$place['logged']."<br>\n";
      
      if( !isset($place['logged']) ) $display_logged = true;
      elseif ( $place['logged'] == 'BOTH' ) $display_logged = true;
      elseif ( $place['logged'] == 'YES' && $P->logged_in ) $display_logged = true;
      elseif ( $place['logged'] == 'NO' && !$P->logged_in ) $display_logged = true;
      else $display_logged = false;
      
      if( !isset($place['enabled']) ) $display_enabled = true;
      else $display_enabled = $place['enabled'];
      
      if( $display_enabled && $display_logged ) {
         ob_start();
         //$this->include_element($place['script'], $place['type']);
         switch( $place['type'] ) {
            case 'COM':
               echo '<div class="content_container content_container_nr' . $idx_place . '">';
               include(DIR_INC_COMPONENTS . DS . $place['script'] . '.php');
               echo '</div>';
            break;
            case 'MOD':
               echo '<div class="module_container module_container_nr' . $idx_place . '">';
               include(DIR_INC_MODULES . DS . $place['script'] . '.php');
               echo '</div>';
            break;
            default: break;
         }
         return ob_get_clean();
      } else {
         return '';
      }
   }
   
   function clean_page() {
   	ob_end_clean();
   }
    

   function include_element($name, $type = 'COM') {
      //TODO check if exist
      $F = Framework::g_global();
      $P = Person::g_global();
      $Page = Page::g_global();
      $BC = Breadcrumbs::g_global();
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
         if( Framework::not_null($this->PLACE[${var_name}]) ) return $this->PLACE[${var_name}];
         else return 'PLACE ' . $var_name;
      } else {
         echo "$name not defined!";
      }
   }
}
?>