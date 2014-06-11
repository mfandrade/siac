<?php echo $form->create(false, array('action'=> 'inadimplencia')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Relatório "Inadimplência no Período"'); ?></span></legend>
		<ol>
			<li>
				<span><?php __('Relação de todos os lançamentos referentes ao meses entre o período dado (inclusíve), que não possuem acordo e que não constam como pagos até a data considerada.'); ?></span>
			</li>
			<li>
				<?php if( !empty($mesesAnos) ): ?>
					<?php echo $form->label('mes_ano1', __('Período entre', true), array('style'=> 'float:left; display:inline;')); ?>
					<?php echo $form->input('mes_ano1', array('label'=> false, 'type'=> 'select', 'options'=> $mesesAnos, 'tabindex'=> 1, 'style'=> 'float:left; display:inline')); ?>
					<?php echo $form->label('mes_ano2', __('e', true), array('style'=> 'float:left; display:inline; width:2em; text-align:center; margin-right:0;')); ?>
					<?php echo $form->input('mes_ano2', array('label'=> false, 'type'=> 'select', 'options'=> $mesesAnos, 'tabindex'=> 2, 'style'=> 'float:left; display:inline')); ?>
				<?php else: ?>
					<?php echo $html->div('error-message', __('Nenhum lançamento efetuado. Sem opções para relatório.', true)); ?>
				<?php endif; ?>

			</li>
			<li>
				<?php echo $form->input('unidade_id', array('label'=> __('Unidade:', true), 'type'=> 'select', 'options'=> $unidades, 'tabindex'=> 3)); ?>
			</li>
			<li>
				<?php echo $form->input('tipo_lancamento_id', array('label'=> __('Tipo de lançamento:', true), 'options'=> $tiposLancamento, 'tabindex'=> 4)); ?>
			</li>
			<li>
				<?php echo $form->input('data', array('label'=> __('Até a data:', true), 'value'=> $hoje, 'readonly'=> 'readonly', 'tabindex'=> 5)); ?>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/report.png').' '.__('Emitir Relatório', true), array('type'=> 'submit',  'tabindex'=> 3)); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 4), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

<?php $html->addCrumb('Relatórios', array('controller'=> 'menus', 'action'=> 'relatorios')); ?>
<?php $html->addCrumb('Inadimplência no Período', array('controller'=> 'relatorios', 'action'=> 'inadimplencia')); ?>
