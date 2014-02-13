<?php
$SP = new SplitPage('PRODUCTS_LIST');

if( $F->check_get('search') ) {
   $Page->head_title = $F->output_string_html( Lang::_('search result') );
   $filters = array();
   //FIXME - move to Data_Products, Products class ?
   if( $F->check_get('product_text') ) {
   	if( $F->check_get('product_text_all') ) {
      	$filters['OR'] = array( 'p.name' => '%'.$F->GET['product_text'].'%',
                       'p.description' => '%'.$F->GET['product_text'].'%');
   	} else {
      	if( $F->check_get('product_text') ) $filters['p.name'] = '%'.$F->GET['product_text'].'%';
   	}
   }
   if( $F->check_get('product_catalog_index') ) $filters['p.catalog_index'] = '%'.$F->GET['product_catalog_index'].'%';
   if( $F->check_get('product_in_warehouse') )  $filters['p.quantity'] = '>0';
   
   $product_list = Data::get_search_product_list($filters);
} else {
   $product_list = array();
   $Page->head_title = $F->output_string_html( Lang::_('search') );
}

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$GET_tmp = $F->make_get();

?>
<div class="search_container">
  <div class="search_container search_title container_header">Szukaj<div class="icon"></div></div>
  <div class="search_container search_form">
<?php echo $F->draw_form('search', $F->make_link(CFG_COM_SEARCH), 'GET'); ?><br>
<?php echo $F->draw_hidden_field('com', 'search'); ?>
<table class="search_table" style="border: 0">
	<tr>
		<td><strong><?php echo Lang::_('Text'); ?></strong></td>
		<td colspan="2"><?php echo $F->draw_input_field('product_text', '', ' style="width: 220px"'); ?></td>
		<td><?php echo Lang::_('product all text') . $F->draw_checkbox_field('product_text_all'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('catalog index'); ?></strong></td>
		<td colspan="2"><?php echo $F->draw_input_field('product_catalog_index', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Price') . ' ' . Lang::_('Max'); ?></strong></td>
		<td><?php echo $F->draw_input_field('product_price_max', '', ' style="width: 120px"'); ?></td>
		<td><strong><?php echo Lang::_('Min'); ?></strong></td>
		<td><?php echo $F->draw_input_field('product_price_min', '', ' style="width: 120px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('PRODUCT_IN_warehouse'); ?></strong></td>
		<td colspan="2"><?php echo $F->draw_checkbox_field('product_in_warehouse'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo $F->dynamic_image_submit(Lang::_('SEARCH'),''); ?></td>
	</tr>
</table>
</div>
<?php echo $F->draw_hidden_field('search', 'search'); ?>
<?php echo $F->draw_form_close(); ?>
<?php
if( $F->not_null($product_list) && sizeof($product_list) > 0 ) {
include 'mod_in' . DS . 'product_list_m2.php';
}
?>
<div class="search_container search_bottom container_bottom"></div>
</div>