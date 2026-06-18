<?php
/**
 * shop.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 */

if( !defined('_I_INIT') ) die();

$template = array('MAIN' => array());

$template['MAIN']['body'] = '<table border="0" cellspacing="3" cellspadding="3">
  <tr>
    <td valign="top"><img src="cid:logo_romi"></td>
    <td valign="top">***REMOVED***<br>
     	Warszawa<br>
		ul. Kłobucka 10, <br>
		02-699 Warszawa<br></td>
    <td valign="top">Telefon: (22) 846 22 62<br>
		Dział Sprzedaży:  wew. 15, 18, 27, 28<br>
		Księgowość:  wew. 20<br>
		Serwis:  wew 22<br>
		Fax:   wew 13<br>
		E-mail: <a href="mailto:biuro@ups_seller.pl">biuro@ups_seller.pl</a><br>
		Godziny otwarcia: pn-pt: 09:00-16:00</td>
</td>
  </tr>
  <tr>
    <td colspan="3">#text#</td>
  </tr>
</table>';

// fillename, cid, name
$template['MAIN']['img'] = array( array('images/logo_romi.png', 'logo_romi', 'Logo RomiSJ') );
$template['MAIN']['type'] = 'html';




?>