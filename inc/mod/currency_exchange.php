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
<div class="currency_exchange_container">
<div class="currency_exchange menu_header"><?php echo Lang::_('Exchange rates') ?><div class="icon"></div></div>
<?php 
foreach($ex_currency_array as $currency_name) {
	if( $F->not_null($exchange['EXCHANGE_RATE_' . $currency_name]) ) {
		$txt  = '<div class="currency_exchange currency_exchange_currency currency_'. $currency_name .'">';
		$txt .= $currency_name . ':<span>' .  $exchange['EXCHANGE_RATE_' . $currency_name] . '</span>';
		if( $F->not_null($exchange['EXCHANGE_DATETIME_' . $currency_name]) ) {
			$txt .= '<span class="span2">' . $exchange['EXCHANGE_DATETIME_' . $currency_name] . '</span>';
		}
		$txt .= '</div>'."\n";
		echo $txt;
	}
}
?>
<div class="currency_exchange currency_exchange_date"><?php echo Lang::_('Exchange from date') ?>: <span><?php echo $exchange['EXCHANGE_DATETIME'] ?></span></div>
</div>
<?php 
}
?>