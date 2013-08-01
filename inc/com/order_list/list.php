<?php
$SP = new SplitPage('ORDER_LIST');
$Page->head_title =Lang::_('Orders');

//$order_chain;

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$BC->add_crumb(Lang::_('Orders list'), $F->make_link(CFG_COM_ORDER_LIST) );

$GET_tmp = $F->make_get('mode');
$GET_tmp = $F->add_local_get('mode', 'show_details', $GET_tmp);
//for all
?>
<div class="list_order_container">
  <div class="list_order_container list_order_title container_header"><?php echo $Page->head_title; ?><div class="icon"></div></div>
<div class="account_container container_subheader"><?php echo Lang::_('Orders statistics'); ?><div class="icon"></div></div>
<table class="tableBox" style="border: 0; width: 400px;" >
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('Field') ?></th>
		<th><?php echo Lang::_('Value') ?></th>
	</tr>
	<tr>
		<td><?php echo Lang::_('TOTAL PRODUCTS'); ?></td>
		<td><?php echo $order_chain->total['product_total']; ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('PRODUCTS TYPES'); ?></td>
		<td><?php echo $order_chain->total['product_types'];  ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('sum gross'); ?></td>
		<td><?php echo Price::val($order_chain->total['sum_gross']); ?></td>
	</tr>
	<tr>
		<td><?php echo Lang::_('sum_netto'); ?></td>
		<td><?php echo Price::val($order_chain->total['sum_netto']); ?></td>
	</tr>
</table>

  <div class="account_container container_subheader"><?php echo Lang::_('Orders list'); ?><div class="icon"></div></div>
  <div class="list_order_container list_order_content">
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('ID') ?></th>
		<th><?php echo Lang::_('order DESCRIPTION') . ',<br>' . Lang::_('basket DESCRIPTION'); ?></th>
		<th><?php echo Lang::_('order statistic') . ',<br>' . Lang::_('order date') . ',<br>' . Lang::_('order update'); ?></th>
		<th><?php echo Lang::_('order STATE') ?></th>
		<th><?php echo Lang::_('order details') ?></th>
	</tr>
	<?php
	foreach( $order_chain->order_list as $id_order => $order ) {   
	   $check_rights = $order->check_rights();
	   $total = $order->calculate_total();
	   
	   $GET_tmp = $F->add_local_get('id_order', $order->id_order, $GET_tmp);
	   
	   $link_product_info = $F->make_link(CFG_COM_ORDER_LIST, $GET_tmp);
	   $cell_order_info = $F->draw_link($link_product_info, 'title="' . Lang::_('show order details') . '"', $F->static_image('icon/folder_16.png', Lang::_('show order details')));
	   $cell_order_desc = Lang::_('order DESCRIPTION') . ':<br>' . nl2br($F->output_string_html( $order->data['description'], 256 ) ) . '<br>
	   ' . Lang::_('basket DESCRIPTION') . ':<br>' . nl2br($F->output_string_html( $order->data['description_basket'], 256 ) );
	   ?>
	<tr valign="top">
		<td width="5%"><?php echo $F->draw_radio_field('list', (int)$order->id_order, false, 'style="display: none"') . (int)$order->id_order; ?></td>
		<td valign="top">
	   <?php echo $cell_order_desc; ?></td>
		<td width="10%">
<div class="basket order_total" style="width: 160px">
<div class="basket_product_total"><?php echo Lang::_('TOTAL PRODUCTS'); ?><span id="nr"><?php echo $total['product_total']; ?></span></div>
<div class="basket_product_types"><?php echo Lang::_('PRODUCTS TYPES'); ?><span id="nr"><?php echo $total['product_types']; ?></span></div>
<div class="basket_sum_gross"><?php echo Lang::_('sum gross'); ?><span id="nr"><?php echo Price::val($total['sum_gross']); ?></span></div>
<div class="basket_sum_netto"><?php echo Lang::_('sum_netto'); ?><span id="nr"><?php echo Price::val($total['sum_netto']); ?></span></div>
</div>
<?php echo Lang::_('order date') . ':<br>' . $F->output_string_html( $order->data['date_create'] ); ?><br><br>
<?php if($F->not_null($order->data['date_modified'])) echo  Lang::_('order update') . ':<br>' . $F->output_string_html( $order->data['date_modified'] ); ?>
		</td>
		<td width="10%"><?php echo $order->data['name']; ?></td>
		<td width="10%"><?php echo $cell_order_info; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<table style="border: 0">
	<tr>
		<td colspan="5"><?php echo $SP->display_links(); ?></td>
	</tr>
</table>
  </div>
  <div class="list_order_container list_order_bottom container_bottom"></div>
</div>
