<div id="usuarios-cadastrar">
    <?php echo $form->create('Usuario', array('action'=> 'cadastrar')); ?>
    <fieldset>
		<legend><span><?php __('Cadastrar Usuário SIAC'); ?></span></legend>
        <ol>
            <li>
                <?php echo $form->input('Usuario.nome_completo', array('label'=> __('Nome completo:', true), 'tabindex'=> 1)); ?>
            </li>
            <li>
                <?php echo $form->input('Usuario.usuario', array('label'=> __('Usuário:', true), 'tabindex'=> 2)); ?>
            </li>
            <li>
                <?php echo $form->input('Usuario.senha', array('label'=> __('Senha:', true), 'type'=> 'password', 'value'=> '', 'tabindex'=> 3)); ?>
            </li>
            <li>
                <?php echo $form->input('Usuario.confirmacao', array('label'=> __('Confirme a senha:', true), 'type'=> 'password', 'value'=> '', 'tabindex'=> 4)); ?>
            </li>
        </ol>
    </fieldset>
	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/user_add.png').' '.__('Cadastrar Usuário', true), array('type'=> 'submit',  'tabindex'=> 5)); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 6), null, false); ?>
	</fieldset>
    <?php echo $form->end(); ?>
</div>

<?php
    echo $javascript->link('prototype');
    echo $javascript->codeBlock('
		$("UsuarioUsuario").focus();
	');
?>

<?php $html->addCrumb('Sistema', array('controller'=> 'menus', 'action'=> 'sistema')); ?>
<?php $html->addCrumb('Dados Auxiliares', array('controller'=> 'menus', 'action'=> 'sistema_dadosauxiliares')); ?>
<?php $html->addCrumb('Usuários', array('controller'=> 'usuarios', 'action'=> 'index')); ?>
<?php $html->addCrumb('Cadastrar', array('controller'=> 'usuarios', 'action'=> 'cadastrar')); ?>
