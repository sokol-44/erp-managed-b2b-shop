<?php

$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();
$Shopping_Basket_Chain->logout_user();
$P->logout();
$Shopping_Basket_Chain = new Shopping_Basket_Chain();

$Info = Info::g_global();
$Info->add(Lang::_('Successfully logged out'), 'success');
$Page->redirect( $F->make_link('main') );
?>