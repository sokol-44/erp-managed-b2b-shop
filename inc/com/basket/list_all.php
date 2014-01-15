<?php
//STR: tmp
$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();
$Shopping_Basket = $Shopping_Basket_Chain->return_default_basket();
// print_debug( array_keys( $Shopping_Basket_Chain->Basket_List ) );
$Price = Price::g_global();
//STR: end
// var_dump($Shopping_Basket);

if( $Shopping_Basket === FALSE ) die('sasa');

$total = $Shopping_Basket->calculate_total();
// $info = Info::g_global();
// print_debug($info);
$Page->head_title = Lang::_('BASKET list');
?>
<div class="basket_list_container">
  <div class="basket_list_container basket_list_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="basket_list_container basket_list_content">
<?php echo Lang::_('Basket list');?>
<table class="tableBox">
	<tr class="tableBoxHeading">
		<th width="5%"><?php echo Lang::_('BASKET NUMBER'); ?></th>
		<th width="5%"><?php echo Lang::_('STATE'); ?></th>
		<th width="5%"><?php echo Lang::_('TOTAL PRODUCTS'); ?></th>
		<th width="5%"><?php echo Lang::_('PRODUCTS TYPES'); ?></th>
		<th width="15%"><?php echo Lang::_('sum gross'); ?></th>
		<th width="15%"><?php echo Lang::_('sum netto'); ?></th>
		<th width="5%"><?php echo Lang::_('WHO_IS_USING'); ?></th>
		<th width="15%"><?php echo Lang::_('available actions'); ?></th>
	</tr>
	<?php
	$GET_tmp = $F->make_get();
	$GET_id = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_tmp);
	if( !empty($F->GET['show']) ) $GET_id = $F->add_local_get('show', $F->GET['show'], $GET_id);
	$remove_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'remove_basket', $GET_id));
	$remove_basket = $F->draw_link($remove_basket_link, 'title="' . Lang::_('remove BASKET') . '"', $F->static_image('icon/delete_16.png', Lang::_('remove BASKET')));
	$clean_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'clean_basket', $GET_id));
	$clean_basket = $F->draw_link($clean_basket_link, 'title="' . Lang::_('clean BASKET') . '"', $F->static_image('icon/trash_16.png', Lang::_('clean BASKET')));
	$show_basket_link = $F->make_link(CFG_COM_BASKET, $GET_tmp);
	$show_basket = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"', $F->static_image('icon/folder_16.png', Lang::_('show BASKET')));

	$lock_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'lock_basket', $GET_id));
	$lock_basket = $F->draw_link($lock_basket_link, 'title="' . Lang::_('lock this BASKET') . '"', $F->static_image('icon/stock_lock_16.png', Lang::_('lock this BASKET')));
	$unlock_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'unlock_basket', $GET_id));
	$unlock_basket = $F->draw_link($unlock_basket_link, 'title="' . Lang::_('unlock this BASKET') . '"', $F->static_image('icon/stock_lock_open_16.png', Lang::_('unlock this BASKET')));

	$show_basket_link = $F->make_link(CFG_COM_BASKET, array('id_shopping_basket' => $Shopping_Basket->id_shopping_basket));
	$show_basket = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"', $Shopping_Basket->id_shopping_basket);
	
	$currently_user_locked  = $Shopping_Basket->currently_user_locked();
	if( $currently_user_locked ) {
	   $class_add = ' class="lockedbasket_user"';
	   $lock_unlock = $unlock_basket;
	} else {
	   $class_add = ' class="mainbasket"';
	   $lock_unlock = $lock_basket;
	    
	}


	$rights['LOCK'] = $Shopping_Basket->check_rights('LOCK', false);
	$rights['UNLOCK'] = $Shopping_Basket->check_rights('UNLOCK', false);

	$available_actions = $addup_basket . $remove_basket . $clean_basket . $lock_unlock;
	$state_html = '0<br>' . $Shopping_Basket->basket_level_text();
	$basket_using_name = (($Shopping_Basket->params['using_name']!=''?$Shopping_Basket->params['using_name']:''));
	?>
	<tr>
		<td class="mainbasket"><?php echo $show_basket; ?>
		</td>
		<td <?php echo $class_add; ?>><?php echo $state_html; ?></td>
		<td><?php echo $total['product_total']; ?></td>
		<td><?php echo $total['product_types']; ?></td>
		<td><?php echo Price::val($total['sum_gross']); ?></td>
		<td><?php echo Price::val($total['sum_netto']); ?></td>
		<td><?php echo $basket_using_name ?></td>
		<td><div class="basket_menu">
				<?php echo $available_actions; ?>
			</div></td>
	</tr>
	<?php
	$Shopping_Basket_Chain->reset_basket_list();

	while( $Shopping_Basket = $Shopping_Basket_Chain->return_basket_next( true ) ) {
	   $total = $Shopping_Basket->calculate_total();
	   $state_html = '';
	   $class_add = '';
	   $available_actions = '';
	    
	   $basket_lvl_diff = $Shopping_Basket->basket_level_rights();

	   if( (int)$basket_lvl_diff == 0 ) {

	      $currently_other_using  = $Shopping_Basket->currently_other_using();
	      $currently_other_locked = $Shopping_Basket->currently_other_locked();
	      $currently_user_locked  = $Shopping_Basket->currently_user_locked();

	      if( $currently_other_locked ) $class_add = ' class="lockedbasket_other"';
	      elseif( $currently_user_locked ) $class_add = ' class="lockedbasket_user"';
	      elseif( $currently_other_using ) $class_add = ' class="usedbasket"';

	      $GET_id = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_tmp);
	      $remove_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'remove_basket', $GET_id));
	      $remove_basket = $F->draw_link($remove_basket_link, 'title="' . Lang::_('remove BASKET') . '"', $F->static_image('icon/delete_16.png', Lang::_('remove BASKET')));
	      $clean_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'clean_basket', $GET_id));
	      $clean_basket = $F->draw_link($clean_basket_link, 'title="' . Lang::_('clean product in BASKET') . '"', $F->static_image('icon/trash_16.png', Lang::_('clean product in BASKET')));
	      $switch_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'switch_basket', $GET_id));
	      $switch_basket = $F->draw_link($switch_basket_link, 'title="' . Lang::_('switch working BASKET to this basket') . '"', $F->static_image('icon/up_16.png', Lang::_('switch working BASKET to this basket')));
	      $addup_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'add_to_mainbasket', $GET_id));
	      $addup_basket = $F->draw_link($addup_basket_link, 'title="' . Lang::_('add this basket to working BASKET') . '"', $F->static_image('icon/add_up_16.png', Lang::_('add this basket to working BASKET')));
	      $lock_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'lock_basket', $GET_id));
	      $lock_basket = $F->draw_link($lock_basket_link, 'title="' . Lang::_('lock this BASKET') . '"', $F->static_image('icon/stock_lock_16.png', Lang::_('lock this BASKET')));
	      $unlock_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'unlock_basket', $GET_id));
	      $unlock_basket = $F->draw_link($unlock_basket_link, 'title="' . Lang::_('unlock this BASKET') . '"', $F->static_image('icon/stock_lock_open_16.png', Lang::_('unlock this BASKET')));

	      $show_basket_link = $F->make_link(CFG_COM_BASKET, array('id_shopping_basket' => $Shopping_Basket->id_shopping_basket));
	      $show_basket = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"', $Shopping_Basket->id_shopping_basket);
	       
	      $rights['MODIFY_CONTENTS'] = $Shopping_Basket->check_rights('MODIFY_CONTENTS', false);
	      if( $rights['MODIFY_CONTENTS'] ) {
	         $available_actions = $addup_basket . $remove_basket . $clean_basket;
	         $rights['LOCK'] = $Shopping_Basket->check_rights('LOCK', false);
	         $rights['UNLOCK'] = $Shopping_Basket->check_rights('UNLOCK', false);
	         $rights['USE'] = $Shopping_Basket->check_rights('USE', false);
	          
	         if( $rights['USE'] ) $available_actions .= $switch_basket;
	         if( $rights['LOCK'] ) $available_actions .= $lock_basket;
	         if( $rights['UNLOCK'] ) $available_actions .= $unlock_basket;
	      }
	   } else {
	      $class_add = ' class="na_basket"';
	      $show_basket = $Shopping_Basket->id_shopping_basket;
	   }
	   $state_html = $Shopping_Basket->basket_level_nr() .'(' . $basket_lvl_diff . ')<br>' .
	     	   $Shopping_Basket->basket_level_text();
	   $basket_using_name = (($Shopping_Basket->params['using_name']!=''?$Shopping_Basket->params['using_name']:''));
	   ?>
	<tr>
		<td <?php echo $class_add; ?>><?php echo $show_basket; ?>
		</td>
		<td <?php echo $class_add; ?>><?php echo $state_html; ?></td>
		<td><?php echo $total['product_total']; ?></td>
		<td><?php echo $total['product_types']; ?></td>
		<td><?php echo Price::val($total['sum_gross']); ?></td>
		<td><?php echo Price::val($total['sum_netto']); ?></td>
		<td><?php echo $basket_using_name ?></td>
		<td><div class="basket_menu">
				<?php echo $available_actions; ?>
			</div></td>
	</tr>
	<?php
}
?>
</table>
<?php
echo Lang::_('Basket help for icons');
?>
<ul>
	<li><?php echo $F->static_image('icon/delete_16.png', Lang::_('remove BASKET')) . ' - ' . Lang::_('remove BASKET'); ?>
	</li>
	<li><?php echo $F->static_image('icon/trash_16.png', Lang::_('clean product in BASKET')) . ' - ' . Lang::_('clean product in BASKET'); ?>
	</li>
	<li><?php echo $F->static_image('icon/up_16.png', Lang::_('switch working BASKET to this basket')) . ' - ' . Lang::_('switch working BASKET to this basket'); ?>
	</li>
	<li><?php echo $F->static_image('icon/add_up_16.png', Lang::_('add this basket to working BASKET')) . ' - ' . Lang::_('add this basket to working BASKET'); ?>
	</li>
	<li><?php echo $F->static_image('icon/stock_lock_16.png', Lang::_('lock this BASKET')) . ' - ' . Lang::_('lock this BASKET'); ?>
	</li>
	<li><?php echo $F->static_image('icon/stock_lock_open_16.png', Lang::_('unlock this BASKET')) . ' - ' . Lang::_('unlock this BASKET'); ?>
	</li>
</ul>
<?php echo Lang::_('Basket help for colors');?>
<ul>
	<li><p class="basket mainbasket">
			<?php echo Lang::_('current working basket');?>
		</p></li>
	<li><p class="basket">
			<?php echo Lang::_('normal basket');?>
		</p></li>
	<li><p class="basket usedbasket">
			<?php echo Lang::_('basket used by somebody else');?>
		</p></li>
	<li><p class="basket lockedbasket_user">
			<?php echo Lang::_('basket locked by you');?>
		</p></li>
	<li><p class="basket lockedbasket_other">
			<?php echo Lang::_('basket locked by somebody else');?>
		</p></li>
</ul>
<?php
// print_debug($Shopping_Basket);
// print_debug($P);
?>
  </div>
  <div class="basket_list_container basket_list_bottom container_bottom"></div>
</div>