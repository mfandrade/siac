<?php
// unidade, proprietario, tipo, vencimento, valor, juros, multa, total

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
define('TAM_FONTE_RESUMO'	, 7);
define('COL_UNIDADE'		, 13);
define('COL_PROPRIETARIO'	, 75.5); //define('COL_TIPO'			, 39.5);
define('COL_VENCIMENTO'		, 19);
define('COL_ATRASO'			, 10);
define('COL_VALOR'			, 14);
define('COL_MULTA'			, 15); // 40
define('COL_JUROS'			, 15);
define('COL_DESCONTOS'		, 10);
define('COL_TOTAL'			, 17.5);
define('COR1', 255);
define('COR2', 239);

$tcpdf->AddPage();	$csv	= ';RELATÓRIO INADIMPLÊNCIA NO PERÍODO'."\n";

$tcpdf->SetFont(FONTE_TITULO_TABELA, 'B', TAM_FONTE_TITULO_TABELA);
$tcpdf->Cell(COL_UNIDADE		, H_TITULO_TABELA, '^'.__('UNIDADE', true)	, 1, 0, 'C');	$csv.= __('UNIDADE', true).';';
$tcpdf->Cell(COL_PROPRIETARIO	, H_TITULO_TABELA, __('PROPRIETÁRIO', true)	, 1, 0, 'C');	$csv.= __('PROPRIETÁRIO', true).';';
$tcpdf->Cell(COL_VENCIMENTO		, H_TITULO_TABELA, __('VENCIMENTO', true)	, 1, 0, 'C');	$csv.= __('VENCIMENTO', true).';';
$tcpdf->Cell(COL_ATRASO			, H_TITULO_TABELA, __('ATRASO', true)		, 1, 0, 'C');	$csv.= __('ATRASO', true).';';
$tcpdf->Cell(COL_VALOR			, H_TITULO_TABELA, __('VALOR', true)	, 1, 0, 'C');		$csv.= __('VALOR', true).';';
$tcpdf->Cell(COL_MULTA			, H_TITULO_TABELA, __('MULTA', true)	, 1, 0, 'C');		$csv.= __('MULTA', true).';';
$tcpdf->Cell(COL_JUROS			, H_TITULO_TABELA, __('JUROS', true)	, 1, 0, 'C');		$csv.= __('JUROS', true).';';
$tcpdf->Cell(COL_DESCONTOS		, H_TITULO_TABELA, __('DESC.', true)	, 1, 0, 'C');		$csv.= __('DESC.', true).';';
$tcpdf->Cell(COL_TOTAL			, H_TITULO_TABELA, __('TOTAL', true)	, 1, 0, 'C');		$csv.= __('TOTAL', true).';';
$tcpdf->Ln();		$csv.= "\n";

$i	= 0;
$tcpdf->SetFont(FONTE_CELULA_TABELA, '', TAM_FONTE_CELULA_TABELA);

$totais['valor_documento']	= 0.00;
$totais['valor_multa']		= 0.00;
$totais['valor_juros']		= 0.00;
$totais['valor_desconto']	= 0.00;
$totais['valor_total']		= 0.00;
$porQuadra[]		= array();

foreach( $resultados as $resultado ):

	$cor	= ($i++ % 2)? COR1: COR2;
	$tcpdf->SetFillColor($cor);

	$unidade		= sprintf('%sL%02s', $resultado['Unidade']['Quadra']['abbr'], $resultado['Unidade']['lote']);
	$proprietario	= '';
	if( array_key_exists('nome', $resultado['Unidade']['Proprietario']) ) {

		$proprietario	= $resultado['Unidade']['Proprietario']['nome'];
	}
	$vencimento		= $formatar->data($resultado['Lancamento']['dta_vencimento']);
	$valor			= $formatar->real($resultado['Lancamento']['valor_documento']);
	$atraso			= $resultado['Pagamento']['atraso'].'d';
	$multa			= $formatar->real($resultado['Pagamento']['valor_multa']);
	$juros			= $formatar->real($resultado['Pagamento']['valor_juros']);
	$descontos		= $formatar->real($resultado['Pagamento']['valor_desconto']);
	$soma			= $resultado['Lancamento']['valor_documento']+$resultado['Pagamento']['valor_multa']+$resultado['Pagamento']['valor_juros']-$resultado['Pagamento']['valor_desconto'];
	$total			= $formatar->real($soma);

	$totais['valor_documento']	+= $resultado['Lancamento']['valor_documento'];
	$totais['valor_multa']		+= $resultado['Pagamento']['valor_multa'];
	$totais['valor_juros']		+= $resultado['Pagamento']['valor_juros'];
	$totais['valor_desconto']	+= $resultado['Pagamento']['valor_desconto'];
	$totais['valor_total']		+= $soma;

	$tcpdf->Cell(COL_UNIDADE		, H_CELULA_TABELA, $unidade		, 1, 0, 'C', 1);	$csv.= $unidade.';';
	$tcpdf->Cell(COL_PROPRIETARIO	, H_CELULA_TABELA, $proprietario, 1, 0, 'L', 1);	$csv.= $proprietario.';';
	$tcpdf->Cell(COL_VENCIMENTO		, H_CELULA_TABELA, $vencimento	, 1, 0, 'C', 1);	$csv.= $vencimento.';';
	$tcpdf->Cell(COL_ATRASO			, H_CELULA_TABELA, $atraso		, 1, 0, 'C', 1);	$csv.= $atraso.';';
	$tcpdf->Cell(COL_VALOR			, H_CELULA_TABELA, $valor		, 1, 0, 'R', 1);	$csv.= $valor.';';
	$tcpdf->Cell(COL_MULTA			, H_CELULA_TABELA, $multa		, 1, 0, 'R', 1);	$csv.= $multa.';';
	$tcpdf->Cell(COL_JUROS			, H_CELULA_TABELA, $juros		, 1, 0, 'R', 1);	$csv.= $juros.';';
	$tcpdf->Cell(COL_DESCONTOS		, H_CELULA_TABELA, $descontos	, 1, 0, 'R', 1);	$csv.= $descontos.';';
	$tcpdf->Cell(COL_TOTAL			, H_CELULA_TABELA, $total		, 1, 0, 'R', 1);	$csv.= $total.';';

	$tcpdf->Ln();		$csv.= "\n";

	$porQuadra[$resultado['Unidade']['Quadra']['abbr']]['valor_documento']	+= $resultado['Lancamento']['valor_documento'];
	$porQuadra[$resultado['Unidade']['Quadra']['abbr']]['valor_multa']	+= $resultado['Pagamento']['valor_multa'];
	$porQuadra[$resultado['Unidade']['Quadra']['abbr']]['valor_juros']	+= $resultado['Pagamento']['valor_juros'];
	$porQuadra[$resultado['Unidade']['Quadra']['abbr']]['valor_desconto']	+= $resultado['Pagamento']['valor_desconto'];
	$porQuadra[$resultado['Unidade']['Quadra']['abbr']]['valor_total']	+= $soma;

endforeach;

// detalhes por quadra
//$tcpdf->Ln();	$csv.= "\n";

$tcpdf->SetFillColor(255);
$tcpdf->SetFont(FONTE_RESUMO, '' , TAM_FONTE_RESUMO);

foreach( $porQuadra as $quadra => $valor ):

	if( !$quadra ) continue;

	$tcpdf->Ln();	$csv.= "\n";

	$tcpdf->Cell(COL_UNIDADE+COL_PROPRIETARIO		, H_CELULA_TABELA, '', 0, 0, 'R', 1);												$csv.= ';';
	$tcpdf->Cell(COL_VENCIMENTO						, H_CELULA_TABELA, __('QUADRA '.$quadra.': ', true), 0, 0, 'L', 1);							$csv.= __('QUADRA '.$quadra.': ', true).';';
	$tcpdf->Cell(COL_ATRASO-(COL_TOTAL-COL_VALOR)	, H_CELULA_TABELA, '', 0, 0, 'L', 1);												$csv.= ';';
	$tcpdf->Cell(COL_TOTAL							, H_CELULA_TABELA, $formatar->real($porQuadra[$quadra]['valor_documento'])	, 1, 0, 'R', 1);	$csv.= $formatar->real($porQuadra[$quadra]['valor_documento']).';';
	$tcpdf->Cell(COL_MULTA							, H_CELULA_TABELA, $formatar->real($porQuadra[$quadra]['valor_multa'])		, 1, 0, 'R', 1);	$csv.= $formatar->real($porQuadra[$quadra]['valor_multa']).';';
	$tcpdf->Cell(COL_JUROS							, H_CELULA_TABELA, $formatar->real($porQuadra[$quadra]['valor_juros'])		, 1, 0, 'R', 1);	$csv.= $formatar->real($porQuadra[$quadra]['valor_juros']).';';
	$tcpdf->Cell(COL_DESCONTOS						, H_CELULA_TABELA, $formatar->real($porQuadra[$quadra]['valor_desconto'])	, 1, 0, 'R', 1);	$csv.= $formatar->real($porQuadra[$quadra]['valor_desconto']).';';
	$tcpdf->Cell(COL_TOTAL							, H_CELULA_TABELA, $formatar->real($porQuadra[$quadra]['valor_total'])		, 1, 0, 'R', 1);	$csv.= $formatar->real($porQuadra[$quadra]['valor_total']).';';
endforeach;

$tcpdf->Ln();	$csv.= "\n";
$tcpdf->SetFont(FONTE_RESUMO, 'B' , TAM_FONTE_RESUMO);

$tcpdf->Cell(COL_UNIDADE+COL_PROPRIETARIO		, H_CELULA_TABELA, '', 0, 0, 'R', 1);												$csv.= ';';
$tcpdf->Cell(COL_VENCIMENTO						, H_CELULA_TABELA, __('TOTAL (R$):', true), 0, 0, 'L', 1);							$csv.= __('TOTAL (R$):', true).';';
$tcpdf->Cell(COL_ATRASO-(COL_TOTAL-COL_VALOR)	, H_CELULA_TABELA, '', 0, 0, 'L', 1);												$csv.= ';';
$tcpdf->Cell(COL_TOTAL							, H_CELULA_TABELA, $formatar->real($totais['valor_documento'])	, 1, 0, 'R', 1);	$csv.= $formatar->real($totais['valor_documento']).';';
$tcpdf->Cell(COL_MULTA							, H_CELULA_TABELA, $formatar->real($totais['valor_multa'])		, 1, 0, 'R', 1);	$csv.= $formatar->real($totais['valor_multa']).';';
$tcpdf->Cell(COL_JUROS							, H_CELULA_TABELA, $formatar->real($totais['valor_juros'])		, 1, 0, 'R', 1);	$csv.= $formatar->real($totais['valor_juros']).';';
$tcpdf->Cell(COL_DESCONTOS						, H_CELULA_TABELA, $formatar->real($totais['valor_desconto'])	, 1, 0, 'R', 1);	$csv.= $formatar->real($totais['valor_desconto']).';';
$tcpdf->Cell(COL_TOTAL							, H_CELULA_TABELA, $formatar->real($totais['valor_total'])		, 1, 0, 'R', 1);	$csv.= $formatar->real($totais['valor_total']).';';

$tcpdf->Output(Configure::read('boletos_arquivo_diretoriogravacao').DS.$filename, 'F');

//file_put_contents(Configure::read('boletos_arquivo_diretoriogravacao').DS.str_replace('.pdf', '.csv', $filename), $csv);
header('Content-type: text/comma-separated-values');
header('Content-Disposition: attachment; filename="'.str_replace('.pdf', '.csv', $filename).'"');
echo $csv;

