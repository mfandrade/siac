<?php
App::import('Model', array('Pagamento', 'Lancamento', 'ArquivoRetorno'));
class CriarIndicesPagamentosLancamentos extends AppMigration {

	function up() {

		$this->createTable('arquivos_retorno', array(
			  'name'	=> array('type'=> 'string', 'null'=> false, 'length'=> 64)
			, 'type'	=> array('type'=> 'string', 'null'=> false, 'length'=> 64, 'default'=> 'text/plain')
			, 'size'	=> array('type'=> 'integer', 'null'=> false)
			, 'content'	=> array('type'=> 'text')
		));

		$this->addColumn('pagamentos', 'arquivo_retorno_id', array('type'=> 'integer'));

		$this->Pagamento	=& ClassRegistry::init('Pagamento');
		$this->Pagamento->query('CREATE INDEX i_pagamentos_lancamento_id USING BTREE ON pagamentos (lancamento_id)');
		$this->Pagamento->query('CREATE INDEX i_pagamentos_forma_pagamento_id USING BTREE ON pagamentos (forma_pagamento_id)');
		$this->Pagamento->query('CREATE INDEX i_pagamentos_arquivo_retorno_id USING BTREE ON pagamentos (arquivo_retorno_id)');

		$this->Lancamento	=& ClassRegistry::init('Lancamento');
		$this->Lancamento->query('CREATE INDEX i_lancamentos_taxa_id USING BTREE ON lancamentos (taxa_id)');
		$this->Lancamento->query('CREATE INDEX i_lancamentos_multa_id USING BTREE ON lancamentos (multa_id)');
		$this->Lancamento->query('CREATE INDEX i_lancamentos_unidade_id USING BTREE ON lancamentos (unidade_id)');
		$this->Lancamento->query('CREATE INDEX i_lancamentos_instrucao_boleto_id USING BTREE ON lancamentos (instrucao_boleto_id)');
		$this->Lancamento->query('CREATE INDEX i_lancamentos_usuario_id USING BTREE ON lancamentos (usuario_id)');
		$this->Lancamento->query('CREATE INDEX i_lancamentos_acordo_id USING BTREE ON lancamentos (acordo_id)');
	}

	function down() {

		$this->Lancamento	=& ClassRegistry::init('Lancamento');
		$this->Lancamento->query('DROP INDEX i_lancamentos_taxa_id ON lancamentos');
		$this->Lancamento->query('DROP INDEX i_lancamentos_multa_id ON lancamentos');
		$this->Lancamento->query('DROP INDEX i_lancamentos_unidade_id ON lancamentos');
		$this->Lancamento->query('DROP INDEX i_lancamentos_instrucao_boleto_id ON lancamentos');
		$this->Lancamento->query('DROP INDEX i_lancamentos_usuario_id ON lancamentos');
		$this->Lancamento->query('DROP INDEX i_lancamentos_acordo_id ON lancamentos');

		$this->Pagamento	=& ClassRegistry::init('Pagamento');
		$this->Pagamento->query('DROP INDEX i_pagamentos_lancamento_id ON pagamentos');
		$this->Pagamento->query('DROP INDEX i_pagamentos_forma_pagamento_id ON pagamentos');
		$this->Pagamento->query('DROP INDEX i_pagamentos_arquivo_retorno_id ON pagamentos');

		$this->removeColumn('pagamentos', 'arquivo_retorno_id');

		$this->dropTable('arquivos_retorno');
	}
}
