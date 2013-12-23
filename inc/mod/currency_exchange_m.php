<?php
$Shop = Shop::g_global();

$ex_rate_array = array('EXCHANGE_RATE_USD','EXCHANGE_DATETIME_USD','EXCHANGE_RATE_EURO','EXCHANGE_DATETIME_EURO','EXCHANGE_DATETIME');
$ex_currency_array = array('EURO', 'USD');
$exchange = array();

foreach( $ex_rate_array as $name ) {
	$res = $Shop->get_shop_attribute($name);
	if( $F->not_null($res) ) $exchange[$name] = $res;
}

// EXCHANGE_RATE_USD
// EXCHANGE_DATETIME_USD
// EXCHANGE_RATE_EURO
// EXCHANGE_DATETIME_EURO
// EXCHANGE_DATETIME

if( $F->not_null($exchange) ) {
?>
<div class="iStoreBox" id="iStoreMenuBox">
		<div class="iStoreBoxWrapper">
			<div class="iStoreBoxHeader"><h2><?php echo Lang::_('Exchange rates') ?></h2></div>
			<div class="iStoreBoxContent">	
	          <ul class="iStoreContactData">
<?php 
foreach($ex_currency_array as $currency_name) {
	if( $F->not_null($exchange['EXCHANGE_RATE_' . $currency_name]) ) {
		$txt  = '<li class="currency_exchange currency_exchange_currency currency_'. $currency_name .'">';
		$txt .= $currency_name . ':<span>' .  $exchange['EXCHANGE_RATE_' . $currency_name] . '</span>';
		$txt .= '</li>'."\n";
		echo $txt;
	}
}
?>
<li class="currency_exchange currency_exchange_date"><?php echo Lang::_('Exchange from date') ?>: <span><?php echo $exchange['EXCHANGE_DATETIME'] ?></span></li>
							</ul> 
		</div>
	  </div> 
	</div>

<?php 
}
?>


