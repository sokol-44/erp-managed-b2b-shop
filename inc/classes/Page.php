<?php
/**
 * Page.php Global initialization file
 * Copyright MichaÅ‚ SokoÅ‚owski 2010
 *
 * @author Micha³ Soko³owski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Page {
   static $path_css, $path_js, $path_img;
   static $title, $head_js, $F;

   public function __construct() {

   }

   public function start() {
      //for autocolpetition in Eclipse
      if(NONE) $this->F = new Framework();

      $this->F = Framework::g_global();
      //$this->F->com;

      $this->set_title();
      $this->set_paths();
   }


   public function set_paths() {
      $this->path_css = DIR_WWW_CSS;
      $this->path_js = DIR_WWW_JS;
      $this->path_img = DIR_WWW_IMG;
   }

   public function set_title() {

   }

   public function render($ala) {
      return true;
   }

   function __call($name, array $arguments) {
      echo "$name not defined!";
   }
}
?>