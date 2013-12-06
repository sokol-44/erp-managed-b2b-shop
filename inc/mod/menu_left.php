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
?>
<div class="article_menu_left">
  <div class="article_menu_left menu_header">Menu<div class="icon"></div></div>
  <div class="article article_first article01"><a href="/">Strona główna</a></div>
  <div class="article article02"><a href="<?php echo $a2; ?>">Platforma B2B ***REMOVED***</a></div>
  <div class="article article03"><a href="<?php echo $a3; ?>">Warunki sprzedaży</a></div>
<!-- <div class="article article04"><a href="<?php echo $a4; ?>">Kontakt bezpośredni</a></div>-->
  <div class="article article04"><a href="<?php echo $cntct; ?>">Formularz kontaktowy</a></div>
  <div class="article article05"><a href="<?php echo $srch; ?>">Wyszukiwanie</a></div>
  <div class="article article_hr"></div>
  <div class="article article06"><a href="<?php echo $acc; ?>"><?php echo Lang::_('Account'); ?></a></div>
  <div class="article article_hr"></div>
  <div class="article article07"><?php echo $ord ?></div>
  <div class="article article_hr"></div>
  <div class="article article07"><?php echo $inv ?></div>
  <div class="article article_tab article07"><?php echo $invpay ?></div>
  <div class="article article_hr"></div>
  <div class="article article08"><?php echo $bsk_all ?></div>  
  <div class="article article_tab article09"><?php echo $bsk ?></div>
  <div class="article article_tab article10"><?php echo $fbsk ?></div>
  <div class="article article_hr"></div>
  <div class="article article11"><?php echo $tool_battery_l; ?></div>
  <div class="article article_hr"></div>
  <div class="article menu_bottom"></div>
</div>