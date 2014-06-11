<?php echo $form->create('Proprietario', array('action'=> 'editar')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Editar Proprietário(a)'); ?></span></legend>
		<?php echo $form->input('Proprietario.id', array('type'=> 'hidden', 'value'=> $proprietario['Proprietario']['id'])); ?>
		<ol>
			<li>
				<?php echo $form->input('Proprietario.nome', array('label'=> __('Nome:', true), 'value'=> $proprietario['Proprietario']['nome'])); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.cpf_cnpj', array('label'=> __('CPF/CNPJ:', true), 'value'=> $proprietario['Proprietario']['cpf_cnpj'])); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.endereco', array('label'=> __('Endereço completo:', true), 'value'=> $proprietario['Proprietario']['endereco'])); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.bairro', array('label'=> __('Bairro:', true), 'value'=> $proprietario['Proprietario']['bairro'])); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.cep', array('label'=> __('CEP:', true), 'value'=> $proprietario['Proprietario']['cep'])); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.cidade', array('label'=> __('Cidade:', true), 'value'=> $proprietario['Proprietario']['cidade'])); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.uf', array('label'=> __('UF:', true), 'type'=> 'select', 'options'=> array('PA'), 'value'=> 'PA')); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.fone1', array('label'=> __('Telefone residencial:', true), 'value'=> $proprietario['Proprietario']['fone1'])); ?>
			</li>
			<!--li>
				<?php echo $form->input('Proprietario.fone2', array('label'=> __('Telefone comercial:', true), 'value'=> $proprietario['Proprietario']['fone2'])); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.celular', array('label'=> __('Celular:', true), 'value'=> $proprietario['Proprietario']['celular'])); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.email', array('label'=> __('E-mail:', true), 'value'=> $proprietario['Proprietario']['email'])); ?>
			</li-->
		</ol>
	</fieldset>
	<!--fieldset class="view-content">
		<legend><span><?php __('Cônjuge do(a) Proprietário(a)'); ?></span></legend>
		<ol>
			<li><?php echo $form->input('Proprietario.conjuge_nome', array('label'=> __('Nome do cônjuge:', true), 'tabindex'=> 15)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_cpf', array('label'=> __('CPF do cônjuge:', true), 'tabindex'=> 16)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_rg', array('label'=> __('RG do cônjuge:', true), 'tabindex'=> 17)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_fone1', array('label'=> __('Telefone residencial do cônjuge:', true), 'tabindex'=> 18)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_fone2', array('label'=> __('Telefone comercial do cônjuge:', true), 'tabindex'=> 19)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_celular', array('label'=> __('Celular do cônjuge:', true), 'tabindex'=> 20)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_email', array('label'=> __('E-mail do cônjuge:', true), 'tabindex'=> 21)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_endereco', array('label'=> __('Endereço completo do cônjuge:', true), 'tabindex'=> 22)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_bairro', array('label'=> __('Bairro do cônjuge:', true), 'tabindex'=> 23)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_cep', array('label'=> __('CEP do cônjuge:', true), 'tabindex'=> 24)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_cidade', array('label'=> __('Cidade do cônjuge:', true), 'tabindex'=> 25)); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_uf', array('label'=> __('UF do cônjuge:', true), 'type'=> 'select', 'options'=> $ufs, 'tabindex'=> 26)); ?>
			</li>
		</ol>
	</fieldset-->
	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/tick.png').' '.__('Confirmar', true), array('type'=> 'submit',  'tabindex'=> 13)); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'sistema_dadosauxiliares'), array('class'=> 'button', 'tabindex'=> 14), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

