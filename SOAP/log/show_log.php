<html>
<head>
 <title>Dirlist</title>
</head>
<body>
<table cellspacing="1" cellpadding="8" border="1">
<tr>
<th>Name</th><th>XML</th><th>ALL IN</th><th>Date</th><th>Size</th></tr>
<?php
$dir_scan = scandir('.');

foreach( $dir_scan as $dir_element) {
	$path_parts = pathinfo($dir_element);
	if( $path_parts['extension'] == 'log' && !is_dir($dir_element) ) {
		$f_stat = stat($dir_element);
		$xml_file = $path_parts['filename'].'.xml';
		$all_file = $path_parts['filename'].'.txt';
		echo '<tr>';
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
</body>
</html>