<?php
/**
 * shop.php
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
<!--  style="table-layout: fixed;"  -->
<table border="1" cellspacing="2" cellpadding="2" width="975" height="100%" align="center">
<tr>
	<td width="975" colspan="3"><?php echo $Page->put_masterhead_html();?></td>
</tr>
<tr>
	<td width="975" colspan="3"><?php echo $Page->put_second_head_html(); ?></td>
</tr>
<tr>
	<td width="160" valign="top"><?php echo $Page->put_left_column_html(); ?></td>
	<td width="611" valign="top"><?php echo $Page->put_component_html(); ?></td>
	<td width="160" valign="top"><?php echo $Page->put_right_column_html(); ?></td>
</tr>

<tr>
	<td width="975" colspan="3"><?php echo $Page->put_bottom_html(); ?></td>
</tr>
<tr>
	<td width="975" colspan="3"><?php echo $Page->put_footer_html(); ?></td>
</tr>
</table>
</body>
</html>
