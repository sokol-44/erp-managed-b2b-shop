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

$filters = array();
$sort = array();

if( $F->check_get('ff_quantity')) {
	$filters['p.quantity'] = '>0';
}
if( $F->check_get('ff_namep') ) {
	//$filters['p.name'] = '%'.$F->GET['ff_namep'].'%';
	$filters['OR'] = array( 'p.name' => '%'.$F->GET['ff_namep'].'%', 'p.description'  => '%'.$F->GET['ff_namep'].'%');
}

$product_list = Data::get_categories_product_list($id_category, $filters, $sort);

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$GET_tmp = $F->make_get();

?>
<div class="catalog_container">
<div class="catalog_container catalog_title container_header"><?php echo Lang::_('Catalog'); ?><div class="icon"></div></div>
<div class="catalog_container catalog_title container_subheader"><?php echo Lang::_('FILTERS'); ?><div class="icon"></div></div>
<?php echo $F->draw_form('filter_form', $F->make_link(CFG_COM_CATALOG), 'GET' ); ?><br>
<?php echo $F->draw_hidden_field('com', CFG_COM_CATALOG); ?>
<?php echo $F->draw_hidden_field('time', microtime(true)); ?>
<?php echo $F->draw_hidden_field('catpath', $F->GET['catpath']); ?>
<table class="tableBox" style="width: 100%">
	<tbody><tr>
		<td><strong>Magazyn &gt; 0</strong></td>
		<td><?php echo $F->draw_checkbox_field('ff_quantity') ?></td>
		<td><strong>Nazwa, opis</strong></td>
		<td><?php echo $F->draw_input_field('ff_namep', (float)$battery_load_power_tmp, ' style="width: 95%"');  ?></td>
	</tr><tr>
		<td colspan="4" align="center"><?php echo $F->dynamic_image_submit(Lang::_('SET'),'SET'); ?></td>
	</tr>
</tbody></table>
<?php echo $F->draw_form_close(); ?>
<div class="catalog_container catalog_content">
<?php include DIR_INC_COMPONENTS . DS . 'mod_in' . DS . 'product_list_m2.php'?>
</div>
<div class="catalog_container catalog_bottom container_bottom"></div>
</div>