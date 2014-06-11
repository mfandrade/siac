<div id="usuarios-login">
	<?php echo $form->create('Usuario', array('action'=> 'login')); ?>
	<fieldset>
<?php 	if( $session->check('Message.auth') ): ?>
			<?php $session->flash('auth'); ?>
<?php 	endif; ?>
		<ol>
			<li>
				<?php echo $form->input('Usuario.usuario', array('label'=> __('Usuário:', true), 'tabindex'=> 1)); ?>
			</li>
			<li>
				<?php echo $form->input('Usuario.senha', array('label'=> __('Senha:', true), 'type'=> 'password', 'tabindex'=> 2)); ?>
			</li>
			<li class="submit">
				<?php echo $form->button(__('Acessar', true), array('type'=> 'submit', 'tabindex'=> 3)); ?>
			</li>
		</ol>
	</fieldset>
	<?php echo $form->end(); ?>
</div>

<?php
	echo $javascript->link('prototype');
	echo $javascript->codeBlock('
		$("UsuarioUsuario").focus();
	');
?>
