<?php
$Page->head_title = Lang::_('Change password');
//FIXME
//remember backtrack
if( !$P->logged_in ) $F->redirect(  );

echo $F->draw_form('admin_edit', $F->self_link());
?>
<div class="account_param_container account_param_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
<div class="account_param_container account_param_content">
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('Field') ?></th>
		<th><?php echo Lang::_('Value') ?></th>
	</tr>
	<tr>
		<td><?php echo Lang::_('User') ?></td>
		<td><?php echo $F->output_string_html($P->data['login']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('Old password') ?></td>
		<td><?php echo $F->draw_password_field('old_password', 'autocomplete="off"'); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('New password') ?></td>
		<td><?php echo $F->draw_password_field('new_password', 'autocomplete="off"'); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('Repeat new password') ?></td>
		<td><?php echo $F->draw_password_field('new_password_retype', 'autocomplete="off"'); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><?php echo $F->draw_submit(Lang::_('UPDATE')); ?></td>
	</tr>
</table>
</div>
<?php
echo $F->draw_form_close();
?>