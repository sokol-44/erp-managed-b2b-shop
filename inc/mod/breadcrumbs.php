<?php


if( $F->com == CFG_COM_CATALOG ) {
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
} else {
   $array_bc = $BC->get_list();
}

echo "<ul>\n";
foreach($array_bc as $bc) {
	echo "<li><a href=\"$bc[path]\">$bc[name]</a></li>\n";
}
echo "</ul>\n";
?>