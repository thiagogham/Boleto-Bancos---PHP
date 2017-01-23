<?php 
/*
*   Descrição:  Classe para geração de Boletos para Bradesco
*   Autor: Thiago R. Gham
*   Versão: 0.1  06-02-2016                                         
*/
class BoletoBradesco{
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    public function GeraDados($info){

        $v               = str_replace(chr(44), "", str_replace("R\$", "", str_replace(".","",$info["valor_documento"])));
        $valor           = sprintf("%010d", $v);
        $dvence          = date('d', strtotime($info["dt_vencimento"]));
        $mvence          = date('m', strtotime($info["dt_vencimento"]));
        $avence          = date('Y', strtotime($info["dt_vencimento"]));
        $dt_vencimento   = "$dvence/$mvence/$avence";
        $fatorvcto       = $this->FatorVencimento($avence, $mvence, $dvence);
        $ano	         = date("y");
        $cart	         = sprintf("%02d",$info["codigo_carteira"]);
        $nnum	         = sprintf("%011s", $info["nosso_numero"]);
        $nnum	         = "$cart$nnum";
        $dv		         = $this->DigitoVerificador($nnum);
        $nnum	         = "$nnum$dv";
        $zero	         = 0;
        //$agencia         = sprintf("%05d",$info["agencia"]);
        $agencia         = sprintf("%04d",$info["agencia"]);
        $contacedente    = substr($info["conta_cedente"], 0, 7);
		$agencia_b       = substr($agencia,0,4);
        $nn				 = substr($nnum, 0, 13);
        $moeda		     = "9";
        $codbank	     = 237;
        $dvcampo	     = "$codbank$moeda$fatorvcto$valor$agencia_b$nn$contacedente$zero";
        $dv				 = $this->Modulo11($dvcampo, 9, 0, 1);
        $num             = "$codbank$moeda$dv$fatorvcto$valor$agencia_b$nn$contacedente$zero";
        $linha_digitavel = $this->MontaLinha($num);
        $codigo_banco    = $this->GeraCodigoBanco($codbank);
        $p1 = substr($nnum, 0, 2);
        $p2 = substr($nnum, 2, 11);
        $p3 = substr($nnum, -1);
        $nosso_numero     = "$p1/$p2-$p3";
        $p1               = $this->DigitoVerificador($contacedente);
		$agencia_cd       = substr($agencia,0,4);
		$agencia_dv       = substr($agencia,4,1);
        //$agencia_codigo   = "$agencia_cd-$agencia_dv $contacedente/$p1";
        $agencia_codigo   = "$agencia_cd-$p1 $contacedente/$p1";
        
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
        $p3 = $this->Modulo10("$p1$p2");
        $p4 = "$p1$p2$p3";
        $p5 = substr($p4, 0, 5);
        $p6 = substr($p4, 5, 5);
        $campo1 = "$p5.$p6";
        $p1 = substr($codigo, 24, 10);
        $p2 = $this->Modulo10($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5, 6);
        $campo2 = "$p4.$p5";
        $p1 = substr($codigo, 34, 10);
        $p2 = $this->Modulo10($p1);
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
        $resto  = $this->Modulo11($numero, 7, 1);
        $digito = 11 - $resto;
        if ($resto == 1) {
            $digito = "P";
        } elseif ($resto == 0) {
            $digito = 0;
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
        $parte2 = $this->Modulo11($parte1, 9, 0);
        return $parte1 . "-" . $parte2;
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function Modulo10($num){    
        $numtotal10 = 0;
        $fator      = 2;
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $parcial10[$i] = $numeros[$i] * $fator;
            $numtotal10 .= $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
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
    private function Modulo11($num, $base=9, $r=0, $bradesco=0){
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
        /* Calculo do modulo 11 */
        if ($bradesco == 1) {
            $resto  = $soma % 11;
            $digito = 11 - $resto;
            if ($digito==10 or $digito==11) {
                $digito = 1;
            }
            return $digito;
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