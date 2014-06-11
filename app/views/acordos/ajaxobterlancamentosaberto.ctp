<ol>
	<li>
		<?php //echo $form->input('Lancamento.id', array('legend'=> __('Lançamentos em aberto:', true), 'options'=> $emAberto['Taxa Condominial'], 'type'=> 'radio', 'class'=> 'group')); ?>
		<fieldset class="group">
			<legend><?php __('Lançamentos em aberto:'); ?></legend>
			<dl style="background-color:#EDECEB;border:1.75px #fcfcf5 inset;-webkit-border-radius:3px;-moz-border-radius:3px;margin-left:15em;margin-top:-1em;margin-bottom:1em;height:7.5em;width:13em;overflow:auto;">
				<?php $total= 0.00; ?>
				<?php foreach( $emAberto as $tipo=> $dados ): ?>
					<dt><?php echo $tipo; ?></dt>
					<?php foreach( $dados as $id=> $valores ): ?>
						<?php list($mesAno, $valor, $juros, $multa, $desconto, $atraso) = explode('|', $valores); ?>
						<?php $total+= $valorMes = $valor+$juros+$multa-$desconto; ?>
						<dd>
							<?php echo $form->input('Lancamento.id'.$id, array('type'=> 'checkbox', 'name'=> 'data[Lancamento][id][]', 'value'=> $id, 'label'=> $mesAno.'&nbsp;&nbsp;('.$formatar->real($valorMes, true).')', 'checked'=> 'checked', 'rel'=> $valorMes)); ?>
							<?php
								echo $javascript->codeBlock('alert($F("LancamentoId3312"))');
							?>
						</dd>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</dl>
		</fieldset>
	</li>
	<!-- // tudo o que é devido	: total_geral -->
	<!-- o que foi selecionado	: total_devido -->
	<!-- o valor que será pago	: total_acordado -->
	<li><?php echo $form->input('Acordo.total_devido', array('label'=> __('Valor Total Devido:', true), 'readonly'=> 'readonly', 'value'=> $formatar->real($total))); ?>
	</li>
	<li><?php echo $form->input('Acordo.total_acordado', array('label'=> __('Valor Total Acordado:', true), 'value'=> $formatar->real($total), 'style'=> 'float:left;')); ?>
		<?php echo $form->button('OK'); ?>
	</li>
</ol>
