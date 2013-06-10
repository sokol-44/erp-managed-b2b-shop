<?php
echo $F->draw_form('search', $F->make_link(CFG_COM_SEARCH), 'GET');
echo  $F->draw_hidden_field('search', 'search') .
      $F->draw_hidden_field('com', 'search').
      $F->draw_hidden_field('product_text_all', 'on');
?>
<div class="search_mini">
<?php echo $F->draw_input_field('product_text', '', ' style="width: 220px"'); ?>
<?php echo $F->dynamic_image_submit(Lang::_('SEARCH'),''); ?>
</div>
<?php echo $F->draw_form_close(); ?>