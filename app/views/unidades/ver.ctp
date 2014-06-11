<?php //$html->addCrumb('SIAC', array('controller'=> 'menus', 'action'=> 'index')); ?>
<?php $html->addCrumb('Sistema', array('controller'=> 'menus', 'action'=> 'sistema')); ?>
<?php $html->addCrumb('Dados Auxiliares', array('controller'=> 'menus', 'action'=> 'sistema_dadosauxiliares')); ?>
<?php $html->addCrumb('Unidades', array('controller'=> 'unidades', 'action'=> 'index')); ?>
<?php $html->addCrumb('Visualizar', array('controller'=> 'unidades', 'action'=> 'ver', $unidade['Unidade']['id'])); ?>

<?php echo $form->create('Unidade', array('action'=> '#')); ?>
<div id="unidades-ver">
	<fieldset class="view-content">
		<legend><span><?php echo sprintf(__('Unidade: %sL%02s', true), $unidade['Quadra']['abbr'], $unidade['Unidade']['lote']); ?></span></legend>
		<div id="navigator">
			<?php echo $html->link($html->image('/img/famfamfam/resultset_first.png'), array('action'=> 'ver', $vizinhos['quadra']['prev']), array('class'=> 'button', 'tabindex'=> 1, 'title'=> __('Quadra Anterior', true)), null, false); ?>
			<?php echo $html->link($html->image('/img/famfamfam/resultset_previous.png'), array('action'=> 'ver', $vizinhos['lote']['prev']), array('class'=> 'button', 'tabindex'=> 2, 'title'=> __('Lote Anterior', true)), null, false); ?>
			<?php echo $html->link($html->image('/img/famfamfam/resultset_next.png'), array('action'=> 'ver', $vizinhos['lote']['next']), array('class'=> 'button', 'tabindex'=> 3, 'title'=> __('Próximo Lote', true)), null, false); ?>
			<?php echo $html->link($html->image('/img/famfamfam/resultset_last.png'), array('action'=> 'ver', $vizinhos['quadra']['next']), array('class'=> 'button', 'tabindex'=> 4, 'title'=> __('Próxima Quadra', true)), null, false); ?>
		</div>
		<ol>
			<li>
				<?php echo $form->label('Aux.id', __('Identificação:', true)); ?>
				<span id="AuxId"><?php echo sprintf(__('RUA %s, %s LOTE %d', true), $unidade['Rua']['descricao'], $unidade['Quadra']['descricao'], $unidade['Unidade']['lote']); ?></span>
			</li>
			<li>
				<?php echo $form->label('Unidade.proprietario_nome', __('Proprietário(a):', true)); ?>
				<?php echo $html->tag('span', $unidade['Proprietario']['nome'], array('id'=> 'UnidadeProprietarioNome')); ?>
				<?php echo $javascript->link(array('prototype', 'scriptaculous', 'effects', 'controls')); ?>
				<?php //echo $ajax->editor('UnidadeProprietarioId', array('controller'=> 'unidades', 'action'=> 'ajaxtrocarproprietario', $unidade['Unidade']['id']), array(
					  echo $ajax->editor('UnidadeProprietarioNome', array('controller'=> 'unidades', 'action'=> 'ajaxrenomearproprietario', $unidade['Unidade']['id']), array(
					  'okText'			=> __('Salvar', true)
					, 'cancelText'		=> __('Cancelar', true)
					, 'cancelControl'	=> 'button'
					, 'clickToEditText'	=> __('Clique para renomear', true)
					, 'savingText'		=> __('SALVANDO...', true)
					, 'update'			=> 'UnidadeProprietarioNome'
				)); ?>
			<?php echo $html->tag('button', $html->image('/img/famfamfam/coins.png'), array('type'=> 'button', 'id'=> 'btnTrocarProprietario', 'title'=> __('Transferir para outro proprietário existente', true), 'style'=> 'height:24px;')); ?>
			<?php //echo 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; debug($unidade); exit; ?>
			<div id="divListaProprietarios" style="display:none">
				<br />
				<?php echo $form->input('Unidade.novo_proprietario_id', array('label'=> __('Novo proprietário da unidade:', true), 'options'=> $proprietarios, 'default'=> $unidade['Proprietario']['nome'])); ?>
				<?php echo $ajax->submit('Trocar proprietário', array(
					  'url'		=> array('controller'=> 'Unidades', 'action'=> 'ajaxtrocarproprietario', $unidade['Unidade']['id'])
					, 'update'	=> 'UnidadeProprietarioNome'
					, 'after'	=> '$("divListaProprietarios").toggle();'
				)); ?>
			</div>
			
			</li>
			<li>
				<?php echo $form->label('Unidade.modified', __('Última modificação em:', true)); ?>
				<span id="UnidadeModified"><?php echo $formatar->datahora($unidade['Unidade']['modified']); ?></span>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<?php echo $html->link($html->image('/img/famfamfam/arrow_left.png').' '.__('Voltar', true), array('controller'=> 'unidades', 'action'=> 'index', $unidade['Unidade']['id']), array('class'=> 'button', 'tabindex'=> 6), null, false); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 7), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>
</div>

<?php
	echo $javascript->codeBlock('
Event.observe("btnTrocarProprietario", "click", function(e) {
	$("divListaProprietarios").toggle();
	e.stop();
});
');
