<?php
class PagamentosChequesLancamentos extends AppMigration {

	function up() {

		$this->createTable('pagamentos', array(
			  'lancamento_id'		=> array('type'=> 'integer',	'null'=> false)
			, 'dta_pagamento'		=> array('type'=> 'date', 		'null'=> false)
			, 'valor_documento'		=> array('type'=> 'float',		'length'=> '10,2',		'null'=> false)
			, 'valor_desconto'		=> array('type'=> 'float',		'length'=> '10,2',		'null'=> false,	'default'=> 0)
			, 'valor_acrescimo'		=> array('type'=> 'float',		'length'=> '10,2',		'null'=> false,	'default'=> 0)
			, 'valor_pago'			=> array('type'=> 'float',		'length'=> '10,2',		'null'=> false)
			, 'parcela'				=> array('type'=> 'integer',	'null'=> false,	'default'=> 1)
			, 'forma_pagamento_id'	=> array('type'=> 'integer')
			, 'cheque_id'			=> array('type'=> 'integer')
			, 'created'				=> 'datetime'
			, 'modified'			=> 'datetime'
		));
		$this->createTable('cheques', array(
			  'dta_cheque'	=> array('type'=> 'date',		'null'=> false)
			, 'informacoes'	=> array('type'=> 'text',		'null'=> false)
			, 'banco_id'	=> array('type'=> 'integer',	'null'=> false)
			, 'created'		=> 'datetime'
			, 'modified'	=> 'datetime'
		));
		$this->dropTable('lancamentos');
		$this->createTable('lancamentos', array(
			  'mes_ano'				=> array('type'=> 'string',		'null'=> false,	'length'=> 7)
			, 'unidade_id'			=> array('type'=> 'integer',	'null'=> false)
			, 'tipo_lancamento_id'	=> array('type'=> 'integer',	'null'=> false,	'default'=> 0)
			, 'dta_vencimento'		=> array('type'=> 'date',		'null'=> false)
			, 'valor_documento'		=> array('type'=> 'float',		'length'=> '10,2',		'null'=> false)
			, 'total_parcelas'		=> array('type'=> 'integer',	'null'=> false,	'default'=> 1)
			, 'taxa_id'				=> 'integer'
			, 'multa_id'			=> 'integer'
			, 'instrucao_boleto_id'	=> array('type'=> 'integer',	'null'=> true)
			, 'usuario_id'			=> array('type'=> 'integer',	'null'=> false)
			, 'acordo_id'			=> 'integer'
			, 'created'				=> 'datetime'
			, 'modified'			=> 'datetime'
		));
	}

	function down() {

		$this->dropTable('lancamentos');
		$this->createTable('lancamentos', array(	// igual ao do "criar banco"
			  'mes_ano'					=> array('type'=> 'string',	'null'=> false)
			, 'dta_vencimento'			=> 'date'
			, 'dta_pagamento'			=> 'date'
			, 'tipo_lancamento_id'		=> array('type'=> 'integer',	'null'=> false,	'default'=> 0)

			, 'valor_documento'			=> array('type'=> 'float',		'length'=> '10,2',	'null'=> false)
			, 'valor_desconto'			=> array('type'=> 'float',		'length'=> '10,2',	'null'=> false,	'default'=> 0)
			, 'valor_acrescimo'			=> array('type'=> 'float',		'length'=> '10,2',	'null'=> false,	'default'=> 0)
			, 'valor_pago'				=> array('type'=> 'float',		'length'=> '10,2',	'null'=> false,	'default'=> 0)

			, 'parcela'					=> array('type'=> 'integer',	'default'=> 1)

			, 'taxa_id'					=> 'integer'
			, 'multa_id'				=> 'integer'
			, 'unidade_id'				=> array('type'=> 'integer',	'null'=> false)
			, 'instrucao_boleto_id'		=> array('type'=> 'integer',	'null'=> false)
			, 'forma_pagamento_id'		=> 'integer'
			, 'usuario_lancamento_id'	=> 'integer'
			, 'usuario_pagamento_id'	=> 'integer'
			, 'acordo_id'				=> 'integer'

			, 'banco_id'				=> 'integer'
			, 'dta_cheque'				=> 'date'
			, 'informacoes_cheque'		=> 'string'

			, 'created'					=> 'datetime'
			, 'modified'				=> 'datetime'
		));
		$this->dropTable('cheques');
		$this->dropTable('pagamentos');
	}
}
