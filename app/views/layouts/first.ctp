<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br">
<head>
	<title><?php __('SIAC - Sistema Integrado de Administração Condominial'); ?></title>
	<?php echo $html->charset() . "\n"; ?>
	<?php echo $html->meta('icon') . "\n"; ?>
	<style type="text/css"><!-- @import url(<?php echo Router::url('/').'css/main.css?'.time(); ?>); --></style>
	<?php echo $scripts_for_layout . "\n"; ?>
</head>
<body>
	<?php echo $content_for_layout; ?>
</body>
</html>
