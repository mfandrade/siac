<?php
define('DEFAULT_CELL_H', 7);
define('H_TITULO_TABELA', DEFAULT_CELL_H/2);
define('H_CELULA_TABELA', H_TITULO_TABELA);
define('FONTE_HEADER', 'helvetica');
define('FONTE_TITULO_TABELA', 'courier');
define('FONTE_CELULA_TABELA', FONTE_TITULO_TABELA);
define('FONTE_RESUMO', FONTE_TITULO_TABELA);
define('TAM_FONTE_HEADER', 12);
define('TAM_FONTE_TITULO_TABELA', 7.5);
define('TAM_FONTE_CELULA_TABELA', 8.5);
define('TAM_FONTE_RESUMO', 9);
define('COL_UNIDADE'		, 13);
define('COL_PROPRIETARIO'	, 62.5+2.5);
define('COL_MESANO'			, 39.5-15+2.5+.5+.5); // reduzindo...
define('COL_DATA'			, 18+.5+.5+1);
define('COL_DETCHEQUE'		, 40+5-.5-2.5);
define('COL_VALOR'			, 16+2.5-.5-1);
define('COL_PGTO'			, 5);
define('COR1', 255);
define('COR2', 239);

$tcpdf->AddPage();	$csv	= ';RELATÓRIO POSIÇÃO FINANCEIRA'."\n";

$tcpdf->SetFont(FONTE_TITULO_TABELA, 'B', TAM_FONTE_TITULO_TABELA);
$tcpdf->Cell(COL_UNIDADE		, H_TITULO_TABELA, '^'.__('UNIDADE', true)		, 1, 0, 'C');	$csv.= __('UNIDADE', true).';';
$tcpdf->Cell(COL_PROPRIETARIO	, H_TITULO_TABELA, __('PROPRIETÁRIO', true)		, 1, 0, 'C');	$csv.= __('PROPRIETÁRIO', true).';';
$tcpdf->Cell(COL_MESANO			, H_TITULO_TABELA, __('MÊS/ANO', true)			, 1, 0, 'C');	$csv.= __('MÊS/ANO', true).';';
$tcpdf->Cell(COL_DATA			, H_TITULO_TABELA, __('DATA', true)				, 1, 0, 'C');	$csv.= __('DATA', true).';';
$tcpdf->Cell(COL_DETCHEQUE		, H_TITULO_TABELA, __('INFO.CHEQUE', true)		, 1, 0, 'C');	$csv.= __('INFO.CHEQUE', true).';';
$tcpdf->Cell(COL_VALOR			, H_TITULO_TABELA, __('VALOR PAGO', true)		, 1, 0, 'C');	$csv.= __('VALOR PAGO', true).';';
$tcpdf->Cell(COL_PGTO			, H_TITULO_TABELA, __('ADM', true)				, 1, 0, 'C');	$csv.= __('ADM', true).';';

$tcpdf->Ln();		$csv	.= "\n";

$i	= 0;
$tcpdf->SetFont(FONTE_CELULA_TABELA, '', TAM_FONTE_CELULA_TABELA);

$total['adm']	= 0.00;
$total['bol']	= 0.00;
foreach( $resultados as $resultado ):

	$cor	= ($i++ % 2)? COR1: COR2;
	$tcpdf->SetFillColor($cor);

	$unidade		= sprintf('%sL%02s', $resultado['Lancamento']['Unidade']['Quadra']['abbr'], $resultado['Lancamento']['Unidade']['lote']);
	$proprietario	= '';
	if( array_key_exists('nome', $resultado['Lancamento']['Unidade']['Proprietario']) ) {

		$proprietario	= $text->truncate($resultado['Lancamento']['Unidade']['Proprietario']['nome'], 35);
	}
	switch($resultado['Lancamento']['tipo_lancamento_id']) { // deixei de usar a descrição para economizar espaço
		case TIPO_LANCAMENTO_TAXACONDOMINIAL: $tipo	= 'T.COND.';
			break;
		case TIPO_LANCAMENTO_TAXAEXTRA		: $tipo	= 'T.EXTRA';
			break;
		case TIPO_LANCAMENTO_MULTAINFRACAO	: $tipo	= 'MULTA';
			break;
		case TIPO_LANCAMENTO_ACORDO			: $tipo	= 'ACORDO';
			break;
		default:	$tipo = '';
	}
	$mes_ano		= $resultado['Lancamento']['mes_ano'].' '.$tipo;
	$dta_pagamento	= $formatar->data($resultado['Pagamento']['dta_pagamento']);
	$det_cheque		= $resultado['Pagamento']['cheque_info'];
	$valor_pago		= $formatar->real($resultado['Pagamento']['valor_pago']);

	$tcpdf->Cell(COL_UNIDADE		, H_CELULA_TABELA, $unidade			, 'LBR', 0, 'L', 1);	$csv.= $unidade.';';
	$tcpdf->Cell(COL_PROPRIETARIO	, H_CELULA_TABELA, $proprietario	, 'LBR', 0, 'L', 1);	$csv.= $proprietario.';';
	$tcpdf->Cell(COL_MESANO			, H_CELULA_TABELA, $mes_ano			, 'LBR', 0, 'L', 1);	$csv.= $mes_ano.';';
	$tcpdf->Cell(COL_DATA			, H_CELULA_TABELA, $dta_pagamento	, 'LBR', 0, 'C', 1);	$csv.= $dta_pagamento.';';
	$tcpdf->Cell(COL_DETCHEQUE		, H_CELULA_TABELA, $det_cheque		, 'LBR', 0, 'L', 1);	$csv.= $det_cheque.';';
	$tcpdf->Cell(COL_VALOR			, H_CELULA_TABELA, $valor_pago		, 'LBR', 0, 'R', 1);	$csv.= $valor_pago.';';

	if( is_null($resultado['Pagamento']['arquivo_retorno_id']) ) {

		$pagoEm	= 'adm';
		$tcpdf->Cell(COL_PGTO			, H_CELULA_TABELA, 'X',  'BR', 0, 'R');		$csv.= 'X;';
	} else {

		$pagoEm	= 'bol';
		$tcpdf->Cell(COL_PGTO			, H_CELULA_TABELA, ' ',  'BR', 0, 'R');		$csv.= ' ;';
	}
	$total[$pagoEm]	+= $resultado['Pagamento']['valor_pago'];
	$tcpdf->Ln();	$csv.= "\n";

endforeach;

$tcpdf->Ln();	$csv.= "\n";

$tcpdf->SetFillColor(255);
$tcpdf->SetFont(FONTE_RESUMO, '' , TAM_FONTE_RESUMO);

$tcpdf->Ln();	$csv.= "\n";
$tcpdf->Cell(COL_UNIDADE+COL_PROPRIETARIO+COL_MESANO+COL_DATA+COL_DETCHEQUE	, H_CELULA_TABELA, __('Pago na administração:', true)	, '', 0, 'R', 1);	$csv.= __('Pago na administração:', true).';';
$tcpdf->Cell(COL_VALOR+COL_PGTO												, H_CELULA_TABELA, $formatar->real($total['adm'], true)	, '' , 0, 'R', 1);	$csv.= $formatar->real($total['adm'], true).';';
$tcpdf->Ln();	$csv.= "\n";
$tcpdf->Cell(COL_UNIDADE+COL_PROPRIETARIO+COL_MESANO+COL_DATA+COL_DETCHEQUE	, H_CELULA_TABELA, __('Pago com bancário:', true)		, '', 0, 'R', 1);	$csv.= __('Pago com bancário:', true).';';
$tcpdf->Cell(COL_VALOR+COL_PGTO												, H_CELULA_TABELA, $formatar->real($total['bol'], true)	, '' , 0, 'R', 1);	$csv.= $formatar->real($total['bol'], true);
$tcpdf->Ln();	$csv.= "\n";
$tcpdf->Cell(COL_UNIDADE+COL_PROPRIETARIO+COL_MESANO+COL_DATA+COL_DETCHEQUE	, H_CELULA_TABELA, __('TOTAL:', true)					, '', 0, 'R', 1);	$csv.= __('TOTAL:', true).';';
$tcpdf->Cell(COL_VALOR+COL_PGTO												, H_CELULA_TABELA, $formatar->real($total['adm']+$total['bol'], true)	, 'T', 0, 'R', 1);	$csv.= $formatar->real($total['adm']+$total['bol'], true).';';

$tcpdf->Output(Configure::read('boletos_arquivo_diretoriogravacao').DS.$filename, 'F');

//file_put_contents(Configure::read('boletos_arquivo_diretoriogravacao').DS.str_replace('.pdf', '.csv', $filename), $csv);
header('Content-type: text/comma-separated-values');
header('Content-Disposition: attachment; filename="'.str_replace('.pdf', '.csv', $filename).'"');
echo $csv;

