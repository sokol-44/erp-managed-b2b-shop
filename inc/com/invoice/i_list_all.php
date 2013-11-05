<?php
//STR: tmp
$Price = Price::g_global();
//STR: end
// var_dump($Shopping_Basket);

$Page->head_title = Lang::_('invoice list');

$inv = Invoice::get_invoice_client_list();
?>
<div class="invoice_list_container">
  <div class="invoice_list_container invoice_list_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
  <div class="invoice_list_container invoice_list_content">
<?php echo Lang::_('Invoice help for colors');?>
<ul>
	<li><p class="invoice invoice_unpaid">
			<?php echo Lang::_('unpaid invoice');?>
		</p></li>
	<li><p class="invoice invoice_paid">
			<?php echo Lang::_('paid invoice');?>
		</p></li>
	<li><p class="invoice invoice_close_payment">
			<?php echo Lang::_('close to payment');?>
		</p></li>
	<li><p class="invoice invoice_overdue">
			<?php echo Lang::_('payment overdue');?>
		</p></li>
</ul>

<div class="invoice_container container_subheader"><?php echo Lang::_('Invoice statistics'); ?><div class="icon"></div></div>
<table class="tableBox" style="border: 0; width: 100%;" >
	<tr class="tableBoxHeading">
		<td><?php echo Lang::_('TOTAL INVOICES'); ?></td>
		<td><?php echo Lang::_('sum_netto'); ?></td>
		<td><?php echo Lang::_('sum gross'); ?></td>
	</tr>
	<tr>
		<td><?php echo (int)$inv['total']['count'];  ?></td>
		<td><?php echo Price::val((float)$inv['total']['sum_netto']); ?></td>
		<td><?php echo Price::val((float)$inv['total']['sum_gross']); ?></td>
	</tr>
</table>
<div class="invoice_container container_subheader"><?php echo Lang::_('invoice list');?><div class="icon"></div></div>
<table class="tableBox">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('Invoice number'); ?></th>
		<th><?php echo Lang::_('ORDER'); ?></th>
		<th><?php echo Lang::_('NET value'); ?></th>
		<th><?php echo Lang::_('gross value'); ?></th>
		<th><?php echo Lang::_('issue date'); ?></th>
		<th><?php echo Lang::_('pay date'); ?></th>
		<th><?php echo Lang::_('invoice image'); ?></th>
	</tr>
	<?php
	
	foreach( $inv['obj_array'] as $key => $Invoice ) {
		$iparams = $Invoice->params;
		$invoice_number = $F->output_string_html($iparams['invoice_number']);
		
		$order_link = $F->make_link(CFG_COM_ORDER, $F->add_local_get(array('mode' => 'show_details', 'id_order' => (int)$iparams['id_order']) ) );
		$order_html = $F->draw_link($order_link, 'title="' . Lang::_('order') . '"', (int)$iparams['id_order']);
		
		if( $F->not_null($iparams['invoice_image'])) {
			$invoice_image_link = $F->make_link(CFG_COM_INVOICE, $F->add_local_get(array('show' => 'show_invoice_image', 'id_invoice' => (int)$iparams['id_invoice']) ) );
			$invoice_image_html = $F->draw_link($invoice_image_link, 'title="' . Lang::_('Invoice') . '"', 'PDF', '_blank');
		} else {
			$invoice_image_html = '-';
		}

		if( $iparams['state'] == 'PAID' ) {
			$class_add = ' class="invoice_paid"';
		} elseif( $iparams['ts_pay'] < time()) {
			$class_add = ' class="invoice_overdue"';
		} elseif( $iparams['ts_pay'] < (time()+172800) ) {
			$class_add = ' class="invoice_close_payment"';
		} else {
			$class_add = ' class="invoice_unpaid"';
		}
		
		
	   ?>
	<tr>
		<td <?php echo $class_add; ?>><?php echo $invoice_number ?></td>
		<td><?php echo $order_html ?></td>
		<td><?php echo Price::val($iparams['net_value']); ?></td>
		<td><?php echo Price::val($iparams['gross_value']); ?></td>
		<td><?php echo $F->output_string_html($iparams['date_issue']); ?></td>
		<td><?php echo $F->output_string_html($iparams['date_pay']); ?></td>
		<td><?php echo $invoice_image_html ?></td>
	</tr>
	<?php
}
?>
</table>
  </div>
  <div class="invoice_list_container invoice_list_bottom container_bottom"></div>
</div>