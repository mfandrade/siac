<table>
	<tr style="background-color:black; color:lightgray;">
		<th>Proprietário</th>
		<th>Quadra/Lote</th>
		<th>Tipo</th>
		<th>Mês/Ano</th>
		<th>Data de Pagamento</th>
		<th>Valor Pago</th>
	<!--
		<th><?php //echo $paginator->sort('Proprietário', 'Proprietario.nome'); ?></th>
		<th><?php //echo $paginator->sort('Quadra/Lote', 'Unidade.quadra_id'); ?></th>
		<th><?php //echo $paginator->sort('Tipo', 'Lancamento.tipo_lancamento_id'); ?></th>
		<th><?php //echo $paginator->sort('Mês/Ano', 'Lancamento.mes_ano'); ?></th>
		<th><?php //echo $paginator->sort('Data de Pagamento', 'Pagamento.dta_pagamento'); ?></th>
		<th><?php //echo $paginator->sort('Valor Pago', 'Pagamento.valor_pago'); ?></th>
	-->
	</tr>
<?php $i= 0; $total= 0.00; ?>
<?php foreach( $registros as $registro ): ?>
	<tr style="background-color:<?php echo ( ++$i % 2 )? '#ffffff': '#f0f0f0'; ?>">
		<td><?php echo $registro['Lancamento']['Unidade']['Proprietario']['nome']; ?></td>
		<td><?php
				$quadra	= $registro['Lancamento']['Unidade']['quadra_id'];
				$lote	= $registro['Lancamento']['Unidade']['lote'];
				$quadra	= str_pad($quadra, 2, '0', STR_PAD_LEFT);
				$lote	= str_pad($lote, 2, '0', STR_PAD_LEFT);
				$unidade= sprintf('Q%sL%s', $quadra, $lote);
				echo $unidade;
			?></td>
		<td><?php
				$tipo	= $registro['Lancamento']['tipo_lancamento_id'];
				switch( $tipo ):
					case 2: echo 'MULTA';
							break;
					case 1: echo 'TAXA EXTRA PÓRTICO';
							break;
					default:
					case 0: echo 'TAXA CONDOMINIAL';
							break;
				endswitch;
			?></td>
		<td><?php echo $registro['Lancamento']['mes_ano']; ?></td>
		<td><?php echo $formatar->data($registro['Pagamento']['dta_pagamento']); ?></td>
		<td><?php echo $registro['Pagamento']['valor_pago']; ?></td>
		<?php $total+= $registro['Pagamento']['valor_pago']; ?>
	</tr>

<?php endforeach; ?>
	<tr>
		<td colspan="4" align="right"><strong>TOTAL:</strong></td>
		<td colspan="2" align="right"><strong><?php echo $formatar->real($total, true); ?></strong></td>
	</tr>
</table>
