<?php

$array_mastermenu = array();

if( $P->logged_in ) {
   $array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_LOGOUT),'', Lang::_('Logout') );
   $array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_ACCOUNT),'', Lang::_('Account') );
   $array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_ORDER_LIST, array('mode' => 'all')),'', Lang::_('Orders') );
} else {
   $array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_LOGIN),'', Lang::_('Login') );
}
$array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_CATALOG),'', Lang::_('Catalog') );
$array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_BASKET),'', Lang::_('Basket') );
$array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_BASKET_FAVORITE),'', Lang::_('Favorite Basket') );
// $array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_CONTACT),'', Lang::_('Contact') );

$a4 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '4', $GET_tmp));
$array_mastermenu[] = $F->draw_link( $a4 ,'', Lang::_('Contact') );

echo '<script>json_data.user_curent = "'.$F->json_string( $P->get_public_data() ).'";</script>';

echo implode(' | ', $array_mastermenu);
?>