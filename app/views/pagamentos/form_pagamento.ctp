<fieldset class="view-content">
	<legend><span><?php __('Pagamento'); ?></span></legend>
	<ol>
		<li>
			<?php echo $form->input('Pagamento.lancamento_id', array('type'=> 'hidden', 'value'=> $lancamento['Lancamento']['id'])); ?>
			<?php echo $form->input('Lancamento.valor_documento', array('label'=> __('Valor do documento', true), 'value'=> $formatar->real($lancamento['Lancamento']['valor_documento']), 'disabled'=> 'disabled')); ?>
		</li>
		<li>
			<?php echo $form->input('Pagamento.dta_pagamento', array('type'=> 'text', 'label'=> __('Data de pagamento', true), 'value'=> $hoje, 'style'=> 'float:left;')); ?>
			<?php echo $html->tag('button', $html->image('/img/famfamfam/cog.png'), array('type'=> 'button', 'id'=> 'btnDetalhesPagamento', 'title'=> __('Detalhes', true), 'style'=> 'height:24px;')); ?>
			<div id="divDetalhesPagamento"><!-- XXX: alterado pelo ajaxrecalcularpagamento (conteúdo idêntico) -->
				<br />
				<?php echo $form->input('Aux.atraso', array('type'=> 'text', 'label'=> __('Atraso', true), 'value'=> sprintf('%d dias', $valores['atraso']), 'disabled'=> 'disabled')); ?>
				<span id="divValorTotal">
					<?php echo $form->input('Aux.valor_total', array('type'=> 'hidden', 'label'=> false, 'value'=> $formatar->real($valores['valor_total']))); ?>
				</span>
				<?php if( $valores['atraso'] > 0 ): ?>
					<?php echo $form->input('Aux.valor_multa', array('label'=> sprintf(__('Multa (%s%% %s)', true), $valores['p_multa'], $formatar->real($lancamento['Lancamento']['valor_documento'])), 'readonly'=> 'readonly', 'value'=> $formatar->real($valores['valor_multa']))); ?>
					<?php echo $form->input('Aux.valor_juros', array('label'=> sprintf(__('Juros (%s%% %s) a.d.', true), $valores['p_juros'], $formatar->real($lancamento['Lancamento']['valor_documento'])), 'readonly'=> 'readonly', 'value'=> $formatar->real($valores['valor_juros']))); ?>
					<?php echo $form->input('Aux.valor_desconto', array('label'=> __('Desconto', true), 'value'=> $formatar->real($valores['valor_desconto']))); ?>
				<?php else: ?>
					<?php echo $form->input('Aux.valor_desconto', array('label'=> __('Informar algum desconto', true), 'value'=> $formatar->real($valores['valor_desconto']))); ?>
				<?php endif; ?>
			</div>
		</li>
		<li>
			<?php echo $form->input('Pagamento.valor_pago', array('label'=> __('Total a pagar', true), 'value'=> $formatar->real($valores['valor_total']), 'readonly'=> 'readonly', 'style'=> 'float:left;')); ?>
			<?php echo $html->tag('button', $html->image('/img/famfamfam/coins.png'), array('type'=> 'button', 'id'=> 'btnFormaPagamento', 'title'=> __('Forma de pagamento', true), 'style'=> 'height:24px;')); ?>
			<div id="divFormaPagamento">
				<br />
				<?php echo $form->input('Pagamento.dinheiro', array('label'=> __('Total pago em dinheiro', true), 'value'=> $formatar->real($valores['valor_total']))); ?>
				<hr style="display:none;" />
				<?php echo $form->input('Pagamento.cheque', array('label'=> __('Total pago em cheque', true), 'value'=> '0,00')); ?>
				<?php //echo $form->input('Pagamento.cheque_banco_id', array('label'=> __('Cheque do banco', true))); ?>
				<?php //echo $form->input('Pagamento.cheque_dia', array('label'=> __('Cheque para o dia', true), 'type'=> 'text')); ?>
				<?php echo $form->input('Pagamento.cheque_info', array('label'=> __('Informações adicionais', true), 'rows'=> 2, 'cols'=> 30)); ?>
			</div>
		</li>
	</ol>
</fieldset>
<?php
	// se renderizou o form, pode habilitar o submit
	echo $javascript->codeBlock('
Form.Element.enable("btnSubmit");'
	);

	// trata os links de detalhes e forma de pagamento
	echo $javascript->codeBlock('
Event.observe("btnDetalhesPagamento", "click", function(e) {
	$("divDetalhesPagamento").toggle();
	e.stop();
});

Event.observe("btnFormaPagamento", "click", function(e) {
	$("divFormaPagamento").toggle();
	e.stop();
});
	');

	// cálculo dos valores de dinheiro/cheque
	echo $javascript->codeBlock('
Event.observe("PagamentoDinheiro", "change", function(e) {
	var p1		= parseFloat($F("PagamentoDinheiro").replace(",", "."));
	var p2		= parseFloat($F("PagamentoCheque").replace(",", "."));
	var total	= parseFloat($F("PagamentoValorPago").replace(",", "."));

	if( p1 > total || p1 < 0 ) {
		alert("Valor inválido.");
		e.stopEvent();
		Form.Element.setValue("PagamentoDinheiro", '.$formatar->real($valorTotal).');
		Form.Element.setValue("PagamentoCheque", "0,00");
	}
	nval		= total - p1;
	nval		= nval.toFixed(2);
	nval		= nval.replace(".", ",");
	Form.Element.setValue("PagamentoCheque", nval);
});
Event.observe("PagamentoCheque", "change", function(e) {
	var p1		= parseFloat($F("PagamentoCheque").replace(",", "."));
	var p2		= parseFloat($F("PagamentoDinheiro").replace(",", "."));
	var total	= parseFloat($F("PagamentoValorPago").replace(",", "."));

	if( p1 > total || p1 < 0 ) {
		alert("Valor inválido.");
		e.stopEvent();
		Form.Element.setValue("PagamentoCheque", '.$formatar->real($valorTotal).');
		Form.Element.setValue("PagamentoDinheiro", "0,00");
	}
	nval		= total - p1;
	nval		= nval.toFixed(2);
	nval		= nval.replace(".", ",");
	Form.Element.setValue("PagamentoDinheiro", nval);
});
');

	// uso do dia do pagamento para recálculo dos valores
	$recalcularPagamento= $ajax->remoteFunction(array(
		  'update'		=> 'divDetalhesPagamento'
		, 'url'			=> array('controller'=> 'pagamentos', 'action'=> 'ajaxrecalcularpagamento')
		, 'with'		=> 'Form.serializeElements(new Array($("LancamentoId"), $("PagamentoDtaPagamento")))'
		, 'before'		=> 'Form.Element.disable("btnSubmit"); Form.Element.setValue("PagamentoValorPago", $F("PagamentoValorPago").replace(/[0-9]/g, "?")); Form.Element.setValue("AuxValorTotal", $F("AuxValorTotal").replace(/[0-9]/g, "?"));'
 		, 'complete'	=> 'Form.Element.setValue("PagamentoValorPago", $F("AuxValorTotal")); Form.Element.setValue("PagamentoDinheiro", $F("PagamentoValorPago")); Form.Element.enable("btnSubmit");'
	));
	echo $javascript->codeBlock('
Event.observe("PagamentoDtaPagamento", "change", function(e) {'.$recalcularPagamento.';});
	');

	// atualiza o valor a partir do desconto dado
	// XXX: está replicado no ajaxrecalcularpagamento
	echo $javascript->codeBlock('
Event.observe("AuxValorDesconto", "change", function(e) {
	Form.Element.disable("btnSubmit");
	try {

		var valorDocumento	= parseFloat($F("LancamentoValorDocumento").replace(",", "."));
		var valorDesconto	= parseFloat($F("AuxValorDesconto").replace(",", "."));
		var valorMulta		= parseFloat("0.00");
		if( $("AuxValorMulta") ) {
			valorMulta	= parseFloat($F("AuxValorMulta").replace(",", "."));
		}
		var valorJuros		= parseFloat("0.00");
		if( $("AuxValorJuros") ) {
			valorJuros	= parseFloat($F("AuxValorJuros").replace(",", "."));
		}
		var descontoMaximo	= '.Configure::read('pagamentos_desconto_porcentagem_maxima').'

		if( !isNaN(valorDocumento) && !isNaN(valorMulta) && !isNaN(valorJuros) && !isNaN(valorDesconto) ) {

			if( valorDesconto > 0 && valorDesconto <= (valorDocumento*descontoMaximo/100) ) {
				var nval	= valorDocumento + valorMulta + valorJuros - valorDesconto;
				nval		= nval.toFixed(2);
				nval		= nval.replace(".", ",");

				Form.Element.setValue("PagamentoValorPago", nval);
				Form.Element.setValue("PagamentoDinheiro", nval); // situação inicial
				Form.Element.setValue("PagamentoCheque", "0,00");
			} else {

				alert("Valor de desconto muito alto. Considere oferecer um acordo.");
				Form.Element.setValue("AuxValorDesconto", "0,00");
				Form.Element.setValue("PagamentoValorPago", $F("AuxValorTotal"));
			}
		} else {

			Form.Element.setValue("AuxValorDesconto", "0,00");
			Form.Element.setValue("PagamentoValorPago", $F("AuxValorTotal"));
		}
		e.stop();

	} catch( err ) {}
	Form.Element.enable("btnSubmit");
});
	');

/*	$incluirDesconto	= $ajax->remoteFunction(array(
		  'update'		=> ''
		, 'url'			=> array('controller'=> 'pagamento', 'action'=> 'ajaxincluirdesconto')
		, 'with'		=> 'Form.serializeElement(new Array($("AuxValorDesconto"), $("AuxValorTotal")))'
		, 'before'		=> 'Form.Element.disable("btnSubmit");'
		, 'complete'	=> '
		if( $F("AuxValorTotal")==$F("PagamentoValorPago") ) {
			alert("Desconto acima do valor máximo permitido.");
			Form.Element.setValue("AuxValorDesconto", "0,00"));
		} else {
			Form.Element.setValue("PagamentoValorPago", $F("AuxValorTotal"));
		}
		$("AuxValorDesconto").readOnly	= true;
		Form.Element.enable("btnSubmit");
		'
	);
	echo $javascript->codeBlock('
Event.observe("AuxValorDesconto", "change", function(e) {'.$incluirDesconto.';});
	');
*/
?>
