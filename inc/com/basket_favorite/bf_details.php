<?php
$product_list = $Shopping_Basket_Favorite->product_list;
$id_shopping_basket_favorite = (int)$Shopping_Basket_Favorite->id_shopping_basket_favorite;

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$Page->head_title = Lang::_('BASKET NR:') . ' ' . $id_shopping_basket_favorite ;
// print_debug($Shopping_Basket);
$GET_tmp = $F->make_get();
$form_link = $F->make_link(CFG_COM_BASKET_FAVORITE, $F->add_local_get('mode', 'update_basket', $GET_tmp));

$GET_id  = $F->add_local_get('id_shopping_basket_favorite', $id_shopping_basket_favorite, $GET_tmp);


$basket_order = ''; $basket_up = ''; $basket_down = '';

$remove_basket_link = $F->make_link(CFG_COM_BASKET_FAVORITE, $F->add_local_get('action', 'remove_basket', $GET_id));
$remove_basket = $F->draw_link($remove_basket_link, 'title="' . Lang::_('remove BASKET') . '"', $F->static_image('icon/up_32.png', Lang::_('CHANGE_LEVEL_UP')));

$arg = array('mode' => 'make_basket', 'id_shopping_basket_favorite' => $id_shopping_basket_favorite);
$make_basket = $F->draw_link(
		$F->make_link(CFG_COM_BASKET_FAVORITE, $arg), 'title="' . Lang::_('create BASKET') . '"',
		$F->static_image('icon/folder_32.png') );

?>
<div class="basket_container">
  <div class="basket_container basket_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="basket_container basket_content">
<script>
function remove_from_basked() { return true; }
</script>
<table class="basket_container basket_tools">
	<tr>
		<td colspan="5"><?php //print_debug($product_list); ?></td>
	</tr>
	<tr>
		<td colspan="5">
      <?php echo Lang::_('basket description'); ?><br>
      <?php echo $F->draw_textarea_field('description', 'auto', '', 6, $Shopping_Basket->params['description'], array('readonly', 'disabled')); ?>
      </td>
	</tr>
<?php
if( $F->not_null($product_list) ) {
?>
	<tr>
		<td colspan="4">&nbsp;</td>
		<td align="right"><?php echo $make_basket; ?></td>
	</tr>
</table>
<div class="basket_container container_subheader"><?php echo Lang::_('basket products'); ?><div class="icon"></div></div>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<!-- <th><?php echo Lang::_('PICTURE') ?></th> -->
		<th><?php echo Lang::_('NAME') . ', ' . Lang::_('DESCRIPTION')?></th>
		<th><?php echo Lang::_('PRICE') ?></th>
		<th><?php echo Lang::_('QUANTITY_IN_WAREHAUSE') ?></th>
		<th><?php echo Lang::_('quantity') ?></th>
		<!-- <th><?php echo Lang::_('remove from BASKET') ?></th>-->
	</tr>
	<?php
	foreach( $product_list as $product_key => $product ) {
		$GET_tmp = $F->add_local_get('id_product', $product['id_product'], $GET_tmp);
		
		$product_info = Data::get_product_info( (int)$product['id_product'] );

		if( $F->is_null($product_info) || $product_info['name'] == 'NA' ) {
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
		
		if( $F->is_null($product_info) || $product_info['name'] == 'NA' ) {
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
		<!-- <td style="cursor: pointer;" width="5%"><?php echo $small_image_html; ?></td> -->
		<td  width="5%" valign="top"><?php echo $F->draw_radio_field('list', $product['id_product'], false, 'style="display: none"')
		. $cell_product_info; ?></td>
		<td width="10%"><?php echo Price::val( $product['price'] ) . '<br>(' . Price::tax( $product['vat'] ) . ')'; ?></td>
		<td width="5%"><?php echo (int)$product_info['quantity']; ?></td>
		<td width="10%"><?php echo (int)$product['quantity']; ?></td>
	</tr>
	<?php
	}
	?>
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
  </div>
  <div class="basket_container basket_bottom container_bottom"></div>
</div>
<?php
echo $F->draw_form_close();
?>