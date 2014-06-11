<?php if( ($qtd= sizeof($proprietarios)) > 0 ): ?>
	<div>
		<table>
		<tr style="border:0;padding:0;margin:0;">
			<th style="padding:.2em;"><?php echo sprintf(__('%d registros para "%s"', true), $qtd, $nome); ?></th>
			<th style="padding:.2em;"><?php __('Visualizar'); ?></th>
		</tr>
		<?php $i= 1; ?>
		<?php foreach( $proprietarios as $proprietario ): ?>
			<tr>
				<td>
					<?php echo $i++; ?>.
					<?php echo str_replace($nome, sprintf('<span title="%s" class="highlight">%s</span>', $nome, $nome), $proprietario['Proprietario']['nome']); ?>
				</td>
				<td>
					<?php foreach($proprietario['Unidade'] as $unidade ): ?>
						<?php echo $html->link(sprintf('%sL%02d', $unidade['Quadra']['abbr'], $unidade['lote']), array('controller'=> 'unidades', 'action'=> 'ver', $unidade['id'])); ?>
						&nbsp;
					<?php endforeach; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	</div>
	<hr />
<?php else: ?>
	<strong class="error-message"><?php echo sprintf(__('Nenhum registro correspondente a "%s".', true), $nome); ?></strong>
<?php endif; ?>
