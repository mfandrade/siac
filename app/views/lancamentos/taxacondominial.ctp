<?php echo $form->create('Lancamento', array('action'=> 'taxacondominial')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Lançamento de Taxa Condominial Mensal'); ?></span></legend>
		<ol>
			<li>
				<?php echo $form->label('Lancamento.diretorio_gravacao', __('Diretório de gravação:', true)); ?>
				<span id="LancamentoDiretorioGravacao"><?php echo $diretorio; ?></span>
			</li>
			<li>
				<?php
					if( sizeof($mesesAnos) > 0 ):
						echo $form->input('Lancamento.mes_ano', array('label'=> __('Mês e ano para lançamento:', true), 'options'=> $mesesAnos, 'type'=> 'select', 'value'=> $mesAno, 'tabindex'=> 1));
					else:
						echo $html->div('error-message', sprintf(__('Todos os lançamentos já efetuados para os próximos %s meses', true), $qtdMeses));
					endif;
				?>
			</li>
			<li>
				<?php echo $form->input('Lancamento.valor_documento', array('label'=> __('Valor a ser lançado:', true), 'value'=> $formatar->real($valorDocumento), 'disabled'=> 'disabled', 'tabindex'=> 2)); ?>
			</li>
			<li>
				<fieldset class="required group">
					<legend><?php __('Instrução para boleto:'); ?></legend>
					<ol>
						<li>
							<?php echo $form->radio('Lancamento.instrucao_boleto_id', $instrucoes, array('separator'=> '</li><li>', 'value'=> 1, 'legend'=> false, 'tabindex'=> 3)); ?>
						</li>
					</ol>
				</fieldset>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<?php
			if( sizeof($mesesAnos) > 0 ):
				echo $html->tag('button', $html->image('/img/famfamfam/book.png').' '.__('Lançar Taxa Condominial', true), array('type'=> 'submit',  'tabindex'=> 4));
			endif;
		?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 5), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

<?php $html->addCrumb('Lançamentos', array('controller'=> 'menus', 'action'=> 'lancamentos')); ?>
<?php $html->addCrumb('Taxa Condominial', array('controller'=> 'lancamentos', 'action'=> 'taxacondominial')); ?>
