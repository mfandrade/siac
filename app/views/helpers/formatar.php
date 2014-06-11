<?php
class FormatarHelper extends AppHelper {

/**
 * Formata um valor de ponto flutuante vindo do banco para Real.
 * @param  float	$valor		o valor vindo do banco.
 * @param  boolean	$simbolo	se deve pôr o símbolo de Real na frente.
 * @return string o valor como string formatada em Real.
 */
	function real($valor, $simbolo= false, $milhar= true) {
		$sep	= ($milhar? '.': '');
		if( $simbolo ) return 'R$ ' . number_format($valor, 2, ',', $sep);
		return number_format($valor, 2, ',', $sep);
	}

/**
 * Escreve um valor numérico por extenso.
 * @param  string  $valor		o valor sem máscara vindo do banco.
 * @return string  o valor correspondente por extenso
 */
	function extenso($valor) {

        $singular = array("CENTAVO", "REAL", "MIL", "MILHÃO", "BILHÃO", "TRILHÃO", "QUATRILHÃO");
        $plural = array("CENTAVOS", "REAIS", "MIL", "MILHÕES", "BILHÕES", "TRILHÕES", "QUATRILHÕES");

        $c = array("", "CEM", "DUZENTOS", "TREZENTOS", "QUATROCENTOS", "QUINHENTOS", "SEISCENTOS", "SETECENTOS", "OITOCENTOS", "NOVECENTOS");
        $d = array("", "DEZ", "VINTE", "TRINTA", "QUARENTA", "CINQUENTA", "SESSENTA", "SETENTA", "OITENTA", "NOVENTA");
        $d10 = array("DEZ", "ONZE", "DOZE", "TREZE", "QUATORZE", "QUINZE", "DEZESSEIS", "DEZESETE", "DEZOITO", "DEZENOVE");
        $u = array("", "UM", "DOIS", "TRÊS", "QUATRO", "CINCO", "SEIS", "SETE", "OITO", "NOVE");

        $z=0;

        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        for($i=0;$i<count($inteiro);$i++)
                for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
                        $inteiro[$i] = "0".$inteiro[$i];

        $fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
        $rt= '';
        for ($i=0;$i<count($inteiro);$i++) {
                $valor = $inteiro[$i];
                $rc = (($valor > 100) && ($valor < 200)) ? "CENTO" : $c[$valor[0]];
                $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
                $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

                $r = $rc.(($rc && ($rd || $ru)) ? " E " : "").$rd.(($rd && $ru) ? " E " : "").$ru;
                $t = count($inteiro)-1-$i;
                $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
                if ($valor == "000")$z++; elseif ($z > 0) $z--;
                if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " DE " : "").$plural[$t];
                if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " E ") : "") . $r;
        }
        return empty($rt)? 'ZERO': $rt;
	}

/**
 * Converte uma data vinda do banco para o formato legível.
 * @param  string $valor  o valor vindo do banco.
 * @return string o valor como string formatada.
 */
	function data($valor) {
		if( preg_match('/^20[0-9]{2}-(0|1)[0-9]-(0|1|2|3)[0-9]$/', $valor) ) {
			list($ano, $mes, $dia)	= explode('-', $valor);
			return sprintf('%s/%s/%s', $dia, $mes, $ano);
		}
		return $valor;
	}

/**
 * Converte um mês numérico para string por extenso.
 * @param  integer $n    representação do mês como número de 1-12
 * @param  boolean $abbr se deve retornar a abreviação do mês com 3 letras
 * @return string  com o nome do mês em português ou o próprio valor caso não seja um mês válido.
 */
	function mes($n, $abbr= false) {

		if( $n > 12 || $n < 1 ) return $n;

		$data	= $n.'/01/2009';
		$time	= strtotime($data);

		$l= setlocale(LC_TIME, 'ptb', 'pt_BR', 'pt_BR.UTF-8', 'pt_BR.iso-8859-1', 'pt', 'pt_BR', 'por', 'portuguese');
		if( $abbr ) {
			$formato	= '%b';
		} else {
			$formato	= '%B';
		}
		return strftime($formato, $time);
	}

/**
 * Converte uma data/hora vinda do banco para o formato legível.
 * @param  string  $valor  o valor vindo do banco.
 * @param  boolean $seg    se deve exibir ou não os segundos.
 * @param  boolean $h      se deve exibir ou não a hora.
 * @return string  o valor como string formatada.
 */
	function datahora($valor, $seg= false, $h= true) {
		//if( preg_match('/20[0-9]{2}-(0|1)[0-9]-(0|1|2|3)[0-9]\ (0|2)[0-9]:(0|5)[0-9]:(0|5)[0-9]/', $valor) ) {
			list($data, $hora)		= explode(' ', $valor);
			list($ano, $mes, $dia)	= explode('-', $data);
			list($hh, $mm, $ss)		= explode(':', $hora);
			$data	= sprintf('%s/%s/%s', $dia, $mes, $ano);
			if( $seg ) {
				$hora	= sprintf('%s:%s:%s', $hh, $mm, $ss);
			} else {
				$hora	= sprintf('%s:%s', $hh, $mm);
			}
			if( $h ) return $data . ' ' . $hora;
			return $data;
		//}
		//return $valor;
	}

/**
 * Aplica máscara a um valor de CPF/CNPJ.  Diferencia-os pela quantidade
 * de dígitos.
 * @param  string  $valor  o valor sem máscara vindo do banco.
 * @param  boolean $force  se deve forçar a formatação completando com zeros.
 * @return string o valor como string mascarada.
 */
	function cpfcnpj($valor, $force= false) {

		if( $force && preg_match('/^[0-9]+$/', $valor) ) {

			if( strlen($valor) < 11 ) {
				$valor	= str_pad($valor, 11, '0', STR_PAD_LEFT);
			} elseif( strlen($valor) < 14 ) {
				$valor	= str_pad($valor, 14, '0', STR_PAD_LEFT);
			}
		}
		if( preg_match('/^[0-9]{11}$/', $valor) ) {

			$aaa	= substr($valor, 0, 3);
			$bbb	= substr($valor, 3, 3);
			$ccc	= substr($valor, 6, 3);
			$dd		= substr($valor, 9);
			return sprintf('%s.%s.%s-%s', $aaa, $bbb, $ccc, $dd);
		} elseif( preg_match('/^[0-9]{14}$/', $valor) ) {

			$aa		= substr($valor, 0, 2);
			$bbb	= substr($valor, 2, 3);
			$ccc	= substr($valor, 5, 3);
			$dddd	= substr($valor, 8, 4);
			$ee		= substr($valor, 12);
			return sprintf('%s.%s.%s.%s/%s-%s', $aa, $bbb, $ccc, $dddd, $ee);
		}
		return $valor;
	}

/**
 * Formata um valor de telefone.
 * @param  string  $valor  o valor sem máscara vindo do banco.
 * @param  boolean $force  se deve forçar a formatação completando com zeros.
 * @return string  o valor de telefone com máscara ddd e separador
 */
	function telefone($valor, $force= false) {
		if( $force && preg_match('/^[0-9]+$/', $valor) ) {

			if( strlen($valor) < 8 ) {
				$valor	= str_pad($valor, 8, '0', STR_PAD_LEFT);
			} elseif( strlen($valor) < 10 ) {
				$valor	= str_pad($valor, 10, '0', STR_PAD_LEFT);
			}
		}
		if( preg_match('/^[0-9]{8}$/', $valor) ) {

			$aaaa	= substr($valor, 0, 4);
			$bbbb	= substr($valor, 4);
			return sprintf('%s %s', $aaaa, $bbbb);
		} elseif( preg_match('/^[0-9]{10}$/', $valor) ) {

			$aa		= substr($valor, 0, 2);
			$bbbb	= substr($valor, 2, 4);
			$cccc	= substr($valor, 6);
			return sprintf('(%s) %s %s', $aa, $bbbb, $cccc);
		}
		return $valor;
	}

/**
 * Formata um valor de cep.
 * @param  string  $valor  o valor sem máscara vindo do banco.
 * @param  boolean $force  se deve forçar a formatação completando com zeros.
 * @return string  o valor de cep
 */
	function cep($valor, $force= false) {
		if( $force && preg_match('/^[0-9]+$/', $valor) ) {
			if( strlen($valor) < 8 ) {
				$valor	= str_pad($valor, 8, '0', STR_PAD_RIGHT);
			}
		}
		if( preg_match('/^[0-9]{8}$/', $valor) ) {
			$aa		= substr($valor, 0, 2);
			$bbb	= substr($valor, 2, 3);
			$ccc	= substr($valor, 5);
			return sprintf('%s%s-%s', $aa, $bbb, $ccc);
		}
		return $valor;
	}
}
?>
