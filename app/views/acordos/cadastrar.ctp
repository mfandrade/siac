<?php echo $form->create('Acordo', array('action'=> 'novo')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Acordo'); ?></span></legend>
		<ol id="quem">
			<li><?php echo $form->input('Acordo.unidade_id', array('label'=> __('Unidade proponente:', true), 'options'=> $unidades, 'div'=> false)); ?>
				<?php echo $javascript->link('prototype'); ?>
				<?php echo $ajax->observeField('AcordoUnidadeId', array(
					  'url'		=> array('controller'=> 'acordos', 'action'=> 'ajaxobterlancamentosaberto')
					, 'update'	=> 'oque'
				)); ?>
				</li>
			<li><?php echo $form->label('Aux.dta_acordo', __('Data da proposta:', true)); ?>
				<?php echo $html->tag('span', $hoje, array('id'=> 'AuxDtaAcordo')); ?>
			</li>
		</ol>
		<?php echo $html->tag('div', '', array('id'=> 'oque')); ?>
		<?php //echo $html->tag('div', '', array('id'=> 'como-quando')); ?>
	</fieldset>
	<fieldset class="buttons">
		<?php
			if( false )://sizeof($mesesAnos) > 0 ):
				echo $html->tag('button', $html->image('/img/famfamfam/book.png').' '.__('Registrar Acordo', true), array('type'=> 'submit',  'tabindex'=> 4));
			endif;
		?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 5), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

<?php $html->addCrumb('Lançamentos', array('controller'=> 'menus', 'action'=> 'lancamentos')); ?>
<?php $html->addCrumb('Acordo', array('controller'=> 'acordos', 'action'=> 'novo')); ?>
