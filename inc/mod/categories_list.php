<?php
$filers = array();

$F->request_split_array('catpath', ',', 'GET');

$category_tree = Data::get_categorie_tree();
echo show_category($category_tree);

function show_category($local_tree, $categories_string = '', $parent_id = 0 ) {
   $F = Framework::g_global();

   foreach($local_tree as $current_id => $category) {
      if( $category['parent'] == $parent_id) {

         $all_products =  ($category['products_in_category'] + $category['products_in_subcategories']);

         if (SHOW_COUNTS == 'true') {
            $category['description'] .= "\n" . TEXT_PRODUCTS_IN_CATEGORY . $all_products ;
         }

         $name_long = trim(str_replace( array("\r\n", "\r", "\n"), '<br>', $category['description']));
   
         $GET_tmp = $F->make_get();

         $categories_string_tmp =
			'<div class="cat_href">' . str_repeat('&nbsp;&nbsp;', $category['level']) .
			'<a href="' . $F->make_link(CFG_COM_CATALOG, $F->add_local_get('catpath', $category['path'], $GET_edit)) . '"';

         if( $name_long != '') {
            $categories_string_tmp .= ' title="' . $category['name'] . ' # ' . $name_long . '">';
         } else {
            $categories_string_tmp .= ' title="' . $category['name'] . '">';
         }
          
         if ( $F->check_request_split_array('catpath', $current_id) ) {
            $categories_string_tmp .= '<b>' . $category['name'] . '</b>';
         } else {
            $categories_string_tmp .= $category['name'];
         }
          
         if ( count($category['children']) > 0 ) $categories_string_tmp .= '-&gt;';

         $categories_string_tmp .= '</a>';

         if (SHOW_COUNTS == 'true' && $all_products > 0 ) $categories_string_tmp .= '&nbsp;(' . $all_products . ')';

         $categories_string .= $categories_string_tmp . '</div>' . "\r\n";
         if( is_array($category['children']) && count($category['children']) > 0 &&
         isset($F->GET['catpath']) && $F->check_request_split_array('catpath', $current_id)) {
            $categories_string = show_category($category['children'], $categories_string, $current_id);
         }
      }
   }
   
   return $categories_string;

}

?>