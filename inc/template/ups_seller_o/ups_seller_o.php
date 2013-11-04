<?php
/**
 * shop.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="pl">
<head>
<title><?php echo $Page->put_head_title(); ?></title>
<meta name="keywords" content="<?php echo $Page->put_head_keywords(); ?>">
<meta name="description" content="<?php echo $Page->put_head_description(); ?>">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" >

<link rel="stylesheet" href="http://yui.yahooapis.com/3.10.1/build/cssreset/cssreset.css" type="text/css">
<link rel="stylesheet" href="http://yui.yahooapis.com/3.10.1/build/cssfonts/cssfonts.css" type="text/css">
<link rel="stylesheet" href="http://yui.yahooapis.com/3.10.1/build/cssgrids/cssgrids.css" type="text/css">
<script src="http://yui.yahooapis.com/3.10.1/build/yui/yui-min.js"></script>
<link href="inc/template/ups_seller_o/css/my_layout.css?ver=<?php echo time(); ?>" rel="stylesheet" type="text/css" >
<link href="inc/template/ups_seller_o/css/nav_shinybuttons.css?ver=<?php echo time(); ?>" rel="stylesheet" type="text/css" >
<link href="inc/template/ups_seller_o/css/colorbox.css?ver=<?php echo time(); ?>" rel="stylesheet" type="text/css" >
<?php $Page->put_css(); ?>
<?php $Page->put_js(); ?>
<?php $Page->put_head_js(); ?>
</head>
<body>
  <div id="page">
	  <div class="yui3-g" id="header_all">
		  <div class="yui3-u-1 header_part" id="header_top">
			  <div id="header_left"><a href="/"><img src="inc/template/ups_seller_o/images/logo_ups_seller.png" border="0" height="64"></a>
			  <?php echo $Page->put_masterhead_html(); ?>
			  </div>
			<div id="topnav">
			   <?php echo $Page->put_mastermenu_html();?>
			</div>
		  </div>
		</div>
	  <div class="yui3-g">
		  <div class="yui3-u-1" id="navigation">
			  <!-- main navigation: horizontal list -->
			  <?php echo $Page->put_second_head_html(); ?>
		  </div>
	  </div>
      <div class="yui3-g" id="layout">
        <div class="yui3-u-1-5" id="lnav">
            <div class="content left_menu">
            <!-- add your content here -->
            <?php echo $Page->put_left_column_html(); ?>
            </div>
        </div>
      
        <div class="yui3-u-3-5" id="lmain">
            <div class="content">
             	<div class="content_info"><?php echo $Page->put_info_html(); ?></div>
               <!-- add your content here -->
               <?php echo $Page->put_component_html(); ?>
            </div>
        </div>
      
        <div class="yui3-u-1-5" id="lextra">
            <div class="content right_menu">
               <!-- add your content here -->
               <?php echo $Page->put_right_column_html(); ?>
            </div>
        </div>
      </div>

    <div id="ft">
       <?php echo $Page->put_bottom_html(); ?>
    </div>
   </div>
</body>
</html>