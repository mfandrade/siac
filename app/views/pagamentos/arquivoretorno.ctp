<?php echo $form->create('Pagamento', array('action'=> 'arquivoretorno', 'type'=> 'file')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Pagamento Via Arquivo de Retorno'); ?></span></legend>
		<ol>
			<li>
				<?php echo $form->label('FormUltimo', __('Último arquivo processado:', true)); ?>
				<span id="FormUltimo"><?php echo $arquivo; if( !empty($data) ) echo ' ' . sprintf(__('(do dia %s)', true), $formatar->data($data)); ?></span>
			</li>
			<li>
				<?php echo $form->input('Pagamento.arquivo', array('label'=> __('Selecione um arquivo:', true), 'type'=> 'file')); ?>
			</li>
		</ol>
	</fieldset>

	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/money.png').' '.__('Processar Arquivo', true), array('type'=> 'submit',  'tabindex'=> 4)); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 5), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

<?php $html->addCrumb('Pagamentos', array('controller'=> 'menus', 'action'=> 'pagamentos')); ?>
<?php $html->addCrumb('Via Arquivo de Retorno', array('controller'=> 'pagamentos', 'action'=> 'arquivo')); ?>
