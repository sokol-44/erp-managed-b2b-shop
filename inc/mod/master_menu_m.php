<?php
// if( $P->logged_in ) {
?>
		<div class="iStoreBox" id="iStoreToolbarMenu">
			<div class="iStoreBoxWrapper">
				<div class="iStoreBoxContent">	
					<ul>
<?php if( $P->logged_in ) { ?>
						<li id="iStoreToolbarMenuMain"><?php echo $F->draw_link($F->make_link(CFG_DEFAULT_COM),'', Lang::_('Main page') ); ?></li>
						<li id="iStoreToolbarMenuAccount"><?php echo $F->draw_link($F->make_link(CFG_COM_ACCOUNT),'', Lang::_('Account') ); ?></li>
<?php } else { ?>
						<li id="iStoreToolbarMenuAccount"><?php echo $F->draw_link($F->make_link(CFG_COM_LOGIN),'', Lang::_('Login') ); ?></li>
						<li id="iStoreToolbarMenuAccount"><?php echo $F->draw_link($F->make_link(CFG_COM_REGISTER),'', Lang::_('REGISTER') ); ?></li>
<?php } ?>
						<li id="iStoreToolbarMenuHelp"><?php echo $F->draw_link($F->make_link(CFG_COM_ARTICLE, $F->add_local_get('key', '4', $GET_tmp)), '', Lang::_('how to buy').'&nbsp;'); ?></li>
						<li id="iStoreToolbarMenuAccount"><?php echo $F->draw_link($F->make_link(CFG_COM_CONTACT),'', Lang::_('Contact') ); ?></li>
					</ul>
				</div>
			</div>
		</div>
