<?php

$GET_tmp = $F->make_get();

$c1 = $F->make_link(CFG_COM_CATALOG, $F->add_local_get('catpath', '2', $GET_tmp));
$c2 = $F->make_link(CFG_COM_CATALOG, $F->add_local_get('catpath', '25', $GET_tmp));
$c3 = $F->make_link(CFG_COM_CATALOG, $F->add_local_get('catpath', '4', $GET_tmp));
?>
<div class="vertical_menu">
  <a href="<?php echo $c1; ?>"><div class="vertical_menu_item vertical_menu01"></div></a>
  <a href="<?php echo $c2; ?>"><div class="vertical_menu_item vertical_menu02"></div></a>
  <a href="<?php echo $c3; ?>"><div class="vertical_menu_item vertical_menu03"></div></a>
</div>