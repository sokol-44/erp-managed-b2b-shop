<?php
/**
 * Page.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Page
 * Manages layouts, dynamic layout areas (places), asset injections,
 * components access privileges, and template compilation streams.
 *
 * @todo Declare explicitly typed properties to replace legacy dynamically created/assigned properties (e.g., $path_css, $head_title, $js_jq_init, etc.).
 * @todo Migrate layout composition from variable variable assignments ($this->PLACE[${place_name}]) to explicit, secure property maps or typed layout DTOs.
 */
class Page {
   //   static $path_css, $path_js, $path_img;
   //   static $head_title, $head_keywords, $head_description, $head_js;
   //   static $masterhead_html, $second_head_html, $component_html, $bottom_html, $footer_html;
   //   static $com, $com_model_inc, $com_viewer_inc;
   //   static $target_com;
   //   static $js_jq_init, $js_jq_body;
   //   static $js_body, $jq_files;

   /**
    * @var Framework Reference utility wrapper hook variable.
    */
   static $F;

   /**
    * @var Page|bool Singleton tracking object storage wrapper.
    */
   static $class;

   /**
    * @var array Keeps aggregated block structural text outputs indexed by area keys.
    */
   private $PLACE;

   /**
    * @var array|bool Structural template rendering matrices map setup, or false before load.
    */
   private $TEMPLATE_places;

   /**
    * @var bool Flag determining whether jQuery setup assets have been instantiated.
    */
   private $jq_init;

   /**
    * @var array Keeps dynamic inline jQuery code pieces collected during execution.
    */
   private $jq_body;

   /**
    * @var array Local asset track collections targeting layout styles configurations.
    */
   private $css_array;

   /**
    * @var array Local tracking paths maps collection pointing towards external script files.
    */
   private $jq_files;

   /**
    * @var array|bool Stores raw inline JavaScript logic sequences, or false if uninitialized.
    */
   private $js_body;

   /**
    * @var array Transformed mapping assignments evaluating running active display areas blocks.
    */
   private $list_places;

   /**
    * Page constructor.
    * Initializes properties to their default states.
    */
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

   /**
    * Retrieves or initializes the global static instance of the Page manager.
    *
    * @return Page The singleton layout manager instance.
    *
    * @todo Transition away from internal singleton lookups toward a decoupled Dependency Injection pattern.
    */
   static function g_global() {
      if(self::$class == false) {
         self::$class = new Page;
      }
      return self::$class;
   }

   /**
    * Triggers external HTTP location forwarding updates.
    * @param string $page Target redirect URI path or structured address location.
    *
    * @return void
    *
    * @todo Eliminate raw instance-level initialization tracking calls like `$this->F->redirect()` before confirming hydration success states.
    */
   public function redirect($page) {
      $this->F->redirect($page);
   }

   /**
    * Orchestrates step routines bootstrapping core variables and component states.
    *
    * @return void
    */
   public function start() {
      $this->F = Framework::g_global();

      $this->set_raw_paths();
      $this->get_template_places();
      $this->get_component();
   }

   /**
    * Extracts system URI string routing parameters tracking execution targets.
    *
    * @return string Extracted URI argument formatting query string.
    *
    * @todo Eliminate reliance on dynamically defined object states ($this->target_com and $this->com) by adding explicit definitions.
    */
   function get_form_get_string() {

      if( Framework::not_null($this->target_com) ) {
         $target_com = $this->target_com;
      } else {
         $target_com = $this->com;
      }
      return '?com=' . $target_com;
   }

   /**
    * Sets up asset routing tracking directories utilizing global framework system constants.
    *
    * @return void
    *
    * @todo Refactor global constant dependencies (DIR_WWW_CSS, etc.) into an injectable Application Configuration class environment.
    */
   public function set_raw_paths() {
      $this->path_css = DIR_WWW_CSS;
      $this->path_js = DIR_WWW_JS;
      $this->path_img = DIR_WWW_IMG;
   }

   /**
    * Appends a dynamic link tag element inside systemic style layout pipelines tracking.
    *
    * @param string $css Relative resource path mapping targeted css style sheet files.
    *
    * @return void
    */
   public function add_css($css) {
      $this->css_array[] = '<link rel="stylesheet" type="text/css" href="' . $this->path_css . $css . '">';
   }

   /**
    * Accesses structural meta head title information strings.
    *
    * @return string Meta context value strings tracking defined attributes.
    */
   public function put_head_title() {
         return $this->head_title;
   }

   /**
    * Accesses strategic structural layout indexing search keywords data.
    *
    * @return string Meta keyword list values.
    */
   public function put_head_keywords() {
         return $this->head_keywords;
   }

   /**
    * Accesses detailed summary details describing running system scopes.
    *
    * @return string Description tracking values.
    */
   public function put_head_description() {
         return $this->head_description;
   }

   /**
    * Flushes gathered css link segments onto standard page view response screens.
    *
    * @return void
    */
   public function put_css() {
      echo implode("\r", $this->css_array) . "\n";
   }

   /**
    * Flushes compiled structural script blocks onto output content streams.
    *
    * @global array $config Application specification parameters mapping dynamic variables.
    * @return void
    *
    * @todo Replace legacy Google JSAPI loaders (`google.load("jquery", ...)`) with modern ECMAScript modules or updated dependency asset bundlers.
    */
   public function put_js() {
      global $config;

      if( $this->js_jq_init ) {
         echo '<script type="text/javascript" src="http://www.google.com/jsapi"></script>' . NL .
         '<script type="text/javascript">' . NL .
         'if ( window[\'google\'] && window[\'google\'][\'loader\']) {' . NL .
         '  google.load("jquery", "1"); ' . NL .
         '  google.load("jqueryui", "1"); ' . NL .
         '} else {' . NL .
         '  document.write(\'<script type="text/javascript" src="' . $this->path_js . $config['TEMPLATES']['jquery'] . '"><\/script>\');' . NL .
         '  document.write(\'<script type="text/javascript" src="' . $this->path_js . $config['TEMPLATES']['jquery-ui'] . '"><\/script>\');' . NL .
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

   /**
    * Appends raw script logic inside standard initialization wrapper closures.
    *
    * @param string $script Native functional logic commands payload blocks.
    * @return void
    */
   public function add_jq_init($script) {
      $this->js_jq_init = true;
      $this->js_jq_body[] = $script;
   }

   /**
    * Appends raw JavaScript context segments directly onto execution queues.
    *
    * @param string $script Execution strings containing raw tracking interactions code.
    * @return void
    */
   public function add_js_raw($script) {
      $this->js_body[] = $script;
   }

   /**
    * Queues path configurations linking structural third party external script integrations.
    *
    * @param string $script Targeted reference filename or raw absolute file resource pathway locator.
    * @param bool $add_path Determines if relative JavaScript systems routing gets prepended. Defaults to true.
    * @return void
    *
    * @todo Remove dead code.
    */
   public function add_js_file($script, $add_path = true) {
      if($add_path) $this->jq_files[] = $this->path_js . $script;
      else $this->jq_files[] = $script;
   }

   //   function set_menus_paths() {
   //      //FIXME - one menu model per whole page, or multiple ?
   //      $this->com_menu_model_inc = Data::get_admin_com_menu_model_inc($this->com);
   //      $this->com_menu_viewer_inc = Data::get_admin_com_menu_view_inc($this->com);
   //   }

   /**
    * Configures structural fallback layout components map tracking locations matrices.
    * @return array Matrix tracking configuration mappings detailing layout components.
    *
    * @todo Refactor hardcoded configuration maps to external configuration resource layers (e.g., YAML, JSON, or PHP array configurations).
    */
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

   /**
    * Evaluates component routing contexts, validating privileges.
    *
    * @return void
    *
    * @todo Abstract component access logic into dedicated Middleware components to eliminate coupling layout classes with security policies.
    */
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
             //print_debug($P); die();
            $this->redirect( $F->make_link(DEFAULT_COM) );
         }
      } else {
         $this->list_places['component_html'][0]['script'] = DEFAULT_COM;
      }

   }

   /**
    * Verifies user roles against component restrictions maps.
    *
    * @param string $component_name System component string reference key identifier.
    * @return bool True if session metrics satisfy authorization levels, false otherwise.
    */
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

   /**
    * Cycles through designated display layout elements parsing text contents.
    *
    * @return void
    */
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

   /**
    * Renders specific element types via output isolation buffers safely.
    *
    * @param array $place Multi-dimensional properties targeting script configurations metadata.
    * @param int $idx_place Optional positional sequence mapping index trackers. Defaults to 0.
    * @return string Stream generated isolated capture strings formatting components blocks.
    */
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
               echo '<div class="iStoreBox module_container module_container_nr' . $idx_place . '">';
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

   /**
    * Evaluates and builds dynamic modules using custom localized scope runtime environments.
    *
    * @param string $script_name Processing resource template identification key file target.
    * @param mixed $environment Variable state containers pass-through contexts arrays.
    * @return string Compiled string markup output fragments.
    *
    * @todo Fix bugs associated with undefined lookup variables (`$idx_place` and `$place['script']`) currently referenced inside this runtime process.
    */
   public function render_module_inplace($script_name, $environment) {
       $F = Framework::g_global();
       $P = Person::g_global();
       $Page = Page::g_global();
       $BC = Breadcrumbs::g_global();
       //echo $place['type'].': '.$place['script'].' ; '.$place['logged']."<br>\n";


       if( true ) { //check rights
           ob_start();
           //$this->include_element($place['script'], $place['type']);
           echo '<div class="module_inplacer' . $idx_place . '">';
           include(DIR_INC_MODULES . DS . $place['script'] . '.php');
           echo '</div>';
           return ob_get_clean();
       } else {
           return '';
       }
   }

   /**
    * Cleans out and terminates active framework buffer capture processing pipes.
    *
    * @return void
    */
   function clean_page() {
       ob_end_clean();
   }

   /**
    * Integrates external layout include resource segments based on layout definitions.
    * @param string $name Targeted file system component path element string lookup key.
    * @param string $type Selection engine routing discriminator context flag ('COM' or 'MOD'). Defaults to 'COM'.
    * @return void
    */
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

   /**
    * Magic method intercepting procedural prefix calls evaluating layout area outputs dynamically.
    *
    * @param string $name Method execution selector triggered by the calling environment script.
    * @param array $arguments Indexed parameters list passed during code execution invocation.
    * @return string Compiled template content outputs block context matching layout keys.
    */
   function __call($name, array $arguments) {
      if( strstr($name, 'put_') ) {
         $var_name = str_replace('put_', '', $name);
         if( Framework::not_null($this->PLACE[${var_name}]) ) return $this->PLACE[${var_name}];
         else return '';
      } else {
         echo "$name not defined!";
      }
   }
}
