<?php
/**
 * Data_Products.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Data_Category extends Data_Product {

   function __construct() {
		//echo get_class();
      parent::__construct();
   }


   function show_category_li() {
   	$P = Person::g_global();
   

	   $F->request_split_array('catpath', '_', 'GET');
	   
	   $category_tree = Data::get_categorie_tree();
	   $categories_string = self::show_category_tree_li($category_tree);
	   $json_string = $F->json_string($category_tree);
	   
	   //echo '<!--'.print_r($category_tree,true).'-->';
	   
	   
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
   
   function show_category_tree_li($local_tree, $level = 0, $parent_id = 0 ) {
   	$F = Framework::g_global();
   	$scategories_string = '';
   	 
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
   
    
   
   function show_category_div() {
   	$P = Person::g_global();
   	
	   $F->request_split_array('catpath', '_', 'GET');
	   
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
   				$categories_string_tmp .= ' title="' . $F->output_string($F-> $category['name'] . ' # ' . $name_long) . '">';
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

?>