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
		<td><?php echo $F->output_string_html(Lang::_($Order->data['name'])); ?></td>
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
<?php
$GET_tmp = $F->make_get('mode,action,show');
$add_basket = $F->draw_link(
   		$F->make_link(CFG_COM_ORDER_LIST, $F->add_local_get('mode', 'make_basket', $GET_tmp)), 'title="' . Lang::_('Create BASKET') . '"',
   		$F->dynamic_image(Lang::_('Create BASKET')) );
echo $add_basket;
?>
<div class="basket_container container_subheader"><?php echo Lang::_('Order products'); ?><div class="icon"></div></div>
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
<hr>
<div class="order_container order_title container_header"><?php echo Lang::_('Source basket'); ?><div class="icon"></div></div>
<div class="order_container container_subheader"><?php echo Lang::_('basket history'); ?><div class="icon"></div></div>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('ID') ?></th>
		<th><?php echo Lang::_('user_client') ?></th>
		<th><?php echo Lang::_('date') ?></th>
		<th><?php echo Lang::_('mode') ?></th>
		<th><?php echo Lang::_('description') ?></th>
	</tr>
	<?php
	foreach( $history_list as $history_key => $history ) {
	      
	?>
	<tr>
		<td style="cursor: pointer;" width="5%"><?php echo $history['id_shopping_basket_history'] ; ?></td>
		<td width="10%"><?php echo $history['login'] . ' (' . $history['id_client_user'] . ')'; ?></td>
		<td width="10%"><?php echo $history['date']; ?></td>
		<td width="10%"><?php echo $history['mode']; ?></td>
		<td width="10%"><?php echo $history['description']; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<div class="order_container container_subheader"><?php echo Lang::_('basket versions'); ?><div class="icon"></div></div>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('ID') ?></th>
		<th><?php echo Lang::_('client_user') ?></th>
		<th><?php echo Lang::_('product_types') ?></th>
		<th><?php echo Lang::_('product_count') ?></th>
		<th><?php echo Lang::_('date_created') ?></th>
		<th><?php echo Lang::_('date_modified') ?></th>
	</tr>
	<?php
	foreach( $version_list as $version_key => $version ) {
	   $GET_version = $F->add_local_get('id_shopping_basket', $version['id_shopping_basket'], $GET_tmp);
	   $GET_version = $F->add_local_get('id_shopping_basket_version', $version_key, $GET_version);

	   $link_version_info = $F->make_link(CFG_COM_BASKET, $F->add_local_get('show', 'version_details', $GET_version));
	   $cell_version_info = $F->draw_link($link_version_info, '', $version['id_shopping_basket_version']);
	   if ( $id_shopping_basket_version == $version_key ) {
	      $row_class = 'class="tableRow-active"';
	   } else {
	      $row_class = '';
	   }
	   $client_user_cell = $version['client_user_name'] . ' (' . $version['id_client_user'] . ')';
	   ?>
	<tr <?php echo $row_class; ?>>
		<td style="cursor: pointer;" width="5%"><?php echo $cell_version_info ; ?></td>
		<td valign="top"><?php echo $client_user_cell; ?></td>
		<td width="10%"><?php echo $version['count_product_types']; ?></td>
		<td width="10%"><?php echo $version['product_count']; ?></td>
		<td width="10%"><?php echo $version['date_created']; ?></td>
		<td width="10%"><?php echo $version['date_created']; ?></td>
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