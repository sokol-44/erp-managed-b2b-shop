<?php
/**
 * Global initialization and domain model representation file for articles.
 *
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 *
 * @todo Remove dependency on global file guards (`_I_INIT`) and adopt modern PSR-4 autoloading standard layout patterns.
 * @todo Avoid hardcoding references to global registry configurations or static cross-domain dependencies like `Data::get_article`.
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Article
 *
 * Implements a variation of the Singleton / Registry pattern to hold global state for an article resource instance.
 * It provides magic methods, a static gateway facade for dynamic execution execution paths, and self-registration hooks upon deserialization.
 *
 * @designPattern: Singleton / Registry
 *
 * @todo Migrate from anti-pattern pseudo-singleton states to structured Dependency Injection workflows.
 * @todo Declare explicit types and visibilities across properties to secure internal structural contexts.
 */
class Article {
   /**
    * @var Article|null Holds the internal global reference instance tracking active article instances globally.
    */
   static $class;

   /**
    * @var array Explicit storage mapping container capturing individual article parameters, configurations, and field elements.
    */
   public $param = array();

   /**
    * Article constructor.
    *
    * Assigns the instance context globally and triggers loading parameters matching the provided key indicator.
    *
    * @param mixed $key Identification target string, integer identifier, or boolean false flag pointing to target record datasets. Defaults to false.
    *
    * @todo Refactor implicit property instantiation behaviors inside constructors to use standard initialization patterns.
    */
   public function __construct($key = false) {
      self::$class = $this;

      $this->load_article($key);
   }

   /**
    * Fetches explicit payload records from data modules and maps structural contents inside local parameters.
    *
    * @param mixed $key Data collection query key signature target identifying the targeted article element.
    * @return array Loaded configuration parameters tracking operational metadata fields.
    *
    * @todo Decouple the hardcoded dependency on the external procedural/static `Data` entity class.
    * @todo Add strict return and input argument typing specifications matching PHP 8+ conventions.
    */
   private function load_article($key) {

      $this->param = Data::get_article($key);
      return $this->param;
   }

   /**
    * Retrieves or builds the active global static singleton instance tracking the article instance container.
    *
    * @return Article Active static model object instance mapping.
    *
    * @todo Replace the fallback `new Article` call with dynamic `new static()` or `get_called_class()` structures to permit sub-classing extensions.
    */
   static function g_global() {
      if( !is_object(self::$class) ) {
         self::$class = new Article; //get_called_class()
      }
      return self::$class;
   }

   /**
    * Static facade routing proxy handler facilitating dynamic invocation workflows against active class methods.
    *
    * Passes explicit array properties over safely using structural callback mappings.
    *
    * @param string $method Context configuration label target naming the target object execution routing path.
    * @param array $args Method argument lists matching signature expectations for the targeted execution routing path. Defaults to empty array.
    * @return mixed Evaluation output results matching executed callback sequences, or boolean false if target routines do not exist.
    *
    * @todo Uncomment and refactor the `throw new Exception` block to enforce reliable system fail-fast boundaries instead of returning silent false statements.
    * @todo Fix the scope runtime error tracking bug `get_class($this)` inside a static context method loop structure.
    */
   static function g( $method, $args = array()) {
      if( !is_object(self::$class) ) {
         self::$class = new Article;
      }
      if(method_exists(self::$class, $method)) {
         return call_user_func_array(array(self::$class, $method), $args);
      } else {
         return false;
         //throw new Exception(sprintf('The required method "%s" does not exist for %s', $method, get_class($this)));
      }
   }

   /**
    * Intercepts standard PHP object unserialization life-cycle routines.
    *
    * Restores and registers the isolated local object state back into the shared static tracking instance placeholder context.
    *
    * @return void
    *
    * @todo Evaluate if maintaining state persistence via a global reference wrapper across serialization scopes remains a necessary design design element.
    */
   public function __wakeup() {
      self::$class = $this;
   }
}
