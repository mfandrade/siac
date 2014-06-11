<?php
class AppError extends ErrorHandler {
	var $name		= 'App';

/**
 * Lançado pelo método que processa arquivos de retorno, quando alguma
 * seção do arquivo esteja aparentemente corrompida.
 * @param  array $params  parâmetros contendo 'msg' (mensagem em si) e 'secao'
 *                        (parte do arquivo em que ocorreu o problema)
 * @return void
 */
	function erroArquivoRetorno($params) {
		$this->controller->set('msg', $params['msg']);
		$this->controller->set('secao', $params['secao']);
		$this->_outputMessage('erro_arquivoretorno');
	}
}
