<?php 
if( $SP->number_of_pages>1 ) echo $SP->display_links(); 
?>
<table class="tableBox" style="border: 0">
<tr class="tableBoxHeading">
		<?php if( $P->logged_in ) { ?>
		<th><?php echo Lang::_('NETT PRICE') ?></th>
		<?php } ?>
		<th><?php echo Lang::_('QUANTITY_IN_warehouse') ?></th>
		<th><?php echo Lang::_('CATALOG INDEX') ?></th>
<th><?php echo Lang::_('NAME') . ', ' . Lang::_('DESCRIPTION')?></th>
	</tr>
	<?php
	foreach( $product_list as $product ) {
	   $GET_tmp = $F->add_local_get('id_product', $product['id_product'], $GET_tmp);

	   if( $P->logged_in ) {
	      $product_index = $F->output_string_html( $product['catalog_index'] );
	      $link_basket = $F->make_link(CFG_COM_BASKET, $F->add_local_get('mode', 'add_to_basket', $GET_tmp));
	      $cell_basket = $F->draw_link($link_basket, 'onclick="add_basked()" title="' . Lang::_('add_to_basket') . '"', $F->static_image('icon/buy_16.png', Lang::_('add_to_basket')));
	   }

	   $link_product_info = $F->make_link(CFG_COM_PRODUCT_INFO, $GET_tmp);
	   if( defined('SHOP_SHOW_PRODUCTS_DESCRIPTION_IN_LIST') && constant('SHOP_SHOW_PRODUCTS_DESCRIPTION_IN_LIST') == 'true')
	   	$description_html = str_replace('\n', "<br>\n", $F->output_string_html( $product['description'], 100 ) );
	   else $description_html = '';
	   $cell_product_info = $F->draw_link($link_product_info, '',
	   '<span class="catalog_product_name">' . $F->output_string_html( $product['name'] ) . '</span>
	   <span class="catalog_product_description">' . $description_html . '</span>');

		$catalog_index = $F->output_string_html( trim($product['catalog_index']) );
	   if( $P->logged_in ) {
	?>
	<tr>
		<td width="15%" align="right"><?php echo Price::val( $product['price'] ) ?></td>
		<td width="10%" align="right"><?php echo (int)$product['quantity']; ?></td>
		<td width="25%"><?php echo $catalog_index; ?></td>
		<td valign="top" width="50%"><?php echo $cell_product_info . $F->draw_radio_field('list', (int)$product['id_product'], false, 'style="display: none"'); ?></td>
	</tr>
	<?php
	   //not logged in
	   } else {
	?>
	<tr>
		<td style="cursor: pointer;" width="5%"><?php echo $F->draw_radio_field('list', $product['id_product'], false, 'style="display: none"')
		. $small_image_html; ?></td>
		<td width="5%"><?php echo $catalog_index; ?></td>
		<td valign="top"><?php echo $cell_product_info; ?></td>
	</tr>
	<?php
	//end else (not logged in)
	   }
	}
	?>
</table>
<?php 
if( $SP->number_of_pages>1 ) echo $SP->display_links(); 
?>