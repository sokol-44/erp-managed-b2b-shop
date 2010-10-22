<?php
$table = array();
$GET_tmp = $F->make_get();
$GET_edit = $F->add_local_get('mode', 'edit');
$GET_remove = $F->add_local_get('mode', 'remove');
$GET_view_persons = $F->add_local_get('mode', 'view_persons');
$GET_view_transactions = $F->add_local_get('mode', 'view_transactions');

foreach( $clients_list as $des ) {
   $link_edit = $F->make_link($F->com, $F->add_local_get('id_client', $des['id_client'], $GET_edit));
   $link_remove = $F->make_link($F->com, $F->add_local_get('id_client', $des['id_client'], $GET_remove));
   $link_view_persons = $F->make_link($F->com, $F->add_local_get('id_client', $des['id_client'], $GET_view_persons));
   $link_view_pictures = $F->make_link('transactions', $F->add_local_get('id_client', $des['id_client'], $GET_view_transactions));

   $tr['params'] = '';
   $tr['boxparam'] = array('name' => 'list', 'value' =>$des['id_client'], 'params' => array());
   $tr['cells'][0]['content'] = $des['id_client'];
   $tr['cells'][1]['content'] = $des['name'];
   $tr['cells'][2]['content'] = $des['description'];
   $tr['cells'][3]['content'] = $des['email'];
   $tr['cells'][4]['content'] = $des['phone'];
   $tr['cells'][5]['content'] = $des['created'];
   $tr['cells'][6]['content'] = $des['state'];
   $tr['cells'][7]['content'] = $F->draw_link($link_view_persons, 'onclick="go_to_row_folder()" title="' . Lang::_('Folder') . '"', $F->static_image('icon/folder_16.png', Lang::_('Folder')));
   $tr['cells'][8]['content'] = $F->draw_link($link_view_pictures, 'onclick="go_to_row_folder()" title="' . Lang::_('Folder') . '"', $F->static_image('icon/folder_16.png', Lang::_('Folder')));
   $tr['cells'][9]['content'] = $F->draw_link($link_edit, 'onclick="go_to_row_edit()" title="' . Lang::_('Edit') . '"', $F->static_image('icon/pencil_16.png', Lang::_('Edit')));
   $tr['cells'][10]['content'] = '';
   if( $des['state'] != 'ERASED' )
      $tr['cells'][10]['content'] = $F->draw_link($link_remove, 'onclick="go_to_row_remove()" title="' . Lang::_('Remove') . '"', $F->static_image('icon/trash_16.png', Lang::_('Remove')));

   $table[] = $tr;
}

ob_start();
$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');
$Page->add_js_raw('var TEXT_LINK_TITLE_EDIT = "' . Lang::_('Edit', true) . '";');
$Page->add_js_raw('var TEXT_LINK_TITLE_REMOVE = "' . Lang::_('Remove', true) . '";');
$Page->add_js_raw('var TEXT_NO_SELECTED_ROW = "' . Lang::_('TEXT_NO_SELECTED_ROW', true) . '";');
$Page->add_js_raw('var TEXT_ARE_YOU_SURE_REMOVE = "' . Lang::_('TEXT_ARE_YOU_SURE_REMOVE', true) . '";');
$Page->add_js_raw('var TEXT_LINK_TITLE_FOLDERS = "' . Lang::_('Folder', true) . '";');
?>
<div class="InfoBox">toolbox - client_list_viewer</div>
<div class="ToolBox"><?php
$GET_tmp = $F->make_get();
$GET_local_add = $F->add_local_get('mode', 'add');
$link = $F->make_link($F->com, $GET_local_add);
// static_image('icon' . DS . 'plus_32.png', $alt = '', $width = '', $height = '', $parameters = '';
echo
$F->static_image('icon/plus_32.png', Lang::_('Add'), 'onclick="go_to_href(\'' . $link . '\')"') .
$F->static_image('icon/trash_32.png', Lang::_('Remove'), 'onclick="go_to_row_remove()"') .
$F->static_image('icon/pencil_32.png', Lang::_('Edit'), 'onclick="go_to_row_edit()"') .
$F->static_image('icon/folder_32.png', Lang::_('Folder'), 'onclick="go_to_row_folders()"');
?></div>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th></th>
		<th><?php echo Lang::_('ID') ?></th>
		<th><?php echo Lang::_('NAME') ?></th>
		<th><?php echo Lang::_('DESCRIPTION') ?></th>
		<th><?php echo Lang::_('EMAIL') ?></th>
		<th><?php echo Lang::_('PHONE') ?></th>
		<th><?php echo Lang::_('CReATED') ?></th>
		<th><?php echo Lang::_('STATE') ?></th>
		<th><?php echo Lang::_('SHOW CLIENT USERS') ?></th>
		<th><?php echo Lang::_('SHOW CLIENT TRANSACTIONS') ?></th>
		<th><?php echo Lang::_('EDIT') ?></th>
		<th><?php echo Lang::_('REMOVE') ?></th>
	</tr>
	<?php
	echo $F->table_rows($table);
	?>
</table>
	<?php
	$Page->component_html = ob_get_clean();
	ob_end_flush();


	$Page->second_head_html;
	?>