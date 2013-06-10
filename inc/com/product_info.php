<?php
$GET_tmp = $F->make_get();

$id_product = (int)$F->GET['id_product'];

$product_info = Data::get_product_info( $id_product );


$image_type = Data::get_product_image_type($product_info);
$small_image_html='';

if( $F->not_null($image_type) ) {
   $Page->add_js_file('jquery.colorbox.js');
   //$small_image_html = $F->static_image($image_type['small_image_path'], Lang::_('show_big_image'));
   //$small_image_html = $image_type['small_image_path'];
   $small_image_html = '<img src="' . $image_type['small_image_path'] .'">';
   $Page->add_jq_init('$(".product_image").colorbox({
   	href:"' . $F->js_escape($image_type['big_image_path']) . '",
   	photo:true});');
}

$price_html = $F->output_string_html( Price::val($product_info['price']) . ' (' . Price::tax($product_info['vat']) . ')' );
$description_html = nl2br( $F->output_string_html( $product_info['description'] ) );

$GET_basket = array('mode' => 'add_to_basket', 'id_product' => $id_product);
$link_basket = $F->make_link(CFG_COM_BASKET, $F->add_local_get($GET_basket, '', $GET_tmp) );
$add_basket_html = $F->draw_link($link_basket,
	'onclick="add_to_basket()" title="' . Lang::_('add_to_basket') . '"',
   Lang::_('add_to_basket'));

echo $F->static_image_submit($F->static_image('icon/buy_16.png', Lang::_('add_to_basket')));


$product_quantity = (int)(($product_info['quantity']>0)?$product_info['quantity']:0);

$Page->head_title = $F->output_string_html( $product_info['name'] );

if( $P->logged_in ) {
?>
<div class="product_info">
   <div class="product_name"><?php echo $F->output_string_html( $product_info['name'] ); ?></div>
   <?php if( $F->not_null($image_type) ) echo '<div class="product_image">' . $small_image_html . "</div>\n"; ?>
   <div class="product_quantity"><?php echo Lang::_('QUANTITY_IN_WAREHAUSE') . ': ' . $product_quantity; ?></div>
   <div class="product_price"><?php echo Lang::_('PRICE') . ': ' . $price_html; ?></div>
   <div class="product_description"><?php echo $description_html; ?></div>
   <div class="product_add_basket"><?php echo $add_basket_html; ?></div>
</div>
<?php } else { ?>
<div class="product_info">
   <div class="product_name"><?php echo $F->output_string_html( $product_info['name'] ); ?></div>
   <?php if( $F->not_null($image_type) ) echo '<div class="product_image">' . $small_image_html . "</div>\n"; ?>
   <div class="product_description"><?php echo $description_html; ?></div>
</div>
<?php } ?>