<?php echo $form->create('Taxa', array('action'=> 'cadastrar')); ?>
	<fieldset class="view-content">
		<legend><span><?php __('Lançamento de Taxa Extra'); ?></span></legend>
		<ol>
			<li>
				<?php echo $form->input('Taxa.dta_assembleia', array('label'=> __('Data da assembleia:', true), 'value'=> $hoje, 'type'=> 'text')); ?>
			</li>
			<li>
				<?php echo $form->input('Taxa.motivo', array('label'=> __('Motivo:', true), 'value'=> '', 'type'=> 'text')); ?>
			</li>
			<li>
				<?php echo $form->input('Taxa.valor_total', array('label'=> __('Valor total por unidade:', true), 'value'=> '0,00')); ?>
			</li>
			<li>
				<?php echo $form->input('Taxa.qtd_parcelas', array('label'=> __('Qtd. de parcelas:', true), 'type'=> 'select', 'options'=> $qtds, 'value'=> '0')); ?>
			</li>
			<li>
				<?php echo $form->input('Lancamento.valor_documento', array('label'=> __('Valor de cada parcela:', true), 'value'=> '0,00', 'disabled'=> 'disabled')); ?>
			</li>
			<li>
				<?php echo $form->input('Lancamento.mes_ano', array('label'=> __('1ª parcela para o mês:', true), 'options'=> $mesesAnos)); ?>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<?php echo $html->tag('button', $html->image('/img/famfamfam/book.png').' '.__('Lançar Taxa Extra', true), array('type'=> 'submit',  'tabindex'=> 4)); ?>
		<?php echo $html->link($html->image('/img/famfamfam/cross.png').' '.__('Desistir', true), array('controller'=> 'menus', 'action'=> 'index'), array('class'=> 'button', 'tabindex'=> 5), null, false); ?>
	</fieldset>
<?php echo $form->end(); ?>

<?php
	echo $javascript->link('prototype');

	echo $javascript->codeBlock('
$("TaxaMotivo").focus();
');

	// calcula o valor do documento a partir do total por unidade
	// e da quantidade de parcelas
	echo $javascript->codeBlock('
var fcalcular	= function(e) {

	var tot	= parseFloat($F("TaxaValorTotal").replace(",", "."));
	if( !isNaN(tot) ) {

		var qtd	= $F("TaxaQtdParcelas");
		var par	= tot/qtd;
		par		= par.toFixed(2);
		par		= par.replace(".", ",");

		Form.Element.setValue("LancamentoValorDocumento", par);
	} else {
		Form.Element.setValue("TaxaValorTotal", "0,00");
		Form.Element.setValue("LancamentoValorDocumento", "0,00");
		// $("TaxaValorTotal").focus();
		// alert("Favor inserir um valor válido.");
	}
}
Event.observe("TaxaValorTotal", "change", fcalcular);
Event.observe("TaxaQtdParcelas", "change", fcalcular);
');
?>
