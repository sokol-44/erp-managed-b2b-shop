<?php

$id_shopping_basket_version = (int)$Shopping_Basket->id_shopping_basket_version;
$product_list = $Shopping_Basket->get_all_product();

if( defined('SHOP_BASKET_SHOW_VERSIONS') && constant('SHOP_BASKET_SHOW_VERSIONS') == 'true' )
	$version_list = $Shopping_Basket->get_all_version();

if( defined('SHOP_BASKET_SHOW_HISTORY') && constant('SHOP_BASKET_SHOW_HISTORY') == 'true' )
	$history_list = $Shopping_Basket->get_all_history();


$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$Page->head_title = Lang::_('BASKET_ORDER') . ' (' . Lang::_('basket nr:') . ' ' . $Shopping_Basket->id_shopping_basket . ')';
// print_debug($Shopping_Basket);

$GET_id = $F->make_get('mode');
$GET_id  = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_id);
$GET_id  = $F->add_local_get('mode', 'order_basket', $GET_id);

$form_link = $F->make_link(CFG_COM_ORDER_BASKET, $GET_id);

// $values (id, text)
foreach(Data::$Data_order_params['PAYMENT_METHOD'] as $method) {
	$values_pm[] = array('id' => $method, 'text' => Lang::_($method) );
}

$Page->add_jq_init('
$.datepicker.regional[ "pl" ];
$("#attr_DELIVERY_DATE").datepicker({
	numberOfMonths: 2,
	showButtonPanel: true,
	minDate: 0, 
	maxDate: "+2M",
	dateFormat: "yy-mm-dd",
	regional: "en"
});');

//$Page->add_js_file('jquery-ui-i18n.min.js');

echo $F->draw_form('prepare_order_basket', $form_link);
?>
<script>
function remove_from_basked() { return true; }
</script>
<div class="prepare_order_container">
  <div class="prepare_order_container prepare_order_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="prepare_order_container prepare_order_content">
<table class="basket_container basket_tools">
	<tr>
		<td colspan="5">
      <?php echo Lang::_('order description'); ?><br>
      <?php echo $F->draw_textarea_field('order_description', 'auto', '', 6); ?>
      </td>
	</tr>
	<tr>
		<td colspan="5">
  <table class="tableBox" style="border: 0; width: 100%">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('PAYMENT_METHOD') ?></th>
		<th><?php echo Lang::_('DELIVERY_PARTIAL')?></th>
		<th><?php echo Lang::_('DELIVERY_DATE') ?></th>
		<!-- <th><?php echo Lang::_('DELIVERY_PERSONAL') ?></th> -->
	</tr>
	<tr>
		<td width="5%"><?php  echo $F->draw_pull_down_menu('attr_PAYMENT_METHOD', $values_pm); ?></td>
		<td width="10%"><?php echo $F->draw_input_field('attr_DELIVERY_PARTIAL', 'YES', '', 'checkbox'); ?></td>
		<td width="10%"><?php echo $F->draw_input_field('attr_DELIVERY_DATE', '', ' style="width: 120px"', 'text'); ?></td>
		<!-- <td width="10%"><?php echo $F->draw_input_field('attr_DELIVERY_PERSONAL', '', '', 'checkbox'); ?></td>  -->
	</tr>
	</table>
	   </td>
	</tr>
	<tr>
		<td colspan="3">
      <?php echo Lang::_('choose address'); ?><br>
      <?php 
      $Address_list = $P->get_address_list();
		$history_last = $Shopping_Basket->get_last_history();
		$default = $P->get_default_address_id();
      if( $F->not_null( $Address_list ) ) {
			$addr = array();
			foreach($Address_list as $id_address => $Address) {
				$addr[$id_address]['id'] = (int)$Address['id_address'];
				$addr[$id_address]['text'] =  $F->output_string_html(
															$F->output_string_html($Address['description'], 16) . '; ' .
															$F->output_string($Address['name']) . '; ' .
															$F->output_string($Address['street']) . '; ' .
															$F->output_string($Address['zip_code'] . ' ' . $Address['city']) . '; ' .
															$F->output_string($Address['country']), 90);
			}

			echo $F->draw_pull_down_menu('order_address', $addr, $default);
		} else {
			echo Lang::_('default address');
		}
      ?>
      </td>
		<td colspan="2">
      <?php echo Lang::_('choose account menager'); ?><br>
      <?php
      $AccountMenager_list = $P->get_account_manager_list();
      //$history_last = $Shopping_Basket->get_last_history();
		$default = $P->get_default_manager_id();
      if( $F->not_null( $AccountMenager_list ) ) {
      	$aqmg = array();
      	foreach($AccountMenager_list as $id_account_manager => $AccountMenager) {
      		$aqmg[$id_account_manager]['id'] = (int)$AccountMenager['id_account_manager'];
      		$aqmg[$id_account_manager]['text'] =  $F->output_string_html($AccountMenager['fullname']);
      	}
         //FIXME
      	echo $F->draw_pull_down_menu('account_manager', $aqmg, $default);
      } else {
      	echo Lang::_('default address');
      }
      ?>
      </td>
	</tr>
	<tr>
		<td width="100%" colspan="4"></td>
		<td align="right"><?php echo $F->draw_submit(Lang::_('ORDER_BASKET')); ?></td>
	</tr>
	<tr>
		<td colspan="5">
      <?php echo Lang::_('basket description'); ?><br>
      <?php echo $F->draw_textarea_field('description', 'auto', '', 6, $Shopping_Basket->params['description'], array('readonly', 'style'=>'background-color: lightgrey;')); ?>
      </td>
	</tr>
</table>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<!-- <th><?php echo Lang::_('PICTURE') ?></th> -->
		<th><?php echo Lang::_('NAME') . ', ' . Lang::_('DESCRIPTION')?></th>
		<th><?php echo Lang::_('PRICE') ?></th>
		<th><?php echo Lang::_('quantity') ?></th>
	</tr>
	<?php
	foreach( $product_list as $product_key => $product ) {
	   $GET_product = $F->add_local_get('product_key', $product_key, $GET_tmp);
	   
	   if( defined('SHOP_SHOW_PRODUCTS_DESCRIPTION_IN_LIST') && constant('SHOP_SHOW_PRODUCTS_DESCRIPTION_IN_LIST') == 'true')
	   	$description_html = str_replace('\n', "<br>\n", $F->output_string_html( $product['description'], 100 ) );
	   else $description_html = '';
	   $link_product_info = $F->make_link(CFG_COM_PRODUCT_INFO, $GET_product);
	   $cell_product_info = $F->draw_link($link_product_info, '',
	   '<div class="catalog_product_name">' . $F->output_string_html( $product['name'] ) . '</div>
	   <div class="catalog_product_description">' . $description_html . '</div>');

	   $small_image_path = Data::get_product_image_path( $product['picture_small_url'] );
	   $si_oc = "$.colorbox({href:'" . Data::get_product_image_path( $product['picture_big_url'] ) . "', photo:true});";
	   $small_image_html = $F->static_image($small_image_path, Lang::_('show_big_image'), " onclick=\"$si_oc\"");;
	   ?>
	<tr>
		<!-- <td style="cursor: pointer;" width="5%"><?php echo $small_image_html; ?></td>  -->
		<td width="10%" valign="top"><?php echo $F->draw_radio_field('list', $product_key, false, 'style="display: none"') . $cell_product_info; ?></td>
		<td width="10%"><?php echo Price::val( $product['price'] ) . '<br>(' . Price::tax( $product['vat'] ) . ')'; ?></td>
		<td width="10%"><?php echo $product['quantity']; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<?php 
if( defined('SHOP_BASKET_SHOW_VERSIONS') && constant('SHOP_BASKET_SHOW_VERSIONS') == 'true' ) {
?>
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
<?php 
}
if( defined('SHOP_BASKET_SHOW_HISTORY') && constant('SHOP_BASKET_SHOW_HISTORY') == 'true' ) {
?>
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
<?php 
}
?>
  </div>
  <div class="prepare_order_container prepare_order_bottom container_bottom"></div>
</div>
<?php
echo $F->draw_form_close();
?>