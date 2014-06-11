<div class="view-content">
	<h2><?php __('Pagamento Direto na Administraçāo'); ?></h2>
</div>
<?php echo $form->create('Lancamento', array('action'=> '#')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Lançamento a Pagar'); ?></span></legend>
		<ol>
			<li>
				<?php echo $form->input('Lancamento.unidade_id', array('label'=> __('Unidade (Quadra e Lote)', true), 'type'=> 'select', 'options'=> $unidades, 'default'=> 0, 'style'=> 'width:20em')); ?>
			</li>
			<li>
				<?php echo $form->input('Lancamento.tipo_lancamento_id', array('label'=> __('Pagamento de', true), 'type'=> 'select', 'options'=> $tiposLancamento, 'default'=> 0)); ?>
			</li>
			<li>
				<?php echo $form->input('Lancamento.id', array('label'=> __('Mês/ano', true), 'type'=> 'select', 'options'=> array(__('Sem opções disponíveis', true)), 'disabled'=> 'disabled')); ?>
			</li>
		</ol>
	</fieldset>
<?php echo $form->end(); ?>

<?php echo $form->create('Pagamento', array('action'=> 'administracao')); ?>
	<div id="form_pagamento"> </div>

	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/money.png').' '.__('Registrar Pagamento', true), array('type'=> 'submit', 'id'=> 'btnSubmit', 'disabled'=> 'disabled', 'onclick'=> 'return confirm("Confirmar o pagamento para esta unidade, com este valor e nesta data?");')); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 5), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

<?php
	echo $javascript->link('prototype');
	$campos= array('LancamentoUnidadeId', 'LancamentoTipoLancamentoId');
	foreach( $campos as $campo ):
		echo $ajax->observeField($campo, array(
			  'update'		=> 'LancamentoId'
			, 'condition'	=> '$F("LancamentoUnidadeId") != "0"'
			, 'frequency'	=> 0.1
			, 'url'			=> array('controller'=> 'pagamentos', 'action'=> 'ajaxobterlancamentosaberto')
			, 'with'		=> 'Form.serialize( $("Lancamento#Form") )'
		));
	endforeach;
	echo $ajax->observeField('LancamentoId', array(
		  'update'		=> 'form_pagamento'
		, 'url'			=> array('controller'=> 'pagamentos', 'action'=> 'ajaxobterlancamento')
		, 'condition'	=> '$F("LancamentoId") != "0"'
		, 'frequency'	=> 0.1
		, 'complete'	=> '$("divDetalhesPagamento").toggle(); $("divFormaPagamento").toggle();'
	));
?>

<?php $html->addCrumb('Pagamentos', array('controller'=> 'menus', 'action'=> 'pagamentos')); ?>
<?php $html->addCrumb('Direto na Administração', array('controller'=> 'pagamentos', 'action'=> 'administracao')); ?>
