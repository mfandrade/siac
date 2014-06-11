<div id="menus-sistema_dadosauxiliares">
	<fieldset class="view-content">
		<legend><span><?php __('Dados Auxiliares'); ?></span></legend>
		<ol>
			<li>
				<?php echo $html->link( $html->image('/img/famfamfam/building.png').' '. __('Unidades', true), array('controller'=> 'unidades', 'action'=> 'index'), array('class'=> 'button'), null, false); ?>
				<span class="info"><?php __('Visualizar dados das unidades.'); ?></span>
			</li>
			<li>
				<?php echo $html->link( $html->image('/img/famfamfam/user_add.png').' '. __('Proprietários', true), array('controller'=> 'proprietarios', 'action'=> 'cadastrar'), array('class'=> 'button'), null, false); ?>
				<span class="info"><?php __('Cadastrar novos proprietários.'); ?></span>
			</li>
			<li>
				<?php echo $html->link( $html->image('/img/famfamfam/text_align_left.png').' '. __('Instruções Boleto', true), array('controller'=> 'instrucoes_boleto', 'action'=> 'index'), array('class'=> 'button'), null, false); ?>
				<span class="info"><?php __('Cadastrar novas, alterar e excluir instruções de boleto.'); ?></span>
			</li>
			<li>
				<?php echo $html->link( $html->image('/img/famfamfam/user_gray.png').' '. __('Usuários SIAC', true), array('controller'=> 'usuarios', 'action'=> 'index'), array('class'=> 'button'), null, false); ?>
				<span class="info"><?php __('Cadastrar e trocar senha de usuários do SIAC.'); ?></span>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 5), null, false); ?>
	</fieldset>
</div>

<?php $html->addCrumb('Sistema', array('controller'=> 'menus', 'action'=> 'sistema')); ?>
<?php $html->addCrumb('Dados Auxiliares', array('controller'=> 'menus', 'action'=> 'sistema_dadosauxiliares')); ?>
