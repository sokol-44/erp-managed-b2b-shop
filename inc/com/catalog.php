<?php
$SP = new SplitPage('PRODUCTS_LIST');

if( $F->check_get('catpath') ) {
   $catpath = $F->request_split_array('catpath', '_', 'GET');
   $id_category = (int)end($catpath);
   $category_list = Data::get_categories_from_list($id_category);
   $category = $category_list[0];
   $Page->head_title = $F->output_string_html( $category['name'] );
} else {
   $id_category = 0;
   $Page->head_title = Lang::_('TOP_CATEGORY');
}
$product_list = Data::get_categories_product_list($id_category);

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$GET_tmp = $F->make_get();

?>
<div class="catalog_container">
<div class="catalog_container catalog_title container_header"><?php echo Lang::_('Catalog'); ?><div class="icon"></div></div>
<div class="catalog_container catalog_content">
<?php include 'mod_in' . DS . 'product_list_m2.php'?>
</div>
<div class="catalog_container catalog_bottom container_bottom"></div>
</div>