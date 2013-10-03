<?php
$Page->add_jq_init('$(\'.vertical_menu_item\').tooltip({
		track: false, delay: 0, showURL: false, fixPNG: true, showBody: ""
	});');
$GET_tmp = $F->make_get();

$c1 = $F->make_link(CFG_COM_CATALOG);



// $a2 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '2', $GET_tmp));
// $a3 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '3', $GET_tmp));

//$array_mastermenu[] = $F->draw_link($F->make_link(CFG_COM_LOGOUT),'', Lang::_('Logout') );
$l1 = $F->make_link(CFG_COM_ORDER_LIST, array('mode' => 'all'));
$l2 = $F->make_link(CFG_COM_BASKET);
$l3 = $F->make_link(CFG_COM_BASKET_FAVORITE);
$l4 = $F->make_link(CFG_COM_ACCOUNT);

$a1 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '4', $GET_tmp));

if( $P->logged_in ) {
?>
<div class="vertical_menu">
  <a href="<?php echo $c1; ?>" class="vertical_menu_a" title="Katalog"><div class="vertical_menu_item vertical_menu01" title="Katalog"></div></a>
  <a href="<?php echo $l1; ?>" class="vertical_menu_a" title="Zamówienia"><div class="vertical_menu_item vertical_menu02" title="Zamówienia"></div></a>
  <a href="<?php echo $l2; ?>" class="vertical_menu_a" title="Koszyk"><div class="vertical_menu_item vertical_menu03" title="Koszyk"></div></a>
  <a href="<?php echo $l3; ?>" class="vertical_menu_a" title="Ulubiony koszyk"><div class="vertical_menu_item vertical_menu04" title="Ulubiony koszyk"></div></a>
  <a href="<?php echo $l4; ?>" class="vertical_menu_a" title="Konto"><div class="vertical_menu_item vertical_menu05" title="Konto"></div></a>
  <a href="<?php echo $a1; ?>" class="vertical_menu_a" title="Kontakt"><div class="vertical_menu_item vertical_menu06" title="Kontakt"></div></a>
</div>
<?php 
} else {
?>
<div class="vertical_menu">
  <a href="<?php echo $a1; ?>" class="vertical_menu_a" title="Kontakt"><div class="vertical_menu_item vertical_menu06" title="Kontakt"></div></a>
</div>
<?php 
}
?>