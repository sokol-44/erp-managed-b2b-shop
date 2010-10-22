<?php

ob_start();
?>
<div class="InfoBox"> toolbox - client_edit
</div>
<div class="ToolBox">
<?php

if( $F->GET['type'] == 'CLIENT' ) {
   $t_GET = $F->make_get(array('mode', 'id_client_user'));
   $link = $F->make_link($F->com, $F->add_local_get('mode', 'view_persons'));
} else {
   $link = $F->make_link($F->com, $F->make_get(array('mode', 'id_client_user')));
}
echo
$F->draw_link($link,'', $F->static_image('icon/left_32.png', Lang::_('Back'))) .
$F->static_image('icon/save_32.png', Lang::_('Save'), 'onclick="form_submit(\'person_edit\')"');

$Page->add_js_file('toolbox.js');
$Page->add_jq_init('set_toolbox_table(".tableBox");');
?>
</div>
<?php
echo $F->draw_form('client_edit', $F->self_link());
?>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('O co') ?></th>
		<th><?php echo Lang::_('Co') ?></th>
	</tr>
	<tr>
		<td><?php echo Lang::_('HOTEL NAME') ?></td>
		<td><?php echo $F->draw_input_field('name', $client_data['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('DESCRIPTION') ?></td>
		<td><?php echo $F->draw_textarea_field('description', 'auto', '30', '7', $client_data['description']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('EMAIL') ?></td>
		<td><?php echo $F->draw_input_field('email', $client_data['email']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('PHONE') ?></td>
		<td><?php echo $F->draw_input_field('phone', $client_data['phone']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('CREATED') ?></td>
		<td><?php echo $client_data['created'] ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('STATE') ?></td>
		<td><?php echo $F->show_person_account_state('ADMIN', $client_data['state']); ?></td>
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