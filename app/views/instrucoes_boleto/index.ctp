<div id="instrucoes_boleto-index" class="view-content">
	<h2><?php __('Instruções de Boleto'); ?></h2> <?php echo $navigator->show(); ?>

	<table summary="<?php __('Relação de instruções de boleto'); ?>">
		<caption><?php __('Relação de instruções de boleto'); ?></caption>
		<?php //echo $html->tableHeaders(array('Texto', 'Descrição', 'Tipo de Lançamento', 'Modificação', '', '')); ?>
		<tr>
			<th><?php echo $paginator->sort(__('Texto', true), 'InstrucaoBoleto.texto'); ?></th>
			<th><?php echo $paginator->sort(__('Tipo de Lançamento', true), 'InstrucaoBoleto.tipo_lancamento_id'); ?></th>
			<th><?php echo $paginator->sort(__('Descrição', true), 'InstrucaoBoleto.descricao'); ?></th>
			<th><?php echo $paginator->sort(__('Modificação', true), 'InstrucaoBoleto.modified'); ?></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>

		<tbody>
			<?php foreach( $instrucoes as $instrucao ): ?>
			<tr>
				<td><?php echo $html->link($instrucao['InstrucaoBoleto']['texto'], array('controller'=> 'instrucoes_boleto', 'action'=> 'editar', $instrucao['InstrucaoBoleto']['id']), null, null, false); ?></td>
				<td><?php echo $html->link($instrucao['TipoLancamento']['descricao'], array('controller'=> 'instrucoes_boleto', 'action'=> 'editar', $instrucao['InstrucaoBoleto']['id'])); ?></td>
				<td><?php echo $html->link($instrucao['InstrucaoBoleto']['descricao'], array('controller'=> 'instrucoes_boleto', 'action'=> 'editar', $instrucao['InstrucaoBoleto']['id'])); ?></td>
				<td><?php echo $html->link($formatar->datahora($instrucao['InstrucaoBoleto']['modified']), array('controller'=> 'instrucoes_boleto', 'action'=> 'editar', $instrucao['InstrucaoBoleto']['id'])); ?></td>
				<td><?php echo $html->link($html->image('/img/famfamfam/pencil.png', array('alt'=> 'Editar')), array('controller'=> 'instrucoes_boleto', 'action'=> 'editar', $instrucao['InstrucaoBoleto']['id']), null, null, false); ?></td>
				<td><?php echo $html->link($html->image('/img/famfamfam/delete.png', array('alt'=> 'Excluir')), array('controller'=> 'instrucoes_boleto', 'action'=> 'excluir', $instrucao['InstrucaoBoleto']['id']), null, __('Quer mesmo excluir esta instrução de boleto?', true), false); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php $html->addCrumb('Sistema', array('controller'=> 'menus', 'action'=> 'sistema')); ?>
<?php $html->addCrumb('Dados Auxiliares', array('controller'=> 'menus', 'action'=> 'sistema_dadosauxiliares')); ?>
<?php $html->addCrumb('Instruções de Boleto', array('controller'=> 'instrucoes_boleto', 'action'=> 'index')); ?>

