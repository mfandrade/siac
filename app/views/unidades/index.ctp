<div id="unidades-index">
	<fieldset class="view-content">
		<legend><span><?php __('Unidades'); ?></span></legend>
		<ol>
			<li>
				<?php echo $form->create('Unidade', array('action'=> 'index')); ?>
					<?php echo $form->input('Unidade.id', array('type'=> 'select', 'options'=> $unidades, 'selected'=> $id, 'label'=> __('Por Quadra/Lote:', true), 'tabindex'=> 1)); ?>
					<?php echo $form->submit(__('Visualizar', true), array('id'=> 'btnVisualizar', 'tabindex'=> 2)); ?>
				<?php echo $form->end(); ?>
			</li>
			<li><span><?php __('OU'); ?></span></li>
			<li>
				<?php echo $form->create(); ?>
					<?php echo $javascript->link(array('prototype'));//, 'scriptaculous', 'effects', 'controls')); ?>
					<?php echo $form->input('Proprietario.nome', array('label'=> __('Por Proprietário:', true), 'tabindex'=> 3)); ?>
					<?php echo $ajax->submit(__('Listar', true), array(
						  'id'		=> 'btnListar'
						, 'url'		=> array('controller'=> 'proprietarios', 'action'=> 'ajaxlistarproprietarios')
						, 'update'	=> 'divProprietarios'
						, 'tabindex'=> 4
					)); ?>
				<?php echo $form->end(); ?>
				<div id="divProprietarios"></div>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<?php echo $html->link($html->image('/img/famfamfam/arrow_left.png').' '.__('Voltar', true), array('controller'=> 'menus', 'action'=> 'sistema_dadosauxiliares'), array('class'=> 'button', 'tabindex'=> 5), null, false); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 6), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>
</div>

<?php $html->addCrumb('Sistema', array('controller'=> 'menus', 'action'=> 'sistema')); ?>
<?php $html->addCrumb('Dados Auxiliares', array('controller'=> 'menus', 'action'=> 'sistema_dadosauxiliares')); ?>
<?php $html->addCrumb('Unidades', array('controller'=> 'unidades', 'action'=> 'index')); ?>
<?php $html->addCrumb('Visualizar', array('controller'=> 'unidades', 'action'=> 'ver')); ?>
