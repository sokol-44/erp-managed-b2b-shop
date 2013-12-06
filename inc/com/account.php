<?php
$Page->head_title = Lang::_('MY ACCOUNT');

$GET_tmp = $F->make_get();

//FIXME
//remember backtrack
//if( !$P->logged_in ) $F->redirect(  );

$BC->add_crumb(Lang::_('Account'), $F->make_link(CFG_COM_ACCOUNT) );
$data_Client = $P->get_client_data();
$Address_list = $P->get_address_list();
$Account_Manager_list = $P->get_account_manager_list();

?>
<div class="account_container">
  <div class="account_container account_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="account_container account_content">
<ul class="account_container account_box_list">
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'change_password')),'', Lang::_('change password') ); ?></li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'show_details')),'', Lang::_('show details') ); ?></li>
<!-- <li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'change_details')),'', Lang::_('change details') ); ?></li>-->
</ul><br>
<div id="account_data_list"><!-- 
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
</table> -->
<div class="account_address_container container_subheader"><?php echo Lang::_('account addresses'); ?><div class="icon"></div></div>
<?php
if( $F->not_null($Address_list) ) {
?>
<table class="tableBox" style="border: 0; width: 100%;" >
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('ID') ?></th>
		<th><?php echo Lang::_('Description') ?></th>
		<th><?php echo Lang::_('Address') ?></th>
	</tr>
<?php 
foreach($Address_list as $Address) {
	$addr_id = (int)$Address['id_address'];
	$addr_desc = $F->output_string($Address['description']);
	$addr_addr = $F->output_string($Address['name']) . '<br>' .
					 $F->output_string($Address['street']) . '<br>' .
					 $F->output_string($Address['zip_code'] . ' ' . $Address['city']) . '<br>' .
					 $F->output_string($Address['country']);
?>
	<tr>
		<td><?php echo $addr_id; ?></td>
		<td><?php echo $addr_desc; ?></td>
		<td><?php echo $addr_addr; ?></td>
	</tr>
<?php 
}
?>
</table>
<?php
} else {
 echo Lang::_('empty');
}
?>
<div class="account_manager_container container_subheader"><?php echo Lang::_('account managers'); ?><div class="icon"></div></div>
<?php
if( $F->not_null($Account_Manager_list) ) {
?>
<table class="tableBox" style="border: 0; width: 100%" >
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('ID') ?></th>
		<th><?php echo Lang::_('Name') ?></th>
		<th><?php echo Lang::_('Email') ?></th>
		<th><?php echo Lang::_('Telephones') ?></th>
	</tr>
<?php 
foreach($Account_Manager_list as $Account_Manager) {
	$am_id = (int)$Account_Manager['id_account_manager'];
	$am_name = $F->output_string($Account_Manager['fullname']);
	$am_email = $F->draw_email_link($Account_Manager['email']);
	$am_phone = $Account_Manager['phone1']."<br>";
	$am_phone .= $Account_Manager['phone2'];
?>
	<tr>
		<td><?php echo $am_id; ?></td>
		<td><?php echo $am_name; ?></td>
		<td><?php echo $am_email; ?></td>
		<td><?php echo $am_phone; ?></td>
	</tr>
<?php 
}
?>
</table>
<?php
} else {
 echo Lang::_('empty');
}
?>
<div class="accout_summery_container container_subheader"><?php echo Lang::_('account summary'); ?><div class="icon"></div></div>
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
		<td><?php echo Lang::_('Description') ?></td>
		<td><?php echo $F->output_string_html($P->data['description']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('LOGGED_USER_CLIENT') ?></td>
		<td><?php echo $F->output_string_html($data_Client['name']); ?></td>
	</tr>
</table>
 <?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'show_details')),'', Lang::_('show details') ); ?>
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