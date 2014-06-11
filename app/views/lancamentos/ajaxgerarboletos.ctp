<div class="view-content">
	<h2><?php __('Gerando Boletos...'); ?> <?php __('concluído!'); ?></h2>
	<div id="divInfo">
		<?php echo $html->image('icon-success-big.png'); ?>
		<?php echo sprintf(__('Arquivo "%s"', true), $nomeArquivo); ?><br />
		<?php echo sprintf(__('gerado com sucesso no diretório "%s".', true), $diretorio); ?>
	</div>
</div>
<fieldset class="buttons">
	<?php echo $html->link( $html->image('/img/famfamfam/arrow_left.png').' '. __('Retornar', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button'), null, false); ?>
</fieldset>
