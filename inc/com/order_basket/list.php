<?php
$product_list = $Shopping_Basket->get_all_product();

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$Page->head_title = Lang::_('ORDER_FROM_BASKET');

//magic mode for order LEVEL_99 -> move to CLASS::Rights
if( !$P->check_roles('LEVEL_99') || !$Shopping_Basket->contents || sizeof($Shopping_Basket->contents) == 0 ) {
   $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode') ) );
}

$id_nr_shopping_basket = $Shopping_Basket->id_nr_shopping_basket;

$GET_tmp = $F->make_get();
$get_form_link =$F->add_local_get(array('mode' => 'order_basket', 'id_nr_shopping_basket' => (int)$id_nr_shopping_basket));
//print_debug($get_form_link);
$form_link = $F->make_link(CFG_COM_ORDER_BASKET, $get_form_link, $GET_tmp);

echo $F->draw_form('basket_order', $form_link);
?>
<div class="list_order_container">
  <div class="list_order_container list_order_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="list_order_container list_order_content">
<table width="100%" style="border: 0">
	<tr>
		<td colspan="5"></td>
	</tr>
	<tr>
		<td colspan="5">
      <?php echo Lang::_('order description'); ?><br>
      <?php echo $F->draw_textarea_field('order_description', 'auto', '95%', 6, ''); ?>
      </td>
   </tr>
	<tr>
		<td width="100%" colspan="4"></td>
		<td align="right"><?php echo $F->draw_submit(Lang::_('ORDER_FROM_BASKET')); ?></td>
	</tr>
	<tr>
		<td colspan="5">
      <?php echo Lang::_('basket description'); ?><br>
      <?php echo $F->draw_textarea_field('description', 'auto', '95%', 6, $Shopping_Basket->params['description'], 'readOnly="readOnly"'); ?>
      </td>
   </tr>
</table>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('PICTURE') ?></th>
		<th><?php echo Lang::_('NAME') . ', ' . Lang::_('DESCRIPTION')?></th>
		<th><?php echo Lang::_('PRICE') ?></th>
		<th><?php echo Lang::_('quantity') ?></th>
	</tr>
	<?php
	foreach( $product_list as $product ) {
	   $GET_tmp = $F->add_local_get('id_product', $product['id_product'], $GET_tmp);
	   $link_remove_from_basket = $F->make_link(CFG_COM_BASKET, $F->add_local_get('mode', 'remove_from_basket', $GET_tmp));
	   $cell_remove_from_basket = $F->draw_link($link_remove_from_basket, 'onclick="remove_from_basked()" title="' . Lang::_('remove from BASKET') . '"', $F->static_image('icon/delete_16.png', Lang::_('remove from BASKET')));
	   
	   $link_product_info = $F->make_link(CFG_COM_PRODUCT_INFO, $GET_tmp);
	   $cell_product_info = $F->draw_link($link_product_info, '',
	   '<div class="catalog_product_name">' . $F->output_string_html( $product['name'] ) . '</div>
	   <div class="catalog_product_description">' . nl2br($F->output_string_html( $product['description'], 384 )) . '</div>');

	   $small_image_path = Data::get_product_image_path( $product['picture_small_url'] );
	   $si_oc = "$.colorbox({href:'" . Data::get_product_image_path( $product['picture_big_url'] ) . "', photo:true});";
	   $small_image_html = $F->static_image($small_image_path, Lang::_('show_big_image'), " onclick=\"$si_oc\"");;
	   
	   ?>
	<tr>
		<td style="cursor: pointer;" width="5%"><?php echo $F->draw_radio_field('list', $product['id_product'], false, 'style="display: none"')
		. $small_image_html; ?></td>
		<td valign="top"><?php echo $cell_product_info; ?></td>
		<td width="10%"><?php echo Price::val( $product['price'] ) . '<br>(' . Price::tax( $product['vat'] ) . ')'; ?></td>
		<td width="10%"><?php echo $product['quantity']; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<table width="100%" style="border: 0">
	<tr>
		<td width="100%" colspan="5"></td>
		<td align="right"><?php echo $F->draw_submit(Lang::_('ORDER_BASKET')); ?></td>
	</tr>
	<tr>
		<td colspan="5"><?php //echo $SP->display_links(); ?></td>
	</tr>
</table>
  </div>
  <div class="list_order_container list_order_bottom container_bottom"></div>
</div>
<?php
echo $F->draw_form_close();
?>