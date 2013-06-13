<?php
if( $F->check_get('id_shopping_basket_version') ) {
   $id_shopping_basket_version = (int)$F->GET['id_shopping_basket_version'];
   $product_list = $Shopping_Basket->get_all_product_version( $id_shopping_basket_version );
} else {
   $product_list = $Shopping_Basket->get_all_product();
   $id_shopping_basket_version = (int)$Shopping_Basket->id_shopping_basket_version;
}

if(
  ($F->GET['show']=='change_level_up'   && $Shopping_Basket->check_move('UP')   ) ||
  ($F->GET['show']=='change_level_down' && $Shopping_Basket->check_move('DOWN') )
  )  {
   $version_list = $Shopping_Basket->get_all_version();
   $history_list = $Shopping_Basket->get_all_history();
   $direction_name = strtoupper($F->GET['show']);
} else {
   $F->redirect( $F->make_link(CFG_COM_BASKET) );
}

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$Page->head_title = Lang::_('BASKET NR:') . ' ' . $id_shopping_basket_version . ' ' . Lang::_($F->GET['show']);
// print_debug($Shopping_Basket);

$GET_tmp = $F->make_get(array('mode', 'show'));
$form_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', $F->GET['show'], $GET_tmp));

$GET_id  = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_tmp);

echo $F->draw_form('basket_edit', $form_link);
?>
<div class="basket_container">
  <div class="basket_container basket_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="basket_container basket_content">
<script>
function remove_from_basked() { return true; }
</script>
<table style="border: 0; width: 100%;">
	<tr>
		<td colspan="5"><?php //print_debug($product_list); ?></td>
	</tr>
	<tr>
		<td colspan="5">
      <?php echo Lang::_('description'); ?><br>
      <?php echo $F->draw_textarea_field('history_description', 'auto', '95%', 6); ?>
      </td>
	</tr>
	<tr>
		<td align="left" colspan="4"></td>
		<td align="right"><?php echo $F->draw_submit($direction_name, false, Lang::_($direction_name)); ?></td>
	</tr>
	<tr>
		<td colspan="5">
      <?php echo Lang::_('basket description'); ?><br>
      <?php echo $F->draw_textarea_field('description', 'auto', '95%', 6, $Shopping_Basket->params['description'], 'readonly="readonly"'); ?>
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
	foreach( $product_list as $product_key => $product ) {
	   $GET_product = $F->add_local_get('product_key', $product_key, $GET_tmp);
	   $link_remove_from_basket = $F->make_link(CFG_COM_BASKET, $F->add_local_get('mode', 'remove_from_basket', $GET_product));
	   $cell_remove_from_basket = $F->draw_link($link_remove_from_basket, 'onclick="remove_from_basked()" title="' . Lang::_('remove from BASKET') . '"', $F->static_image('icon/delete_16.png', Lang::_('remove from BASKET')));
	   
	   $link_product_info = $F->make_link(CFG_COM_PRODUCT_INFO, $GET_product);
	   $cell_product_info = $F->draw_link($link_product_info, '',
	   '<div class="catalog_product_name">' . $F->output_string_html( $product['name'] ) . '</div>
	   <div class="catalog_product_description">' . nl2br($F->output_string_html( $product['description'], 384 )) . '</div>');

	   $small_image_path = Data::get_product_image_path( $product['picture_small_url'] );
	   $si_oc = "$.colorbox({href:'" . Data::get_product_image_path( $product['picture_big_url'] ) . "', photo:true});";
	   $small_image_html = $F->static_image($small_image_path, Lang::_('show_big_image'), " onclick=\"$si_oc\"");;
	   
	   ?>
	<tr>
		<td style="cursor: pointer;" width="5%"><?php echo $F->draw_radio_field('list', $product_key, false, 'style="display: none"')
		. $small_image_html; ?></td>
		<td valign="top"><?php echo $cell_product_info; ?></td>
		<td width="10%"><?php echo Price::val( $product['price'] ) . '<br>(' . Price::tax( $product['vat'] ) . ')'; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<table style="border: 0; width: 100%;">
	<tr>
		<td colspan="5">
      <?php echo Lang::_('basket history'); ?>
      <br>
      </td>
	</tr>
</table>
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
<table style="border: 0; width: 100%;">
	<tr>
		<td colspan="5">
      <?php echo Lang::_('basket versions'); ?>
      <br>
      </td>
	</tr>
</table>
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
  </div>
  <div class="basket_container basket_bottom container_bottom"></div>
</div>
<?php
echo $F->draw_form_close();
?>