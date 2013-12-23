<?php

$Page->head_title = Lang::_('Register');

$BC->add_crumb( array( 'name' => Lang::_('Register'), 'path' => $F->make_link(CFG_COM_REGISTER) ) );

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');

$GET_tmp = $F->make_get();
$GET = $F->add_local_get('action', 'send', $GET_tmp);
?>
<div class="register_form_container">
  <div class="register_form_container register_form_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="register_form_container register_form_form">
<?php echo $F->draw_form('register_form', $F->make_link(CFG_COM_REGISTER, $GET), 'POST'); ?><br>
<?php echo $F->draw_hidden_field('time', microtime(true)); ?>
<span class="need_el"></span> - <?php echo Lang::_('required information'); ?>
<div class="register_form_container container_subheader"><?php echo Lang::_('company information'); ?><div class="icon"></div></div>
<table class="tableBox">
	<tr>
		<td><strong><?php echo Lang::_('Name'); ?></strong><span class="need_el"></span></td>
		<td colspan="2"><?php echo $F->draw_input_field('rf_name', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Address email'); ?><span class="need_el"></span></strong></td>
		<td colspan="2"><?php echo $F->draw_input_field('rf_email', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Telephone'); ?><span class="need_el"></span></strong></td>
		<td colspan="2"><?php echo $F->draw_input_field('rf_telephone', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td valign="top"><strong><?php echo Lang::_('CONTENTS'); ?></strong><span class="need_el"></span>
		<br><span class="dscr_el">
		<?php echo 'Jeżeli nawiązałeś już współpracę<br>wpisz informację pomogące cię zidetyfikować.<br><br>
		Jeżeli chcesz nawiązać współprace<br>wpisz dlaczego chcesz nawiązać współpracę.'; ?></span>
		</td>
		<td colspan="2"><?php echo $F->draw_textarea_field('rf_contents', 'auto'); ?></td>
	</tr>
</table>
<div class="register_form_container container_subheader"><?php echo Lang::_('admin account details'); ?><div class="icon"></div></div>
<span class="dscr_el">Jest to główne konto firmy posiadające największe uprawnienia i służące do zarządzania innymi urzytkownikami przypisanymi do firmy.</span>
<table class="tableBox">
	<tr>
		<td><strong><?php echo Lang::_('Name'); ?></strong><span class="need_el"></span></td>
		<td colspan="2"><?php echo $F->draw_input_field('rf_uname', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Login name'); ?></strong><span class="need_el"></span></td>
		<td colspan="2"><?php echo $F->draw_input_field('rf_ulname', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Password'); ?></strong><span class="need_el"></span></td>
		<td colspan="2"><?php echo $F->draw_password_field('rf_upassword', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Repeat Passoword'); ?></strong><span class="need_el"></span></td>
		<td colspan="2"><?php echo $F->draw_password_field('rf_upass2', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Address email'); ?><span class="need_el"></span></strong></td>
		<td colspan="2"><?php echo $F->draw_input_field('rf_uemail', '', ' style="width: 220px"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('Telephone'); ?><span class="need_el"></span></strong></td>
		<td colspan="2"><?php echo $F->draw_input_field('rf_utelephone', '', ' style="width: 220px"'); ?></td>
	</tr><!-- 
	<tr>
		<td><strong><?php echo Lang::_('MOBILE_PHONE'); ?></strong></td>
		<td colspan="2"><?php echo $F->draw_input_field('rf_utelephone2', '', ' style="width: 220px"'); ?></td>
	</tr> -->
	<tr>
		<td colspan="2" align="center"><?php echo $F->dynamic_image_submit(Lang::_('SEND'),'SEND'); ?></td>
	</tr>
</table>
<?php echo $F->draw_form_close(); ?>
</div>
<div class="register_form_container register_form_bottom container_bottom"></div>
</div>