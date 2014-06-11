<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br">
<head>
	<title><?php __('SIAC'); ?> / <?php echo $title_for_layout; ?></title>
	<?php echo $html->charset() . "\n"; ?>
	<?php echo $html->meta('icon') . "\n"; ?>
	<?php echo $html->css(array('reset-fonts-grids', 'base-min', /*'siac-default',*/ '../menu/menu_style'), null, false) . "\n"; ?>
	<style type="text/css"><!-- @import url(<?php echo Router::url('/').'css/main.css?'.time(); ?>); --></style>
	<?php echo $scripts_for_layout . "\n"; ?>
</head>
<body>
<div id="container">
	<div id="header" class="menu">
		<ul>
			<li><?php echo $html->link('Lançamentos', array('controller'=> 'menus', 'action'=> 'lancamentos'), array('onclick'=> 'return false;')); ?>
				<ul>
					<li><?php echo $html->link(__('Taxa Condominial Mensal', true), array('controller'=> 'lancamentos', 'action'=> 'taxacondominial')); ?></li>
					<li><?php echo $html->link(__('Taxa Extra', true), array('controller'=> 'taxas', 'action'=> 'cadastrar')); ?></li>
					<li><?php echo $html->link(__('Multa por Infração', true), array('controller'=> 'lancamentos', 'action'=> 'multainfracao'), array('onclick'=> 'alert("Em breve..."); return false;')); ?></li>
					<li><hr /></li>
					<li><?php echo $html->link(__('Regerar Boletos', true), array('controller'=> 'lancamentos', 'action'=> 'regerarboletos')); ?></li>
				</ul>
			</li>
			<li><?php echo $html->link(__('Pagamentos', true), array('controller'=> 'menus', 'action'=> 'pagamentos'), array('onclick'=> 'return false;')); ?>
				<ul>
					<li><?php echo $html->link(__('Via Arquivo de Retorno', true), array('controller'=> 'pagamentos', 'action'=> 'arquivoretorno')); ?></li>
					<li><?php echo $html->link(__('Direto na Administração', true), array('controller'=> 'pagamentos', 'action'=> 'administracao')); ?></li>
					<li><hr /></li>
					<li><?php echo $html->link(__('Estorno/Devolução', true), array('controller'=> 'pagamentos', 'action'=> 'estorno')); ?></li>
					<li><hr /></li>
					<li><?php echo $html->link(__('Acordo', true), array('controller'=> 'acordos', 'action'=> 'cadastrar')); ?></li>
				</ul>
			</li>
			<li><?php echo $html->link(__('Relatórios', true), array('controller'=> 'menus', 'action'=> 'relatorios'), array('onclick'=> 'return false;')); ?>
				<ul>
					<li><?php echo $html->link(__('Parabéns Condôminos', true), array('controller'=> 'relatorios', 'action'=> 'parabens')); ?></li>
					<li><?php echo $html->link(__('Posição Financeira', true), array('controller'=> 'relatorios', 'action'=> 'posicao')); ?></li>
					<li><?php echo
					$html->link(__('Inadimplência no Período', true), array('controller'=> 'relatorios', 'action'=>	'inadimplencia')); ?></li>
				</ul>
			</li>
			<li><?php echo $html->link(__('Sistema', true), array('controller'=> 'menus', 'action'=> 'sistema'), array('onclick'=> 'return false;')); ?>
				<ul>
					<li><?php echo $html->link(__('Dados Auxiliares', true), array('controller'=> 'menus', 'action'=> 'sistema_dadosauxiliares')); ?></li>
					<li><?php echo $html->link(__('Lista de Arquivos Processados', true), array('controller'=> 'arquivos_retorno', 'action'=> 'index'), array('onclick'=> 'alert("Em breve..."); return false;')); ?></li>
					<li><hr /></li>
					<li><?php echo $html->link(__('Gerar/Recuperar Backup', true), array('controller'=> 'sistema', 'action'=> 'index'), array('onclick'=> 'alert("Em breve..."); return false;')); ?></li>
					<li><hr /></li>
					<li><?php echo $html->link(__('Configurações', true), array('controller'=> 'sistema', 'action'=> 'index'), array('onclick'=> 'alert("Em breve..."); return false;')); ?></li>
				</ul>
			</li>
			<li><?php echo $html->link(__('Sair', true), array('controller'=> 'usuarios', 'action'=> 'logout'), null, __('Deseja mesmo sair do sistema?', true)); ?>
		</ul>
	</div>
	<div id="content">
		<?php $session->flash(); ?>
		<!--div id="breadcrumbs"><strong>SIAC</strong> &raquo; <?php echo $html->getCrumbs(' &raquo; '); ?></div-->
		<?php echo $content_for_layout; ?>
	</div>
	<!--div id="footer">
		<!-- RODAPÉ -- >
		<hr />
		<address>

		</address>
	</div -->
</div>
</body>
</html>
