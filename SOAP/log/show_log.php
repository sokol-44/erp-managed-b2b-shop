<html>
<head>
 <title>Dirlist</title>
 <style type="text/css">
 table {
 	border: 1px solid black;
 	border-collapse: collapse;
 }
 table th {
 	border: 1px solid black;
 	border-collapse: collapse; 
 	padding: 5px;
 }
 table td {
 	border: 1px solid black;
 	border-collapse: collapse; 
 	padding: 5px;
 }
 </style>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load("jquery", "1");
</script>
</head>
<body>
<table>
<tr>
<th>Name</th><th>XML</th><th>ALL IN</th><th>Date</th><th>Size</th></tr>
<?php
$dir_scan = scandir('.');
$mthd_array = array();

foreach( $dir_scan as $dir_element) {
	$path_parts = pathinfo($dir_element);
	if( $path_parts['extension'] == 'log' && !is_dir($dir_element) ) {
		$f_stat = stat($dir_element);
		$xml_file = $path_parts['filename'].'.xml';
		$all_file = $path_parts['filename'].'.txt';
		$mthd = explode('--', $path_parts['filename']);
		$mthd_array[$mthd[1]] += 1;
		echo '<tr class="'.(empty($mthd[1])?'mthd_empty':'mthd_'.$mthd[1]).'">';
		echo '<td><a href="' . $dir_element . '">' . $dir_element . '</a></td>';
		if( is_file( $xml_file ) ) {
			$xml_filesize = filesize($xml_file);
			if( $xml_filesize > 0 ) {
				echo '<td><a href="' . $xml_file . '">XML</a></td>';
			} else {
				echo '<td>PUSTY</td>';
			}
		} else {
			echo '<td>&nbsp;</td>';
		}
		if( is_file( $all_file ) ) {
			$xml_filesize = filesize($all_file);
			if( $xml_filesize > 0 ) {
				echo '<td><a href="' . $all_file . '">ALL</a></td>';
			} else {
				echo '<td>PUSTY</td>';
			}
		} else {
			echo '<td>&nbsp;</td>';
		}
		echo '<td>' . date('Y-m-d H:i:s', $f_stat['mtime']) . '</td>';
		echo '<td>' . $f_stat['size'] . '</td>';
		echo '</tr>';
	}
}
?>
</table>
<div style="position: fixed; right: 5px; top: 5px; border: 1px solid red; padding: 10px;">
Method:<br>
<select name="methods" id="methods" onchange="md(this)">
<option value="0">- - - - - - - - - - - - - -</option>
<?php 
ksort($mthd_array);
foreach( $mthd_array as $mthd => $count) {
	if( empty($mthd) ) $mthd = 'empty';
	echo '<option value="'.$mthd.'">'.$mthd.' - ' . $count . '</option>'."\n";
}
?>
</select>
</div>

<script type="text/javascript">
function md(obj_slc) {
	var val = obj_slc.value;
	if( val == "0" ) {
		$("[class^=mthd_]").show();
		$(".mthd_"+val).hide();
	} else {
		$("[class^=mthd_]").hide();
		$(".mthd_"+val).show();
	}
}
</script>
</body>
</html>