<?php echo $form->create(false, array('action'=> 'posicao')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Relatório "Posição Financeira"'); ?></span></legend>
		<ol>
			<li>
				<span><?php __('Relação de todos os pagamentos efetuados entre as datas informadas (inclusive), ordenados por unidade, para todas as unidades ou apenas para uma em específico.'); ?></span>
			</li>
			<li>
				<?php echo $form->label('data1', 'Pagamentos entre', array('style'=> 'float:left; display:inline;')); ?>
				<?php echo $form->input('data1', array('label'=> false, 'value'=> $periodo['data1'], 'style'=> 'float:left; display:inline')); ?>
				<?php echo $form->label('data2', 'e', array('style'=> 'float:left; display:inline; width:2em; text-align:center; margin-right:0;')); ?>
				<?php echo $form->input('data2', array('label'=> false, 'value'=> $periodo['data2'], 'style'=> 'float:left; display:inline')); ?>
			</li>
			<li>
				<?php echo $form->input('unidade_id', array('label'=> __('Unidade:', true), 'type'=> 'select', 'options'=> $unidades)); ?>
			</li>
			<li>
				<?php echo $form->input('tipo_pagamento_id', array('label'=> __('Tipo de pagamento', true), 'type'=> 'select', 'options'=> $tiposLancamento)); ?>
			</li>
			<li>
				<?php echo $form->input('via_pagamento', array('label'=> __('Pagamentos feitos', true), 'type'=> 'select', 'options'=> $viasPagamento)); ?>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/report.png').' '.__('Emitir Relatório', true), array('type'=> 'submit',  'tabindex'=> 3)); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 4), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

<?php $html->addCrumb('Relatórios', array('controller'=> 'menus', 'action'=> 'relatorios')); ?>
<?php $html->addCrumb('Posição Financeira', array('controller'=> 'relatorios', 'action'=> 'posicao')); ?>
