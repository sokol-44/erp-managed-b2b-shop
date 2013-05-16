<?php

$GET_tmp = $F->make_get();

$a2 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '2', $GET_tmp));
$a3 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '3', $GET_tmp));
$a4 = $F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '4', $GET_tmp));
$srch = $F->make_link(CFG_COM_SEARCH, $GET_tmp);

?>
<div class="article_menu_left">
  <div class="article menu_header">Menu</div>
  <div class="article article_first article01"><a href="/">Strona główna</a></div>
  <div class="article article02"><a href="<?php echo $a2; ?>">Platforma ZEBRA on-line</a></div>
  <div class="article article03"><a href="<?php echo $a3; ?>">Warunki sprzedaży</a></div>
  <div class="article article04"><a href="<?php echo $a4; ?>">Kontakt bezpośredni</a></div>
  <div class="article article04"><a href="<?php echo $srch; ?>">Wyszukiwanie</a></div>

</div>