<?php
$Page->head_title = Lang::_('MY ACCOUNT');

$GET_tmp = $F->make_get();

//FIXME
//remember backtrack
if( !$P->logged_in ) $F->redirect(  );

$BC->add_crumb(Lang::_('Account'), $F->make_link(CFG_COM_ACCOUNT) );

//for all
?>
<div class="account_box">
<div id="account_box_title"></div>
<div id="account_box_graph"></div>
<div id="account_box_list">
<ul class="account_box_list">
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'change_password')),'', Lang::_('change password') ); ?></li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'show_details')),'', Lang::_('show details') ); ?></li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'change_details')),'', Lang::_('change details') ); ?></li>
</ul>
</div>
</div>
<?php

//admin + watcher
if( $P->check_roles('ADMIN,OPERATOR') ) {
?>
<div class="account_box">
<div id="account_box_title"></div>
<div id="account_box_graph"></div>
<div id="account_box_list">
<ul class="account_box_list">
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ORDER_LIST, array('mode' => 'all')),'', Lang::_('all orders') ); ?></li>
<li>1</li>
<li>2</li>
<li>3</li>
<li>4</li>
<li>5</li>
</ul>
</div>
</div>
<?php
}

//admin
if( $P->check_roles('ADMIN') ) {
?>
<div class="account_box">
<div id="account_box_title"></div>
<div id="account_box_graph"></div>
<div id="account_box_list">
<ul class="account_box_list">
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'account_list')),'', Lang::_('all account list') ); ?>
<ul>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'list_all', 'type' => 'administrator' )),'', Lang::_('administrators') ); ?></li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'list_all', 'type' => 'operator' )),'', Lang::_('operators') ); ?></li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'list_all', 'type' => 'user' )),'', Lang::_('users') ); ?></li>
</ul>
</li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'add_account')),'', Lang::_('add account') ); ?></li>
<li><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT_LIST, array('mode' => 'disabled_account')),'', Lang::_('disabled account') ); ?></li>
</ul>
</div>
</div>
<?php
}
?>