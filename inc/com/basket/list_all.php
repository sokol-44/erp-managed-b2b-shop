<?php
//STR: tmp
//FIXME
$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();
$Shopping_Basket = $Shopping_Basket_Chain->return_default_basket();
// print_debug( array_keys( $Shopping_Basket_Chain->Basket_List ) );
$Price = Price::g_global();
//STR: end
// var_dump($Shopping_Basket);

if( $Shopping_Basket === FALSE ) die('sasa');

$total = $Shopping_Basket->calculate_total();

$Page->head_title = Lang::_('BASKET list');

echo Lang::_('Basket help for icons');
?>
<ul>
<li><?php echo $F->static_image('icon/delete_16.png', Lang::_('remove BASKET')) . ' - ' . Lang::_('remove BASKET'); ?></li>
<li><?php echo $F->static_image('icon/trash_16.png', Lang::_('clean product in BASKET')) . ' - ' . Lang::_('clean product in BASKET'); ?></li>
<li><?php echo $F->static_image('icon/up_16.png', Lang::_('switch working BASKET to this basket')) . ' - ' . Lang::_('switch working BASKET to this basket'); ?></li>
<li><?php echo $F->static_image('icon/add_up_16.png', Lang::_('add this basket to working BASKET')) . ' - ' . Lang::_('add this basket to working BASKET'); ?></li>
</ul>
<?php echo Lang::_('Basket help for colors');?>
<ul>
<li><p class="basket mainbasket"><?php echo Lang::_('current working basket');?></p></li>
<li><p class="basket"><?php echo Lang::_('normal basket');?></p></li>
<li><p class="basket usedbasket"><?php echo Lang::_('basket used by somebody else');?></p></li>
</ul>
<?php
// print_debug($Shopping_Basket);
// print_debug($P);
?>

<?php echo Lang::_('Basket list');?>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('BASKET NUMBER'); ?></th>
		<th><?php echo Lang::_('TOTAL PRODUCTS'); ?></th>
		<th><?php echo Lang::_('PRODUCTS TYPES'); ?></th>
		<th><?php echo Lang::_('sum gross'); ?></th>
		<th><?php echo Lang::_('sum netto'); ?></th>
		<th>kto</th>
		<th><?php echo Lang::_('available actions'); ?></th>
	</tr>
<?php
$GET_tmp = $F->make_get();
$GET_id = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_tmp);
$remove_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'remove_basket', $GET_id));
$remove_basket = $F->draw_link($remove_basket_link, 'title="' . Lang::_('remove BASKET') . '"', $F->static_image('icon/delete_16.png', Lang::_('remove BASKET')));
$clean_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'clean_basket', $GET_id));
$clean_basket = $F->draw_link($clean_basket_link, 'title="' . Lang::_('clean BASKET') . '"', $F->static_image('icon/trash_16.png', Lang::_('clean BASKET')));
$show_basket_link = $F->make_link(CFG_COM_BASKET, $GET_tmp);
$show_basket = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"', $F->static_image('icon/folder_16.png', Lang::_('show BASKET')));

$lock_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'lock_mainbasket', $GET_id));
$lock_basket = $F->draw_link($lock_basket_link, 'title="' . Lang::_('lock this BASKET') . '"', $F->static_image('icon/stock_lock_16.png', Lang::_('lock this BASKET')));

$available_actions = $addup_basket . $remove_basket . $clean_basket . $lock_basket;
?>
	<tr>
		<td class="mainbasket"><?php echo $Shopping_Basket->id_shopping_basket; ?></td>
		<td><?php echo $total['product_total']; ?></td>
		<td><?php echo $total['product_types']; ?></td>
		<td><?php echo Price::val($total['sum_gross']); ?></td>
		<td><?php echo Price::val($total['sum_netto']); ?></td>
		<td><?php echo $Shopping_Basket->params['id_client'] . ',' . $Shopping_Basket->params['using_id_client_user'] . ';<br>'
		. $Shopping_Basket->params['date_create'] . '=' . $Shopping_Basket->params['ts_create'] . ';<br>'
		. $Shopping_Basket->params['date_modified'] . '=' . $Shopping_Basket->params['ts_modified'] . ';<br>'
		. '"' . $Shopping_Basket->res_debug; ?></td>
		<td><div class="basket_menu"><?php echo $available_actions; ?></div></td>
	</tr>
<?php
$Shopping_Basket_Chain->reset_basket_list();

while( $Shopping_Basket = $Shopping_Basket_Chain->return_basket_next( true ) ) {
   $total = $Shopping_Basket->calculate_total();
   
   $currently_other_using = $Shopping_Basket->currently_other_using();
   
   $basket_params = $Shopping_Basket->params;
   
   if( $currently_other_using ) $class_add = ' class="usedbasket"';
   else $class_add = '';
   
   $GET_id = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_tmp);
   $remove_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'remove_basket', $GET_id));
   $remove_basket = $F->draw_link($remove_basket_link, 'title="' . Lang::_('remove BASKET') . '"', $F->static_image('icon/delete_16.png', Lang::_('remove BASKET')));
   $clean_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'clean_basket', $GET_id));
   $clean_basket = $F->draw_link($clean_basket_link, 'title="' . Lang::_('clean product in BASKET') . '"', $F->static_image('icon/trash_16.png', Lang::_('clean product in BASKET')));
   $switch_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'switch_basket', $GET_id));
   $switch_basket = $F->draw_link($switch_basket_link, 'title="' . Lang::_('switch working BASKET to this basket') . '"', $F->static_image('icon/up_16.png', Lang::_('switch working BASKET to this basket')));
   $addup_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'add_to_mainbasket', $GET_id));
   $addup_basket = $F->draw_link($addup_basket_link, 'title="' . Lang::_('add this basket to working BASKET') . '"', $F->static_image('icon/add_up_16.png', Lang::_('add this basket to working BASKET')));
   $lock_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'lock_mainbasket', $GET_id));
   $lock_basket = $F->draw_link($lock_basket_link, 'title="' . Lang::_('lock this BASKET') . '"', $F->static_image('icon/stock_lock_16.png', Lang::_('lock this BASKET')));
   $unlock_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'unlock_mainbasket', $GET_id));
   $unlock_basket = $F->draw_link($unlock_basket_link, 'title="' . Lang::_('unlock this BASKET') . '"', $F->static_image('icon/stock_lock_open_16.png', Lang::_('unlock this BASKET')));
   
   $rights['MODIFY_CONTENTS'] = $Shopping_Basket->check_rights('MODIFY_CONTENTS', false);
   //$rights['LOCK'] = $Shopping_Basket->check_rights('LOCK', false);
   if( $rights['MODIFY_CONTENTS'] ) {
      $available_actions = $addup_basket . $remove_basket . $clean_basket . $switch_basket . $lock_basket;
   } else {
      $available_actions = '' ;
   }
   
   
   
   ?>
	<tr>
		<td<?php echo $class_add; ?>><?php echo $Shopping_Basket->id_shopping_basket; ?></td>
		<td><?php echo $total['product_total']; ?></td>
		<td><?php echo $total['product_types']; ?></td>
		<td><?php echo Price::val($total['sum_gross']); ?></td>
		<td><?php echo Price::val($total['sum_netto']); ?></td>
		<td><?php echo $Shopping_Basket->params['id_client'] . ',' . $Shopping_Basket->params['using_id_client_user'] . ';<br>'
		. $Shopping_Basket->params['date_create'] . '=' . $Shopping_Basket->params['ts_create'] . ';<br>'
		. $Shopping_Basket->params['date_modified'] . '=' . $Shopping_Basket->params['ts_modified'] . ';<br>'
		. '"' . $Shopping_Basket->res_debug; ?></td>
		<td><div class="basket_menu"><?php echo $available_actions; ?></div></td>
	</tr>
<?php
}
?>
</table>