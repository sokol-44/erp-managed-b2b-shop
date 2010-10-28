<?php

$array_mastermenu = array();

//$array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_LOGIN),'', Lang::_('Login') );
$array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_CATALOG),'', Lang::_('Catalog') );
$array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_BASKET),'', Lang::_('Basket') );

echo implode(' | ', $array_mastermenu);
?>