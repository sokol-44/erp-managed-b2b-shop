<?php
$filers = array();

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.tooltip.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('$(\'.cat_href a\').tooltip({
		track: false, delay: 0, showURL: false, fixPNG: true, showBody: " # "
	});');

$F->request_split_array('catpath', '_', 'GET');

$category_tree = Data::get_categorie_tree();
$categories_string = show_category($category_tree);
$json_string = $F->json_string($category_tree);

if( !(SHOP_SHOW_CATALOG_ONLY_LOGGED == 'true' && !$P->logged_in) ) {
   echo '<script>json_data.categories_list="'.$json_string.'";</script>
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

function show_category($local_tree, $categories_string = '', $parent_id = 0 ) {
   $F = Framework::g_global();
   

   foreach($local_tree as $current_id => $category) {
      $all_products =  ($category['products_in_category'] + $category['products_in_subcategories']);
      
      if( $category['parent'] == $parent_id && !(SHOP_SHOW_EMPTY!='true' && $all_products==0) ) {

         if (SHOP_SHOW_COUNTS == 'true') {
            $category['description'] .= "\n" . Lang::_("PRODUCTS_IN_CATEGORY") . ' ' . $all_products ;
         }

         $name_long = trim(str_replace( array("\r\n", "\r", "\n"), '<br>', $category['description']));

         $GET_tmp = $F->make_get();

         $id_select ='';
         if ( $F->check_request_split_array('catpath', $current_id) ) {
            $id_select = ' category_selected selected';
         }
         $categories_string_tmp = '<li id="cat'.$current_id.'" class="'.$id_select.'">
         		<a href="' . $F->make_link(CFG_COM_CATALOG, $F->add_local_get('catpath', $category['path'], $GET_tmp)) . '"';

         if( $name_long != '') {
            $categories_string_tmp .= ' title="' . $category['name'] . ' # ' . $name_long . '">';
         } else {
            $categories_string_tmp .= ' title="' . $category['name'] . '">';
         }
          
         $categories_string_tmp .= $category['name'];
          
         if ( count($category['children']) > 0 ) $categories_string_tmp .= '&nbsp;-&gt;';


         if ($all_products > 0 ) $categories_string_tmp .= '&nbsp;(' . $all_products . ')';
         $categories_string_tmp .= '</a>';

         if( is_array($category['children']) && count($category['children']) > 0 &&
         isset($F->GET['catpath']) && $F->check_request_split_array('catpath', $current_id)) {
            $categories_string_tmp .= '<ul id="iStorieCategoriesList">'.
            	show_category($category['children'], $categories_string, $current_id) . '</ul>';
         }

         $categories_string .= $categories_string_tmp . '</li>' . "\r\n";
      }
   }

   return $categories_string;
}

?>