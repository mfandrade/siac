<br />
<?php echo $form->input('Aux.atraso', array('type'=> 'text', 'label'=> __('Atraso', true), 'value'=> sprintf('%d dias', $valores['atraso']), 'disabled'=> 'disabled')); ?>
<span id="divValorTotal"><!-- XXX: vide ajaxincluirdesconto -->
	<?php echo $form->input('Aux.valor_total', array('type'=> 'hidden', 'label'=> false, 'value'=> $formatar->real($valores['valor_total']))); ?>
</span>
<?php if( $valores['atraso'] > 0 ): ?>
	<?php echo $form->input('Aux.valor_multa', array('label'=> sprintf(__('Multa (%s%% %s)', true), $valores['p_multa'], $formatar->real($lancamento['Lancamento']['valor_documento'])), 'readonly'=> 'readonly', 'value'=> $formatar->real($valores['valor_multa']))); ?>
	<?php echo $form->input('Aux.valor_juros', array('label'=> sprintf(__('Juros (%s%% %s) a.d.', true), $valores['p_juros'], $formatar->real($lancamento['Lancamento']['valor_documento'])), 'readonly'=> 'readonly', 'value'=> $formatar->real($valores['valor_juros']))); ?>
	<?php echo $form->input('Aux.valor_desconto', array('label'=> __('Desconto', true), 'value'=> $formatar->real($valores['valor_desconto']))); ?>
<?php else: ?>
	<?php echo $form->input('Aux.valor_desconto', array('label'=> __('Informar algum desconto', true), 'value'=> $formatar->real($valores['valor_desconto']))); ?>
<?php endif; ?>

<?php
	// atualiza o valor a partir do desconto dado
	// XXX: está replicado no form_pagamento
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
?>
