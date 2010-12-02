<?php

//FIXME
//remember backtrack
if( !$P->logged_in ) $F->redirect(  );

?>
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
		<td><?php echo Lang::_('Login') ?></td>
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
		<td><?php echo Lang::_('state') ?></td>
		<td><?php echo $F->output_string_html($P->data['state']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('roles') ?></td>
		<td><?php echo $F->output_string_html( implode(', ', $P->roles)); ?></td>
	</tr>
</table>
<?php

?>