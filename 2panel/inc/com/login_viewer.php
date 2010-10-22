<?php

ob_start();
?>
<div id="innerFrame">
<div id="formularz"><?php echo $F->draw_form('login', $Page->get_form_get_string(), 'post'); ?><br>
<table class="pass_table" style="border: 0">
	<tr>
		<td><strong><?php echo TEXT_LOGIN; ?>:&nbsp;</strong></td>
		<td><?php echo $F->draw_input_field('lgn_ADMIN', '', ' style="width: 120px"'); ?>
		</td>
		<td rowspan="2"><?php echo $F->static_image_submit($Page->path_img . 'guzik-zaloguj.jpg', TEXT_LOGIN,''); ?>
		</td>
	</tr>
	<tr>
		<td><strong><?php echo TEXT_PASSWORD; ?>:&nbsp;</strong></td>
		<td><?php echo $F->draw_password_field('pswrd_ADMIN', ' style="width: 120px"'); ?></td>
	</tr>
</table>
<?php echo $F->draw_form_close(); ?>
</div>
</div>
<?php
$Page->component_html = ob_get_clean();
ob_end_flush();


$Page->second_head_html;
?>