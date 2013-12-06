<?php
   $Page->head_title = $F->output_string_html( 'Badanie osiągów akumulatorów rozładowywanych stałomocowo i stałoprądowo' );
if( $F->check_get('search') ) {
   $filters = array();
   //FIXME - move to Data_Products, Products class ?
   $load_result = array('a');
} else {
   $load_result = array();
}

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');
$Page->add_js_file('tool_battery.js');

$GET_tmp = $F->make_get();
?>
<div class="search_container">
  <div class="search_container search_title container_header">Badanie osiągów akumulatorów rozładowywanych stałomocowo i stałoprądowo<div class="icon"></div></div>
  <div class="search_container search_form">
<?php echo $F->draw_form('search', $F->make_link(CFG_COM_TOOL), 'GET'); ?><br>
<?php echo $F->draw_hidden_field('com', 'tool'); ?>
<table class="pass_table" style="border: 0">
	<tr>
		<td><strong>Rodzaj rozładowania:</strong></td>
		<td colspan="3"><select name="battery_load" size="1" onchange="rodz_charakt(this.value);">
   	 	<option value="1">--Wybierz opcje--</option>
   	 	<option value="2">Stałomocowe</option>
   	 	<option value="3" selected="selected">Stałoprądowe</option>   	 	
	      </select></td>
	</tr>
	<tr>
		<td><strong>Marka i seria:</strong></td>
		<td colspan="1"><select name="battery_series" size="1">
   	 	<option selected="selected" value="EH7_100">EUROPOWER seria EH (7÷100Ah)</option>	
	      </select></td>
		<td>model:</td>
		<td><select name="batter_model" size="1" class="select">
	     <option selected="selected" value="EH 7">EH 7-12</option>
	     <option value="EH 28">EH 28-12</option>
	     </select></td>
	</tr>
	<tr><td colspan="4"><hr></td></tr>
	<tr>
		<td><strong>Temperatura otoczenia:</strong></td>
		<td><?php echo $F->draw_input_field('battery_temperature', '25', ' style="width: 30px"'); ?> <strong>[°C]</strong></td>
	</tr>
	<tr id="battery_load_current">
		<td><strong>Wartość pobieranego prądu: </strong></td>
		<td><?php echo $F->draw_input_field('battery_load_current', '10.5', ' style="width: 30px"'); ?> <strong>[A]</strong></td>
	</tr>
	<tr id="battery_load_power" style="display: none;">
		<td colspan="4">
		<table>
		<tr>
		<td><strong>Moc czynna P:</strong></td>
		<td colspan="3"><?php echo $F->draw_radio_field('battery_load_power_type', 'P', true); ?>
		<?php echo $F->draw_input_field('battery_load_power_p', '0.1', ' style="width: 30px"'); ?> <strong>[kW]</strong></td>
		</tr>
		<tr>
		<td><strong>Moc czynna S:</strong></td>
		<td colspan="3"><?php echo $F->draw_radio_field('battery_load_power_type', 'S', false); ?>
		<?php echo $F->draw_input_field('battery_load_power_s', '0.1', ' style="width: 30px"'); ?> <strong>[kW]</strong></td>
		</tr>
		<tr>
		<td></td>
		<td colspan="3">Współczynnik mocy cosø: 0.8  [-]	    Sprawność falownika η: 94 [%]</td>
		</tr>	
		</table>
		</td>
	</tr>
	<tr><td colspan="4"><hr></td></tr>
	<tr>
		<td><strong>Liczba ogniw w gałęzi:</strong></td>
		<td colspan="3"><?php echo $F->draw_radio_field('battery_cells_mode', 'Q', true); ?>
		<?php echo $F->draw_input_field('battery_cells', '6', ' style="width: 20px"'); ?> <strong>[°C]</strong></td>
	</tr>
	<tr>
		<td><strong>Minimalne końcowe napięcie rozładowania baterii:</strong></td>
		<td colspan="3"><?php echo $F->draw_radio_field('battery_cells_mode', 'V', false); ?>
		<?php echo $F->draw_input_field('battery_min_v', '299', ' style="width: 20px"'); ?> <strong>[V]</strong></td>
	</tr>
	<tr><td colspan="4"><hr></td></tr>
	<tr>
		<td><strong>Końcowe napięcie rozładowania ogniwa:</strong></td>
		<td><select name="battery_end_v" size="1">
   	  <option  selected="selected"  value="1.80">1.80</option><option value="1.60">1.60</option></select> <strong>[V/ogniwo]</strong></td>
	</tr>
	<tr>
		<td><strong>Liczba równoległych gałęzi:</strong></td>
		<td><?php echo $F->draw_input_field('battery_branch_num', '1', ' style="width: 20px"'); ?></td>
	</tr>
	<tr>
		<td colspan="4" align="center"><?php echo $F->dynamic_image_submit(Lang::_('Compute'),'COMPUTE'); ?></td>
	</tr>
</table>
</div>
<?php echo $F->draw_hidden_field('search', 'search'); ?>
<?php echo $F->draw_form_close(); ?>
<?php
if( $F->not_null($load_result) ) {
?>
<hr>
<div class="search_container search_result">
<div class="account_address_container container_subheader">Wynik<div class="icon"></div></div>
<fieldset style="margin: auto; padding-left:10px; padding-right:10px; padding-bottom:6px; margin-top:20px; width:510px; display:block; border:1px solid #C2C2C2; text-align:left">
  <legend align="left" style="font-size:11px; margin:0px; padding:1px; color:black; background-color:white; border:1px solid #939393;"> Wstępne oblicznia </legend>
Ilość akumulatorów 2 [szt]<br>
Końcowe napięcie rozładowania baterii: 10.80 [V]<br>
Wymagana moc ogniwa: 8.33 [W/ogniwo] </fieldset>
<fieldset style="margin: auto; padding-left:10px; padding-right:10px; padding-bottom:6px; margin-top:20px; width:510px; display:block; border:1px solid #C2C2C2; text-align:left">
  <legend align="left" style="font-size:11px; margin:0px; padding:1px; color:black; background-color:white; border:1px solid #939393;"> Czas pracy </legend>
Przy 100% pojemności:	5 [h] 24 [min]<br>
Przy 80% pojemności: 	3 [h] 52 [min]</fieldset>
</div>
<?php
}
?>
<div class="search_container search_bottom container_bottom"></div>
</div>