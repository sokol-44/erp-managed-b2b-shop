<?php

$Page->head_title = Lang::_('Contact');

$BC->add_crumb( array( 'name' => Lang::_('Basket'), 'path' => $F->make_link(CFG_COM_BASKET) ) );

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');

$GET_tmp = $F->make_get();
$GET = $F->add_local_get('action', 'send', $GET_tmp);
?>
<div class="contact_form_container">
  <div class="contact_form_container contact_form_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="contact_form_container contact_form_form">
<?php echo $F->draw_form('contect_form', $F->make_link(CFG_COM_CONTACT, $GET), 'POST'); ?><br>
<?php echo $F->draw_hidden_field('time', microtime(true)); ?>
<span class="need_el"></span> - <?php echo Lang::_('required information'); ?>
<table class="tableBox">
	<tr>
		<td><strong><?php echo Lang::_('firstname and secondname'); ?></strong><span class="need_el"></span></td>
		<td colspan="2"><?php echo $F->draw_input_field('cf_name', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Address email'); ?><span class="need_el"></span></strong></td>
		<td colspan="2"><?php echo $F->draw_input_field('cf_email', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Telephone'); ?><span class="need_el"></span></strong></td>
		<td colspan="2"><?php echo $F->draw_input_field('cf_telephone', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Second Telephone'); ?></strong></td>
		<td colspan="2"><?php echo $F->draw_input_field('cf_second_telephone', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('CONTENTS'); ?></strong><span class="need_el"></span></td>
		<td colspan="2"><?php echo $F->draw_textarea_field('cf_contents', 'auto'); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><?php echo $F->dynamic_image_submit(Lang::_('SEND'),''); ?></td>
	</tr>
</table>
<?php echo $F->draw_form_close(); ?>
</div>
<div class="contact_form_container contact_form_bottom container_bottom"></div>
</div>