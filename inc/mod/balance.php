<?php
//STR: tmp
//FIXME

$balance = $P->get_client_balance();
?>
<div class="balance_container">
<div class="balance menu_header"><?php echo Lang::_('Balance') ?><div class="icon"></div></div>

<div class="balance balance_credit_limit"><?php echo Lang::_('credit_limit') ?><span><?php echo Price::val($balance['credit_limit']); ?></span></div>
<div class="balance balance_free_credit"><?php echo Lang::_('free_credit') ?><span><?php echo Price::val($balance['free_credit']); ?></span></div>
<div class="balance balance_punctuality"><?php echo Lang::_('punctuality') ?><span><?php echo $F->output_string($balance['punctuality']); ?></span></div>
</div>