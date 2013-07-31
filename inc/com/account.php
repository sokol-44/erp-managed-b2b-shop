<?php
$Page->head_title = Lang::_('MY ACCOUNT');

$GET_tmp = $F->make_get();

//FIXME
//remember backtrack
//if( !$P->logged_in ) $F->redirect(  );

$BC->add_crumb(Lang::_('Account'), $F->make_link(CFG_COM_ACCOUNT) );

?>
<div class="account_container">
  <div class="account_container account_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="account_container account_content">
<ul class="account_container account_box_list">
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'change_password')),'', Lang::_('change password') ); ?></li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'show_details')),'', Lang::_('show details') ); ?></li>
<!-- <li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'change_details')),'', Lang::_('change details') ); ?></li>-->
</ul><br>
<div id="account_data_list">
<div class="account_container container_subheader">Rachunki<div class="icon"></div></div>
<table class="tableBox" style="border: 0; width: 400px;" >
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('Field') ?></th>
		<th><?php echo Lang::_('Value') ?></th>
	</tr>
	<tr>
		<td><?php echo 'Rachunki'; ?></td>
		<td><?php echo '0.00 zł' ?></td>
	</tr>
	<tr>
		<td><?php echo Limit; ?></td>
		<td><?php echo '1000.00 zł' ?></td>
	</tr>
	<tr>
		<td><?php echo 'Suma zakupów'; ?></td>
		<td><?php echo '876.00 zł' ?></td>
	</tr>
</table>

<div class="basket_container container_subheader"><?php echo Lang::_('basket history'); ?><div class="icon"></div></div>
<table class="tableBox" style="border: 0; width: 400px;" >
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
</table>
<div class="basket_container container_subheader"><?php echo Lang::_('basket versions'); ?><div class="icon"></div></div>
</div>
</div>
<?php
//admin + watcher
if( $P->check_roles('ADMIN,OPERATOR') ) {
?>
<div class="account_container container_subheader"><?php echo Lang::_('Orders');?><div class="icon"></div></div>
<div class="account_container account_box">
<div id="account_box_title"></div>
<div id="account_box_graph"></div>
<div id="account_box_list">
<ul class="account_box_list">
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ORDER_LIST, array('mode' => 'all')),'', Lang::_('all orders') ); ?></li>
</ul>
</div>
</div>
<?php
}

//admin
if( $P->check_roles('ADMIN') ) {
?>
<div class="account_container container_subheader"><?php echo Lang::_('Account list');?><div class="icon"></div></div>
<div class="account_container count_box">
<div id="account_box_title"></div>
<div id="account_box_graph"></div>
<div id="account_box_list">
<ul class="account_box_list">
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'account_list')),'', Lang::_('all account list') ); ?>
<!--
<ul>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'account_list', 'type' => 'administrator' )),'', Lang::_('administrators') ); ?></li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'account_list', 'type' => 'operator' )),'', Lang::_('operators') ); ?></li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'account_list', 'type' => 'user' )),'', Lang::_('users') ); ?></li>
</ul>
</li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'add_account')),'', Lang::_('add account') ); ?></li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'disabled_account')),'', Lang::_('disabled account') ); ?></li>
 -->
</ul>
</div>
</div>
<?php
}
?>
  <div class="account_container aaccount_bottom container_bottom"></div>
</div>