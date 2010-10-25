<?php
$SP = new SplitPage('PRODUCTS_LIST');
$product_list = Data::get_categories_product_list();

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
   $link_2basket = $F->make_link(CFG_COM_BASKET, $F->add_local_get('mode', 'add_basked', $GET_tmp));
   $cell_2basket = $F->draw_link($link_2basket, 'onclick="add_basked()" title="' . Lang::_('add_basked') . '"', $F->static_image('icon/buy_16.png', Lang::_('Remove')));
?>
	<tr>
		<td><?php echo $F->draw_radio_field('list', $product['id_product'], false, 'style="display: none"')
		. $F->output_string_html( $product['name'] ); ?></td>
		<td><?php echo $F->output_string_html( $product['description'] ); ?></td>
		<td><?php echo $F->output_string_html( $product['picture_small_url'] ); ?></td>
		<td><?php echo $F->output_string_html( $product['price'] . ' (' . $product['vat'] . ')' ); ?></td>
		<td><?php echo $cell_2basket; ?></td>
	</tr>
<?php
}
?>
   <tr>
   	<td colspan="5"><?php $SP->display_links; ?></td>
   </tr>
</table>