<?php
$GET_tmp = $F->make_get();

$id_product = (int)$F->GET['id_product'];

$product_info = Product::get_product_info( $id_product );

$image_type = Product::get_product_image_type( $id_product );
$small_image_html='';
?>
<div class="search_container">
<div class="search_container search_title container_header">Promocje<div class="icon"></div></div>
<?php
if( $F->not_null($load_result) ) {
?>
<hr>
<div class="search_container search_result">
<?php //print_debug($query) ?>
<div class="account_address_container container_subheader">Wyniki<div class="icon"></div></div>
<?php 
$col_array = array('product_column_quality1', 'product_column_quality5', 'product_column_quality10');
$col_dsp_array = array('1' => 'Tanio', '5' => 'Optymalnie', '10' => 'Najlepiej');
foreach($col_array as $nr_col => $class_add) {
echo '<div class="product_columns product_column_'.(int)$nr_col.' '.$class_add.'">';
	$quality = (int)str_replace('product_column_quality', '', $class_add);
	
	echo '<h3>'.$col_dsp_array[$quality].'</h3>';
	$data = array('data'=> '', 'bottom'=> '', 'footer' => '');
	if( Framework::not_null($ups_3cls_array[$quality]['over']) ) {
	  $product = $ups_3cls_array[$quality]['over'];
	  
	  if( $product['time'] < $need_time ) $time_txt = '<span style="color: red">'.(int)$product['time'].' min</span>';
	  else $time_txt = '<span style="color: black">'.(int)$product['time'].' min</span>';
	  
	  $data = array(
			'data'=> 
			'Producent: ' . $product['maker'] . "<br>".
			'Model: ' . $product['model'] . "<br>" .
(($product['cabinet']!='')?"Szafka: " . $product['cabinet_count'] . ' x '.$product['cabinet']."<br>":'') . "<br>".
			'Typologia: ' . strtolower($product['typology']) . "<br>".
			'Fazy: ' . strtolower($product['phase']) . "<br>",
		 'bottom'=> 
			$product['output_power'].'/'.$product['output_power_w'] . ' [VA/W]',
		 'footer' => $time_txt
		);
		echo product_box($data);
	} else {
		echo product_box( false );
		//echo product_box( array('data'=> '<h5>Brak</h5>', 'bottom'=> '-', 'footer' => '-') );
	}
	
	if( Framework::not_null($ups_3cls_array[$quality]['under']) ) {
	  $product = $ups_3cls_array[$quality]['under'];
	  
	  if( $product['time'] < $need_time ) $time_txt = '<span style="color: red">'.(int)$product['time'].' min</span>';
	  else $time_txt = '<span style="color: black">'.(int)$product['time'].' min</span>';

	  	if( (int)$product['cabinet_count'] == 1 ) {
	  		$cabinet_txt = "Szafka: " . $product['cabinet'];
		} elseif ( (int)$product['cabinet_count'] > 1 ) {
	  		$cabinet_txt = "Szafka: " . (int)$product['cabinet_count'].' x '.$product['cabinet'];
	  	}	else {
	  		$cabinet_txt = '';
	  	}
	  
	  $data = array(
			'data'=> 
			'Producent: ' . $product['maker'] . "<br>".
			'Model: ' . $product['model'] . "<br>" .
			(($cabinet_txt!='')?$cabinet_txt."<br>":'') . "<br>".
			'Typologia: ' . strtolower($product['typology']) . "<br>".
			'Fazy: ' . strtolower($product['phase']) . "<br>",
		 'bottom'=> 
			$product['output_power'].'/'.$product['output_power_w'] . ' [VA/W]',
		 'footer' => $time_txt
		);
		echo product_box($data);
	} else {
		echo product_box( false );
		//echo product_box( array('data'=> '<h5>Brak</h5>', 'bottom'=> '-', 'footer' => '-') );
	}

echo '</div>';
}
?>
</div>
<?php
}

function product_box($data) {

if( !$data ) { 
return '
<div class="product_in_box" style="visibility: hidden;">
	<div class="product_in_box_data">
	' . $data['data'] . '
	</div>
	<div class="product_in_box_bottom">
	' . $data['bottom'] . '
	</div>
	<div class="product_in_box_footer">
	' . $data['footer'] . '
	</div>
</div>
';
	


}

return '
<div class="product_in_box">
	<div class="product_in_box_data">
	' . $data['data'] . '
	</div>
	<div class="product_in_box_bottom">
	' . $data['bottom'] . '
	</div>
	<div class="product_in_box_footer">
	' . $data['footer'] . '
	</div>
</div>
';
}
?>
<div class="search_container search_bottom container_bottom"></div>
</div>
