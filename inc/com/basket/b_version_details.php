<?php
if( $F->check_get('id_shopping_basket_version') ) {
   $id_shopping_basket_version = (int)$F->GET['id_shopping_basket_version'];
   $product_list = $Shopping_Basket->get_all_product_version( $id_shopping_basket_version );
} else {
   $F->redirect( $F->make_link(CFG_COM_BASKET));
}

$id_shopping_basket = $Shopping_Basket->id_shopping_basket;
$GET_tmp = $F->make_get();

if( $id_shopping_basket_version == $Shopping_Basket->params['id_shopping_basket_version'] ) {
	$F->redirect( $F->make_link(CFG_COM_BASKET, array('id_shopping_basket' => $id_shopping_basket)) );
}

$version_list = $Shopping_Basket->get_all_version();
$history_list = $Shopping_Basket->get_all_history();

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$Page->head_title = Lang::_('BASKET NR:') . ' ' . $id_shopping_basket . ' ' . Lang::_('VERSION') . ' ' . $id_shopping_basket_version;
// print_debug($Shopping_Basket);
?>
<div class="basket_container">
  <div class="basket_container basket_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="basket_container basket_content">
<table style="border: 0; width: 100%;">
	<tr>
		<td colspan="5"><?php //print_debug($product_list); ?></td>
	</tr>
	<tr>
		<td colspan="5">
      <?php echo Lang::_('basket description'); ?><br>
      <?php echo $F->draw_textarea_field('description', 'auto', '', 6, $Shopping_Basket->params['description'], array('readonly', 'disabled')); ?>
      </td>
	</tr>
</table>
<?php
if( $F->not_null($product_list) ) {
?>
<div class="basket_container container_subheader"><?php echo Lang::_('basket products'); ?><div class="icon"></div></div>
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
	   
	   $link_product_info = $F->make_link(CFG_COM_PRODUCT_INFO, $GET_product);

	   if( defined('SHOP_SHOW_PRODUCTS_DESCRIPTION_IN_LIST') && constant('SHOP_SHOW_PRODUCTS_DESCRIPTION_IN_LIST') == 'true')
	   	$description_html = str_replace('\n', "<br>\n", $F->output_string_html( $product['description'], 100 ) );
	   else $description_html = '';
	   $cell_product_info = $F->draw_link($link_product_info, '',
	   '<div class="catalog_product_name">' . $F->output_string_html( $product['name'] ) . '</div>
	   <div class="catalog_product_description">' . $description_html . '</div>');

	   $small_image_path = Data::get_product_image_path( $product['picture_small_url'] );
	   $si_oc = "$.colorbox({href:'" . Data::get_product_image_path( $product['picture_big_url'] ) . "', photo:true});";
	   $small_image_html = $F->static_image($small_image_path, Lang::_('show_big_image'), " onclick=\"$si_oc\"");;   
	   ?>
	<tr>
		<td style="cursor: pointer;" width="5%"><?php echo $F->draw_radio_field('list', $product_key, false, 'style="display: none"')
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
} else {
?>
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
	   
	   if ( $id_shopping_basket_version == $Shopping_Basket->params['id_shopping_basket_version'] ) {
	   	  $link_version_info = $F->make_link(CFG_COM_BASKET, $F->add_local_get('show', $GET_version));
	   } else {
          $GET_version = $F->add_local_get('id_shopping_basket_version', $version_key, $GET_version);
	   	  $link_version_info = $F->make_link(CFG_COM_BASKET, $F->add_local_get('show', 'version_details', $GET_version));
	   }
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
?>