<?php
class RelatoriosController extends AppController {
	var $name		= 'Relatorios';
	var $uses		= array('Pagamento');
	var $helpers	= array('Javascript', 'Ajax', 'Formatar', 'Text');

/**
 * Action para o relatório "Parabéns Adimplentes" em PDF.
 */
	function parabens() {

		$mesesAnos		= $this->Pagamento->Lancamento->obterMesesAnosLancados();
		$tiposLancamento= $this->Pagamento->Lancamento->TipoLancamento->find('list', array('fields'=> array('cod', 'descricao'), 'order'=> 'id', 'conditions'=> array('TipoLancamento.cod <>'=> TIPO_LANCAMENTO_QUALQUER)));

		$this->set(compact('mesesAnos', 'tiposLancamento'));


		if( !empty($this->data) ) {

			$mes_ano	= $this->data['mes_ano'];
			$tipo		= $this->data['tipo_lancamento_id'];

			if( !isset($mes_ano) || !isset($tipo) ) {

				$this->redirect(array('action'=> 'parabens'));
			}
			// obtém todas as quadras
			$this->Pagamento->Lancamento->Unidade->Quadra->contain();
			$quadras	= $this->Pagamento->Lancamento->Unidade->Quadra->find('list', array('conditions'=> array('Quadra.id <>'=> 20)));

			foreach( $quadras as $qid=> $quadra ) {

				$res[$qid]= array();

				// obtém as ruas da quadra
				$this->Pagamento->Lancamento->Unidade->contain('Rua');
				$ruas=	$this->Pagamento->Lancamento->Unidade->find('all', array(
					  'fields'=> array(
						  'DISTINCT Unidade.quadra_id'
						, 'Rua.descricao'
					)
					, 'conditions'=> array('Unidade.quadra_id'=> $qid)
				));
				$ruas= Set::combine($ruas, '{n}.Rua.id', '{n}.Rua.descricao');

				foreach( $ruas as $rid=> $rua ) {
					$res[$qid][$rua]= array();

					// obtém as unidades da quadra/rua que pagaram em dia
					$this->Pagamento->Lancamento->contain(array(
						  'Unidade'
						, 'Unidade.quadra_id'
						, 'Unidade.rua_id'
						, 'Unidade.lote'
						, 'Unidade.Rua.descricao'
						, 'Pagamento.dta_pagamento'
						, 'Pagamento.valor_pago'
						, 'Pagamento.arquivo_retorno_id'
//						, 'order'=> array('Pagamento.arquivo_retorno_id ASC', 'Unidade.quadra_id ASC', 'Unidade.lote ASC')
					));
					$unidades	= $this->Pagamento->Lancamento->find('all', array(
						  'conditions'=> array(
							  'Unidade.quadra_id'=> $qid
							, 'Unidade.rua_id'=> $rid
							, 'not'=> array('Pagamento.dta_pagamento'=> null)
							, 'Pagamento.dta_pagamento <='=> 'Lancamento.dta_vencimento'
							, 'Lancamento.mes_ano'=> $mes_ano
							, 'Lancamento.tipo_lancamento_id'=> $tipo
						)
					));
					$lotes	= Set::classicExtract($unidades, '{n}.Unidade.lote');
					$res[$qid][$rua]	= $lotes;
				}
			}
			// soma dos valores em aberto por quadra
			$this->Pagamento->Lancamento->contain('Unidade', 'Pagamento');
			$emAberto	= $this->Pagamento->Lancamento->find('all', array(
				  'fields'=> array('Unidade.quadra_id', 'SUM(Lancamento.valor_documento) as total_naopago')
				, 'conditions'=> array(
					  'Lancamento.mes_ano'=> $mes_ano
					, 'Lancamento.tipo_lancamento_id'=> $tipo
					, 'or'=> array(
						  'Pagamento.dta_pagamento IS NULL'
						, 'Pagamento.dta_pagamento >'=> 'Lancamento.dta_vencimento'
					)
				)
				, 'group'=> array('Unidade.quadra_id')
			));
			$emAberto		= Set::combine($emAberto, '{n}.Unidade.quadra_id', '{n}.0.total_naopago');
			$mesAno			= $mes_ano;

			list($mes, $ano)= explode('/', $mesAno);
			// se o mês for o mesmo em que estamos, o dia é ontem(?); senão, é o último dia do mês
			$dia			= ($mes == date('m'))? date('d')-1: date('t', mktime(0, 0, 0, $mes, 1, $ano));
			$data			= sprintf('%02d/%s', $dia, $mesAno);

			$tipoLancamento	= $this->Pagamento->Lancamento->TipoLancamento->find('first', array('conditions'=> array('TipoLancamento.cod'=> $tipo)));
			$tipoLancamento	= $tipoLancamento['TipoLancamento']['descricao'];
			$filename		= 'relatorio-parabens-condominos.pdf';
			$this->set(compact('res', 'emAberto', 'mesAno', 'tipoLancamento' ,'data', 'filename'));


			$tcpdf			= $this->__instanciarGeradorPDF('L');
			$this->set(compact('tcpdf'));


			$this->autoRender	= false;
			$this->render(null, 'pdf', 'relat_parabens');
		}
	}

/**
 *
 */
	function posicao() {

		$unidades		= $this->Pagamento->Lancamento->Unidade->findList();
		$periodo		= $this->__determinarPeriodo();
		$tiposLancamento= $this->Pagamento->Lancamento->TipoLancamento->find('list');
		$viasPagamento	= array(__('POR QUALQUER MEIO', true), __('DIRETO NA ADMINISTRAÇÃO', true), __('COM BOLETO BANCÁRIO', true));

		$this->set(compact('unidades', 'periodo', 'tiposLancamento', 'viasPagamento'));

		if( !empty($this->data) ) {

			$filename	= 'relatorio-posicao-financeira.pdf';
			$tit		= __('POSIÇÃO FINANCEIRA', true);
			$sub		= sprintf(__('(todas as unidades, entre %s e %s)',true), $this->data['data1'], $this->data['data2']);

			$condicoes	= array('Pagamento.dta_pagamento BETWEEN ? AND ?'=> array($this->data['data1'], $this->data['data2']));
			if( $this->data['unidade_id'] ) {

				$condicoes	+= array('Lancamento.unidade_id' => $this->data['unidade_id']);

				$this->Pagamento->Lancamento->Unidade->contain('Quadra');
				$this->Pagamento->Lancamento->Unidade->id	= $this->data['unidade_id'];
				$unidade	= $this->Pagamento->Lancamento->Unidade->read();
				$quadraLote	= sprintf('%sL%02s', $unidade['Quadra']['abbr'], $unidade['Unidade']['lote']);
				$sub		= sprintf(__('(unidade %s, entre %s e %s)', true), $quadraLote, $this->data['data1'], $this->data['data2']);
				$filename	= str_replace('.pdf', '-'.strtolower($quadraLote).'.pdf', $filename);
			}
			if( array_key_exists('tipo_pagamento_id', $this->data) && $this->data['tipo_pagamento_id'] != TIPO_LANCAMENTO_QUALQUER ) {

				$condicoes += array('Lancamento.tipo_lancamento_id' => $this->data['tipo_pagamento_id']);
				switch( $this->data['tipo_pagamento_id'] ) {
					case TIPO_LANCAMENTO_TAXACONDOMINIAL:
						$sufixoTipo = '-tcond.pdf';
						break;
					case TIPO_LANCAMENTO_TAXAEXTRA:
						$sufixoTipo = '-textra.pdf';
						break;
					case TIPO_LANCAMENTO_MULTAINFRACAO:
						$sufixoTipo = '-multa.pdf';
						break;
					case TIPO_LANCAMENTO_ACORDO:
						$sufixoTipo = '-acordo.pdf';
						break;
				}
				$filename	= str_replace('.pdf', $sufixoTipo, $filename);
			}

			switch( $this->data['via_pagamento'] ) {
				case 1: // direto na adm. // ahhhh... driver...
					$condicoes += array('Pagamento.arquivo_retorno_id IS NULL');
					$filename	= str_replace('.pdf', '-adm.pdf', $filename);
					break;
				case 2: // via boleto
					$condicoes += array('NOT' => array('Pagamento.arquivo_retorno_id IS NULL'));
					$filename	= str_replace('.pdf', '-bol.pdf', $filename);
					break;
				case 0:
				default:	// tanto faz
			}

			$this->Pagamento->contain(array(
				  'Lancamento'=> array(
					  'Unidade'=> array(
						  'Proprietario.nome'
						, 'Quadra.abbr'
						, 'fields'=> array('Unidade.quadra_id', 'Unidade.lote')
					)
					, 'TipoLancamento.descricao'
					, 'fields'=> array('Lancamento.mes_ano')
				//	, 'order'=> array('Lancamento'=> array('Unidade.quadra_id', 'Unidade.lote'))
				)
			));
			$resultados	= $this->Pagamento->find('all', array(
				  'conditions'=> $condicoes
			));

			$resultados	= Set::sort($resultados, '{n}.Lancamento.Unidade.quadra_id, Lancamento.Unidade.lote', 'ASC');

			if( empty($resultados) ) {

				$this->Session->setFlash(sprintf(__('Nenhum pagamento entre %s e %s.', true), $this->data['data1'], $this->data['data2']), 'flash_warning');
				$this->redirect(array('action'=> 'posicao'));
			}
			$tcpdf		= $this->__instanciarGeradorPDFRelatorio($tit, $sub);

			$this->set(compact('resultados', 'tcpdf', 'filename'));


			$this->autoRender	= false;
			$this->render(null, 'pdf', 'relat_posicao');
		}
	}

	function __determinarPeriodo() {

		if( date('d') <= 15 ) {

			$dia1	= '01';
			$mes1	= date('m')-1;
			$ano1	= date('Y');
			if( $mes1 == 0 ) {

				$mes1	= 12;
				$ano1	= date('Y')-1;
			}

			$dia2	= date('t');
			$mes2	= date('m')-1;
			$ano2	= date('Y');
			if( $mes2 == 0 ) {

				$mes2	= 12;
				$ano2	= date('Y')-1;
			}
		} else {

			$dia1	= '01';
			$dia2	= date('d');
			$mes1	= $mes2	= date('m');
			$ano1	= $ano2	= date('Y');
		}
		$data1	= sprintf('%02d/%02d/%04d', $dia1, $mes1, $ano1);
		$data2	= sprintf('%02d/%02d/%04d', $dia2, $mes2, $ano2);

		return array('data1'=> $data1, 'data2'=> $data2);
	}

/**
 *
 */
	function inadimplencia() {

		$mesesAnos		= $this->Pagamento->Lancamento->obterMesesAnosLancados();
		$unidades		= $this->Pagamento->Lancamento->Unidade->findList();
		$tiposLancamento= $this->Pagamento->Lancamento->TipoLancamento->find('list');
		$hoje			= date('d/m/Y');

		$this->set(compact('mesesAnos', 'unidades', 'tiposLancamento', 'hoje'));


		if( !empty($this->data) ) {

			// me diz todos os lancamentos, em que mesAno estah entre
			// os informados (além da unidade e tipo) e para os quais
			// não há pagamento registrado até hoje
			$intervalo	= $this->__obterIntervaloMesesAnos($this->data['mes_ano1'], $this->data['mes_ano2']);
			$resultados	= $this->Pagamento->Lancamento->obterLancamentosAberto(
				  $this->data['unidade_id']
				, $intervalo
				, $this->data['tipo_lancamento_id']
			);
			$filename	= 'relatorio-inadimplencia-no-periodo.pdf';
			$tit		= __('INADIMPLÊNCIA', true);
			$sub		= sprintf(__('(todas as unidades, %s a %s, até o dia %s)',true), $this->data['mes_ano1'], $this->data['mes_ano2'], $this->data['data']);
			if( !empty($this->data['unidade_id']) ) {

				$this->Pagamento->Lancamento->Unidade->contain('Quadra');
				$this->Pagamento->Lancamento->Unidade->id	= $this->data['unidade_id'];
				$unidade	= $this->Pagamento->Lancamento->Unidade->read();
				$quadraLote	= sprintf('%sL%02s', $unidade['Quadra']['abbr'], $unidade['Unidade']['lote']);
				$sub		= sprintf(__('(unidade %s, %s a %s, pagos até %s)', true), $quadraLote, $this->data['mes_ano1'], $this->data['mes_ano2'], $this->data['data']);
				$filename	= str_replace('.pdf', '-'.strtolower($quadraLote).'.pdf', $filename);
			}
			$tcpdf		= $this->__instanciarGeradorPDFRelatorio($tit, $sub);

			$this->set(compact('resultados', 'tcpdf', 'filename'));


			$this->autoRender	= false;
			$this->render(null, 'pdf', 'relat_inadimplencia');
		}
	}

	function __obterIntervaloMesesAnos($mesAnoA, $mesAnoB) {

		$mesesAnos = array($mesAnoA, $mesAnoB);

		$mesesAnos = preg_replace('#(\d{2})/(\d{4})#', '${2}-${1}', $mesesAnos);
		sort($mesesAnos);

		$primeiro	= $mesesAnos[0];
		$ultimo		= $mesesAnos[1];

		list($ano1, $mes1)	= explode('-', $primeiro);
		list($ano2, $mes2)	= explode('-', $ultimo);
		$tsi	= mktime(0, 0, 0, $mes1, 1, $ano1);
		$tsf	= mktime(0, 0, 0, $mes2, 1, $ano2);

		$mes	= $mes1;
		$ano	= $ano1;
		$ts		= $tsi;
		$res	= array();

		do {
			$res[]	= date('m/Y', $ts);
			$ts		= mktime(0, 0, 0, ++$mes, 1, $ano);

		} while( $ts <= $tsf );

		return $res;
	}

	function __instanciarGeradorPDFRelatorio($titulo, $subtitulo, $orientation= 'P', $unit= 'mm', $format= 'A4') {

		$mypdf = new TCPDFRel($orientation, $unit, $format, true, 'UTF-8', false);

		$mypdf->SetMargins(10, 17.5, 10);
		$mypdf->SetHeaderMargin(5);
		$mypdf->SetFooterMargin(0);

		$mypdf->setTitulo($titulo);
		$mypdf->setSubTitulo($subtitulo);

		return $mypdf;
	}

	function __instanciarGeradorPDF($orientation= 'P', $unit= 'mm', $format= 'A4') {

		$tcpdf = new MyTCPDF($orientation, $unit, $format, true, 'UTF-8', false);
		return $tcpdf;
	}
}

App::import('Vendor','tcpdf/tcpdf');
class MyTCPDF extends TCPDF {

	function Header() {}

	function Footer() {}
}


class TCPDFRel extends MyTCPDF {

	var $titulo	= '';
	var $subtitulo	= '';

	function setTitulo($titulo) {
		$this->titulo	= $titulo;
	}

	function setSubTitulo($subtitulo) {
		$this->subtitulo= $subtitulo;
	}

	function Header() {

		parent::Header();
		$this->Image(WWW_ROOT.DS.'img'.DS.'arvores.png', 10, 5, 93/6); // altura deve dar 10 (..). a imagem eh 93x60,
		$this->Cell(93/6+2.5);

		$this->SetTextColor(96);
		$this->SetFont('times', 'B', 22);
		$this->Cell(90, 10, $this->titulo, 0, 0, 'L');

		$this->SetFont('helvetica', 'B', 10);
		$this->Cell(0, 10, $this->subtitulo, 0, 0, 'R');
	}

	function Footer() {

		parent::Footer();
		//$this->SetY(-15);
		$this->SetFont('helvetica', '', 10);
		$this->Cell(115, 5, sprintf(__('- Página %s de %s -', true), $this->getAliasNumPage(), $this->getAliasNbPages()), 0, 0, 'R');

		$this->SetFont('helvetica', 'I', 8);
		$this->Cell(75, 5, sprintf(__('Impresso em %s', true), date('d/m/Y H:i:s')), 0, 0, 'R');
	}
}
