<?php
$product_list = $Shopping_Basket->get_all_product();
$id_shopping_basket_version = (int)$Shopping_Basket->id_shopping_basket_version;

$version_list = $Shopping_Basket->get_all_version();
$history_list = $Shopping_Basket->get_all_history();

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$Page->head_title = Lang::_('BASKET NR:') . ' ' . $Shopping_Basket->id_shopping_basket ;
// print_debug($Shopping_Basket);
$GET_tmp = $F->make_get();
$form_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('mode', 'update_basket', $GET_tmp));

$GET_id  = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_tmp);


$basket_order = ''; $basket_up = ''; $basket_down = '';


$remove_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'remove_basket', $GET_id));
$remove_basket = $F->draw_link($remove_basket_link, 'title="' . Lang::_('remove BASKET') . '"', $F->static_image('icon/up_32.png', Lang::_('CHANGE_LEVEL_UP')));


if( $Shopping_Basket->check_rights('MAKE_ORDER', false) )
	$basket_order = $F->static_image_submit($F->static_image_src('icon/wallet_32.png'), Lang::_('PREPARE_ORDER_BASKET'),' name="PREPARE_ORDER_BASKET"');
   //$basket_order = $F->dynamic_image_submit(Lang::_('PREPARE_ORDER_BASKET'),'PREPARE_ORDER_BASKET');
   //$basket_order = $F->draw_submit('PREPARE_ORDER_BASKET', false, Lang::_('PREPARE_ORDER_BASKET'));

if( $Shopping_Basket->check_move('UP') )
	$basket_up = $F->static_image_submit($F->static_image_src('icon/up_32.png'), Lang::_('CHANGE_LEVEL_UP'),' name="CHANGE_LEVEL_UP"');
  // $basket_up    = $F->dynamic_image_submit(Lang::_('CHANGE_LEVEL_UP'), 'CHANGE_LEVEL_UP');
   //$basket_up    = $F->draw_submit('CHANGE_LEVEL_UP', false, Lang::_('SEND_BASKET_HIGHER'));

if( $Shopping_Basket->check_move('DOWN') )
	$basket_down = $F->static_image_submit($F->static_image_src('icon/down_32.png'), Lang::_('CHANGE_LEVEL_DOWN'),' name="CHANGE_LEVEL_DOWN"');
//    $basket_down  = $F->dynamic_image_submit(Lang::_('CHANGE_LEVEL_DOWN'),'SEND_BASKET_LOWER');
   //$basket_down  = $F->draw_submit('CHANGE_LEVEL_DOWN', false, Lang::_('SEND_BASKET_LOWER'));

//$update_basket = $F->dynamic_image_submit(Lang::_('UPDATE_BASKET'),'UPDATE_BASKET');
$update_basket = $F->static_image_submit($F->static_image_src('icon/tick_32.png'), Lang::_('UPDATE_BASKET'),' name="UPDATE_BASKET"');


$arg = array('mode' => 'make_favorite_basket', 'id_shopping_basket' => $Shopping_Basket->id_shopping_basket);
$add_basket = $F->draw_link(
		$F->make_link(CFG_COM_BASKET_FAVORITE, $arg), 'title="' . Lang::_('MAKE FAVORITE BASKET') . '"',
		$F->static_image('icon/heart_32.png') );


echo $F->draw_form('basket_edit', $form_link);
/* print_debug( array( 'id_basket_current' => $Shopping_Basket_Chain->id_basket_current,
		'id_basket_set' => $Shopping_Basket_Chain->id_basket_set,
		'id_nr_shopping_basket' => $Shopping_Basket_Chain->id_nr_shopping_basket) ); */
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
      <?php echo Lang::_('basket description'); ?><br>
      <?php echo $F->draw_textarea_field('description', 'auto', '', 6, $Shopping_Basket->params['description']); ?>
      </td>
	</tr>
<?php
if( $F->not_null($product_list) ) {
?>
	<tr>
		<td align="left"><?php echo $basket_up . '&nbsp;' . $basket_down; ?></td>
		<td colspan="3">&nbsp;</td>
		<td align="right"><?php echo $add_basket . '&nbsp;' . $basket_order . '&nbsp;' . $update_basket ?></td>
	</tr>
</table>
<div class="basket_container container_subheader"><?php echo Lang::_('basket products'); ?><div class="icon"></div></div>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('PICTURE') ?></th>
		<th><?php echo Lang::_('NAME') . ', ' . Lang::_('DESCRIPTION')?></th>
		<th><?php echo Lang::_('PRICE') ?></th>
		<th><?php echo Lang::_('quantity') ?></th>
		<th><?php echo Lang::_('remove from BASKET') ?></th>
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
		<td width="10%"><?php echo $F->draw_input_field('product_quantity[' . $product_key . ']', $product['quantity'], array('size' => '5')); ?></td>
		<td><?php echo $cell_remove_from_basket; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<table  style="border: 0; width: 100%;">
	<tr>
		<td align="left"><?php echo $basket_up . '&nbsp;' . $basket_down; ?></td>
		<td colspan="3">&nbsp;</td>
		<td align="right"><?php echo $basket_order . '&nbsp;' .  $update_basket ?></td>
	</tr>
	<tr>
		<td colspan="5"><?php //echo $SP->display_links(); ?></td>
	</tr>
</table>
<?php 
} else {
?>
</table>
<div class="basket_container container_subheader"><?php echo Lang::_('basket products'); ?><div class="icon"></div></div>
<?php
}
?>
<hr>
<div class="basket_container container_subheader"><?php echo Lang::_('basket history'); ?><div class="icon"></div></div>
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
<div class="basket_container container_subheader"><?php echo Lang::_('basket versions'); ?><div class="icon"></div></div>
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