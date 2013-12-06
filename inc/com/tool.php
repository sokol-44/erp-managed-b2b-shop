<?php
   $Page->head_title = $F->output_string_html( 'Dobór UPS' );
if( $F->check_get('search') ) {
   $filters = array();
   $load_result = array();
   /*
   battery_load_power
   battery_time
    */
   $wm_sf = 0.8602;
   $need_power = $F->GET['battery_load_power']*1000;
   $need_time = (int)$F->GET['battery_time'];
//    echo "   $need_power  $need_time  ";
   if( $F->GET['less_ups'] == 'yes' ) {
	   if( $need_power > 600 )  $max_power = 3*$need_power;
	   else $max_power=2000;
	   if( $need_power > 600 )  $max_output = 3*$need_power;
	   else $max_output=2000;
   } else {
   	$max_power=$need_power*10000;
   	$max_output=$need_power*10000;
   }
  
   $query = 'SELECT *, (rp_i+rp_c) as `sum_result` FROM (
SELECT bu.id_ups, bu.model, bu.output_power, bu.cabinet, if( bu.internal_count>0, (bu.internal_count * bd_i.`result`), 0) as rp_i,
if( bu.external_count>0, (bu.external_count * bd_c.`result`), 0) as rp_c
FROM `tool_battery_ups` bu 
left join `tool_battery_data` bd_i
on (bu.internal_count > 0 and bu.internal_capacity = bd_i.capacity and bd_i.minutes = ' . db_int($need_time) . ')
left join `tool_battery_data` bd_c
on (bu.external_count > 0 and bu.external_capacity = bd_c.capacity and bd_c.minutes = ' . db_int($need_time) . ')
WHERE bu.output_power > ' . db_int($need_power/$wm_sf) . ' and  bu.output_power < ' . db_int($max_output) . '
) as a
where (rp_i+rp_c) > ' . db_int($need_power) . ' and (rp_i+rp_c) < ' . db_int($max_power) . '
order by output_power, (rp_i+rp_c)';
   $db_res = db_query($query);
   $load_result = db_result_array_full_id($db_res);
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
  <div class="search_container search_title container_header">Dobór UPS<div class="icon"></div></div>
  <div class="search_container search_form">
<?php echo $F->draw_form('tool_battery', $F->make_link(CFG_COM_TOOL), 'GET'); ?><br>
<?php echo $F->draw_hidden_field('com', 'tool'); ?>
<?php echo $F->draw_hidden_field('sub', 'tool_battery'); ?>
<table class="pass_table" style="border: 0; width: 100%;">
	<tr>
		<td><strong>Temperatura otoczenia:</strong></td>
		<td>25 <strong>[°C]</strong></td>
		<!-- <td><?php echo $F->draw_input_field('battery_temperature', '25', ' style="width: 30px"'); ?> <strong>[°C]</strong></td> -->
	</tr>
	<tr>
		<td><strong>Moc czynna:</strong></td>
		<td colspan="3"><?php echo $F->draw_input_field('battery_load_power', '0.1', ' style="width: 30px"'); ?> <strong>[kW]</strong></td>
	</tr>
	<tr>
		<td><strong>współczynnik mocy / sprawność falowanik</strong></td>
		<td colspan="3">0,8 / 0,93 ~= 0,8602</td>
	</tr>
	<tr>
		<td><strong>Czas podtrzymywania:</strong></td>
		<td colspan="3"><?php echo $F->draw_input_field('battery_time', '30', ' style="width: 30px"'); ?> <strong>[min]</strong></td>
	</tr>
	<tr>
 		<td><strong>Ogranicz liczbę wyników:</strong></td>
		<td colspan="3"><?php echo $F->draw_checkbox_field('less_ups', 'yes'); ?> <strong></strong></td>
	</tr>
	<tr><td colspan="4"><hr></td></tr>
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
<?php //print_debug($query) ?>
<div class="account_address_container container_subheader">Wynik<div class="icon"></div></div>
Wymagana moc: <?php echo round($need_power/$wm_sf,2); ?> [kVA]<br><br>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('UPS NAME')?></th>
		<th><?php echo Lang::_('CABINET NAME') ?></th>
		<th><?php echo Lang::_('OUTPUT_POWER') ?></th>
		<th><?php echo Lang::_('ACTUAL_CAPACITY') ?></th>
	</tr>
	<?php
	foreach( $load_result as $product ) {
	?>
	<tr>
		<td valign="top" width="10%"><?php echo $product['model']?></td>
		<td valign="top" width="10%"><?php echo $product['cabinet']?></td>
		<td valign="top" width="10%"><?php echo $product['output_power']?></td>
		<td valign="top" width="10%"><?php echo round($product['sum_result'],2) ?></td>
	</tr>
<?php 
	}
?>
</table>
</div>
<?php
}
?>
<div class="search_container search_bottom container_bottom"></div>
</div>