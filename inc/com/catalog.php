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

if( !$P->logged_in ) $F->redirect( $F->make_link(CFG_COM_LOGIN) );

$GET_tmp = $F->make_get();

?>
<div class="catalog_container">
<div class="catalog_container catalog_title container_header"><?php echo Lang::_('Catalog'); ?><div class="icon"></div></div>
<div class="catalog_container catalog_content">
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('NAME') . ', ' . Lang::_('DESCRIPTION')?></th>
		<th><?php echo Lang::_('CATALOG INDEX') ?></th>
		<th><?php echo Lang::_('QUANTITY_IN_warehouse') ?></th>
		<?php if( $P->logged_in ) { ?>
		<th><?php echo Lang::_('PRICE') ?></th>
		<th><?php echo Lang::_('ADD TO BASKET') ?></th>
		<?php } ?>
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
	   '<div class="catalog_product_name">' . $F->output_string_html( $product['name'] ) . '</div>
	   <div class="catalog_product_description">' . $description_html . '</div>');

		$catalog_index = $F->output_string_html( trim($product['catalog_index']) );
	   if( $P->logged_in ) {
	?>
	<tr>
		<td valign="top" width="10%"><?php echo $cell_product_info . $F->draw_radio_field('list', (int)$product['id_product'], false, 'style="display: none"'); ?></td>
		<td width="5%"><?php echo $catalog_index; ?></td>
		<td width="5%"><?php echo (int)$product['quantity']; ?></td>
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
		<td width="5%"><?php echo $catalog_index; ?></td>
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
</div>
<div class="catalog_container catalog_bottom container_bottom"></div>
</div>