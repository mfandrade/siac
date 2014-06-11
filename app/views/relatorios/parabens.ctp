<?php echo $form->create(false, array('action'=> 'parabens')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Relatório "Parabéns Condôminos!"'); ?></span></legend>
		<ol>
			<li>
				<span><?php __('Mapa com a relação das unidades, agrupadas por quadra e rua, cujos lançamentos referentes ao mês/ano e tipo dados foram pagos até o vencimento ou antes do final do próprio mês de referência.'); ?></span>
			</li>
			<li>
				<?php echo $form->input('mes_ano', array('label'=> __('Mês e ano para o relatório:', true), 'options'=> $mesesAnos, 'tabindex'=> 1)); ?>
			</li>
			<li>
				<?php echo $form->input('tipo_lancamento_id', array('label'=> __('Tipo de lançamento:', true), 'options'=> $tiposLancamento, 'tabindex'=> 2)); ?>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/report.png').' '.__('Emitir Relatório', true), array('type'=> 'submit',  'tabindex'=> 3)); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 4), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

<?php $html->addCrumb('Relatórios', array('controller'=> 'menus', 'action'=> 'relatorios')); ?>
<?php $html->addCrumb('Parabéns Adimplentes', array('controller'=> 'relatorios', 'action'=> 'parabens')); ?>
