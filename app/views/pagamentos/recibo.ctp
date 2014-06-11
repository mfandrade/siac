<div class="view-content" style="text-align:left; padding:1em 1em 1em 5em; font-weight:bold; font-size:120%;">
	<h1>RECIBO</h1>
	<p>
		Recebemos do(a) Sr(a) <?php echo $lancamento['Unidade']['Proprietario']['nome']; ?> (<?php echo sprintf('%sL%02d', $lancamento['Unidade']['Quadra']['abbr'], $lancamento['Unidade']['lote']); ?>),<br/>
		a importância de <?php echo $formatar->real($lancamento['Pagamento']['valor_pago'], true); ?> (<?php echo $formatar->extenso($lancamento['Pagamento']['valor_pago']); ?>),<br/>
		referente ao pagamento da Taxa Condominial do mês <?php echo $lancamento['Lancamento']['mes_ano']; ?>.
	<br />
	</p>
	<p style="font-size:70%;">
		PS: Pagamentos realizados com cheque só serão efetivados após a compensação do mesmo.
	</p>
	<br />
	<p>
		<?php setlocale(LC_ALL, array('ptb', 'pt_BR', 'por')); echo strftime('Belém, %d de %B de %Y.'); ?>
	</p>
	<br />
	<br />
	<p>
		_________________________________________________________
	</p>

</div>
<br /><br />
<div class="view-content" style="text-align:left; padding:1em 1em 1em 5em; font-weight:bold; font-size:120%;">
	<h1>RECIBO</h1>
	<p>
		Recebemos do(a) Sr(a) <?php echo $lancamento['Unidade']['Proprietario']['nome']; ?> (<?php echo sprintf('%sL%02d', $lancamento['Unidade']['Quadra']['abbr'], $lancamento['Unidade']['lote']); ?>),<br/>
		a importância de <?php echo $formatar->real($lancamento['Pagamento']['valor_pago'], true); ?> (<?php echo $formatar->extenso($lancamento['Pagamento']['valor_pago']); ?>),<br/>
		referente ao pagamento da Taxa Condominial do mês <?php echo $lancamento['Lancamento']['mes_ano']; ?>.
	<br />
	</p>
	<p style="font-size:70%;">
		PS: Pagamentos realizados com cheque só serão efetivados após a compensação do mesmo.
	</p>
	<br />
	<p>
		<?php setlocale(LC_ALL, array('ptb', 'pt_BR', 'por')); echo strftime('Belém, %d de %B de %Y.'); ?>
	</p>
	<br />
	<br />
	<p>
		_________________________________________________________
	</p>

</div>

<?php $html->addCrumb('Pagamentos', array('controller'=> 'menus', 'action'=> 'pagamentos')); ?>
<?php $html->addCrumb('Direto na Administração', array('controller'=> 'pagamentos', 'action'=> 'administracao')); ?>
<?php $html->addCrumb('Recibo', array('controller'=> 'pagamentos', 'action'=> 'recibo')); ?>
