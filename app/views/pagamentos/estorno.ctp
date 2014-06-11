<?php echo $form->create('Pagamento', array('action'=> 'estorno')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Estorno/Devolução de Pagamentos'); ?></span></legend>
		<ol>
			<li>
				<?php echo $form->input('Lancamento.mes_ano', array('label'=> __('Mês/ano do lançamento', true), 'type'=> 'text', 'value'=> $mesAnoAtual)); ?>
			</li>
			<li>
				<?php echo $form->input('Lancamento.tipo_lancamento_id', array('label'=> __('Pagamento de', true), 'type'=> 'select', 'options'=> $tiposLancamento, 'default'=> 0)); ?>
			</li>
			<li>
				<?php echo $form->input('Lancamento.unidade_id', array('label'=> __('Unidade', true), 'type'=> 'select', 'options'=> $unidades)); ?>
			</li>
		</ol>
	</fieldset>

	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/delete.png').' '.__('Estornar Pagamento', true), array('type'=> 'submit', 'onclick'=> 'return confirm("Deseja realmente estornar o pagamento informado?\\nEsta operação não poderá ser desfeita.")')); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button'), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

<?php $html->addCrumb('Pagamentos', array('controller'=> 'menus', 'action'=> 'pagamentos')); ?>
<?php $html->addCrumb('Estorno/Devolução', array('controller'=> 'pagamentos', 'action'=> 'estorno')); ?>
