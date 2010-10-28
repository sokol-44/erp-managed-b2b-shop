<?php
$SP = new SplitPage('PRODUCTS_LIST');

if( $F->check_get('catpath') ) {
   $catpath = $F->request_split_array('catpath', ',', 'GET');
   $id_category = (int)end($catpath);
} else {
   $id_category = 0;
}
$product_list = Data::get_categories_product_list($id_category);

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$GET_tmp = $F->make_get();
?>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('NAME') ?></th>
		<th><?php echo Lang::_('DESCRIPTION') ?></th>
		<th><?php echo Lang::_('picture') ?></th>
		<th><?php echo Lang::_('price') ?></th>
		<th><?php echo Lang::_('ADD TO BASKET') ?></th>
	</tr>
	<?php
	foreach( $product_list as $product ) {
	   $GET_tmp = $F->add_local_get('id_product', $product['id_product'], $GET_tmp);
	   $link_basket = $F->make_link(CFG_COM_BASKET, $F->add_local_get('mode', 'add_to_basket', $GET_tmp));
	   $link_product_info = $F->make_link(CFG_COM_PRODUCT_INFO, $GET_tmp);
	   $cell_basket = $F->draw_link($link_basket, 'onclick="add_basked()" title="' . Lang::_('add_to_basket') . '"', $F->static_image('icon/buy_16.png', Lang::_('add_to_basket')));
	   $cell_product_info = $F->draw_link($link_product_info, '', $F->output_string_html( $product['name'] ));
	   $description_html = nl2br( $F->output_string_html( substr($product['description'], 0, 256) ) );
	   $small_image_path = Data::get_product_image_path( $product['picture_small_url'] );
	   $small_image_html = $F->static_image($small_image_path, Lang::_('add_to_basket'));;
	   $big_image_path = Data::get_product_image_path( $product['picture_big_url'] );
	   ?>
	<tr>
		<td><?php echo $F->draw_radio_field('list', $product['id_product'], false, 'style="display: none"')
		. $cell_product_info; ?></td>
		<td><?php echo $description_html; ?></td>
		<td><?php echo $small_image_html; ?></td>
		<td><?php echo $F->output_string_html( Price::val( $product['price'] ) . ' (' . Price::tax( $product['vat'] ) . ')' ); ?></td>
		<td><?php echo $cell_basket; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<table style="border: 0">
	<tr>
		<td colspan="5"><?php echo $SP->display_links(); ?></td>
	</tr>
</table>
