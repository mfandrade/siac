<?php echo $form->create('Unidade', array('action'=> 'editar')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Editar Unidade'); ?></span></legend>
		<ol>
			<li><?php echo $form->label('txtUnidade', 'Unidade:'); ?>
				<span id="txtUnidade"><?php echo sprintf('Q%02sL%02s', $this->data['Unidade']['quadra_id'], $this->data['Unidade']['lote']); ?></span>
				<?php echo $form->hidden('Unidade.id', array('value'=> $this->data['Unidade']['id'])); ?>
				<?php echo $form->hidden('Unidade.rua_id', array('value'=> $this->data['Unidade']['rua_id'])); ?>
				<?php echo $form->hidden('Unidade.quadra_id', array('value'=> $this->data['Unidade']['quadra_id'])); ?>
				<?php echo $form->hidden('Unidade.lote', array('value'=> $this->data['Unidade']['lote'])); ?>
			</li>
			<li>
				<?php echo $form->input('Unidade.proprietario_id', array('options'=> $proprietarios, 'default'=> $this->data['Unidade']['proprietario_id'], 'type'=> 'select', 'label'=> __('Proprietário(a)', true), 'tabindex'=> 1)); ?>
				<?php echo $html->link(__('cadastrar novo proprietário', true), array('controller'=> 'proprietarios', 'action'=> 'cadastrar'), array('style'=> 'margin-left:15em;', 'tabindex'=> 2)); ?>
			</li>
		</ol>
	</fieldset>
	<fieldset class="view-content">
		<legend><span><?php __('Morador(a)'); ?></span></legend>
		<ol>
			<li>
				<?php echo $form->input('Unidade.morador_nome', array('label'=> __('Nome:', true), 'tabindex'=> 5)); ?>
			</li>
			<li>
				<?php echo $form->input('Unidade.morador_rg', array('label'=> __('RG:', true), 'tabindex'=> 6)); ?>
			</li>
			<li>
				<?php echo $form->input('Unidade.morador_cpf', array('label'=> __('CPF:', true), 'value'=> $formatar->cpfcnpj($this->data['Unidade']['morador_cpf']), 'tabindex'=> 7)); ?>
			</li>
			<li>
				<?php echo $form->input('Unidade.morador_fone1', array('label'=> __('Telefone Residencial:', true), 'value'=> $formatar->telefone($this->data['Unidade']['morador_fone1']), 'tabindex'=> 8)); ?>
			</li>
			<li>
				<?php echo $form->input('Unidade.morador_fone2', array('label'=> __('Telefone Comercial:', true), 'value'=> $formatar->telefone($this->data['Unidade']['morador_fone2']), 'tabindex'=> 9)); ?>
			</li>
			<li>
				<?php echo $form->input('Unidade.morador_celular', array('label'=> __('Celular:', true), 'value'=> $formatar->telefone($this->data['Unidade']['morador_celular']), 'tabindex'=> 10)); ?>
			</li>
			<li>
				<?php echo $form->input('Unidade.morador_email', array('label'=> __('E-mail:', true), 'tabindex'=> 11)); ?>
			</li>
			<li>
				<?php echo $form->input('Unidade.morador_obs', array('label'=> __('Obs:', true), 'type'=> 'textarea', 'rows'=> 3, 'cols'=> 30, 'tabindex'=> 12)); ?>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/tick.png').' '.__('Confirmar', true), array('type'=> 'submit',  'tabindex'=> 3)); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'unidades', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 4), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

