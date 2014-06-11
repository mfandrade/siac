<?php echo $form->create('Proprietario', array('action'=> 'cadastrar')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Cadastrar Proprietário(a)'); ?></span></legend>
		<ol>
			<li>
				<?php echo $form->input('Proprietario.nome', array('label'=> __('Nome:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.cpf_cnpj', array('label'=> __('CPF/CNPJ:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.endereco', array('label'=> __('Endereço completo:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.bairro', array('label'=> __('Bairro:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.cep', array('label'=> __('CEP:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.cidade', array('label'=> __('Cidade:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.uf', array('label'=> __('UF:', true), 'value'=> 'PA')); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.fone1', array('label'=> __('Telefone residencial:', true))); ?>
			</li>
			<!--li>
				<?php echo $form->input('Proprietario.fone2', array('label'=> __('Telefone comercial:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.celular', array('label'=> __('Celular:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.email', array('label'=> __('E-mail:', true))); ?>
			</li-->
		</ol>
	</fieldset>
	<!--fieldset class="view-content">
		<legend><span><?php __('Cônjuge do(a) Proprietário(a)'); ?></span></legend>
		<ol>
			<li><?php echo $form->input('Proprietario.conjuge_nome', array('label'=> __('Nome do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_cpf', array('label'=> __('CPF do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_rg', array('label'=> __('RG do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_fone1', array('label'=> __('Telefone residencial do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_fone2', array('label'=> __('Telefone comercial do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_celular', array('label'=> __('Celular do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_email', array('label'=> __('E-mail do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_endereco', array('label'=> __('Endereço completo do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_bairro', array('label'=> __('Bairro do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_cep', array('label'=> __('CEP do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_cidade', array('label'=> __('Cidade do cônjuge:', true))); ?>
			</li>
			<li>
				<?php echo $form->input('Proprietario.conjuge_uf', array('label'=> __('UF do cônjuge:', true), 'type'=> 'select', 'options'=> $ufs)); ?>
			</li>
		</ol>
	</fieldset-->
	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/tick.png').' '.__('Confirmar', true), array('type'=> 'submit')); ?>
		<?php echo $html->link($html->image('/img/famfamfam/arrow_left.png').' '.__('Voltar', true), array('controller'=> 'menus', 'action'=> 'sistema_dadosauxiliares'), array('class'=> 'button'), null, false); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button'), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

