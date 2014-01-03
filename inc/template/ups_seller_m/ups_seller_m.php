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
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="robots" content="all, index, follow">
		<meta name="coverage" content="global">
		<meta name="audience" content="all">
		<meta name="classification" content="global,all">
		<meta name="rating" content="general">
		<meta name="keywords" content="<?php echo $Page->put_head_keywords(); ?>">
		<meta name="description" content="<?php echo $Page->put_head_description(); ?>">
 
		<link rel="stylesheet" href="inc/template/ups_seller_m/css/18784813.css" type="text/css" media="screen">
		<link rel="stylesheet" href="inc/template/ups_seller_m/css/master.css" type="text/css" media="screen">
		<link rel="stylesheet" href="inc/template/ups_seller_m/css/miniatures.css" type="text/css" media="screen">  
		<link rel="stylesheet" href="inc/template/ups_seller_m/css/my_layout.css?ver=<?php echo time(); ?>" type="text/css" media="screen">  
		<link rel="stylesheet" href="inc/template/ups_seller_m/css/colorbox.css" type="text/css" >

<style type="text/css">embed[type*="application/x-shockwave-flash"],embed[src*=".swf"],object[type*="application/x-shockwave-flash"],object[codetype*="application/x-shockwave-flash"],object[src*=".swf"],object[codebase*="swflash.cab"],object[classid*="D27CDB6E-AE6D-11cf-96B8-444553540000"],object[classid*="d27cdb6e-ae6d-11cf-96b8-444553540000"],object[classid*="D27CDB6E-AE6D-11cf-96B8-444553540000"]{	display: none !important;}</style>	
<link href="inc_html/css/jquery-ui.min.css?ver=<?php echo time(); ?>" rel="stylesheet" type="text/css" >
<?php $Page->put_css(); ?>
<?php $Page->put_js(); ?>
<?php $Page->put_head_js(); ?>
</head>
	<body class="iStorePL iStorePageBasket">
				
		<div id="iStoreStatusBarWrapper">
		<?php echo $Page->put_second_head_html() ?>
</div>
		<div id="iStoreWrapper">
			<div id="iStoreTop">
       		<div class="iStoreBox hasLogo" id="iStoreHeader">
			<div class="iStoreBoxWrapper">
				<div class="iStoreBoxContent">
					<div class="iStoreShopLogo">
						<a href="/" title="RomiSJ"><img src="inc/template/ups_seller_m/Uwaga_files/5062674.jpg" alt="SKLEP INTERNETOWY"></a>
	        		</div>
				</div>
			</div>
		</div>
	<div id="iStoreToolbar">

	   <?php echo $Page->put_mastermenu_html();?>
		

	</div>
<?php echo $Page->put_bottomhead_html();?>
			</div>
       			<!-- 
					<div class="iStoreBox" id="iStoreBreadCrumbs">
						<div class="iStoreBoxWrapper">
							<div class="iStoreBoxContent">
								Jesteś tutaj: 
								<ul>
									<li><a href="/" title="strona główna w sklepie http://ups_seller.istore.pl">strona główna</a></li>  <li><strong title="koszyk w sklepie http://ups_seller.istore.pl">koszyk</strong></li>
								</ul>
							</div>
						</div>		
					</div>
					 -->
									
		<div id="iStoreContent">
		   <div id="iStoreStatic" class="iStoreBox">
			<div class="iStoreBoxWrapper">
				<div class="iStoreBoxHeader">
			     <h2><?php echo $Page->put_head_title(); ?></h2>
			  </div>
				<div class="iStoreBoxContent">
				 <div class="content_info"><?php echo $Page->put_info_html(); ?></div>
			    <?php echo $Page->put_component_html(); ?>
			  </div>
		  </div>
		</div>
    	    		
	</div>
	
	
	<!-- iStoreContent -->
	<div id="iStoreSidebar">
		
	<?php echo $Page->put_left_column_html(); ?>
	
	<script type="text/javascript">
		
	//zaznaczenie aktywnej kategorii
	
	if(activeCatId) {
		activeCat = document.getElementById('cat'+ activeCatId);
		
		if(activeCat) {
	    activeCat.setAttribute('class', activeCat.getAttribute('class') + ' selected')
		}
	}
	
	</script>
  
		<div class="iStoreBox" id="iStoreContactBox">
		<div class="iStoreBoxWrapper">
			<div class="iStoreBoxHeader">
		    		      <h2>Kontakty</h2>
		    			</div>
			<div class="iStoreBoxContent">
	          <ul class="iStoreContactData">
									<li class="phone"><b>Telefon kontaktowy</b><br>22-846-22-62</li>
									<li class="email"><b>Email kontaktowy</b><br><a href="mailto:b2b@ups_seller.pl">b2b@ups_seller.pl</a></li>
							</ul> 
		</div>
	  </div> 
	</div>
	</div><!-- iStoreSidebar -->
	
			<div id="iStoreFooter">	
						<div class="iStoreBox">
			<div class="iStoreBoxWrapper">
				<div class="iStoreBoxContent">
						
					<div class="iStoreFooterBox footerSection6 copyrights">
						<a class="upLink" href="#iStoreStatusBarWrapper" title="Do góry">Do góry</a>
						<p>Podmiot prowadzący sklep: ROMI M. OLSZEWSKI R. DRABIK SPÓŁKA JAWNA, Kłobucka 10, 02-699 Warszawa</p>
					</div>

          <div id="iStoreCookies">
          <img src="inc/template/ups_seller_m/Uwaga_files/iconwarning.jpg">
          <p>Strona korzysta z plików cookies w celu realizacji usług i zgodnie z Polityką Plików Cookies. Możesz określić warunki przechowywania lub dostępu do plików cookies w Twojej przeglądarce.</p>
        </div>
				</div>
        	
			</div>
      	
		</div>
    			</div><!-- iStoreFooter -->
		</div><!-- iStoreWrapper -->
</body></html>