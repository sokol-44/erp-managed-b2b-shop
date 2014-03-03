<?php
   $Page->head_title = $F->output_string_html( 'Dobór UPS' );
   $battery_load_power_tmp = 1000;

   $battery_load_power_array = array(
   		'W'   => array('id' => 'W',   'text' => 'W'),
   		'kW'  => array('id' => 'kW',  'text' => 'kW'),
   		'Va'  => array('id' => 'Va',  'text' => 'Va'),
   		'kVa' => array('id' => 'kVa', 'text' => 'kVa')
   );
   
   $battery_typology_array = array(
   		'0'  => array('id' => '',  'text' => 'Wszystkie'),
   		'LI'  => array('id' => 'LI',  'text' => 'LINE-INTERACTIVE'),
   		'OL' => array('id' => 'OL', 'text' => 'ON-LINE')
   );
   
   $battery_box_array = array(
   		'0'  => array('id' => '',  'text' => 'Wszystkie'),
   		'19'  => array('id' => '19',  'text' => 'RACK 19"'),
   		'TOWER' => array('id' => 'TOWER', 'text' => 'TOWER')
   );
   
   $battery_phase_array = array(
   		'0'  => array('id' => '',  'text' => 'Wszystkie'),
   		'1'  => array('id' => '1',  'text' => '1f/1f i 1f/3f'),
   		'3' => array('id' => '3', 'text' => '3f/3f')
   );
   
   $battery_maker_array = array(
   		'0'  => array('id' => '',  'text' => 'Wszystkie'),
   		'GT'  => array('id' => 'GT',  'text' => 'GT'),
   		'GTEC' => array('id' => 'GTEC', 'text' => 'GTEC')
   );
   
if( $F->check_get('search') ) {
   $filters = array();
   $load_result = array();
   /*
   battery_load_power
   battery_time
    */
   $wm_sf = 0.8602;
   $need_power = $F->get_float('battery_load_power');
   $blp_denom = $F->GET['battery_load_power_denomination'];
   
   if( isset($battery_load_power_array[$blp_denom]) ) {
   	if( stristr($blp_denom, 'W') !== FALSE ) {
   		$where_denom = 'output_power_w';
   		$normalize_w = false;
   	} else {
   		$where_denom = 'output_power';
   		$normalize_w = true;
   	}
   	
   	if( stristr($blp_denom, 'k') !== FALSE  ) {
   		$need_power = 1000 * $need_power;
   	}
   	
   	if( (int)$F->GET['additional_power'] > 0  ) {
   		$additional_power = (int)$F->GET['additional_power']/100;
   		$need_power = $additional_power*$need_power + $need_power;
   	}
   	
      	$where_array = array();
   	
   	if( $F->check_get('battery_typology') && isset( $battery_typology_array[$F->GET['battery_typology']] ) 
   		&& strlen($F->GET['battery_typology']) > 1 ) {
   		$where_array['bu.typology'] = $battery_typology_array[$F->GET['battery_typology']]['text'];
   	}
   	
   	if( $F->check_get('battery_box') && isset( $battery_box_array[$F->GET['battery_box']] ) 
   		&& strlen($F->GET['battery_box']) > 1 ) {
   		$where_array['bu.box'] = $battery_box_array[$F->GET['battery_box']]['text'];
   	}
   	
   	if( $F->check_get('battery_maker') && isset( $battery_maker_array[$F->GET['battery_maker']] ) 
   		&& strlen($F->GET['battery_maker']) > 1 ) {
   		$where_array['bu.maker'] = $battery_maker_array[$F->GET['battery_maker']]['text'];
   	}
   	
   } else {
   	$where_denom = 'output_power_w';
   	$normalize_w = false;
   }
   
   $need_time = (int)$F->GET['battery_time'];
   if( $need_time < 5 ) $need_time = 5; 
//    echo "   $need_power  $need_time  ";
   
   if( sizeof($where_array) > 0 ) $where_add = ' and ' . db_unroll_conditions($where_array);
   else $where_add = '';
   
   $query = 'SELECT bu.id_ups, bu.model, bu.output_power, bu.output_power_w, bu.cabinet,
   bu.internal_count, bu.internal_capacity, bu.external_count, bu.external_capacity, 
   bu.box, bu.typology, bu.phase, bu.output_power_w/bu.output_power as normalize_w,
   bu.quality, bu.maker
   FROM `tool_battery_ups` bu
   WHERE bu.'.$where_denom.' >= ' . db_int($need_power) . ' ' . $where_add . '
   order by bu.'.$where_denom.'';
//    print_debug($query);
   $db_res = db_query($query);

   $load_result = db_result_array_full_id($db_res);
   
   $query2 = '';
   $query2_tmpl1 = '(SELECT #id_ups# as id_ups, (bd_i.minutes-1) as time FROM tool_battery_data bd_i
	WHERE bd_i.capacity = #cap1# and ( bd_i.result * #count1# ) <= #need_power_batt#
	LIMIT 1)';
   $query2_tmpl2 = '(SELECT #id_ups# as id_ups, (bd_i.minutes-1) as time FROM tool_battery_data bd_i
	left join tool_battery_data bd_c ON ( bd_i.minutes = bd_c.minutes )
	WHERE bd_i.capacity = #cap1# and bd_c.capacity = #cap2# and 
   ( bd_i.result * #count1# + bd_c.result * #count2# ) <= #need_power_batt#
	LIMIT 1)';
   $query2_arr = array();

   $need_power_batt = $need_power;
   $ar_ch = array('#id_ups#', '#need_power_batt#', '#cap1#', '#count1#', '#cap2#', '#count2#');
   foreach($load_result as $id_ups => $ups_data) {
   	if( $ups_data['internal_count'] > 0 && $ups_data['external_count'] > 0  ) {
   		if( $normalize_w ) $need_power_batt = $need_power * $ups_data['normalize_w'];
   		$ar_r = array($id_ups, $need_power_batt,
   				$ups_data['internal_capacity'], $ups_data['internal_count'], 
   				$ups_data['external_capacity'], $ups_data['external_count']);
   		$query2_arr[] = str_replace($ar_ch, $ar_r, $query2_tmpl2);
   	} else {		
   		$ar_r = array($id_ups, $need_power_batt, 
   				$ups_data['internal_capacity'] + $ups_data['external_capacity'],
   				$ups_data['internal_count'] + $ups_data['external_count'], 0 , 0);
   		$query2_arr[] = str_replace($ar_ch, $ar_r, $query2_tmpl1);
   	}
   }

   if( $F->not_null($query2_arr) )  {
   	$query2 = implode("\nUNION ALL\n", $query2_arr);
	// print_debug($query2);
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
	<div class="account_address_container container_subheader">Parametry stałe<div class="icon"></div></div>
<table class="pass_table" style="border: 0; width: 100%;">
	<tr>
		<td><strong>Temperatura otoczenia:</strong></td>
		<td>25 <strong>[°C]</strong></td>
	</tr>
</table>
<div class="account_address_container container_subheader">Parametry szukane<div class="icon"></div></div>
<?php echo $F->draw_form('tool_battery', $F->make_link(CFG_COM_TOOL), 'GET'); ?><br>
<?php echo $F->draw_hidden_field('com', 'tool'); ?>
<?php echo $F->draw_hidden_field('sub', 'tool_battery'); ?>
<table class="pass_table" style="border: 0; width: 100%;">
	<tr>
		<td><strong>Moc czynna:</strong></td>
		<td colspan="3"><?php 
		echo $F->draw_input_field('battery_load_power', (float)$battery_load_power_tmp, ' style="width: 30px"'); 
		//array('id' => '', 'text' => '');
		
		echo $F->draw_pull_down_menu('battery_load_power_denomination', $battery_load_power_array, $F->GET['battery_load_power_denomination']);
		?>
		</td>
	</tr>
	<tr>
		<td><strong>Czas podtrzymywania:</strong></td>
		<td colspan="3"><?php echo $F->draw_input_field('battery_time', '30', ' style="width: 30px"'); ?> <strong>[min]</strong></td>
	</tr>
	<tr>
		<td><strong>Producent:</strong></td>
		<td colspan="3"><?php 
		echo $F->draw_pull_down_menu('battery_maker', $battery_maker_array, $F->GET['battery_maker']);
		?>
		</td>
	</tr>
	<tr>
		<td><strong>Typologia:</strong></td>
		<td colspan="3"><?php 
		echo $F->draw_pull_down_menu('battery_typology', $battery_typology_array, $F->GET['battery_typology']);
		?>
		</td>
	</tr>
	<tr>
		<td><strong>Obudowa:</strong></td>
		<td colspan="3"><?php 
		echo $F->draw_pull_down_menu('battery_box', $battery_box_array, $F->GET['battery_box']);
		?>
		</td>
	</tr>
	<tr>
		<td><strong>Fazy:</strong></td>
		<td colspan="3"><?php 
		echo $F->draw_pull_down_menu('battery_phase', $battery_phase_array, $F->GET['battery_phase']);
		?>
		</td>
	</tr>
	<tr>
		<td><strong>Moc zapasowa:</strong></td>
		<td colspan="3"><?php echo $F->draw_input_field('additional_power', '0', ' style="width: 30px"'); ?> <strong>[%]</strong></td>
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
<?php //print_debug($query) 


?>
<div class="account_address_container container_subheader">Wynik<div class="icon"></div></div>
<!-- Wymagana moc: <?php echo round($need_power/$wm_sf,2); ?> [kVA]<br><br>-->
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php /*echo Lang::_('UPS NAME')*/ ?>Producent<br>Model</th>
		<th><?php echo Lang::_('UPS CABINET NAME') ?></th>
		<th><?php echo Lang::_('OUTPUT_POWER'). '<br>[VA/W]'?></th>
		<th><?php echo 'Typologia'."<br>".'Obudowa'; ?></th>
		<th><?php echo 'Fazy'; ?></th>
		<th><?php echo 'Jakosc'; ?></th>
		<th><?php /*echo Lang::_('UPS BACKUP TIME').' [min]'*/ ?>Czas podtrzymy&shy;wania [min]</th>
	</tr>
	<?php
	foreach( $load_result as $product ) {
		if( !isset($product['time']) ) continue;
		if( $product['time'] < 5 ) continue;
 		// if( ($product['time']-$need_time) < -5 )  continue;
		// if( $product['time']-$need_time > $min_time )  continue;
		
		if( $product['time'] < $need_time ) $time_txt = '<span style="color: red">'.(int)$product['time'].'</span>';
		else $time_txt = '<span style="color: black">'.(int)$product['time'].'</span>';
		
		
		/*
		$g = array('a' => 'b', 'c' => array('qq' => 'q1', 'ww'));
		
		$add_basket_link = $F->make_link(CFG_COM_BASKET, $g);
		$add_basket_link = $F->draw_link($add_basket_link, 'title="' . Lang::_('show BASKET') . '"', Lang::_('show BASKET'), Lang::_('show BASKET'));
		
		echo $add_basket_link;
		*/
	?>
	<tr>
		<td valign="top" width="20%"><?php echo $product['model'].'<br>&nbsp;<small><i>'.$product['maker']."</i>"?></td>
		<td valign="top" width="20%"><?php echo $product['cabinet']?></td>
		<td valign="top" width="10%" align="right"><?php echo $product['output_power'].'/'.$product['output_power_w'] ?></td>
		<td valign="top" width="10%" align="right"><?php echo strtolower($product['typology'])."<br>".$product['box']; ?></td>
		<td valign="top" width="5%" align="right"><?php echo $product['phase']; ?></td>
		<td valign="top" width="5%" align="right"><?php echo $product['quality']; ?></td>
		<td valign="top" width="5%" align="right"><?php echo $time_txt; ?></td>
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