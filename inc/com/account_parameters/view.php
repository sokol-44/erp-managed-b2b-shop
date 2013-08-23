<?php
$Page->head_title =Lang::_('account details');
//FIXME
//remember backtrack
if( !$P->logged_in ) $F->redirect(  );
$data_Client = $P->get_client_data();
?>
<div class="account_param_container account_param_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
<div class="account_param_container account_param_content">
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('Field') ?></th>
		<th><?php echo Lang::_('Value') ?></th>
	</tr>
	<tr>
		<td><?php echo Lang::_('ID') ?></td>
		<td><?php echo $F->output_string_html($P->id); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('User') ?></td>
		<td><?php echo $F->output_string_html($P->data['login']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('description') ?></td>
		<td><?php echo $F->output_string_html($P->data['description']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('email') ?></td>
		<td><?php echo $F->output_string_html($P->data['email']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('created') ?></td>
		<td><?php echo $F->output_string_html($P->data['created']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('last login') ?></td>
		<td><?php echo $F->output_string_html($P->data['last_login']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('state') ?></td>
		<td><?php echo $F->output_string_html($P->data['state']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('roles') ?></td>
		<td><?php echo $F->output_string_html( implode(', ', $P->roles)); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('LOGGED_USER_CLIENT') ?></td>
		<td><?php echo $F->output_string_html($data_Client['name']); ?></td>
	</tr>
</table>
</div>
<?php

?>
<div class="account_param_container account_param_bottom container_bottom"></div>