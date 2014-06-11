<?php echo $form->create(false, array('controller'=> 'lancamentos', 'action'=> 'regerarboletos')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Regerar Boletos'); ?></span></legend>
		<ol>
			<li>
				<?php
					if( sizeof($mesesAnos) > 0 ):
						echo $form->input('Lancamento.mes_ano', array('label'=> __('Mês e ano para lançamento:', true), 'options'=> $mesesAnos, 'type'=> 'select', 'value'=> $mesAno, 'tabindex'=> 1));
					else:
						echo $html->div('error-message', __('Nenhum lançamento efetuado. Sem boletos a regerar.', true));
					endif;
				?>
			</li>
			<li>
				<?php echo $form->input('Lancamento.unidade_id', array('label'=> __('Unidade (Quadra e Lote):', true), 'type'=> 'select', 'options'=> $unidades, 'default'=> 0, 'tabindex'=> 2, 'style'=> 'width:20em;')); ?>
			</li>
			<li>
				<?php echo $form->input('Lancamento.tipo_lancamento_id', array('label'=> __('Boleto referente a:', true), 'type'=> 'select', 'options'=> $tiposLancamento, 'default'=> TIPO_LANCAMENTO_TAXACONDOMINIAL, 'tabindex'=> 3)); ?>
			</li>
			<li>
				<fieldset class="group">
					<legend><?php __('Instrução para boleto:'); ?></legend>
					<ol>
						<li>
							<?php echo $form->radio('Lancamento.instrucao_boleto_id', $instrucoes, array('separator'=> '</li><li>', 'value'=> 1, 'legend'=> false, 'tabindex'=> 4)); ?>
						</li>
					</ol>
				</fieldset>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/book_open.png').' '.__('Regerar Boletos', true), array('type'=> 'submit',  'tabindex'=> 5, 'id'=> 'btnOkay')); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 6, 'id'=> 'btnCancel'), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

<?php $html->addCrumb('Lançamentos', array('controller'=> 'menus', 'action'=> 'lancamentos')); ?>
<?php $html->addCrumb('Regerar Boletos', array('controller'=> 'lancamentos', 'action'=> 'regerarboletos')); ?>
