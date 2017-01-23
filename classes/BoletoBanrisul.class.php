<?php 
/*
*   Descrição:  Classe para geração de Boletos para Banrisul
*   Autor: Thiago R. Gham
*   Versão: 0.1  06-02-2016                                         
*/
class BoletoBanrisul{
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    public function GeraDados($info){

		$v                  = str_replace(chr(44), "", str_replace("R\$", "", str_replace(".","",$info["valor_documento"])));
        $valor              = sprintf("%010d", $v);
        $dvence             = date('d', strtotime($info["dt_vencimento"]));
        $mvence             = date('m', strtotime($info["dt_vencimento"]));
        $avence             = date('Y', strtotime($info["dt_vencimento"]));
        $dt_vencimento      = "$dvence/$mvence/$avence";
        $fatorvcto          = $this->FatorVencimento($avence, $mvence, $dvence);
        $ano                = date("y");
        $cart	            = sprintf("%03d",$info["codigo_carteira"]);
        $nosso_numero       = sprintf("%08d", $info["nosso_numero"]);
        $agencia		    = substr($info["agencia"], 0, 5);
		$agencia_sdac	    = substr($info["agencia"], 0, 3);
		$dac_agencia	    = substr($info["agencia"], 3, 2);
        $contacedente	    = substr($info["conta_cedente"],0,9);
        $contacedente	    = sprintf("%09d",$contacedente);
        $contacedente_sdac  = substr($contacedente, 0, strlen($contacedente)-2);
        $contacedente_sdac  = sprintf("%07d",$contacedente_sdac);
		$dac_contacedente	= substr($info["conta_cedente"], 7, 2);
        $moeda		        = "9";
        $codbank	        = "041";
		$tpcobranca         = "21";
		$codbankconstante   = '40';
	    $DAC_D1	= $this->Modulo10("$tpcobranca$agencia$contacedente_sdac$nosso_numero$codbankconstante");
		while (true) {
			$resto = $this->Modulo11("$tpcobranca$agencia$contacedente_sdac$nosso_numero$codbankconstante$DAC_D1", 7, 1);
			if ($resto==1) {
				$DAC_D1++;
				if ($DAC_D1==10) {
					$DAC_D1 = 0;
				}
			}elseif ($resto==0) {
		        $DAC_D2 = 0;			
				break;
			}else {
                $DAC_D2 = $this->Modulo11("$tpcobranca$agencia$contacedente_sdac$nosso_numero$codbankconstante$DAC_D1", 7, 0);	
				break;
			}
		}
        $DAC_NN1 = $this->Modulo10("$nosso_numero");
		while (true) {
	        $resto = $this->Modulo11("$nosso_numero$DAC_NN1", 7, 1);			
			if ($resto==1) {
				$DAC_NN1++;
				if ($DAC_NN1==10) {
					$DAC_NN1 = 0;
				}
			}elseif($resto==0) {
		        $DAC_NN2 = 0;			
				break;
			}else{
		        $DAC_NN2 = $this->Modulo11("$nosso_numero$DAC_NN1", 7, 0);			
				break;
			}
		}
        $dvcampo  = "0419$fatorvcto$valor$tpcobranca$agencia$contacedente_sdac$nosso_numero$codbankconstante$DAC_D1$DAC_D2";
		$dv       =  $this->DigitoVerificador($dvcampo);
        $num      = "$codbank$moeda$dv$fatorvcto$valor$tpcobranca$agencia$contacedente_sdac$nosso_numero$codbankconstante$DAC_D1$DAC_D2";	
        $linha_digitavel = $this->Montalinha($num);
        $codigo_banco	 = $this->GeraCodigoBanco($codbank);
        $nosso_numero    = "$nosso_numero-$DAC_NN1$DAC_NN2";
		$AGENCIA_NC1	 = $this->Modulo10("$agencia");
		while (true) {
	        $resto = $this->Modulo11("$agencia$AGENCIA_NC1", 7, 1);			
			if ($resto == 1) {
				$AGENCIA_NC1++;
				if ($AGENCIA_NC1 == 10) {
					$AGENCIA_NC1 = 0;
				}
			}elseif($resto == 0) {
		        $AGENCIA_NC2 = 0;			
				break;
			}else{
		        $AGENCIA_NC2 = $this->Modulo11("$agencia$AGENCIA_NC1", 7, 0);			
				break;
			}
		}
		$agencia_codigo = "$agencia-$AGENCIA_NC1$AGENCIA_NC2/".substr($info["conta_cedente"], 0, 6).'.'.substr($info["conta_cedente"], 6, 1).'.'.substr($info["conta_cedente"], 7, 2);
	    return array(
            "dt_vencimento"   => $dt_vencimento,
            "linha_digitavel" => $linha_digitavel,
            "agencia_codigo"  => $agencia_codigo,
            "codigo_barras"   => $num,
            "codigo_banco"    => $codigo_banco,
            "nosso_numero"    => $nosso_numero
        );
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function Montalinha($codigo){
        $p1 = substr($codigo, 0, 4);
        $p2 = substr($codigo, 19, 5);
        $p3 = $this->Modulo10Linha("$p1$p2");
        $p4 = "$p1$p2$p3";
        $p5 = substr($p4, 0, 5);
        $p6 = substr($p4, 5, 5);
        $campo1 = "$p5.$p6";
        $p1 = substr($codigo, 24, 10);
        $p2 = $this->Modulo10Linha($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5, 6);
        $campo2 = "$p4.$p5";
        $p1 = substr($codigo, 34, 10);
        $p2 = $this->Modulo10Linha($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5, 6);
        $campo3 = "$p4.$p5";
        $campo4 = substr($codigo, 4, 1);
        $campo5 = substr($codigo, 5, 14);
        return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function DigitoVerificador($numero){
        $digito = $this->Modulo11DV($numero);
        if (in_array((int)$digito,array(0,1,10,11))) {
            $digito = 1;
        }
        return $digito;
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function GeraCodigoBanco($numero){
        $parte1 = substr($numero, 0, 3);
        $parte2 = $this->Modulo11DV($parte1, 9, 0);
        return $parte1 . "-" . $parte2;
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function Modulo10Linha($num){
        $numtotal10 = 0;
        $fator      = 2;
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $parcial10[$i] = $numeros[$i] * $fator;
            $numtotal10 .= $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            }else{
                $fator = 2;
            }
        }
        $soma = 0;
        for ($i = strlen($numtotal10); $i > 0; $i--) {
            $numeros[$i] = substr($numtotal10,$i-1,1);
            $soma += $numeros[$i];              
        }
        $resto = $soma % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }
        return $digito;
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function Modulo10($num){
        $fator = 2;
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $multiplicacao[$i] = ($numeros[$i] * $fator);
			if ($multiplicacao[$i]>9) {
				$multiplicacao[$i] = $multiplicacao[$i] - 9;
			}
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2;	
            }
        }
        $soma = 0;
        for ($i = strlen($num); $i > 0; $i--) {
            $soma += $multiplicacao[$i];				
        }
        $resto = $soma % 10;
		if ($soma < 10) {
			$resto = $soma;
		}
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }
        return $digito;
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function Modulo11($num, $base=9, $r=0){
        $soma   = 0;
        $fator  = 2;
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $parcial[$i] = $numeros[$i] * $fator;
            $soma += $parcial[$i];
            if ($fator == $base) {
                $fator = 1;
            }
            $fator++;
        }
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }
            return $digito;
        } elseif ($r == 1){
            $resto = $soma % 11;
            return $resto;
        }
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function Modulo11DV($num, $base=9, $r=0){
        $soma   = 0;
        $fator  = 2;
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $parcial[$i] = $numeros[$i] * $fator;
            $soma += $parcial[$i];
            if ($fator == $base) {
                $fator = 1;
            }
            $fator++;
        }
        if ($r == 0) {
            $resto = $soma % 11;
            $digito = 11 - $resto;
            if ($digito>9) {
                $digito = 0; 
            }
            return $digito;
        } elseif ($r == 1){
            $resto = $soma % 11;
            return $resto;
        }
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function FatorVencimento($ano, $mes, $dia){
        return(abs(($this->DataDias("1997","10","07")) - ($this->DataDias($ano, $mes, $dia))));
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function DataDias($year,$month,$day){
        $century = substr($year, 0, 2);
        $year = substr($year, 2, 2);
        if ($month > 2) {
            $month -= 3;
        }else{
            $month += 9;
            if ($year) {
                $year--;
            }else{
                $year = 99;
                $century --;
            }
        }
        return (floor((146097 * $century) / 4) + floor((1461 * $year) / 4) + floor((153 * $month + 2) / 5) + $day + 1721119);
    }
}