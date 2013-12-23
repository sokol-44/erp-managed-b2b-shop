<?php
//STR: tmp
//FIXME
$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();
$Shopping_Basket = $Shopping_Basket_Chain->return_default_basket();
$Price = Price::g_global();
//STR: end

$total = $Shopping_Basket->calculate_total();

$GET_tmp = $F->make_get('mode,action,show');

$show_basket_link = $F->make_link(CFG_COM_BASKET, $GET_id);
$basket_link = $F->draw_link($show_basket_link, 'title="' . Lang::_('show BASKET') . '"', Lang::_('show BASKET') . ' »');

echo '<script>json_data.basket_list_default="'.$F->json_string($Shopping_Basket->params).'";</script>';
?>
		<div class="iStoreBox" id="iStoreBasket">
			<div class="iStoreBoxWrapper">
				<div class="iStoreBoxContent" id="clienttemplatebasketpreview">
<?php if ( $total['product_total'] > 0 ) { ?>
<p>
  	produktów: <?php echo $total['product_total']; ?>
  	(<strong><?php echo Price::val($total['sum_gross']); ?></strong>)
  </p>
  <?php echo $basket_link ?>

<?php } else { ?>
  <p id="iStoreEmptyBasket">Twój koszyk jest pusty</p>
<?php } ?>
				</div>
			</div>
		</div>