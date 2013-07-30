<?php
$Page->add_jq_init('$(\'.vertical_menu_item\').tooltip({
		track: false, delay: 0, showURL: false, fixPNG: true, showBody: ""
	});');
$GET_tmp = $F->make_get();

$c1 = $F->make_link(CFG_COM_CATALOG);
$c2 = $F->make_link(CFG_COM_CATALOG, $F->add_local_get('catpath', '2', $GET_tmp));
$c3 = $F->make_link(CFG_COM_CATALOG, $F->add_local_get('catpath', '3', $GET_tmp));
$a2 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '2', $GET_tmp));
$a3 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '3', $GET_tmp));

?>
<div class="vertical_menu">
  <a href="<?php echo $c1; ?>" class="vertical_menu_a" title="Hurtownia elektryczna"><div class="vertical_menu_item vertical_menu01" title="Hurtownia elektryczna"></div></a>
  <a href="<?php echo $c2; ?>" class="vertical_menu_a" title="Zasilacze awaryjne UPS"><div class="vertical_menu_item vertical_menu02" title="Zasilacze awaryjne UPS"></div></a>
  <a href="<?php echo $c3; ?>" class="vertical_menu_a" title="Agregaty prądotwórcze"><div class="vertical_menu_item vertical_menu03" title="Agregaty prądotwórcze"></div></a>
  <a href="<?php echo $a2; ?>" class="vertical_menu_a" title="Projektowanie i realizacja"><div class="vertical_menu_item vertical_menu04" title="Projektowanie i realizacja"></div></a>
  <a href="<?php echo $a3; ?>" class="vertical_menu_a" title="Serwis"><div class="vertical_menu_item vertical_menu05" title="Serwis"></div></a>
</div>