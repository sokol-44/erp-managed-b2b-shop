<?php
/**
 * shop.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

$template = array('MAIN' => array());

$template['MAIN']['body'] = '<table border="0" cellspacing="3" cellspadding="3">
  <tr>
    <td valign="bottom"><img src="cid:logo_damen"></td>
    <td valign="top">***REMOVED***<br>
      Al. Józefa Piłsudskiego 257a<br>
      05-261 Marki<br>
      woj. mazowieckie<br></td>
    <td valign="top">Telefon: 22 771 34 10, 771 33 85<br>
		Fax: 22 771 34 10, 771 36 38<br>
		Kom. 509 787 87<br>
		E-mail: <a href="mailto:orders@example.com">orders@example.com</a><br>
		Godziny otwarcia: pn-pt: 09:00-16:00</td>
</td>
  </tr>
  <tr>
    <td colspan="3">#text#</td>
  </tr>
</table>';

// fillename, cid, name
$template['MAIN']['img'] = array( array('images/logo_damen.gif', 'logo_damen', 'Logo Damen') );
$template['MAIN']['type'] = 'html';




?>