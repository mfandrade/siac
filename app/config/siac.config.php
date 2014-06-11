<?php
// lancamentos
// ---------------------------------------------------------------------
$config['lancamentos_qtdmeses_listagem']						= 6;

$config['lancamentos_taxacondominial_valor']					= 350.00;
$config['lancamentos_taxacondominial_valor_descontopadrao']		= 70.00;
$config['lancamentos_taxacondominial_vencimento_dia']			= 15;
$config['lancamentos_taxacondominial_vencimento_antecedencia']	= 7;

$config['lancamentos_taxaextra_vencimento_dia']					= 15;

$config['lancamentos_multainfracao_vencimento_dia']				= 15;

$config['lancamentos_acordo_vencimento_dia']					= 15;


// pagamento
// ---------------------------------------------------------------------
$config['pagamentos_atraso_porcentagem_multa']		= 2;
$config['pagamentos_atraso_porcentagem_juros_ad']	= 0.033;

$config['pagamentos_atraso_autoabono']				= true;

$config['pagamentos_desconto_porcentagem_maxima']	= 50;


// arquivo_retorno
// ---------------------------------------------------------------------
//define('MB', 1024*1024);
$config['arquivos_retorno_tamanho_maximo']		= 4;
$config['arquivos_retorno_tamanho_maximo_mb']	= 1024*1024 * $config['arquivos_retorno_tamanho_maximo']; // não mexa



// boleto
// ---------------------------------------------------------------------
$config['boletos_arquivo_diretorioimagens']		= 'http://localhost/siac/img/boleto';
$config['boletos_arquivo_diretoriogravacao']	= 'c:'.DS.'siac-boletos';
$config['boletos_arquivo_nomearquivo']			= 'boletos-{TIPO}-{ANOMES}.pdf';
$config['boletos_arquivo_pdf_titulo']			= 'BOLETOS';
$config['boletos_arquivo_pdf_criador']			= 'SIAC - SISTEMA INTEGRADO DE AUTOMAÇÃO CONDOMINIAL';

$config['boletos_codigobarra_fatorlargura']		= 0.25;
$config['boletos_codigobarra_altura']			= 13;				// não mexa

$config['boletos_dados_banco_imagemlogo']		= 'logohsbc.jpg';	// não mexa
$config['boletos_dados_banco_codigo']			= '399';			// não mexa

$config['boletos_dados_agencia_cod_cedente']	= '3432599';		// não mexa
$config['boletos_dados_cedente']				= 'SOC. RESID. ALTO DE PINHEIROS';
$config['boletos_dados_cpfcnpj_cedente']		= 'CNPJ 0333.3692/0001-88';



// gerais
// ---------------------------------------------------------------------
$config['siac_listagem_qtdregistros']		= 100;
$config['siac_listagem_status_navigator']	= 'Página %page% de %pages% (%current% registros, do %start%º ao %end%º, de um total de %count%)';



// CONSTANTES
// ---------------------------------------------------------------------
!defined('TIPO_LANCAMENTO_QUALQUER') 		&& define('TIPO_LANCAMENTO_QUALQUER'		,-1);
!defined('TIPO_LANCAMENTO_TAXACONDOMINIAL')	&& define('TIPO_LANCAMENTO_TAXACONDOMINIAL'	, 0);
!defined('TIPO_LANCAMENTO_TAXAEXTRA') 		&& define('TIPO_LANCAMENTO_TAXAEXTRA'		, 1);
!defined('TIPO_LANCAMENTO_MULTAINFRACAO') 	&& define('TIPO_LANCAMENTO_MULTAINFRACAO'	, 2);
!defined('TIPO_LANCAMENTO_ACORDO') 			&& define('TIPO_LANCAMENTO_ACORDO'			, 3);

!defined('STATUS_PARCELA_CORRETA') 			&& define('STATUS_PARCELA_CORRETA'		, 0);
!defined('STATUS_PARCELA_REGULARIZADA')		&& define('STATUS_PARCELA_REGULARIZADA'	, 1);
!defined('STATUS_PARCELA_PENDENTE') 		&& define('STATUS_PARCELA_PENDENTE'		, 2);

!defined('TIPO_LIQUIDACAO_CHEQUE') 			&& define('TIPO_LIQUIDACAO_CHEQUE'		, 1);
!defined('TIPO_LIQUIDACAO_DINHEIRO')		&& define('TIPO_LIQUIDACAO_DINHEIRO'	, 2);
!defined('TIPO_LIQUIDACAO_COMPENSACAO')		&& define('TIPO_LIQUIDACAO_COMPENSACAO'	, 3);

!defined('OCORRENCIA_RETORNO_LIQUIDACAO')	&& define('OCORRENCIA_RETORNO_LIQUIDACAO'	, '06');
!defined('OCORRENCIA_EMISSAO_CONFIRMADA')	&& define('OCORRENCIA_EMISSAO_CONFIRMADA'	, '07');
!defined('OCORRENCIA_PARCELA_REJEITADA')	&& define('OCORRENCIA_PARCELA_REJEITADA'	, '08');

!defined('MOEDA_REAL')						&& define('MOEDA_REAL'		, 9);
!defined('MOEDA_VARIAVEL')					&& define('MOEDA_VARIAVEL'	, 0);
