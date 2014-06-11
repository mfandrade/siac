<?php
define('DEFAULT_CELL_H', 7);

define('H_QUADRA'			, 12);
define('TAM_FONTE_QUADRA'	, 9);
define('TAM_FONTE_RUA'		, 8);
define('TAM_FONTE_LOTES'	, 9);
define('H_INTERQUADRAS'		, H_QUADRA);
define('SUMARIO_AXIS'		, 116.125);
define('TAM_FONTE_SUMARIO'	, 7);

$sumario	= __('(em aberto: R$%s)', true);


	//$arquivo	= basename(__FILE__);
	//$arquivo	= substr($arquivo, 0, strpos($arquivo, '.')).'.pdf';

	$tcpdf->SetFont('helvetica', '', 12);
	$tcpdf->AddPage();

//	$tcpdf->Image('/tmp/logo-b.png', 10, 10, 40);
//	$tcpdf->Cell(40, 2*DEFAULT_CELL_H, '', 0, 0);

	$tcpdf->SetFont('times', 'B', 26);
	$tcpdf->SetTextColor(128); // cinza
	$tcpdf->Cell(125, 2*DEFAULT_CELL_H, __('PARABÉNS CONDÔMINOS!', true), 'R', 0, 'C');
	$tcpdf->SetTextColor(64); // preto+cinza

	$tcpdf->SetFont('helvetica', 'B', 10);
	$tcpdf->MultiCell(115, 2*DEFAULT_CELL_H, __("Parabéns a todos os condôminos (proprietários e moradores) que efetuaram o pagamento dentro do mês e encontram-se em dia com esta mensalidade.   SEU INVESTIMENTO AGRADECE!", true), 0, 'C', 0, 0, 135, 10);

	$tcpdf->SetFont('helvetica', '', 8);
	$tcpdf->MultiCell(35, 0.5*DEFAULT_CELL_H, $tipoLancamento, 'TLR', 'C', 0, 0, 250, 10);
	$tcpdf->SetFont('helvetica', 'B', 16);

	list($mes, $ano)	= explode('/', $mesAno);
	$mesAno	= strtoupper($formatar->mes($mes, true)).'/'.$ano;
	$tcpdf->MultiCell(35, 1.5*DEFAULT_CELL_H, $mesAno, 'BLR', 'C', 0, 0, 250, 10+0.5*DEFAULT_CELL_H);

	$i= 0;
	$alt= 3.75 * DEFAULT_CELL_H;
	foreach( $res as $quadra=> $ruas_etc ): // LOOP ESQUERDA

		$esq	= (++$i % 2);
		if( !$esq ) continue;

		if( sizeof($ruas_etc) == 2 ):

			// quadra
			$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_QUADRA);
			$tcpdf->MultiCell(15, H_QUADRA, sprintf('Q%02d', $quadra), 1, 'C', 0, 0, 10, $alt);

			// rua "de cima"
			$rua	= key($ruas_etc);
			$lotes	= $ruas_etc[$rua];

			$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_RUA);
			$tcpdf->MultiCell(20, H_QUADRA/2, $rua, 1, 'L', 0, 0, 10+15, $alt);

			$tcpdf->SetFont('helvetica', '', TAM_FONTE_LOTES);
			$tcpdf->MultiCell(100, H_QUADRA/2, implode(', ', $lotes), 1, 'L', 0, 0, 10+15+20, $alt);

			// rua "de baixo"
			next($ruas_etc);
			$alt	+= H_QUADRA/2;
			$rua	= key($ruas_etc);
			$lotes	= $ruas_etc[$rua];

			$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_RUA);
			$tcpdf->MultiCell(20, H_QUADRA/2, $rua, 1, 'L', 0, 0, 10+15, $alt);

			$tcpdf->SetFont('helvetica', '', TAM_FONTE_LOTES);
			$tcpdf->MultiCell(100, H_QUADRA/2, implode(', ', $lotes), 1, 'L', 0, 0, 10+15+20, $alt);

			// sumário quadra
			$tcpdf->SetFont('helvetica', '', TAM_FONTE_SUMARIO);
			$tcpdf->SetTextColor(0);

			$tcpdf->Text(SUMARIO_AXIS, $alt + H_QUADRA/2 + 2.5, sprintf($sumario, number_format($emAberto[$quadra], 2, ',', '.')));
			$tcpdf->SetTextColor(64); // preto+cinza

		endif;
		$alt	+= H_INTERQUADRAS; // entre blocos de quadra
	endforeach;


	$i= 0;
	$alt= 3.75 * DEFAULT_CELL_H;
	foreach( $res as $quadra=> $ruas_etc ): // LOOP DIREITA

		$esq	= (++$i % 2);
		if( $esq ) continue;

		$x	= 15+25+95+5;
		if( sizeof($ruas_etc) == 2 ):

			// quadra
			$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_QUADRA);
			$tcpdf->MultiCell(15, H_QUADRA, sprintf('Q%02d', $quadra), 1, 'C', 0, 0, $x+10, $alt);


			// rua "de cima"
			$rua	= key($ruas_etc);
			$lotes	= $ruas_etc[$rua];

			$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_RUA);
			$tcpdf->MultiCell(20, H_QUADRA/2, $rua, 1, 'L', 0, 0, $x+10+15, $alt);

			$tcpdf->SetFont('helvetica', '', TAM_FONTE_LOTES);
			$tcpdf->MultiCell(100, H_QUADRA/2, implode(', ', $lotes), 1, 'L', 0, 0, $x+10+15+20, $alt);

			// rua "de baixo"
			next($ruas_etc);
			$alt	+= H_QUADRA/2;
			$rua	= key($ruas_etc);
			$lotes	= $ruas_etc[$rua];

			$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_RUA);
			$tcpdf->MultiCell(20, H_QUADRA/2, $rua, 1, 'L', 0, 0, $x+10+15, $alt);

			$tcpdf->SetFont('helvetica', '', TAM_FONTE_LOTES);
			$tcpdf->MultiCell(100, H_QUADRA/2, implode(', ', $lotes), 1, 'L', 0, 0, $x+10+15+20, $alt);

			// sumário quadra
			$tcpdf->SetFont('helvetica', '', TAM_FONTE_SUMARIO);
			$tcpdf->SetTextColor(0);
			$tcpdf->Text(15+25+95+5+ SUMARIO_AXIS, $alt + H_QUADRA/2 + 2.5, sprintf($sumario, number_format($emAberto[$quadra], 2, ',', '.')));
			$tcpdf->SetTextColor(64); // preto+cinza

		endif;
		$alt	+= H_INTERQUADRAS;
	endforeach;


	// -----------------------------------------------------------------
	// quadra 17 e quadra 18 na próxima altura
	$alt	-= H_INTERQUADRAS;
	{
		// quadra 17
		$QUADRA	= 17;
		$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_QUADRA);
		$tcpdf->MultiCell(15, H_QUADRA/2, 'Q'.$QUADRA, 1, 'C', 0, 0, 10, $alt);

		// rua "de cima"
		$ruas_etc	= $res[$QUADRA];
		$rua	= key($ruas_etc);
		$lotes	= $ruas_etc[$rua];

		$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_RUA);
		$tcpdf->MultiCell(20, H_QUADRA/2, $rua, 1, 'L', 0, 0, 10+15, $alt);

		$tcpdf->SetFont('helvetica', '', TAM_FONTE_LOTES);
		$tcpdf->MultiCell(100, H_QUADRA/2, implode(', ', $lotes), 1, 'L', 0, 0, 10+15+20, $alt);

		// sumário quadra 17
		$tcpdf->SetFont('helvetica', '', TAM_FONTE_SUMARIO);
		$tcpdf->SetTextColor(0);
		$tcpdf->Text(SUMARIO_AXIS, $alt + H_QUADRA/2 + 2.5, sprintf($sumario, number_format($emAberto[$QUADRA], 2, ',', '.')));
		$tcpdf->SetTextColor(64); // preto+cinza
	}
	{
		$x	= 15+25+95+5;

		// quadra 18
		$QUADRA	= 18;
		$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_QUADRA);
		$tcpdf->MultiCell(15, H_QUADRA/2, 'Q'.$QUADRA, 1, 'C', 0, 0, $x+10, $alt);

		// rua "de cima"
		$ruas_etc	= $res[$QUADRA];
		$rua	= key($ruas_etc);
		$lotes	= $ruas_etc[$rua];

		$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_RUA);
		$tcpdf->MultiCell(20, H_QUADRA/2, $rua, 1, 'L', 0, 0, $x+10+15, $alt);

		$tcpdf->SetFont('helvetica', '', TAM_FONTE_LOTES);
		$tcpdf->MultiCell(100, H_QUADRA/2, implode(', ', $lotes), 1, 'L', 0, 0, $x+10+15+20, $alt);

		// sumário quadra 18
		$tcpdf->SetFont('helvetica', '', TAM_FONTE_SUMARIO);
		$tcpdf->SetTextColor(0);
		$tcpdf->Text(15+25+95+5+ SUMARIO_AXIS, $alt + H_QUADRA/2 + 2.5, sprintf($sumario, number_format($emAberto[$QUADRA], 2, ',', '.')));
		$tcpdf->SetTextColor(64); // preto+cinza
	}


	$alt	+= H_INTERQUADRAS;
	{
		// quadra "com"
		$QUADRA	= 99;
		$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_QUADRA);
		$tcpdf->MultiCell(15, H_QUADRA/2, 'COM', 1, 'C', 0, 0, 10, $alt);

		// rua "de cima"
		$ruas_etc	= $res[$QUADRA];
		$rua	= key($ruas_etc);
		$lotes	= $ruas_etc[$rua];

		$tcpdf->SetFont('helvetica', 'B', TAM_FONTE_RUA);
		$tcpdf->MultiCell(20, H_QUADRA/2, $rua, 1, 'L', 0, 0, 10+15, $alt);

		$tcpdf->SetFont('helvetica', '', TAM_FONTE_LOTES);
		$tcpdf->MultiCell(100, H_QUADRA/2, implode(', ', $lotes), 1, 'L', 0, 0, 10+15+20, $alt);

		// sumário quadra "com"
		$tcpdf->SetFont('helvetica', '', TAM_FONTE_SUMARIO);
		$tcpdf->SetTextColor(0);
		$tcpdf->Text(SUMARIO_AXIS, $alt + H_QUADRA/2 + 2.5, sprintf($sumario, number_format($emAberto[$quadra], 2, ',', '.')));
		$tcpdf->SetTextColor(64); // preto+cinza

	}

	$tcpdf->SetFont('helvetica', 'BI', TAM_FONTE_SUMARIO*1.25);
	$tcpdf->Text(15+25+95+5+ SUMARIO_AXIS*2/3, $alt + H_QUADRA/2 + 2.5, sprintf(__('(*) Pagamentos registrados até %s', true),$data));


	$tcpdf->Output($filename, 'D');
