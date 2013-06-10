<?php


$Page->head_title = Lang::_('Login');


$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();

if( $P->logged_in ) $F->redirect( $F->make_link('account') );

$Info = Info::g_global();

if( $F->check_login('CLIENT') ) {
//   list($login, $password) = $F->get_login_data(PERSON_TYPE);
   $status = $P->check_person_login($F->POST['lgn_CLIENT'], $F->POST['pswrd_CLIENT'], 'CLIENT');
   if( $status ) {
      //login
      $Shopping_Basket_Chain->login_user();
      $Info->add(Lang::_('LOGGED_IN_USER') . ' ' . $P->login, 'success');
      $Page->redirect( $F->make_link('main') );
   } else {
      $Info->add(Lang::_('wrong user or password'));
      $Page->redirect( $F->make_link(CFG_COM_LOGIN) );
   }
   die();
} else {
   //nothing
   //$Info->add(Lang::_('ala ma kota'));
}

?>
<?php echo $F->draw_form('login', $F->make_link(CFG_COM_LOGIN), 'post'); ?><br>
<table class="pass_table" style="border: 0">
	<tr>
		<td><strong><?php echo Lang::_('User'); ?></strong></td>
		<td><?php echo $F->draw_input_field('lgn_CLIENT', '', ' style="width: 120px"'); ?></td>
		<td rowspan="2"><?php echo $F->static_image_submit($Page->path_img . 'guzik-zaloguj.jpg', TEXT_LOGIN,''); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Lang::_('PASSWORD'); ?></strong></td>
		<td><?php echo $F->draw_password_field('pswrd_CLIENT', ' style="width: 120px"'); ?></td>
	</tr>
</table>
<?php echo $F->draw_form_close(); ?>
