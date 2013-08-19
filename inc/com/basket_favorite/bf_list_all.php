<?php
//STR: tmp
$Price = Price::g_global();
//STR: end
// var_dump($Shopping_Basket);

$Page->head_title = Lang::_('favorite BASKET list');
?>
<div class="basket_list_container">
  <div class="basket_list_container basket_list_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="basket_list_container basket_list_content">
<?php echo Lang::_('Basket list');?>
<table class="tableBox">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('BASKET NUMBER'); ?></th>
		<th><?php echo Lang::_('TOTAL PRODUCTS'); ?></th>
		<th><?php echo Lang::_('PRODUCTS TYPES'); ?></th>
		<th><?php echo Lang::_('Description'); ?></th>
		<th><?php echo Lang::_('sum gross'); ?></th>
		<th><?php echo Lang::_('sum netto'); ?></th>
	</tr>
	<?php
	//$Shopping_Basket_Chain->reset_basket_list();

	foreach( $basket_favorite_list as $key => $Shopping_Basket_Favorite ) {
	   //$total = $Shopping_Basket->calculate_total();
	   //print_debug($Shopping_Basket_Favorite);
	   $total = $Shopping_Basket_Favorite->calculate_total();
	   $description = $F->output_string_html( $Shopping_Basket_Favorite->params['description'] );
	   
	   $id = (int)$Shopping_Basket_Favorite->id_shopping_basket_favorite;
	   //$basket_lvl_diff = $Shopping_Basket->basket_level_rights();
	   $show_basket_link = $F->make_link(CFG_COM_BASKET_FAVORITE, array('mode' => 'show_details', 'id_shopping_basket_favorite' => $id));
	   $show_basket = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"',$id);
	   ?>
	<tr>
		<td><?php echo $show_basket ?></td>
		<td><?php echo $total['product_total']; ?></td>
		<td><?php echo $total['product_types']; ?></td>
		<td><?php echo $description ?></td>
		<td><?php echo Price::val($total['sum_gross']); ?></td>
		<td><?php echo Price::val($total['sum_netto']); ?></td>
	</tr>
	<?php
}
?>
</table>
  </div>
  <div class="basket_list_container basket_list_bottom container_bottom"></div>
</div>