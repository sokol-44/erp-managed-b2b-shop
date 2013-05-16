<?php
$SP = new SplitPage('PRODUCTS_LIST');

if( $F->check_get('search') ) {
   $Page->head_title = $F->output_string_html( Lang::_('search result') );
   $filters = array();
   if( $F->check_get('product_name') ) $filters['p.name'] = '%'.$F->GET['product_name'].'%';
   if( $F->check_get('product_description') ) $filters['p.description'] = '%'.$F->GET['product_description'].'%';
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
<?php echo $F->draw_form('search', $F->make_link(CFG_COM_SEARCH), 'GET'); ?><br>
<?php echo $F->draw_hidden_field('search', 'search'); ?>
<?php echo $F->draw_hidden_field('com', 'search'); ?>
<table class="pass_table" style="border: 0">
	<tr>
		<td><strong><?php echo Lang::_('Product Name'); ?></strong></td>
		<td colspan="3"><?php echo $F->draw_input_field('product_name', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Product Description'); ?></strong></td>
		<td colspan="3"><?php echo $F->draw_input_field('product_description', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Price') . ' ' . Lang::_('Max'); ?></strong></td>
		<td><?php echo $F->draw_input_field('product_price_max', '', ' style="width: 120px"'); ?></td>
		<td><strong><?php echo Lang::_('Min'); ?></strong></td>
		<td><?php echo $F->draw_input_field('product_price_min', '', ' style="width: 120px"'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo $F->static_image_submit($Page->path_img . 'guzik-zaloguj.jpg', TEXT_LOGIN,''); ?></td>
	</tr>
	
</table>
<?php echo $F->draw_form_close(); ?>
<?php


if( $F->not_null($product_list) && sizeof($product_list) > 0 ) {
?>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('PICTURE') ?></th>
		<th><?php echo Lang::_('NAME') . ', ' . Lang::_('DESCRIPTION')?></th>
		<?php if( $P->logged_in ) { ?>
		<th><?php echo Lang::_('QUANTITY') ?></th>
		<th><?php echo Lang::_('PRICE') ?></th>
		<th><?php echo Lang::_('ADD TO BASKET') ?></th>
		<?php } ?>
	</tr>
	<?php
	foreach( $product_list as $product ) {
	   $GET_tmp = $F->add_local_get('id_product', $product['id_product'], $GET_tmp);

	   if( $P->logged_in ) {
	      $product_quantity = (int)(($product['quantity']>0)?$product['quantity']:0);
	      $link_basket = $F->make_link(CFG_COM_BASKET, $F->add_local_get('mode', 'add_to_basket', $GET_tmp));
	      $cell_basket = $F->draw_link($link_basket, 'onclick="add_basked()" title="' . Lang::_('add_to_basket') . '"', $F->static_image('icon/buy_16.png', Lang::_('add_to_basket')));
	   }

	   $link_product_info = $F->make_link(CFG_COM_PRODUCT_INFO, $GET_tmp);
	   $cell_product_info = $F->draw_link($link_product_info, '',
	   '<div class="catalog_product_name">' . $F->output_string_html( $product['name'] ) . '</div>
	   <div class="catalog_product_description">' . nl2br($F->output_string_html( $product['description'], 384 )) . '</div>');


	   $small_image_path = Data::get_product_image_path( $product['picture_small_url'] );
       //FIXME
	   $si_oc = "$.colorbox({href:'/" . Data::get_product_image_path( $product['picture_big_url'] ) . "', photo:true});";
	   $small_image_html = $F->static_image($small_image_path, Lang::_('show_big_image'), " onclick=\"$si_oc\"");

	   if( $P->logged_in ) {
	?>
	<tr>
		<td style="cursor: pointer;" width="5%"><?php echo $F->draw_radio_field('list', $product['id_product'], false, 'style="display: none"')
		. $small_image_html; ?></td>
		<td valign="top"><?php echo $cell_product_info; ?></td>
		<td width="5%"><?php echo $product_quantity; ?></td>
		<td width="10%"><?php echo Price::val( $product['price'] ) . '<br>(' . Price::tax( $product['vat'] ) . ')'; ?></td>
		<td width="5%"><?php echo $cell_basket; ?></td>
	</tr>
	<?php
	   //not logged in
	   } else {
	?>
	<tr>
		<td style="cursor: pointer;" width="5%"><?php echo $F->draw_radio_field('list', $product['id_product'], false, 'style="display: none"')
		. $small_image_html; ?></td>
		<td valign="top"><?php echo $cell_product_info; ?></td>
	</tr>
	<?php
	//end else (not logged in)
	   }
	}
	?>
</table>
<table style="border: 0">
	<tr>
		<td colspan="5"><?php echo $SP->display_links(); ?></td>
	</tr>
</table>
<?php
}
?>
