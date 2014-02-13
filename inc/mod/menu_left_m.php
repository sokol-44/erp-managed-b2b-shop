<?php

$GET_tmp = $F->make_get();

$a2 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '2', $GET_tmp));
$a3 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '3', $GET_tmp));
//$a4 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '4', $GET_tmp));
$cntct = $F->make_link(CFG_COM_CONTACT);
$srch = $F->make_link(CFG_COM_SEARCH, $GET_tmp);
$acc = $F->make_link(CFG_COM_ACCOUNT);

$ord = $F->draw_link($F->make_link(CFG_COM_ORDER_LIST, array('mode' => 'all')),'', Lang::_('Orders') );
$inv = $F->draw_link($F->make_link(CFG_COM_INVOICE),'', Lang::_('Invoices') );
$invpay = $F->draw_link($F->make_link(CFG_COM_INVOICE, array('mode' => 'pay')),'', Lang::_('Invoices to pay') );
$bsk = $F->draw_link($F->make_link(CFG_COM_BASKET),'', Lang::_('Basket') );
$bsk_all = $F->draw_link($F->make_link(CFG_COM_BASKET, $F->add_local_get('show', 'all', $GET_tmp)),'', Lang::_('Baskets') );
$fbsk= $F->draw_link($F->make_link(CFG_COM_BASKET_FAVORITE),'', Lang::_('Favorite Basket') );


$tool_battery_l = $F->draw_link($F->make_link(CFG_COM_TOOL,array('battery' => 'unload')),'', 'Dobór UPS');
$tool_battery_l2 = $F->draw_link($F->make_link('tool',array('battery' => 'unload')),'', 'Dobór UPS Stary');
?>
<div class="iStoreBox" id="iStoreMenuBox">
<div class="iStoreBoxWrapper">
<div class="iStoreBoxHeader"><h2>Menu</h2></div>
<div class="iStoreBoxContent">
	<ul id="iStorieMenuList">
		<li><a href="<?php echo $acc; ?>"><?php echo Lang::_('Account'); ?></a></li>
		<li><?php echo $ord ?></li>
		<li><?php echo $inv ?>
		<?php if( $F->com == CFG_COM_INVOICE ) { ?>
			<ul><li><?php echo $invpay ?></li></ul>
		<?php } ?>
		</li>
		<li><?php echo $bsk_all ?>
		<?php if( $F->com == CFG_COM_BASKET ||  $F->com == CFG_COM_BASKET_FAVORITE  ) { ?>
			<ul><li><?php echo $bsk ?></li></ul>
			<ul><li><?php echo $fbsk ?></li></ul>
		<?php } ?>
		</li>
		<li><?php echo $tool_battery_l; ?></li>
		<li><?php echo $tool_battery_l2; ?></li>
	</ul> 
</div>
</div>
</div>