<?php
$GET_tmp = $F->make_get();

$id_product = (int)$F->GET['id_product'];

$product_info = Data::get_product_info( $id_product );

//print_debug($product_info);

$small_image_path = Data::get_product_image_path( $product_info['picture_small_url'] );
$small_image_html = $F->static_image($small_image_path, Lang::_('add_to_basket'));;
$big_image_path = Data::get_product_image_path( $product_info['picture_big_url'] );

$price_html = $F->output_string_html( $product_info['price'] . ' (' . $product_info['vat'] . ')' );
$description_html = nl2br( $F->output_string_html( substr($product_info['description'], 0, 256) ) );

$GET_basket = array('mode' => 'add_to_basket', 'id_product' => $id_product);
$link_basket = $F->make_link(CFG_COM_BASKET, $F->add_local_get($GET_basket, '', $GET_tmp) );
$add_basket_html = $F->draw_link($link_basket, 'onclick="add_to_basket()" title="' . Lang::_('add_to_basket') . '"', $F->static_image('icon/buy_16.png', Lang::_('add_to_basket')));

?>
<script>
function show_big_img(str) { alert('big picture\n' + str); }
</script>
<div class="product_info">
   <div class="product_name"><?php echo $F->output_string_html( $product_info['name'] ); ?></div>
   <div class="product_image" onclick='show_big_img("<?php echo $F->js_escape($big_image_path); ?>");'><?php echo $small_image_html; ?></div>
   <div class="product_price"><?php echo $price_html; ?></div>
   <div class="product_description"><?php echo $description_html; ?></div>
   <div class="product_add_basket"><?php echo $add_basket_html; ?></div>
</div>
