<?php


if( $F->com == CFG_COM_CATALOG ||
   $F->com == CFG_COM_PRODUCT_INFO && $F->check_get('catpath')) {
   $category_list = Data::get_categories_from_list();
   $array_bc = array( array('name' => Lang::_('TOP_CATEGORY'),
   	'path' => $F->make_link(CFG_COM_CATALOG) ) );
   $path_bc = '';
   foreach($category_list as $category) {
      if( $path_bc == '' ) $path_bc = $category['id_category'];
      else $path_bc .= '_' . $category['id_category'];
      $array_bc[] = array( 'name' => $category['name'],
      'path' => $F->make_link(CFG_COM_CATALOG, array('catpath' => $path_bc ) ) );
   }
   if( $F->com == CFG_COM_PRODUCT_INFO && isset($product_info) ) {
      $array_bc[] = array( 'name' => $product_info['name'],
      'path' => $F->self_link());
   }
   
   //FIXME multipath array
//} elseif( $F->com == CFG_COM_PRODUCT_INFO && isset($product_info) && is_array($product_info)
//&& isset($product_info['id_category_list']) ) {
//   $category_array = explode(',', $product_info['id_category_list']);
//   $category_list = Data::get_categories_from_list( $category_array );
} else {
   $array_bc = $BC->get_list();
}

echo "<div class=\"hlist\"><ul>\n";
foreach($array_bc as $bc) {
   echo "<li><a href=\"$bc[path]\">$bc[name]</a></li>\n";
}
echo "</ul></div>\n";
?>