<?php
class NavigatorHelper extends AppHelper {

	var $helpers	= array('Paginator');
/**
 * Formata um valor de ponto flutuante vindo do banco para Real.
 * @param  float	$valor		o valor vindo do banco.
 * @param  boolean	$simbolo	se deve pôr o símbolo de Real na frente.
 * @return string o valor como string formatada em Real.
 */
	function show() {
		$s	= '<div class="navigator">';
		$s	.= $this->Paginator->prev('« Anterior', null, null);
		$s	.= '&nbsp;';
		$s	.= $this->Paginator->numbers();
		$s	.= '&nbsp;';
		$s	.= $this->Paginator->next('Próximo »', null, null);
		$s	.= '&nbsp;|&nbsp;';
		$s	.= $this->Paginator->counter(array('format'=> Configure::read('siac_listagem_status_navigator')));
		$s	.= '</div>'."\n";
		return $s;
	}
}
?>
