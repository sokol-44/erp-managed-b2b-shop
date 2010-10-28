<?php
//STR: tmp
//FIXME
if( DEBUG_DB_QUERIES == 'false' && false) $Shopping_Basket = new Shopping_Basket();
else $Shopping_Basket = Shopping_Basket::g_global();
//STR: end

$total = $Shopping_Basket->calculate_total();

?>
<div class="basket" id="basket_prev_<?php echo $Shopping_Basket->id_nr_shopping_basket ?>">
<div class="basket_number"><?php// echo Lang::_('NUMBER'); ?><span id="nr"><?php //echo $Shopping_Basket->id_nr_shopping_basket; ?></span></div>
<div class="basket_product_total"><?php echo Lang::_('TOTAL PRODUCTS'); ?><span id="nr"><?php echo $total['product_total']; ?></span></div>
<div class="basket_product_types"><?php echo Lang::_('PRODUCTS TYPES'); ?><span id="nr"><?php echo $total['product_types']; ?></span></div>
<div class="basket_sum_gross"><?php echo Lang::_('sum gross'); ?><span id="nr"><?php echo $total['sum_gross']; ?></span></div>
<div class="basket_sum_netto"><?php echo Lang::_('sum_netto'); ?><span id="nr"><?php echo $total['sum_netto']; ?></span></div>
</div>