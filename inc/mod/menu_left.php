<?php
$filers = array();

   $category['description'] .= "\n" . TEXT_PRODUCTS_IN_CATEGORY . $all_products ;
   
   $name_long = trim(str_replace( array("\r\n", "\r", "\n"), '<br>', $category['description']));
   
   $GET_tmp = $F->make_get();
   
   $id_select ='';
   if ( $F->check_request_split_array('catpath', $current_id) ) {
      $id_select = 'id="category_selected"';
   }
   
   $categories_string_tmp =
   '<div class="cat_href" ' . $id_select . '>' . str_repeat('&nbsp;&nbsp;', $category['level']) .
   '<a href="' . $F->make_link(CFG_COM_CATALOG, $F->add_local_get('catpath', $category['path'], $GET_edit)) . '"';
   
   if( $name_long != '') {
      $categories_string_tmp .= ' title="' . $category['name'] . ' # ' . $name_long . '">';
   } else {
      $categories_string_tmp .= ' title="' . $category['name'] . '">';
   }
    
   $categories_string_tmp .= $category['name'];
    
   if ( count($category['children']) > 0 ) $categories_string_tmp .= '-&gt;';
   
   $categories_string_tmp .= '</a>';


return '<div class="categories_list">' . $categories_string . '</div>';

?>