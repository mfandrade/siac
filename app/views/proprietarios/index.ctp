<div id="proprietarios-index" class="view-content">
	<h2><?php __('Proprietários'); ?></h2> <?php echo $navigator->show(); ?>
	<?php echo $html->link(__('Cadastrar Novo', true), array('controller'=> 'proprietarios', 'action'=> 'cadastrar')); ?>

	<table summary="<?php __('Relação de proprietários'); ?>">
		<caption><?php __('Relação de proprietários'); ?></caption>
		<?php //echo $html->tableHeaders(array('Texto', 'Descrição', 'Tipo de Lançamento', 'Modificação', '', '')); ?>
		<tr>
			<th><?php echo $paginator->sort(__('Nome', true), 'Proprietario.nome'); ?></th>
			<th><?php echo $paginator->sort(__('CPF/CNPJ', true), 'Proprietario.cpf_cnpj'); ?></th>
			<!--th><?php echo $paginator->sort(__('Endereço', true), 'Proprietario.endereco'); ?></th-->
			<th><?php echo $paginator->sort(__('Telefone', true), 'Proprietario.fone1'); ?></th>
			<th><?php echo $paginator->sort(__('Modificado em', true), 'Proprietario.modified'); ?></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>

		<tbody>
			<?php foreach( $proprietarios as $proprietario ): ?>
			<tr>
				<td><?php echo $html->link($proprietario['Proprietario']['nome'], array('controller'=> 'proprietarios', 'action'=> 'editar', $proprietario['Proprietario']['id'])); ?></td>
				<td><?php echo $html->link($formatar->cpfcnpj($proprietario['Proprietario']['cpf_cnpj']), array('controller'=> 'proprietarios', 'action'=> 'editar', $proprietario['Proprietario']['id'])); ?></td>
				<!--td><?php echo $html->link($proprietario['Proprietario']['endereco'], array('controller'=> 'proprietarios', 'action'=> 'editar', $proprietario['Proprietario']['id'])); ?></td-->
				<td><?php echo $html->link($formatar->telefone($proprietario['Proprietario']['fone1']), array('controller'=> 'proprietarios', 'action'=> 'editar', $proprietario['Proprietario']['id'])); ?></td>
				<td><?php echo $html->link($formatar->datahora($proprietario['Proprietario']['modified']), array('controller'=> 'proprietarios', 'action'=> 'editar', $proprietario['Proprietario']['id'])); ?></td>
				<td><?php echo $html->link($html->image('/img/famfamfam/pencil.png', array('alt'=> 'Editar')), array('controller'=> 'instrucoes_boleto', 'action'=> 'editar', $proprietario['Proprietario']['id']), null, null, false); ?></td>
				<td><?php echo $html->link($html->image('/img/famfamfam/delete.png', array('alt'=> 'Excluir')), array('controller'=> 'instrucoes_boleto', 'action'=> 'excluir', $proprietario['Proprietario']['id']), null, __('Quer mesmo excluir este proprietário?', true), false); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
