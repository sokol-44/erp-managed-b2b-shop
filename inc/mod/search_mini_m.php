<div class="iStoreBox" id="iStoreSearch">
	<div class="iStoreBoxWrapper">
		<div class="iStoreBoxContent">
<?php
echo $F->draw_form('search', $F->make_link(CFG_COM_SEARCH), 'GET');
echo  $F->draw_hidden_field('search', 'search') .
      $F->draw_hidden_field('com', 'search').
      $F->draw_hidden_field('product_text_all', 'on');
?>
	          <div>
	            <label for="search">Szukaj produktu</label> 
<?php //echo $F->draw_input_field('product_text', '', ' style="width: 220px"'); ?>           
			      <input class="text search" type="text" tabindex="1" value="" id="search" name="product_text" placeholder="<?php echo Lang::_('SEARCH') ?>">
	          	<button type="submit" class="submit" tabindex="2"><span>Szukaj</span></button>
	          </div>
<?php echo $F->draw_form_close(); ?>
		</div>
	</div>		
</div>