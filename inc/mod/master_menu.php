<?php

$array_mastermenu = array();

if( $P->logged_in ) {
   $array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_LOGOUT),'', Lang::_('Logout') );
   $array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_ACCOUNT),'', Lang::_('Account') );
} else {
   $array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_LOGIN),'', Lang::_('Login') );
}
$array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_CATALOG),'', Lang::_('Catalog') );
$array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_BASKET),'', Lang::_('Basket') );

//FIXME - ugly hack
$array_mastermenu[] = '<a href="http://zebra.waw.pl/other.php">' . Kontakt . '</a>';

echo implode(' | ', $array_mastermenu);
?>