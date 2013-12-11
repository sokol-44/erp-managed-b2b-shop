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
   if( $need_time < 5 ) $need_time = 5; 
//    echo "   $need_power  $need_time  ";
   
   $query = 'SELECT bu.id_ups, bu.model, bu.output_power, bu.output_power_w, bu.cabinet,
   bu.internal_count, bu.internal_capacity, bu.external_count, bu.external_capacity
   FROM `tool_battery_ups` bu
   WHERE bu.output_power_w >= ' . db_int($need_power) . '
   order by bu.output_power_w';
   $db_res = db_query($query);

   //print_debug($query);

   $load_result = db_result_array_full_id($db_res);
   
   $query2 = '';
   $query2_tmpl1 = '(SELECT #id_ups# as id_ups, (bd_i.minutes-1) as time FROM tool_battery_data bd_i
	WHERE bd_i.capacity = #cap1# and ( bd_i.result * #count1# ) <= ' . db_int($need_power) . '
	LIMIT 1)';
   $query2_tmpl2 = '(SELECT #id_ups# as id_ups, (bd_i.minutes-1) as time FROM tool_battery_data bd_i
	left join tool_battery_data bd_c ON ( bd_i.minutes = bd_c.minutes )
	WHERE bd_i.capacity = #cap1# and bd_c.capacity = #cap2# and 
   ( bd_i.result * #count1# + bd_c.result * #count2# ) <= ' . db_int($need_power) . '
	LIMIT 1)';
   $query2_arr = array();

   $ar_ch = array('#id_ups#', '#cap1#', '#count1#', '#cap2#', '#count2#');
   foreach($load_result as $id_ups => $ups_data) {
   	if( $ups_data['internal_count'] > 0 && $ups_data['external_count'] > 0  ) {		
   		$ar_r = array($id_ups, 
   				$ups_data['internal_capacity'], $ups_data['internal_count'], 
   				$ups_data['external_capacity'], $ups_data['external_count']);
   		$query2_arr[] = str_replace($ar_ch, $ar_r, $query2_tmpl2);
   	} else {		
   		$ar_r = array($id_ups, 
   				$ups_data['internal_capacity'] + $ups_data['external_capacity'],
   				$ups_data['internal_count'] + $ups_data['external_count'], 0 , 0);
   		$query2_arr[] = str_replace($ar_ch, $ar_r, $query2_tmpl1);
   	}
   }
   if( $F->not_null($query2_arr) )  {
   	$query2 = implode("\nUNION ALL\n", $query2_arr);
   	$db_res2 = db_query($query2);
   	$load_result2 = db_result_array_full_id($db_res2);
   	//print_debug($load_result2);
   	foreach( $load_result2 as $id_ups => $ups_data ) {
   		$load_result[$id_ups]['time'] = $ups_data['time'];
   	}
   	//print_debug($load_result);
   	foreach($load_result as $id_ups => $ups_data) {
   		$output_power[$id_ups]  = $ups_data['output_power'];
   		$time[$id_ups] = $ups_data['time'];
   	}
   	array_multisort($output_power, SORT_ASC, $time, SORT_ASC, $load_result);
   	
		$nr_ups = array('30' => 0, '60' => 0, '120' => 0, '180' => 0, '240' => 0, '360' => 0, '2400' => 0);
   	foreach($load_result as $id_ups => $ups_data) {
   		$time_diff = $ups_data['time'] - $need_time;
   		foreach($nr_ups as $tt => $tc) { 
   			if( $time_diff > 0 && $time_diff < $tt ) $nr_ups[$tt]++;
   		}
   	}
   	
   	$min_time = 2400;
   	foreach($nr_ups as $tt => $tc) {
   		if( $tc>4 ) { $min_time = $tt; break; }
   	}  	
   	
   } else {
   	$load_result = array();
   }
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
	</tr><!-- 
	<tr>
		<td><strong>współczynnik mocy / sprawność falowanik</strong></td>
		<td colspan="3">0,8 / 0,93 ~= 0,8602</td>
	</tr>-->
	<tr>
		<td><strong>Czas podtrzymywania:</strong></td>
		<td colspan="3"><?php echo $F->draw_input_field('battery_time', '30', ' style="width: 30px"'); ?> <strong>[min]</strong></td>
	</tr><!-- 
	<tr>
 		<td><strong>Ogranicz liczbę wyników:</strong></td>
		<td colspan="3"><?php echo $F->draw_checkbox_field('less_ups', 'yes'); ?> <strong></strong></td>
	</tr> -->
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
<!-- Wymagana moc: <?php echo round($need_power/$wm_sf,2); ?> [kVA]<br><br>-->
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('UPS NAME')?></th>
		<th><?php echo Lang::_('UPS CABINET NAME') ?></th>
		<th><?php echo Lang::_('OUTPUT_POWER'). '<br>[VA/W]'?></th>
		<th><?php echo Lang::_('UPS BACKUP TIME').' [min]' ?></th>
	</tr>
	<?php
	foreach( $load_result as $product ) {
		if( !isset($product['time']) ) continue;
 		if( ($product['time']-$need_time) < -5 )  continue;
		if( $product['time']-$need_time > $min_time )  continue;
		
		if( $product['time'] < $need_time ) $time_txt = '<span style="color: red">'.(int)$product['time'].'</span>';
		else $time_txt = '<span style="color: black">'.(int)$product['time'].'</span>';
	?>
	<tr>
		<td valign="top" width="30%"><?php echo $product['model']?></td>
		<td valign="top" width="30%"><?php echo $product['cabinet']?></td>
		<td valign="top" width="20%" align="right"><?php echo $product['output_power'].'/'.$product['output_power_w'] ?></td>
		<td valign="top" width="20%" align="right"><?php echo $time_txt; ?></td>
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