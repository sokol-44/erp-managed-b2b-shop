<?php
?>
<div class="iStoreBox displayAsList" id="iStoreProductsList">
<div class="iStoreBoxWrapper">

<div class="iStoreBoxToolbar">
	<div class="iStorePagination">
		<?php echo $SP->display_links_m(); ?>
	</div>
</div>

<div class="iStoreBoxContent">
<?php
echo ' <ul class="iStoreProducts ">'."\n";
$idx_prdt=1;
foreach ( $product_list as $product ) {
	$GET_tmp = $F->add_local_get ( 'id_product', $product ['id_product'], $GET_tmp );

	if ($P->logged_in) {
		$product_index = $F->output_string_html ( $product ['catalog_index'] );
		$link_basket = $F->make_link ( CFG_COM_BASKET, $F->add_local_get ( 'mode', 'add_to_basket', $GET_tmp ) );
		$cell_basket = $F->draw_link ( $link_basket, 'onclick="add_basked()" title="' . Lang::_ ( 'add_to_basket' ) . '"', $F->static_image ( 'icon/buy_16.png', Lang::_ ( 'add_to_basket' ) ) );
	}

	$link_product_info = $F->make_link ( CFG_COM_PRODUCT_INFO, $GET_tmp );
	if (defined ( 'SHOP_SHOW_PRODUCTS_DESCRIPTION_IN_LIST' ) && constant ( 'SHOP_SHOW_PRODUCTS_DESCRIPTION_IN_LIST' ) == 'true')
		$description_html = str_replace ( '\n', "<br>\n", $F->output_string_html ( $product ['description'], 100 ) );
	else
		$description_html = '';
	
	$cell_product_info = $F->draw_link ( $link_product_info, '',  $F->output_string_html ( $product ['name'] ) );

	  	$image_type = Data::get_product_image_type($product);
		$small_image_html='';

		if( $F->not_null($image_type) ) {
				/*$Page->add_js_file('jquery.colorbox.js');
				$small_image_html = '<img src="' . $image_type['small_image_path'] .'">';
				$small_image_html = '<div class="product_image_' . (int)$product['id_product'] . '">' . $small_image_html . "</div>\n";
				$Page->add_jq_init('$(".product_image_' . (int)$product['id_product'] . '").colorbox({
		   	href:"' . $F->js_escape($image_type['big_image_path']) . '",
		   	photo:true});');*/
				$small_image_html = '<a class="iStoreProductImageLink" rel="link" href="'.$link_product_info.'" title="'.$F->output_string_html ( $product ['name'] ).'">
				<img class="iStoreProductPhoto photo" width="98" height="76" src="'.$image_type['small_image_path'].'" alt="Agregat Prądotwórczy FOGO FV 15000 E">
				</a>';
		}

	$catalog_index = $F->output_string_html ( trim ( $product ['catalog_index'] ) );
		
	$cell_product_info2 = Lang::_('CATALOG INDEX') . ': ' .  $catalog_index . "<br>\n" .
				Lang::_('QUANTITY_IN_warehouse') . ': ' . (int)$product['quantity'];

	echo '<li class="'.((($idx_prdt%2)==0)?'odd':'even').' col' . (int)$idx_prdt . '">';
?>
			<div class="iStoreProduct  ">
			<div class="iStoreProductWrapper">
				<div class="iStoreProductContent">
					<!--  
						<ul class="iStoreSpecialIcons">
							<li class="iStoreProductIsPromotedPL" title="Promocja"><span>Promocja</span></li>
						</ul>
					-->
					<div class="iStoreProductImage">
						<?php echo $small_image_html; ?>
					</div>
					<h3 class="name title">
						<?php echo $cell_product_info; ?>
					</h3>

					<div class="iStoreProductPurchase">
						<div class="iStoreProductCosts">
						<!-- 
							<del class="iStorePrice">
								<strong>2195.00</strong>
							</del>
						 -->
							<span class="iStorePrice">
								<strong><?php echo Price::val( $product['price'] ) . ' (' . Price::tax( $product['vat'] ) . ')'; ?></strong>
								<small class="iStoreCurrency"></small></span>
						</div>
						<div class="iStoreAddToBasket">
							<a href="<?php echo $link_basket; ?>" rel="nofollow"
								class="iStoreAddToBasketLink" title=""
								style="background-image: url(http://img.istore.pl/css/client5/version1/skins/standard_0/img/icons/istButtonCart.png);">
								<strong>Dodaj do koszyka</strong>
							</a>
						</div>
					</div>

					<div class="iStoreProductDescription description"><?php echo $cell_product_info2; ?></div>
				</div>
			</div>
		</div>
<?php 
}
echo "	</ul>\n";
?>
</div>


<div class="iStoreBoxFooter">
	<div class="iStoreBoxToolbar">
		<div class="iStorePagination">
			<?php echo $SP->display_links_m(); ?>
		</div>
	</div>
</div>

</div>
</div>
