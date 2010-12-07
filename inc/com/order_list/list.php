<?php
$SP = new SplitPage('ORDER_LIST');
$Page->head_title =Lang::_('Orders list');

$order_list = Data::get_order_list((int)$P->data['id_client']);

$Page->add_js_file('table.js');
$Page->add_js_file('toolbox.js');
$Page->add_js_file('jquery.colorbox.js');
$Page->add_jq_init('colorize_table(".tableBox");');
$Page->add_jq_init('set_toolbox_table(".tableBox");');

$BC->add_crumb(Lang::_('Orders list'), $F->make_link(CFG_COM_ORDER_LIST) );

$GET_tmp = $F->make_get('mode');
$GET_tmp = $F->add_local_get('mode', 'show_details', $GET_tmp);
//for all
echo Lang::_('Orders list');
?>
<table class="tableBox" style="border: 0">
	<tr class="tableBoxHeading">
		<th><?php echo Lang::_('ID') ?></th>
		<th><?php echo Lang::_('order DESCRIPTION') . ',<br>' . Lang::_('basket DESCRIPTION'); ?></th>
		<th><?php echo Lang::_('order statistic') . ',<br>' . Lang::_('order date') . ',<br>' . Lang::_('order update'); ?></th>
		<th><?php echo Lang::_('order STATE') ?></th>
		<th><?php echo Lang::_('order details') ?></th>
	</tr>
	<?php
	foreach( $order_list as $order ) {
	   $Order = new Order($order['id_order']);
	   
	   $check_rights = $Order->check_rights();
	   
	   $total = $Order->calculate_total();
	   
	   $GET_tmp = $F->add_local_get('id_order', $order['id_order'], $GET_tmp);
	   
	   $link_product_info = $F->make_link(CFG_COM_ORDER_LIST, $GET_tmp);
	   $cell_order_info = $F->draw_link($link_product_info, 'title="' . Lang::_('show order details') . '"', $F->static_image('icon/folder_16.png', Lang::_('show order details')));
	   $cell_order_desc = Lang::_('order DESCRIPTION') . ':<br>' . nl2br($F->output_string_html( $order['description'], 256 ) ) . '<br>
	   ' . Lang::_('basket DESCRIPTION') . ':<br>' . nl2br($F->output_string_html( $order['description_basket'], 256 ) );
	   ?>
	<tr valign="top">
		<td width="5%"><?php echo $F->draw_radio_field('list', $order['id_order'], false, 'style="display: none"') . (int)$order['id_order']; ?></td>
		<td valign="top">
	   <?php echo $cell_order_desc; ?></td>
		<td width="10%">
<div class="basket order_total" style="width: 160px">
<div class="basket_product_total"><?php echo Lang::_('TOTAL PRODUCTS'); ?><span id="nr"><?php echo $total['product_total']; ?></span></div>
<div class="basket_product_types"><?php echo Lang::_('PRODUCTS TYPES'); ?><span id="nr"><?php echo $total['product_types']; ?></span></div>
<div class="basket_sum_gross"><?php echo Lang::_('sum gross'); ?><span id="nr"><?php echo Price::val($total['sum_gross']); ?></span></div>
<div class="basket_sum_netto"><?php echo Lang::_('sum_netto'); ?><span id="nr"><?php echo Price::val($total['sum_netto']); ?></span></div>
</div>
<?php echo Lang::_('order date') . ':<br>' . $F->output_string_html( $order['date_create'] ); ?><br><br>
<?php if($F->not_null($order['date_modified'])) echo  Lang::_('order update') . ':<br>' . $F->output_string_html( $order['date_modified'] ); ?>
		</td>
		<td width="10%"><?php echo $order['name']; ?></td>
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
