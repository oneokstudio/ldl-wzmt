<?php
$classFile = 'BCGi25.check_exist.php';
$className = 'BCGi25';
$baseClassFile = 'BCGBarcode1D.php';
$codeVersion = '5.2.0';

function customSetup($barcode, $get) {
    if (isset($get['checksum'])) {
        $barcode->setChecksum($get['checksum'] === '1' ? true : false);
    }
}
?>