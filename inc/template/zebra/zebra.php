<?php
/**
 * shop.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 */

if( !defined('_I_INIT') ) die();


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $Page->put_head_title(); ?></title>
<meta name="keywords" content="<?php echo $Page->put_head_keywords(); ?>">
<meta name="description" content="<?php echo $Page->put_head_description(); ?>">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" >

<link href="inc/template/zebra/css/my_layout.css?ver=<?php echo time(); ?>" rel="stylesheet" type="text/css" >
<!--[if lte IE 7]>
<link href="inc/template/shop/css/patch_my_layout.css" rel="stylesheet" type="text/css" >
<![endif]-->
<?php $Page->put_css(); ?>
<?php $Page->put_js(); ?>
<?php $Page->put_head_js(); ?>
</head>
<body>
  <div class="page_margins">
    <div id="border-top">
      <div id="edge-tl"></div>
      <div id="edge-tr"></div>
    </div>
    <div class="page">
      <div id="header">
  		  <div id="header_left"><a href="/"><img src="inc/template/zebra/images/logo_zebra_2.png" border="0"></a><?php echo $Page->put_masterhead_html(); ?></div>
        <div id="topnav">
          <!-- start: skip link navigation -->
          <a class="skip" title="skip link" href="#navigation">Skip to the navigation</a><span class="hideme">.</span>
          <a class="skip" title="skip link" href="#content">Skip to the content</a><span class="hideme">.</span>
          <!-- end: skip link navigation --><?php echo $Page->put_mastermenu_html();?>
        </div>
      </div>
      <div id="nav">
        <!-- skiplink anchor: navigation -->
        <a id="navigation" name="navigation"></a>
        <div class="hlist">
          <!-- main navigation: horizontal list -->
          <?php echo $Page->put_second_head_html(); ?>
        </div>
      </div>
      <div id="main">
        <div id="col1">
          <div id="col1_content" class="clearfix">
            <!-- add your content here -->
            <?php echo $Page->put_left_column_html(); ?>
          </div>
        </div>
        <div id="col2">
          <div id="col2_content" class="clearfix">
            <!-- add your content here -->
            <?php echo $Page->put_right_column_html(); ?>
          </div>
        </div>
        <div id="col3">
          <div id="col3_content" class="clearfix">
          	<div><?php echo $Page->put_component_info(); ?></div>
            <!-- add your content here -->
            <?php echo $Page->put_component_html(); ?>
          </div>
          <!-- IE Column Clearing -->
          <div id="ie_clearing"> &#160; </div>
        </div>
      </div>
      <!-- begin: #footer -->
      <div id="footer">
      <?php echo $Page->put_bottom_html(); ?>
      <!--Layout based on <a href="http://www.yaml.de/">YAML</a>-->
      </div>
    </div>
    <div id="border-bottom">
      <div id="edge-bl"></div>
      <div id="edge-br"></div>
    </div>
  </div>
</body>
</html>
