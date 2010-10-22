<?php

ob_start();
?>
<style>
.left {
	background-color: red;
	float: left;
	width: 33%;
}
.center {
	background-color: green;
	float: left;
	width: 33%;
}
.right {
	background-color: blue;
	float: left;
	width: 33%;
}

</style>
<div class="left">a</div>
<div class="center">b</div>
<div class="right">c</div>
<?php
$Page->component_html = ob_get_clean();
ob_end_flush();

?>