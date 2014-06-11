<?php
App::import('Model', 'Unidade');
class CriarBanco extends AppMigration {

	// do something!
	function up() {

		$this->createTable('lancamentos', array(
			  'mes_ano'					=> array('type'=> 'string',	'null'=> false,		'length'=> 7)
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
			, 'instrucao_boleto_id'		=> array('type'=> 'integer',	'null'=> true)
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

		$this->createTable('tipos_lancamento', array(
			  'descricao'				=> array('type'=> 'string', 'null'=> false)
			, 'created'					=> 'datetime'
			, 'modified'				=> 'datetime'
		));

		$this->createTable('taxas', array(
			  'ref'				=> array('type'=> 'string',		'null'=> false)
			, 'dta_assembleia'	=> array('type'=> 'date',		'null'=> false)
			, 'motivo'			=> array('type'=> 'string',		'null'=> false)
			, 'valor_total'		=> array('type'=> 'float',		'length'=> '10,2',	 'null'=> false)
			, 'qtd_parcelas'	=> array('type'=> 'integer',	'null'=> false, 'default'=> 1)
			, 'created'			=> 'datetime'
			, 'modified'		=> 'datetime'
		));

		$this->createTable('multas', array(
			  'tipo_multa_id'	=> array('type'=> 'integer', 'null'=> false)
			, 'motivo'			=> array('type'=> 'string', 'length'=> 50)
			, 'proprietario_id'	=> array('type'=> 'integer', 'null'=> false)
			, 'valor_total'		=> array('type'=> 'float', 'length'=> '10,2', 'null'=> false)
			, 'created'			=> 'datetime'
			, 'modified'		=> 'datetime'
		));

		$this->createTable('tipos_multa', array(
			  'descricao'		=> array('type'=> 'string', 'null'=> false)
			, 'created'			=> 'datetime'
			, 'modified'		=> 'datetime'
		));

		$this->createTable('proprietarios', array(
			  'nome'			=> array('type'=> 'string', 'null'=> false)
			, 'rg'				=> 'string'
			, 'cpf_cnpj'		=> array('type'=> 'string', 'null'=> false)
			, 'endereco'		=> array('type'=> 'string', 'null'=> false)
			, 'bairro'			=> array('type'=> 'string', 'null'=> false)
			, 'cep'				=> array('type'=> 'string', 'null'=> false)
			, 'cidade'			=> array('type'=> 'string', 'null'=> false)
			, 'uf'				=> array('type'=> 'string', 'null'=> false)
			, 'fone1'			=> array('type'=> 'string', 'null'=> false)
			, 'fone2'			=> 'string'
			, 'celular'			=> 'string'
			, 'email'			=> 'string'
			, 'conjuge_nome'	=> 'string'
			, 'conjuge_rg'		=> 'string'
			, 'conjuge_cpf'		=> 'string'
			, 'conjuge_endereco'=> 'string'
			, 'conjuge_bairro'	=> 'string'
			, 'conjuge_cep'		=> 'string'
			, 'conjuge_cidade'	=> 'string'
			, 'conjuge_uf'		=> 'string'
			, 'conjuge_fone1'	=> 'string'
			, 'conjuge_fone2'	=> 'string'
			, 'conjuge_celular'	=> 'string'
			, 'conjuge_email'	=> 'string'
			, 'created'			=> 'datetime'
			, 'modified'		=> 'datetime'
		));

		$this->createTable('unidades', array(
			  'rua_id'			=> array('type'=> 'string', 'null'=> false)
			, 'quadra_id'		=> array('type'=> 'integer', 'null'=> false)
			, 'lote'			=> array('type'=> 'integer', 'length'=> 2, 'null'=> false)
			, 'proprietario_id'	=> array('type'=> 'integer', 'null'=> false)
			, 'morador_nome'	=> 'string'
			, 'morador_rg'		=> 'string'
			, 'morador_cpf'		=> 'string'
			, 'morador_fone1'	=> 'string'
			, 'morador_fone2'	=> 'string'
			, 'morador_celular'	=> 'string'
			, 'morador_email'	=> 'string'
			, 'morador_obs'		=> 'string'
			, 'created'			=> 'datetime'
			, 'modified'		=> 'datetime'
		));

		$this->createTable('ruas', array(
			  'id'			=> array('key'=> 'primary', 'type'=> 'string', 'null'=> false, 'length'=> 3)
			, 'descricao'	=> array('type'=> 'string', 'null'=> false)
			, 'ordem'		=> array('type'=> 'integer', 'null'=> false, 'default'=> 1)
			, 'created'		=> 'datetime'
			, 'modified'	=> 'datetime'
		));

		$this->createTable('quadras', array(
			  'abbr'		=> 'string'
			, 'descricao'	=> array('type'=> 'string', 'null'=> false)
			, 'total_lotes'	=> 'integer'
			, 'created'		=> 'datetime'
			, 'modified'	=> 'datetime'
		));

		$this->createTable('instrucoes_boleto', array(
			  'texto'				=> array('type'=> 'text',		'null'=> false)
			, 'descricao'			=> array('type'=> 'string',		'length'=> 30)
			, 'tipo_lancamento_id'	=> array('type'=> 'integer',	'null'=> false, 	'default'=> -1)
			, 'created'				=> 'datetime'
			, 'modified'			=> 'datetime'
		));

		$this->createTable('formas_pagamento', array(
			  'descricao'			=> array('type'=> 'string',		'null'=> false)
			, 'created'				=> 'datetime'
			, 'modified'			=> 'datetime'
		));

		$this->createTable('usuarios', array(
			  'usuario'			=> array('type'=> 'string',		'null'=> false)
			, 'senha'			=> array('type'=> 'string',		'null'=> false)
			, 'nome_completo'	=> 'string'
//			, 'perfil_id'		=> array('type'=> 'integer',	'null'=> false,	'default'=> 1)
			, 'created'			=> 'datetime'
			, 'modified'		=> 'datetime'
		));

		$this->createTable('acordos', array(
			  'unidade_id'		=> array('type'=> 'integer',	'null'=> false)
			, 'total_devido'	=> array('type'=> 'float',		'length'=> '10,2',	'null'=> false)
			, 'total_acrescimo'	=> array('type'=> 'float',		'length'=> '10,2',	'null'=> false, 	'default'=> 0.0)
			, 'desconto'		=> array('type'=> 'float',		'length'=> '10,2')
			, 'total_geral'		=> array('type'=> 'float',		'length'=> '10,2',	'null'=> false)
			, 'em_aberto'		=> array('type'=> 'float',		'length'=> '10,2',	'null'=> false, 	'default'=> 0.0)
			, 'total_acordado'	=> array('type'=> 'float',		'length'=> '10,2',	'null'=> false)
			, 'valor_entrada'	=> array('type'=> 'float',		'length'=> '10,2',	'null'=> false, 	'default'=> 0.0)
			, 'dta_entrada'		=> array('type'=> 'date',		'null'=> false)
			, 'qtd_parcelas'	=> array('type'=> 'integer', 	'null'=> false, 	'default'=> 1)
			, 'usuario_id'		=> array('type'=> 'integer', 	'null'=> false)
			, 'created'			=> 'datetime'
			, 'modified'		=> 'datetime'
		));

		$this->createTable('parcelas_acordo', array(
			  'acordo_id'		=> array('type'=> 'integer',	'null'=> false)
			, 'parcela'			=> array('type'=> 'integer',	'null'=> false)
			, 'valor'			=> array('type'=> 'integer',	'null'=> false)
			, 'dta_vencimento'	=> array('type'=> 'date',		'null'=> false)
			, 'dta_pagamento'	=> array('type'=> 'date',		'null'=> false)
			, 'usuario_id'		=> array('type'=> 'integer',	'null'=> false)
			, 'created'			=> 'datetime'
			, 'modified'		=> 'datetime'
		));

		$this->createTable('bancos', array(
				  'codigo'			=> 'string'
				, 'descricao'		=> array('type'=> 'string',		'null'=> false)
				, 'created'			=> 'datetime'
				, 'modified'		=> 'datetime'
		));
	}

	// crash something!
	function down() {

		$this->dropTable('bancos');
		$this->dropTable('parcelas_acordo');
		$this->dropTable('acordos');
		$this->dropTable('usuarios');
		$this->dropTable('formas_pagamento');
		$this->dropTable('instrucoes_boleto');
		$this->dropTable('quadras');
		$this->dropTable('ruas');
		$this->dropTable('unidades');
		$this->dropTable('proprietarios');
		$this->dropTable('tipos_multa');
		$this->dropTable('multas');
		$this->dropTable('taxas');
		$this->dropTable('tipos_lancamento');
		$this->dropTable('lancamentos');
	}
}
