<?php
//STR: tmp
//FIXME
$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();
$Shopping_Basket = $Shopping_Basket_Chain->return_default_basket();
$Price = Price::g_global();
//STR: end

$total = $Shopping_Basket->calculate_total();

$GET_tmp = $F->make_get('mode,action,show');

//$all_basket = $F->draw_link( $F->make_link(CFG_COM_BASKET, array('show' => 'all')), 'title="' . Lang::_('show all BASKETS') . '"',
//      $F->static_image('icon/folder_16.pnb2b Lang::_('show all BASKETS')) . ' ' . Lang::_('show all BASKETS') . ' ' . $F->static_image('icon/folder_16.png', Lang::_('show all BASKETS')));

$all_basket = $F->draw_link( 
		$F->make_link(CFG_COM_BASKET, array('show' => 'all')), 'title="' . Lang::_('show all BASKETS') . '"',
		$F->dynamic_image(Lang::_('show all')) );
?>
<div class="basket_container">
<div class="basket menu_header"><?php echo Lang::_('BASKETS') ?><div class="icon"></div></div>
<div class="basket basket_show_all"><?php echo $all_basket; ?></div>
<?php
echo '<script>json_data.basket_list_default="'.$F->json_string($Shopping_Basket->params).'";</script>';
$GET_id = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_tmp);
$remove_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'remove_basket', $GET_id));
$remove_basket = $F->draw_link($remove_basket_link, 'title="' . Lang::_('remove BASKET') . '"', $F->static_image('icon/delete_16.png', Lang::_('remove BASKET')));
$clean_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'clean_basket', $GET_id));
$clean_basket = $F->draw_link($clean_basket_link, 'title="' . Lang::_('clean BASKET') . '"', $F->static_image('icon/trash_16.png', Lang::_('clean BASKET')));
$show_basket_link = $F->make_link(CFG_COM_BASKET, $GET_id);
$show_basket = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"', $F->static_image('icon/folder_16.png', Lang::_('show BASKET')));
$desc_basket = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"', 
	Lang::_('BASKET') . ' <span>' . (int)$Shopping_Basket->id_shopping_basket . '</span>');
?>
<div class="basket mainbasket" id="basket_prev_<?php echo (int)$Shopping_Basket->id_shopping_basket; ?>">
<div class="basket_menu"><?php echo $show_basket . $remove_basket . $clean_basket; ?></div>
<div class="basket_single basket_number"><?php echo $desc_basket ?></div>
<div class="basket_single basket_product_total"><?php echo Lang::_('TOTAL PRODUCTS'); ?><span id="nr"><?php echo $total['product_total']; ?></span></div>
<div class="basket_single basket_product_types"><?php echo Lang::_('PRODUCTS TYPES'); ?><span id="nr"><?php echo $total['product_types']; ?></span></div>
<div class="basket_single basket_sum_gross"><?php echo Lang::_('sum gross'); ?><span id="nr"><?php echo Price::val($total['sum_gross']); ?></span></div>
<div class="basket_single basket_sum_netto"><?php echo Lang::_('sum_netto'); ?><span id="nr"><?php echo Price::val($total['sum_netto']); ?></span></div>
</div>
<script>json_data.basket_list = new Array();</script>
<?php
$Shopping_Basket_Chain->reset_basket_list();

while( $Shopping_Basket = $Shopping_Basket_Chain->return_basket_next( true ) ) {
   $total = $Shopping_Basket->calculate_total();
   
$total = $Shopping_Basket->calculate_total();
	   $state_html = '';
	   $class_add = ' cleanstate_basket';
	   $available_actions = '';
	    
	   $basket_lvl_diff = $Shopping_Basket->basket_level_rights();

	   if( (int)$basket_lvl_diff == 0 ) {

	      $currently_other_using  = $Shopping_Basket->currently_other_using();
	      $currently_other_locked = $Shopping_Basket->currently_other_locked();
	      $currently_user_locked  = $Shopping_Basket->currently_user_locked();

	      if( $currently_other_locked ) $class_add = ' lockedbasket_other';
	      elseif( $currently_user_locked ) $class_add = ' lockedbasket_user';
	      elseif( $currently_other_using ) $class_add = ' usedbasket';

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

	      $show_basket_link = $F->make_link(CFG_COM_BASKET, $GET_id);
	      $show_basket = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"', $Shopping_Basket->id_shopping_basket);
	      $desc_basket = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"',
	      		Lang::_('BASKET') . ' <span>' . (int)$Shopping_Basket->id_shopping_basket . '</span>');

	      $rights['MODIFY_CONTENTS'] = $Shopping_Basket->check_rights('MODIFY_CONTENTS', false);
	      if( $rights['MODIFY_CONTENTS'] ) {
	         $available_actions = $addup_basket . $remove_basket . $clean_basket;
	         $rights['LOCK'] = $Shopping_Basket->check_rights('LOCK', false);
	         $rights['UNLOCK'] = $Shopping_Basket->check_rights('UNLOCK', false);
	         $rights['USE'] = $Shopping_Basket->check_rights('USE', false);
	          
	         if( $rights['USE'] ) $available_actions .= $switch_basket;
	         if( $rights['LOCK'] ) $available_actions .= $lock_basket;
	         if( $rights['UNLOCK'] ) $available_actions .= $unlock_basket;
	      } else {
            continue;
         }
	   } else {
         continue;
	   }
	   $state_html = $Shopping_Basket->basket_level_nr() .'(' . $basket_lvl_diff . ')<br>' . $Shopping_Basket->basket_level_text();
	   echo '<script>json_data.basket_list.push("'.$F->json_string(array_merge($Shopping_Basket->params, $rights)).'");</script>';
	   ?>
<div class="basket<?php echo $class_add; ?>" id="basket_prev_<?php echo (int)$Shopping_Basket->id_shopping_basket; ?>">
<div class="basket_single basket_menu"><?php echo $available_actions; ?></div>
<div class="basket_single basket_number"><?php echo $desc_basket; ?></div>
<div class="basket_single basket_product_total"><?php echo Lang::_('TOTAL PRODUCTS'); ?><span id="nr"><?php echo $total['product_total']; ?></span></div>
<div class="basket_single basket_product_types"><?php echo Lang::_('PRODUCTS TYPES'); ?><span id="nr"><?php echo $total['product_types']; ?></span></div>
<div class="basket_single basket_sum_gross"><?php echo Lang::_('sum gross'); ?><span id="nr"><?php echo Price::val($total['sum_gross']); ?></span></div>
<div class="basket_single basket_sum_netto"><?php echo Lang::_('sum_netto'); ?><span id="nr"><?php echo Price::val($total['sum_netto']); ?></span></div>
</div>
<?php
}

if( $P->logged_in && $Shopping_Basket_Chain->get_can_add_basket() ) {
   //$add_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('add_basket', 'add_basket', $GET_tmp));
   //$add_basket = $F->draw_link($add_basket_link, 'title="' . Lang::_('add BASKET') . '"', $F->static_image('icon/plus_16.png', Lang::_('add BASKET')) . ' ' . Lang::_('add BASKET') . ' ' . $F->static_image('icon/plus_16.png', Lang::_('add BASKET')));
   $add_basket = $F->draw_link(
   		$F->make_link(CFG_COM_BASKET, $F->add_local_get('add_basket', 'add_basket', $GET_tmp)), 'title="' . Lang::_('add BASKET') . '"',
   		$F->dynamic_image(Lang::_('add BASKET')) );
?>
<div class="basket_add">
<div class="basket_add_icon"><?php echo $add_basket; ?></div>
</div>
<div class="basket menu_bottom"></div>
</div>
<?php
}
/**
 * View template block rendering an interactive summary list tracking all current shopping baskets.
 * * Iterates across chains of multiple user selection baselines, evaluating action conditions, 
 * permission restrictions (e.g., LOCK, UNLOCK, USE), concurrency indicators, and total metrics.
 *
 * @package Views
 * @subpackage Basket
 * @psr-5
 * @todo Enforce a clear Model-View-Controller abstraction layer to completely isolate inline PHP data processing out of raw template output presentation scopes.
 * @todo Standardize short bracket array structures `[]` across loop environments.
 * @todo Modernize script injections by passing configuration state parameters into decoupling client-side JSON adapters rather than mapping dynamic raw string concats inside `<script>` blocks.
 * @todo Eliminate multiple duplicate initialization assignments discovered across consecutive calculations calls loops.
 */
?>