<?php
if( $P->logged_in ) {
$data_Person = $P->get_public_data();
$data_Client = $P->get_client_data();

$person_text = Lang::_('LOGGED_IN_USER') . ': ' . $data_Person['login'];
if( $data_Person['data']['name']!='') $person_text .= ' (' . $data_Person['data']['name'] . ')';
else								  $person_text .= ' (' . $data_Person['data']['desciption'] . ')';

$client_text = Lang::_('LOGGED_USER_CLIENT') . ': ' . $data_Client['name'];

?>
<div class="person_data person_data_main">
	<div class="person_data_info person_text"><?php echo $F->output_string_html($person_text); ?></div>
	<div class="person_data_info client_text"><?php echo $F->output_string_html($client_text); ?></div>
</div>
<?php
}
?>