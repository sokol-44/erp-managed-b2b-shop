<?php
$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');



if( $F->not_null( $Order ) ) {
   $Page->head_title = Lang::_('Order nr ') . $Order->id_order;
} else {
   $F->redirect( $F->make_link(CFG_COM_DEFAULT) );
}
?>

<div class="order_show_container">
  <div class="order_show_container order_show_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="order_show_container order_show_content">
<table width="100%" style="border: 0">
	<tr>
		<td colspan="5"></td>
	</tr>
	<tr>
		<td colspan="5"><?php echo Lang::_('order description'); ?><br>
		<?php echo $F->draw_textarea_field('description', 'auto', '95%', 6, $Order->data['description'], 'readOnly="readOnly"'); ?>
		</td>
	</tr>
	<tr>
		<td colspan="5"><?php echo Lang::_('basket description'); ?><br>
		<?php echo $F->draw_textarea_field('description', 'auto', '95%', 6, $Order->data['description_basket'], 'readOnly="readOnly"'); ?>
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
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('DATE') ?></th>
		<th><?php echo Lang::_('NAME') . ', ' . Lang::_('DESCRIPTION')?></th>
		<th><?php echo Lang::_('DESCRIPTION') ?></th>
	</tr>
	<?php
	foreach( $Order->status_history as $status ) {
	?>
	<tr>
		<td width="15%"><?php echo $F->output_string_html( $status['timestamp'] ) ?></td>
		<td width="15%"><?php echo $F->output_string_html( $status['name'] ) ?></td>
		<td width="60%"><?php echo nl2br($F->output_string_html( $status['description'] )) ?></td>
	</tr>
	<?php
	}
	?>
</table>
<table width="100%" style="border: 0">
	<tr>
		<td colspan="5"><?php //echo $SP->display_links(); ?></td>
	</tr>
</table>
	<?php
	?>
  </div>
  <div class="order_show_container order_show_bottom container_bottom"></div>
</div>