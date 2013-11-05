<?php
$Page->clean_page();

$iparams = $Invoice->params;

$filename = $iparams['invoice_number'].'_'.$iparams['id_order'].'_'.$iparams['date_issue'].'.pdf';

header("Content-Disposition: attachment; filename=" . urlencode($filename));   
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Description: File Transfer");            
header("Content-Length: " . strlen($iparams['invoice_image']));
echo $iparams['invoice_image'];
die();
?>