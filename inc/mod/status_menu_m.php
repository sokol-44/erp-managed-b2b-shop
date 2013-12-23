<?php
if( $P->logged_in ) {
$data_Person = $P->get_public_data();
$data_Client = $P->get_client_data();

echo '<script>json_data.user_curent = "'.$F->json_string( $P->get_public_data() ).'";</script>';
?>
	<div id="iStoreStatusBar">
		<ul class="Menu" id="iStoreStatusMenu">
			<li id="iStoreStatusMenuLogout"><?php echo $F->draw_link($F->make_link(CFG_COM_LOGOUT),'', Lang::_('Logout') ); ?></li>
		</ul>
	</div>
<?php
} else {

?>
	<div id="iStoreStatusBar">
	</div>
<?php
}
?>