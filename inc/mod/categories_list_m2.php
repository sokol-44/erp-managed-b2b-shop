<?php
$filers = array();

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.tooltip.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('$(\'.cat_href a\').tooltip({
		track: false, delay: 0, showURL: false, fixPNG: true, showBody: " # "
	});');

echo Data_Category::show_category_li();
?>