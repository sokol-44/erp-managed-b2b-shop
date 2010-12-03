<?php
//STR: tmp
//FIXME
$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();
$Shopping_Basket = $Shopping_Basket_Chain->return_default_basket();
$Price = Price::g_global();
//STR: end

$total = $Shopping_Basket->calculate_total();

$GET_tmp = $F->make_get();
$GET_id = $F->add_local_get('id_nr_shopping_basket', (int)$Shopping_Basket->id_nr_shopping_basket, $GET_tmp);
$remove_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'remove_basket', $GET_id));
$remove_basket = $F->draw_link($remove_basket_link, 'title="' . Lang::_('remove BASKET') . '"', $F->static_image('icon/delete_16.png', Lang::_('remove BASKET')));
$clean_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'clean_basket', $GET_id));
$clean_basket = $F->draw_link($clean_basket_link, 'title="' . Lang::_('clean BASKET') . '"', $F->static_image('icon/trash_16.png', Lang::_('clean BASKET')));
$show_basket_link = $F->make_link(CFG_COM_BASKET, $GET_tmp);
$show_basket = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"', $F->static_image('icon/folder_16.png', Lang::_('show BASKET')));
?>
<div class="basket mainbasket" id="basket_prev_<?php echo $Shopping_Basket->id_nr_shopping_basket ?>">
<div class="basket_menu"><?php echo $show_basket . $remove_basket . $clean_basket; ?></div>
<div class="basket_number"><?php echo Lang::_('BASKET'); // echo Lang::_('NUMBER'); ?><span id="nr"><?php echo $Shopping_Basket->id_nr_shopping_basket; ?></span></div>
<div class="basket_product_total"><?php echo Lang::_('TOTAL PRODUCTS'); ?><span id="nr"><?php echo $total['product_total']; ?></span></div>
<div class="basket_product_types"><?php echo Lang::_('PRODUCTS TYPES'); ?><span id="nr"><?php echo $total['product_types']; ?></span></div>
<div class="basket_sum_gross"><?php echo Lang::_('sum gross'); ?><span id="nr"><?php echo Price::val($total['sum_gross']); ?></span></div>
<div class="basket_sum_netto"><?php echo Lang::_('sum_netto'); ?><span id="nr"><?php echo Price::val($total['sum_netto']); ?></span></div>
</div>
<?php
$Shopping_Basket_Chain->reset_basket_list();

while( $Shopping_Basket = $Shopping_Basket_Chain->return_basket_next( true ) ) {
   $total = $Shopping_Basket->calculate_total();
   
   $currently_other_using = $Shopping_Basket->currently_other_using();
   
   if( $currently_other_using ) $class_add = ' usedbasket';
   else $class_add = '';
   
   $GET_id = $F->add_local_get('id_nr_shopping_basket', (int)$Shopping_Basket->id_nr_shopping_basket, $GET_tmp);
   $remove_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'remove_basket', $GET_id));
   $remove_basket = $F->draw_link($remove_basket_link, 'title="' . Lang::_('remove BASKET') . '"', $F->static_image('icon/delete_16.png', Lang::_('remove BASKET')));
   $clean_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'clean_basket', $GET_id));
   $clean_basket = $F->draw_link($clean_basket_link, 'title="' . Lang::_('clean product in BASKET') . '"', $F->static_image('icon/trash_16.png', Lang::_('clean product in BASKET')));
   $switch_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'switch_basket', $GET_id));
   $switch_basket = $F->draw_link($switch_basket_link, 'title="' . Lang::_('switch working BASKET') . '"', $F->static_image('icon/up_16.png', Lang::_('switch working BASKET')));
   $addup_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'add_to_mainbasket', $GET_id));
   $addup_basket = $F->draw_link($addup_basket_link, 'title="' . Lang::_('add to main BASKET') . '"', $F->static_image('icon/add_up_16.png', Lang::_('add to main BASKET')));
?>
<div class="basket<?php echo $class_add; ?>" id="basket_prev_<?php echo $Shopping_Basket->id_nr_shopping_basket ?>">
<div class="basket_menu"><?php echo $addup_basket . $remove_basket . $clean_basket . $switch_basket; ?></div>
<div class="basket_number"><?php echo Lang::_('BASKET'); // echo Lang::_('NUMBER'); ?><span id="nr"><?php echo $Shopping_Basket->id_nr_shopping_basket; ?></span></div>
<div class="basket_product_total"><?php echo Lang::_('TOTAL PRODUCTS'); ?><span id="nr"><?php echo $total['product_total']; ?></span></div>
<div class="basket_product_types"><?php echo Lang::_('PRODUCTS TYPES'); ?><span id="nr"><?php echo $total['product_types']; ?></span></div>
<div class="basket_sum_gross"><?php echo Lang::_('sum gross'); ?><span id="nr"><?php echo Price::val($total['sum_gross']); ?></span></div>
<div class="basket_sum_netto"><?php echo Lang::_('sum_netto'); ?><span id="nr"><?php echo Price::val($total['sum_netto']); ?></span></div>
</div>
<?php
}

if( $P->logged_in && $Shopping_Basket_Chain->get_can_add_basket() ) {
   $add_basket_link = $F->make_link(CFG_COM_BASKET, $F->add_local_get('action', 'add_basket', $GET_tmp));
   $add_basket = $F->draw_link($add_basket_link, 'title="' . Lang::_('add BASKET') . '"', Lang::_('add BASKET') . ' ' . $F->static_image('icon/plus_16.png', Lang::_('add BASKET')));
?>
<div class="basket_add">
<div class="basket_add_icon"><?php echo $add_basket; ?></div>
</div>
<?php
}
?>