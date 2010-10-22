<?php

ob_start();
?>
<div class="InfoBox"> toolbox - admin_add
</div>
<div class="ToolBox">
<?php
$link = $F->make_link($F->com, $F->make_get('mode'));
echo
$F->draw_link($link,'', $F->static_image('icon/left_32.png', Lang::_('Back'))) .
$F->static_image('icon/save_32.png', Lang::_('Add'), 'onclick="form_submit(\'admin_edit\')"');

$Page->add_js_file('toolbox.js');
$Page->add_jq_init('set_toolbox_table(".tableBox");');
?>
</div>
<?php
echo $F->draw_form('admin_edit', $F->self_link());
?>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('O co') ?></th>
		<th><?php echo Lang::_('Co') ?></th>
	</tr>
	<tr>
		<td><?php echo Lang::_('LOGIN') ?></td>
		<td><?php echo $F->draw_input_field('login', $person_data['login']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('DESCRIPTION') ?></td>
		<td><?php echo $F->draw_textarea_field('description', 'auto', '30', '7', $person_data['description']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('EMAIL') ?></td>
		<td><?php echo $F->draw_input_field('email', $person_data['email']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('NEW_PASSWORD') ?></td>
		<td><?php echo $F->draw_password_field('new_password', 'autocomplete="off"'); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('RETYPE_PASSWORD') ?></td>
		<td><?php echo $F->draw_password_field('new_password_retype', 'autocomplete="off"'); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('RIGHTS') ?></td>
		<td><?php echo $F->show_rights_list('ADMIN', $person_data['rights_ids']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('STATE') ?></td>
		<td><?php echo $F->show_person_account_state('ADMIN', $person_data['state']); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><?php echo $F->draw_submit(Lang::_('SAVE')); ?></td>
	</tr>
</table>
<?php
echo $F->draw_form_close();
$Page->component_html = ob_get_clean();

$Page->second_head_html;
?>