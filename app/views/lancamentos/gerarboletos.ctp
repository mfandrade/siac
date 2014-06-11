<div id="lancamentos-gerarboletos">
	<div class="view-content">
		<h2><?php __('Gerando Boletos...'); ?></h2>
		<div id="divLoading">
			<?php if( isset($msg) ): ?>
				<strong><?php echo $msg; ?></strong><br />
			<?php endif; ?>
			<?php echo $html->image('ajax-loader.gif'); ?>
			<?php __('Isto pode demorar alguns minutos.'); ?><br />
			<strong><?php __('Não feche esta janela!'); ?></strong>
			<span id="placeholderStatus"><?php __('Por favor, aguarde...'); ?></span>
		</div>
	</div>
</div>

<?php
	echo $javascript->link('prototype');

	$ajaxCall   = $ajax->remoteFunction(array(
		  'url'			=> array('controller'=> 'lancamentos', 'action'=> 'ajaxgerarboletos')
		, 'indicator'	=> 'divLoading'
		, 'update'		=> 'lancamentos-gerarboletos'
	));
	echo $javascript->codeBlock($ajaxCall);
/*	echo $ajax->remoteTimer(array(
		  'url'			=> array('controller'=> 'lancamentos', 'action'=> 'ajaxatualizarstatusboletos')
		, 'frequency'	=> 10
		, 'update'		=> 'placeholderStatus'
	));
*/
?>
