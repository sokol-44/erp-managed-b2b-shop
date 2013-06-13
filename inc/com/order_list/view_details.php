<?php
$Page->head_title =Lang::_('Order details');

//FIXME
//remember backtrack

if( !$Order->check_rights() )  $F->redirect($F->make_link(CFG_COM_ORDER_LIST));

$BC->add_crumb(Lang::_('Orders details'), $F->self_link() );
?>
<div class="order_container">
  <div class="order_container order_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="order_container order_content">
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('Field') ?></th>
		<th><?php echo Lang::_('Value') ?></th>
	</tr>
	<tr>
		<td><?php echo Lang::_('ID') ?></td>
		<td><?php echo $F->output_string_html($Order->data['id_order']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('order DESCRIPTION') ?></td>
		<td><?php echo nl2br($F->output_string_html( $Order->data['description'] ) ) ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('basket DESCRIPTION') ?></td>
		<td><?php echo nl2br($F->output_string_html( $Order->data['description_basket'] ) ) ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('order date') ?></td>
		<td><?php echo $F->output_string_html($Order->data['date_create']); ?></td>
	</tr>
	<?php if($F->not_null($Order->data['date_modified'])) {?>
	<tr>
		<td><?php echo Lang::_('order update') ?></td>
		<td><?php echo $F->output_string_html($Order->data['date_modified']); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td><?php echo Lang::_('order STATE') ?></td>
		<td><?php echo $F->output_string_html($Order->data['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('order statistic') ?></td>
		<td><div class="basket order_total" style="width: 160px">
<div class="basket_product_total"><?php echo Lang::_('TOTAL PRODUCTS'); ?><span id="nr"><?php echo $total['product_total']; ?></span></div>
<div class="basket_product_types"><?php echo Lang::_('PRODUCTS TYPES'); ?><span id="nr"><?php echo $total['product_types']; ?></span></div>
<div class="basket_sum_gross"><?php echo Lang::_('sum gross'); ?><span id="nr"><?php echo Price::val($total['sum_gross']); ?></span></div>
<div class="basket_sum_netto"><?php echo Lang::_('sum_netto'); ?><span id="nr"><?php echo Price::val($total['sum_netto']); ?></span></div>
</div></td>
	</tr>
</table>
<?php echo Lang::_('Order products'); ?>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('PICTURE') ?></th>
		<th><?php echo Lang::_('NAME') . ', ' . Lang::_('DESCRIPTION')?></th>
		<th><?php echo Lang::_('PRICE') ?></th>
		<th><?php echo Lang::_('quantity') ?></th>
	</tr>
	<?php
	foreach( $Order->product_list as $product ) {
	   $GET_tmp = $F->add_local_get('id_product', $product['id_product'], $GET_tmp);

	   if( $F->not_null($product['p_name']) && $product['name'] != $product['p_name'] ) {
	      $product_name = $F->output_string_html( $product['p_name'] ) . ' <STRIKE>' . $F->output_string_html( $product['name'] ) . '</STRIKE>';
	   } else {
	      $product_name = $F->output_string_html( $product['name'] );
	   }

	   $name_desc_cell = '<div class="catalog_product_name">' . $product_name . '</div>
	   <div class="catalog_product_description">' . nl2br($F->output_string_html( $product['description'], 384 )) . '</div>';

	   $link_product_info = $F->make_link(CFG_COM_PRODUCT_INFO, $GET_tmp);

	    
	   if( $F->is_null($product['p_name']) ) {
	      $cell_product_info = $name_desc_cell;
	      $small_image_html = Lang::_('PRODUCT INACTIVE/REMOVED');;
	   } else {
	      $cell_product_info = $F->draw_link($link_product_info, '', $name_desc_cell);
	      $small_image_path = Data::get_product_image_path( $product['picture_small_url'] );
	      $si_oc = "$.colorbox({href:'" . Data::get_product_image_path( $product['picture_big_url'] ) . "', photo:true});";
	      $small_image_html = $F->static_image($small_image_path, Lang::_('show_big_image'), " onclick=\"$si_oc\"");;
	   }

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
<?php

?>

  </div>
  <div class="order_container order_bottom container_bottom"></div>
</div>