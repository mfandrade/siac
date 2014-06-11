<?php if( sizeof($mesesAnos) == 0 ): ?>
	<?php echo $javascript->codeBlock('$("LancamentoId").disable();'); ?>
	<?php echo $javascript->codeBlock('alert("Não há lançamentos deste tipo em\naberto para a unidade em questão.");'); ?>
	<option value="0"><?php __('Sem opções disponíveis'); ?></option>

<?php else: ?>
	<?php //foreach( $unidadeMesesAnos as $unidade=> $mesesAnos ): ?>
		<option value="0" selected="selected"><?php __('SELECIONE'); ?></option>
		<!--optgroup label="<?php echo $unidade; ?>"-->
		<?php foreach( $mesesAnos as $id=> $mesAno ): ?>
			<?php echo sprintf('<option value="%s">%s</option>', $id, $mesAno) . "\n"; ?>
		<?php endforeach; ?>
		<!--/optgroup-->
	<?php //endforeach; ?>
	<?php echo $javascript->codeBlock('$("LancamentoId").enable();'); ?>
<?php endif; ?>
