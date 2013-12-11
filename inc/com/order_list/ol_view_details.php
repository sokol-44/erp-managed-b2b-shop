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
		<td><?php echo Lang::_('ID') ?></td>
		<td><?php echo Lang::_('order DESCRIPTION') . '<hr>' . Lang::_('basket DESCRIPTION') ?></td>
		<td><?php echo Lang::_('order date');  
		if($F->not_null($Order->data['date_modified'])) {?>
		<hr><?php echo Lang::_('order update') ?>
		<?php } ?></td>
		<td><?php echo Lang::_('order address'); ?></td>
		<td><?php echo Lang::_('order STATE') ?></td>
	</tr>
	<tr>
		<td><?php echo $F->output_string_html($Order->data['id_order']); ?></td>
		<td><?php echo nl2br($F->output_string_html( $Order->data['description'] ) ) . '<hr>' .
							nl2br($F->output_string_html( $Order->data['description_basket'] ) ) ?></td>
		<td><?php echo $F->output_string_html($Order->data['date_create']);
		if($F->not_null($Order->data['date_modified'])) {
			echo '<hr>' . $F->output_string_html($Order->data['date_modified']) . '';
		} ?>
		<td>
		<?php
		$addr_id = (int)$Order->get_address_id();
		if( $addr_id == 0 ) {
			$addr_desc = Lang::_('EMPTY_OR_INCORECT_ADDRESS');
			$addr_id = false;
		} elseif( $addr_id < 0 ) {
			$Address = Data::additional_addreses($addr_id, $P->data);
			if( $F->is_null($Address) ) {
				$addr_desc = Lang::_('EMPTY_OR_INCORECT_ADDRESS');
				$addr_id = false;
			}
		} else {
			$Address = $Order->get_address();
		}
		
		if( $addr_id ) {
			$addr_desc = $F->output_string($Address['description']);
			$addr_addr = $F->output_string($Address['name']) . '<br>' .
					$F->output_string($Address['street']) . '<br>' .
					$F->output_string($Address['zip_code'] . ' ' . $Address['city']) . '<br>' .
					$F->output_string($Address['country']);
		}
		?>
		<div class="address order_address" style="width: 160px">
		<div class=""><?php echo Lang::_('description') . ': ' . $addr_desc ?></div>
		<?php if( $addr_id ) { ?>
		<div class=""><?php echo Lang::_('address') . ":<br>\n" . $addr_addr ?></div>
		<?php } ?>
		</div></td>
		<td><?php echo $F->output_string_html(Lang::_($Order->data['name'])); ?></td>
	</tr>
</table>
<?php 
$attributes = $Order->get_attributes();
if( $F->not_null($attributes) ){ 
	$attr_pm = $Order->get_attribute_val('PAYMENT_METHOD');
	$attr_dp = $Order->get_attribute_val('DELIVERY_PARTIAL');
	$attr_dd = $Order->get_attribute_val('DELIVERY_DATE');

	if( $F->not_null($attr_pm) ) {
		$attr_pm_txt = Lang::_($attr_pm);
	} else {
		$attr_pm_txt = '-';
	}
	if( $F->not_null($attr_dp) ) {
		$attr_dp_txt = Lang::_($attr_dp);
	} else {
		$attr_dp_txt = '-';
	}
	if( $F->not_null($attr_dd) ) {
		$attr_dd_txt = $attr_dd;
	} else {
		$attr_dd_txt = '-';
	}


$GET_tmp = $F->make_get('mode,action,show');
$add_basket = $F->draw_link(
		$F->make_link(CFG_COM_ORDER_LIST, $F->add_local_get('mode', 'make_basket', $GET_tmp)), 'title="' . Lang::_('Create BASKET') . '"',
		$F->dynamic_image(Lang::_('Create BASKET')) );
echo $add_basket;	
?>
<div class="basket_container container_subheader"><?php echo Lang::_('Order attributes'); ?><div class="icon"></div></div>
<table class="tableBox" style="border: 0; width: 100%">
<tr class="tableBoxHeading">
	<th><?php echo Lang::_('PAYMENT_METHOD') ?></th>
	<th><?php echo Lang::_('DELIVERY_PARTIAL')?></th>
	<th><?php echo Lang::_('DELIVERY_DATE') ?></th>
	<!-- <th><?php echo Lang::_('DELIVERY_PERSONAL') ?></th> -->
</tr>
<tr>
	<td width="5%"><?php  echo $attr_pm_txt; ?></td>
	<td width="10%"><?php echo $attr_dp_txt; ?></td>
	<td width="10%"><?php echo $attr_dd_txt ?></td>
	<!-- <td width="10%"><?php echo ''; ?></td>  -->
</tr>
</table>
<?php } ?>
<div class="basket_container container_subheader"><?php echo Lang::_('Order products'); ?><div class="icon"></div></div>
<table class="tableBox" style="border: 0; width: 100%">
	<tr class="tableBoxHeading">
		<!-- <th><?php echo Lang::_('PICTURE') ?></th>  -->
		<th><?php echo Lang::_('NAME') . ', ' . Lang::_('DESCRIPTION')?></th>
		<th><?php echo Lang::_('PRICE') ?></th>
		<th><?php echo Lang::_('quantity') ?></th>
	</tr>
	<?php
	foreach( $Order->product_list as $product ) {
	   $GET_tmp = $F->add_local_get('id_product', $product['id_product'], $GET_tmp);

	   if( $F->is_null($product['p_name']) ) {
	      $product_name = $F->output_string_html( $product['p_name'] ) . ' <STRIKE>' . $F->output_string_html( $product['name'] ) . '</STRIKE>';
	   } else {
	      $product_name = $F->output_string_html( $product['name'] );
	   }
	   
	   if( defined('SHOP_SHOW_PRODUCTS_DESCRIPTION_IN_LIST') && constant('SHOP_SHOW_PRODUCTS_DESCRIPTION_IN_LIST') == 'true')
	   	$description_html = str_replace('\n', "<br>\n", $F->output_string_html( $product['description'], 100 ) );
	   else $description_html = '';
	   $name_desc_cell = '<div class="catalog_product_name">' . $product_name . '</div>
	   <div class="catalog_product_description">' . $description_html . '</div>';

	   $link_product_info = $F->make_link(CFG_COM_PRODUCT_INFO, $GET_tmp);

	    
	   if( $F->is_null($product['p_name']) ) {
	      $cell_product_info = $name_desc_cell;
	      $small_image_html = Lang::_('PRODUCT INACTIVE/REMOVED');;
	   } else {
	      $cell_product_info = $F->draw_link($link_product_info, '', $name_desc_cell);
			$image_type = Data::get_product_image_type($product);
			$small_image_html='';
	      if( $F->not_null($image_type) ) {
				$Page->add_js_file('jquery.colorbox.js');
				$small_image_html = '<img src="' . $image_type['small_image_path'] .'">';
				$small_image_html = '<div class="product_image_' . (int)$product['id_product'] . '">' . $small_image_html . "</div>\n";
				$Page->add_jq_init('$(".product_image_' . (int)$product['id_product'] . '").colorbox({
			   	href:"' . $F->js_escape($image_type['big_image_path']) . '",
			   	photo:true});');
			}
	   }

	   ?>
	<tr>
		<!-- <td style="cursor: pointer;" width="5%"><?php echo $small_image_html; ?></td>  -->
		<td width="70%" valign="top"><?php echo $F->draw_radio_field('list', $product['id_product'], false, 'style="display: none"') . $cell_product_info; ?></td>
		<td width="30%"><?php echo Price::val( $product['price'] ) . '<br>(' . Price::tax( $product['vat'] ) . ')'; ?></td>
		<td width="10%"><?php echo $product['quantity']; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<?php
if( defined('SHOP_BASKET_SHOW_VERSIONS') && constant('SHOP_BASKET_SHOW_VERSIONS') == 'true' ) {
?>
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
<?php 
}

if( defined('SHOP_BASKET_SHOW_HISTORY') && constant('SHOP_BASKET_SHOW_HISTORY') == 'true' ) {
?>
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
}
?>

  </div>
  <div class="order_container order_bottom container_bottom"></div>
</div>