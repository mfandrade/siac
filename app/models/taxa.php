<?php
class Taxa extends AppModel {
	var $name			= 'Taxa';
	var $displayField	= 'motivo';
	var $hasMany		= array('Lancamento');

	var $validate		= array(
		  'dta_assembleia'	=> array(
			  'obrigatoria'	=> array('rule'=> 'date', 'required'=> true, 'message'=> 'A data da assembleia é obrigatória')
			, 'unica'		=> array('rule'=> 'isUnique', 'message'=> 'Uma taxa com esta mesma data de assembleia já está cadastrada')
		)
		, 'motivo'			=> array('required'=> true, 'rule'=> 'notEmpty', 'message'=> 'O motivo da taxa é obrigatório')
		, 'valor_total'		=> array('required'=> true, 'rule'=> array('decimal', 2), 'message'=> 'O valor total por unidade é obrigatório')
		, 'qtd_parcelas'	=> array('required'=> false, 'rule'=> 'numeric')
	);

/**
 *
 */
	function cadastrarLancar($data, $usuario_id) {

		$this->begin($this->name);
		{
			$data['Taxa']['ref']	= 'REF'; // TODO: remover o campo da tabela
			$data['Taxa']['motivo']	= strtoupper($data['Taxa']['motivo']);
			$this->data	= $data;
			if( !$this->save($this->data) ) return false;

			if( empty($data['Taxa']['qtd_parcelas']) ) {
				$data['Taxa']['qtd_parcelas']	= 1;
			}
			$idTaxa	= $this->getLastInsertID();

			$taxa['Lancamento']['tipo_lancamento_id']	= TIPO_LANCAMENTO_TAXAEXTRA;
			$taxa['Lancamento']['valor_documento']		= number_format($this->data['Taxa']['valor_total'] / $this->data['Taxa']['qtd_parcelas'], 2);
			$taxa['Lancamento']['taxa_id']				= $idTaxa;
			$taxa['Lancamento']['multa_id']				= null;
			$taxa['Lancamento']['instrucao_boleto_id']	= null; // TODO: verificar o que fazer
			$taxa['Lancamento']['usuario_id']			= $usuario_id;
			$taxa['Lancamento']['acordo_id']			= null;
			$taxa['Lancamento']['total_parcelas']		= 1; // TODO: remover este campo

			$okay	= $this->Lancamento->efetuarLancamentos($taxa, $data['Taxa']['qtd_parcelas']);
			if( $okay ) {

				$this->commit($this->name);
				return true;
			}
			$this->rollback($this->name);
			return false;
		}
	}
}
