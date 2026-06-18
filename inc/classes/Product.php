<?php
/**
 * Product.php Helper file for products
 * Copyright Michał Sokołowski 2013
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Product
 *
 * Domain data model and helper class for handling product records, configurations,
 * attributes, and image parsing.
 *
 * @designPattern Entity
 *
 * @todo Add strict type declarations (declare(strict_types=1);) at the top of the file.
 * @todo Define missing/incomplete return types on static tracking properties like $class.
 * @todo Refactor the static state collection pattern ($class registry) to a modern Repository or Cache pattern.
 * @todo Refactor static helper methods to dynamic instances, injected via standard DI containers (PSR-11).
 */
class Product {
   /**
    * @var array Static registry caching instantiated Product instances indexed by their product ID.
    */
   static $class = array();

   /**
    * @var int Unique identifier for the product.
    */
   public $id_product = 0;

   /**
    * @var array Collection of sub-types assigned to this product.
    */
   public $id_product_subtype = array();

   /**
    * @var array Schema array storing product parameters, features, metrics, and attributes.
    */
   public $info = array();

   /**
    * Product constructor.
    *
    * Instantiates the object, parses a given product key string/integer, and pre-populates metadata.
    *
    * @param int|string $product_key Product token string (e.g. alphanumeric/composite key) or raw integer ID. Defaults to 0.
    * @return $this
    *
    * @todo Remove standard constructor return statement since native constructors should return void implicitly.
    * @todo Use strict type declarations for input constraints.
    */
   public function __construct( $product_key = 0) {
        if( (int)$product_key > 0 && ( strpos($product_key, '_') || ctype_digit($product_key) ) )  {

           $res = Data::get_product_params_from_key($product_key);
           $this->id_product = (int)$res['id_product'];
          self::$class[$this->id_product] = $this;
           $this->fill_product();
       }
       return $this;
   }

   /**
    * Hydrates the product metadata array from internal database queries.
    *
    * @return void
    *
    * @todo Remove commented-out print_debug statement.
    */
   private function fill_product() {
       $this->info = Data::get_product_info( (int)$this->id_product );
       //print_debug($this->info);
   }

   /**
    * Fetches and extracts the core data parameters array for a specific product key.
    *
    * @param int|string $product_key Target composite product reference key or integer ID. Defaults to 0.
    * @return array Extracted product context details.
    *
    * @todo Add strict typehints for incoming value configurations and return types.
    */
   static function get_product_info( $product_key = 0) {
        $Product = Product::get_product( $product_key );
        return $Product->info;
   }

   /**
    * Retrieves the specialized classification layout type mapping for associated product graphics.
    *
    * @param int|string $product_key Target composite product reference key or integer ID. Defaults to 0.
    * @return mixed Parsed asset configuration profile format string or type flag.
    *
    * @todo Add parameter type properties and structural return annotations.
    */
   static function get_product_image_type( $product_key = 0) {
       $Product = Product::get_product( $product_key );
       return Data::get_product_image_type( $Product->info );
   }

   /**
    * Evaluates, sanitizes, and filters the internal item parameters list to extract visible display elements.
    *
    * @param int|string $product_key Target composite product reference key or integer ID. Defaults to 0.
    * @return array Processed collection schema representing product view properties.
    *
    * @todo Fix string matching bug where `strpos() == 0` check behaves incorrectly for matches at later indexes. Use strict boolean types.
    */
   static function get_attribute_display( $product_key = 0) {
       $Product = Product::get_product( $product_key );
       $ha = Data::$Data_Products_hidden_attributes;
       $attribute_list = $Product->info['attribute'];
       $attribute_list_tmp = $Product->info['attribute'];
       foreach($attribute_list_tmp as $type => $value) {
           foreach ($ha as $hidden_attr) {
                if ( strpos($value, $hidden_attr) == 0 && strpos($value, $hidden_attr) !== FALSE ) {
                    unset($attribute_list[$type]);
                }
            }
       }
       return $attribute_list;
   }
v
   /**
    * Strips away designated private, administrative, or system tags from grouped structural product variants.
    *
    * @param int|string $product_key Target composite product reference key or integer ID. Defaults to 0.
    * @return array Normalized array of display attributes mapped under layout structures.
    *
    * @todo Fix logical pattern check in string match tracking expressions to meet modern compliance standards.
    * @todo Standardize naming signatures to match modern PSR camelCase conventions (e.g. getAttributeGroupDisplay).
    */
   static function get_attribute_group_display( $product_key = 0) {
       $Product = Product::get_product( $product_key );
       $ha = Data::$Data_Products_hidden_attributes;
       $attribute_group = $Product->info['attribute_group'];
       $attribute_group_tmp = $Product->info['attribute_group'];
       foreach($attribute_group_tmp as $id_group => $group) {
            foreach($group['attribute'] as $id_attribute => $attribute) {
                foreach ($ha as $hidden_attr) {
                    if ( strpos($attribute['attribute_name'], $hidden_attr) == 0 &&
                            strpos($attribute['attribute_name'], $hidden_attr) !== FALSE ) {
                        unset($attribute_group[$id_group]['attribute'][$id_attribute]);
                    }
                }

            }
       }
       return $attribute_group;
   }

   /**
    * Factory method managing product memory state or querying a fresh entity instantiation.
    *
    * @param int|string $product_key Target composite product reference key or integer ID. Defaults to 0.
    * @return Product Instantiated product object wrapper instance.
    *
    * @todo Optimize object reuse checks and refactor nesting layers to reduce dynamic overhead.
    */
   static function get_product( $product_key = 0) {
        if( (int)$product_key > 0 && ( strpos($product_key, '_') || ctype_digit($product_key) ) )  {
           $res = Data::get_product_params_from_key($product_key);
            if( isset(self::$class[$res['id_product']]) ) {
                return self::$class[$res['id_product']];
            } else {
                return new Product($res['id_product']);
            }

        } else {
           return new Product();
       }
   }

   /**
    * Magic lifecycle deserialization hook.
    *
    * @return void
    */
   public function __wakeup() {
   }

   /**
    * Magic lifecycle breakdown hook.
    *
    * @return void
    */
   public function __destruct() {
   }

}
