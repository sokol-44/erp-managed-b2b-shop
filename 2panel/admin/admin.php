<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 */

if( !defined('_I_INIT') ) die();


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $Page->put_head_title(); ?></title>
<meta name="keywords" content="<?php echo $Page->put_head_keywords(); ?>">
<meta name="description" content="<?php echo $Page->put_head_description(); ?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $Page->put_path_css(); ?>style_admin.css">
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="<?php echo $Page->put_path_css(); ?>style_admin_ie.css">
<![endif]-->
<?php $Page->put_css(); ?>
<?php $Page->put_js(); ?>
<?php $Page->put_head_js(); ?>
</head>
<body>
<div id="doc3" class="yui-t7">
	<div id="hd">
		<?php echo $Page->put_masterhead_html();?>
	</div>
	<div id="hds">
       <?php echo $Page->put_second_head_html(); ?>
	</div>
	<div id="bd">
		<div id="yui-main">
			<div class="yui-b">
			<?php echo $Page->put_component_html(); ?>
			</div>
		</div>
		<div id="bt">
		 <?php echo $Page->put_bottom_html(); ?>
		</div>
	</div>
	<div id="ft">
		 <?php echo $Page->put_footer_html(); ?>
	</div>
</div>
</body>
</html>
