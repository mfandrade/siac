<?php
Configure::write('debug', 0);
header('Last-Modified: ' . gmdate('D,d M YH:i:s') . ' GMT');
header('Expires: Mon, 01 Jan 2000 00:00:00 GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Description: '.__('Documento PDF gerado pelo SIAC'));
header('Content-Type: application/pdf');

echo $content_for_layout;
