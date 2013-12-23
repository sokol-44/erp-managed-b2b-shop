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

$price_html = Price::val($product_info['price']) . ' <span class="product_vat">(' . Price::tax($product_info['vat']) . ')</span>';
$description_html = str_replace('\n', "<br>\n", $F->output_string_html( $product_info['description'] ) );

$GET_basket = array('mode' => 'add_to_basket', 'id_product' => $id_product);
$link_basket = $F->make_link(CFG_COM_BASKET, $F->add_local_get($GET_basket, '', $GET_tmp) );

$add_basket_quantity = '<div class="add_basket_quantity">' . Lang::_('QUANTITY') . $F->draw_input_field('quantity' , 1, 'size="1"') . '</div>';
$add_basket_form = $F->draw_form('add_to_basket', $link_basket, 'GET') .
   $F->draw_hidden_field('com', CFG_COM_BASKET) .
   $F->draw_hidden_field('mode', 'add_to_basket') .
   $F->draw_hidden_field('id_product', $id_product);

$add_basket_html = $add_basket_form .
   $add_basket_quantity .
   $F->static_image_submit($F->static_image_path('icon/buy_16.png'), Lang::_('add_to_basket')) .
   $F->draw_form_close();

$product_quantity = (int)(($product_info['quantity']>0)?$product_info['quantity']:0);
$product_producent = $F->output_string_html( $product_info['producent'] );
$product_index = $F->output_string_html( trim($product_info['catalog_index']) );


$Page->head_title = $F->output_string_html( $product_info['name'] );
// if ( $F->not_null($product_producent) )
//   $Page->head_title .= $Page->head_title . ', ' . $product_producent;
// if ( $F->not_null($product_index) )
//   $Page->head_title .= $Page->head_title . ', ' . $product_index;
?>
<div class="product product_info">
<?php
if( $P->logged_in ) {
?>
   <div class="product product_name container_header" ><?php echo $F->output_string_html( $product_info['name'] ); ?></div>
   <?php if( $F->not_null($image_type) ) echo '<div class="product product_image">' . $small_image_html . "</div>\n"; ?>
   <div class="product product_price"><?php echo '<span>' . Lang::_('PRICE') . '</span>: ' . $price_html; ?></div>
   <?php
   //if ( $product_quantity > 0 )
      echo '<div class="product product_quantity"><span>' . Lang::_('QUANTITY_IN_WAREHAUSE') . '</span>: ' . $product_quantity . "</div>\n";
   if ( $F->not_null($product_producent) )
      echo '<div class="product product_producent"><span>' . Lang::_('PRODUCENT') . '</span>: ' . $product_producent . "</div>\n";
   if ( $F->not_null($product_index) )
      echo '<div class="product product_index"><span>' . Lang::_('CATALOG INDEX') . '</span>: ' . $product_index . "</div>\n";
   ?>
   <div class="product product_description"><?php echo $description_html; ?></div>
   <div class="product product_add_basket"><?php echo $add_basket_html; ?></div>
<?php } else { ?>
   <div class="product product_name"><?php echo $F->output_string_html( $product_info['name'] ); ?></div>
   <?php if( $F->not_null($image_type) ) echo '<div class="product product_image">' . $small_image_html . "</div>\n"; ?>
   <div class="product product_description"><?php echo $description_html; ?></div>
<?php } ?>
</div>
<div class="product product_bottom container_bottom"></div>
