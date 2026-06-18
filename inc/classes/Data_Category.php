<?php
/**
 * Data_Category.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Data_Category
 *
 * Represents category data and provides methods to render category trees in HTML formats.
 *
 * @todo Add strict types declaration `declare(strict_types=1);` to the file header.
 * @todo Add explicit visibility modifiers (`public`, `protected`, `private`) to all methods.
 * @todo Implement PSR-4 namespacing and autoloading structural standards.
 * @todo Inject dependencies (`Framework`, `Person`, `Data`) instead of using static global accessors.
 */
class Data_Category extends Data_Product {

   /**
    * Data_Category constructor.
    *
    * Initializes the category data object by calling the parent constructor.
    *
    * @return void
    * @todo Add public visibility modifier.
    * @todo Add return type hint (void).
    */
   function __construct() {
        //echo get_class();
      parent::__construct();
   }


   /**
    * Renders the category tree as an HTML unordered list (ul/li) structure.
    *
    * Generates a JSON representation of the categories and returns the HTML wrapper
    * containing the list of categories if the user has permission to view it.
    *
    * @return string|null HTML string of the category box, or null if access is restricted.
    *
    * @todo Add public visibility modifier and string|null return type hint.
    * @todo Avoid using global constants like SHOP_SHOW_CATALOG_ONLY_LOGGED; inject configuration instead.
      * @todo Move html building outside data object.
    */
   function show_category_li() {
        $P = Person::g_global();

       $category_tree = Data::get_categorie_tree();
       $categories_string = self::show_category_tree_li($category_tree);
       $json_string = $F->json_string($category_tree);

       //echo '';


       if( !(SHOP_SHOW_CATALOG_ONLY_LOGGED == 'true' && !$P->logged_in) ) {
           return '<script>json_data.categories_list="'.$json_string.'";</script>
               <div class="iStoreBox" id="iStoreCategoriesBox">
            <div class="iStoreBoxWrapper">
            <div class="iStoreBoxHeader"><h2>Kategorie</h2>
            </div>
            <div class="iStoreBoxContent">
                       <ul id="iStorieCategoriesList">
                  ' . $categories_string . '
                 </ul>
            </div>
            </div>
            </div>';
        }
   }

   /**
    * Recursively generates HTML list items (li) for the category tree.
    *
    * @param array<int|string, array<string, mixed>> $local_tree The category tree array structure.
    * @param int   $level      The current recursion depth level. Defaults to 0.
    * @param int   $parent_id  The parent category ID. Defaults to 0.
    * @return string HTML list items string.
    *
    * @todo Add public visibility modifier and type hints for parameters and return value.
    * @todo Fix potential syntax error: `$F-> $category['name']` contains an unexpected space and variable property accessor.
    * @todo Fix call to undefined function `show_category()` (should probably be a recursive call to `self::show_category_tree_li()`).
    * @todo Avoid deep nesting and reduce complexity.
      * @todo Move html building outside data object.
    */
   function show_category_tree_li($local_tree, $level = 0, $parent_id = 0 ) {
       $F = Framework::g_global();
       $categories_string = '';

       if( $level > 16 ) return '';

       foreach($local_tree as $current_id => $category) {
           $all_products =  ($category['products_in_category'] + $category['products_in_subcategories']);

           if( $current_id > 0 && $category['parent'] == $parent_id && !(SHOP_SHOW_EMPTY!='true' && $all_products==0) ) {
               if (SHOP_SHOW_COUNTS == 'true') {
                   $category['description'] .= "\n" . Lang::_("PRODUCTS_IN_CATEGORY") . ' ' . $all_products ;
               }

               $name_long = trim(str_replace( array("\r\n", "\r", "\n"), '<br>', $F->output_string($category['description'])));

               $GET_tmp = $F->make_get();

               $id_select ='';
               if ( $F->check_request_split_array('catpath', $current_id) ) {
                   $id_select = ' category_selected selected';
               }
               $categories_string_tmp = '<li id="cat'.$current_id.'" class="'.$id_select.'">
                 <a href="' . $F->make_link(CFG_COM_CATALOG, $F->add_local_get('catpath', $category['path'], $GET_tmp)) . '"';

               if( $name_long != '') {
                   $categories_string_tmp .= ' title="' . $F->output_string($F-> $category['name'] . ' # ' . $name_long) . '">';
               } else {
                   $categories_string_tmp .= ' title="' . $F->output_string($category['name']) . '">';
               }

               $categories_string_tmp .= $F->output_string($category['name']);

               if ( count($category['children']) > 0 ) $categories_string_tmp .= '&nbsp;-&gt;';


               if ($all_products > 0 ) $categories_string_tmp .= '&nbsp;(' . $all_products . ')';
               $categories_string_tmp .= '</a>';

               if( is_array($category['children']) && count($category['children']) > 0 &&
               isset($F->GET['catpath']) && $F->check_request_split_array('catpath', $current_id)) {
                   $categories_string_tmp .= '<ul id="iStorieCategoriesList">'.
                           show_category($category['children'], $level+1, $current_id) . '</ul>';
               }

               $categories_string .= $categories_string_tmp . '</li>' . "\r\n";
           }
       }

       return $categories_string;
   }



   /**
    * Renders the category tree as an HTML div-based structure.
    *
    * Generates a JSON representation of the categories and returns the HTML wrapper
    * containing the div-based category menu if the user has permission to view it.
    *
    * @return string|null HTML string of the category box, or null if access is restricted.
    *
    * @todo Add public visibility modifier and string|null return type hint.
    * @todo Avoid using global constants like SHOP_SHOW_CATALOG_ONLY_LOGGED; inject configuration instead.
    */
   function show_category_div() {
        $P = Person::g_global();

       $category_tree = Data::get_categorie_tree();
       $categories_string = self::show_category_tree_div($category_tree);
       $json_string = $F->json_string($category_tree);

       if( !(SHOP_SHOW_CATALOG_ONLY_LOGGED == 'true' && !$P->logged_in) ) {
           return '<script>json_data.categories_list="'.$json_string.'";</script>
       <div class="categories categories_list">
          <div class="categories menu_header">Kategorie<div class="icon"></div></div>
          ' . $categories_string . '
          <div class="categories menu_bottom"></div>
       </div>';
       }
   }

   /**
    * Recursively generates HTML div elements for the category tree.
    *
    * @param array<int|string, array<string, mixed>> $local_tree The category tree array structure.
    * @param int   $level      The current recursion depth level. Defaults to 0.
    * @param int   $parent_id  The parent category ID. Defaults to 0.
    * @return string HTML div elements string.
    *
    * @todo Add public visibility modifier and type hints for parameters and return value.
    * @todo Fix call to undefined function `show_category()` (should probably be a recursive call to `self::show_category_tree_div()`).
    * @todo Avoid deep nesting and reduce complexity.
    */
   function show_category_tree_div($local_tree, $level = 0, $parent_id = 0 ) {
       $F = Framework::g_global();
       $categories_string = '';

       if( $level > 16 ) return '';

       foreach($local_tree as $current_id => $category) {
           $all_products =  ($category['products_in_category'] + $category['products_in_subcategories']);

           if( $current_id > 0 && $category['parent'] == $parent_id && !(SHOP_SHOW_EMPTY!='true' && $all_products==0) ) {

               if (SHOP_SHOW_COUNTS == 'true') {
                   $category['description'] .= "\n" . $F->output_string( Lang::_("PRODUCTS_IN_CATEGORY") . ' ' . $all_products );
               }

               $name_long = trim(str_replace( array("\r\n", "\r", "\n"), '<br>', $F->output_string($category['description'])));

               $GET_tmp = $F->make_get();

               $id_select ='';
               if ( $F->check_request_split_array('catpath', $current_id) ) {
                   $id_select = ' category_selected';
               }

               $categories_string_tmp =
               '<div class="categories cat_href' . $id_select . '" id="idcat_'.$category['path'].'">' . str_repeat('&nbsp;&nbsp;', $category['level']) .
               '<a href="' . $F->make_link(CFG_COM_CATALOG, $F->add_local_get('catpath', $category['path'], $GET_tmp)) . '"';

               if( $name_long != '') {
                   $categories_string_tmp .= ' title="' . $F->output_string($F->$category['name'] . ' # ' . $name_long) . '">';
               } else {
                   $categories_string_tmp .= ' title="' . $F->output_string($category['name']) . '">';
               }

               $categories_string_tmp .= $F->output_string($category['name']);

               if ( count($category['children']) > 0 ) $categories_string_tmp .= '-&gt;';

               $categories_string_tmp .= '</a>';

               if ($all_products > 0 ) $categories_string_tmp .= '&nbsp;(' . $all_products . ')';

               $categories_string .= $categories_string_tmp . '</div>' . "\r\n";
               if( is_array($category['children']) && count($category['children']) > 0 &&
               isset($F->GET['catpath']) && $F->check_request_split_array('catpath', $current_id)) {
                   $categories_string = show_category($category['children'], $level+1, $current_id);
               }
           }
       }

       return $categories_string;
   }


}
