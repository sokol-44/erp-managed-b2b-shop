<?php
$SP = new SplitPage('ACCOUNT_LIST');
$Page->head_title =Lang::_('Account list');

if( !$P->logged_in ) $F->redirect( $F->make_link('main') );

if( !$P->check_roles('ADMIN') ) $F->redirect( $F->make_link('account') );

$type = false;

if( $F->check_get('type') ) {
   switch( $F->GET['type'] ) {
      case 'administrator': $type = 'ADMIN'; break;
      case 'operator': $type = 'OPERATOR'; break;
      case 'user': $type = 'USER'; break;
      default: break;
   }
}

$BC->add_crumb(Lang::_('Account'), $F->make_link(CFG_COM_ACCOUNT) );
$BC->add_crumb(Lang::_('Account list'), $F->make_link(CFG_COM_ORDER_LIST) );


$account_list = Data::get_persons_list('CLIENT', (int)$P->data['id_client'], $type);

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$GET_tmp = $F->make_get();

//for all
echo Lang::_('Account list');
?>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('ID') ?></th>
		<th><?php echo Lang::_('NAME') . ', ' . Lang::_('EMAIL') . ', ' . Lang::_('DESCRIPTION') . ', ' . Lang::_('Created'); ?></th>
		<th><?php echo Lang::_('Last login') ?></th>
		<th><?php echo Lang::_('ROLES') ?></th>
		<th><?php echo Lang::_('STATE') ?></th>
	</tr>
	<?php
	foreach( $account_list as $account ) {
	   $GET_tmp = $F->add_local_get('id_client_user', $account['id_client_user'], $GET_tmp);
	   
	   $link_product_info = $F->make_link(CFG_COM_PRODUCT_INFO, $GET_tmp);
	   $cell_account_info = Lang::_('NAME') . ': ' . $F->output_string_html( $account['login'] ) . '<br>
	   ' . Lang::_('EMAIL') . ': ' . $F->output_string_html( $account['email'] ) . '<br>
	   ' . Lang::_('DESCRIPTION') . ':<br>' . nl2br($F->output_string_html( $account['description'], 256 ) ) . '<br>
	   ' . Lang::_('created') . ': ' . $F->output_string_html( $account['created'] );
	   ?>
	<tr>
		<td width="5%"><?php echo $F->draw_radio_field('list', $account['id_client_user'], false, 'style="display: none"') . (int)$account['id_client_user']; ?></td>
		<td valign="top"><?php echo $cell_account_info; ?></td>
		<td width="10%"><?php echo $F->output_string_html( $account['last_login'] );; ?></td>
		<td width="10%"><?php echo str_replace(',', "<br>\n", $account['rights_list']); ?></td>
		<td width="10%"><?php echo $account['state']; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<table style="border: 0">
	<tr>
		<td colspan="5"><?php echo $SP->display_links(); ?></td>
	</tr>
</table>
