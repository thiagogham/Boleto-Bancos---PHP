<?php 
/*
*   Descrição:  Classe para geração de Boletos para Itau
*   Autor: Thiago R. Gham
*   Versão: 0.1  06-02-2016                                         
*/
class BoletoSicredi{
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */   
    public function GeraDados($info){

        $filler1             = 1;
        $filler2             = 0;
        $tipo_cobranca       = 3;
        $posto               = '05';
        $nummoeda            = "9";
        $codigobanco         = "748";
        $codigo_banco_com_dv = $this->GeraCodigoBanco($codigobanco);      
        $fator_vencimento    = $this->FatorVencimento($info["dt_vencimento"]);
        $v                   = str_replace(chr(44), "", str_replace("R\$", "", str_replace(".","",$info["valor_documento"])));
        $valor               = sprintf("%010d", $v);
        $agencia             = sprintf("%04d", $info["agencia"]);
        $conta               = substr($info["conta_cedente"],0,5);
        $conta_dv            = substr($info["conta_cedente"],-1);
        $tipo_carteira       = 1;
        $carteira            = $info["codigo_carteira"];

        if(strlen($info["nosso_numero"]) >= 9){
            $nossonumero_dv      = str_replace(array('-','/','.'), '', $info["nosso_numero"]);
        }else{
            $nossonumero_dv = $this->GeraNossoNumero($agencia, $posto, $conta, $info["nosso_numero"]);
        }
        //formação do campo livre
        $campolivre          = "$tipo_cobranca$tipo_carteira$nossonumero_dv$agencia$posto$conta$filler1$filler2";
        $campolivre_dv       = $campolivre . $this->DigitoVerificadorCampoLivre($campolivre); 
        // 43 numeros para o calculo do digito verificador do codigo de barras
        $dv = $this->DigitoVerificadorBarra("$codigobanco$nummoeda$fator_vencimento$valor$campolivre_dv", 9, 0);
        // Numero para o codigo de barras com 44 digitos
        $linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$campolivre_dv";
        // Formata strings para impressao no boleto
        $nossonumero = substr($nossonumero_dv,0,2).'/'.substr($nossonumero_dv,2,6).'-'.substr($nossonumero_dv,8,1);
        $agencia_codigo = $agencia.".". $posto.".".$conta;

        $codigo_barras      = $linha;
        $linha_digitavel    = $this->MontaLinhaDigitavel($linha);
        $agencia_codigo     = $agencia_codigo;
        $nosso_numero       = $nossonumero;
        $codigo_banco       = $codigo_banco_com_dv;

        $dvence          = date('d', strtotime($info["dt_vencimento"]));
        $mvence          = date('m', strtotime($info["dt_vencimento"]));
        $avence          = date('Y', strtotime($info["dt_vencimento"]));
        $dt_vencimento   = "$dvence/$mvence/$avence";

        return array(
                "dt_vencimento"   => $dt_vencimento,
                "linha_digitavel" => $linha_digitavel,
                "agencia_codigo"  => $agencia_codigo,
                "codigo_barras"   => $codigo_barras,
                "codigo_banco"    => $codigo_banco,
                "nosso_numero"    => $nossonumero
            );
    }
    /**
     * [GeraNossoNumero description]
     * @param [type] $numero [description]
     */
    public function GeraNossoNumero($agencia, $posto, $conta, $numero) {
        $byteidt             = 2;
        $inicio_nosso_numero = date("y");
        $numero              = sprintf("%05d", $numero);
        $nosso_numero        = $inicio_nosso_numero . $byteidt . $numero;
        $dv_nosso_numero     = $this->DigitoVerificadorNossoNumero("$agencia$posto$conta$nosso_numero");
        $nosso_numero        = "$nosso_numero$dv_nosso_numero";

        return $nosso_numero;
    }
    /**
     * [GeraCodigoBanco description]
     * @param  [type] $numero [description]
     * @return [type]         [description]
     */
    private function GeraCodigoBanco($numero) {
        $parte1 = substr($numero, 0, 3);
        return $parte1 . "-X";
    }
    /**
     * [FatorVencimento description]
     * @param [type] $data [description]
     */
    private function FatorVencimento($data) {
        $data = explode("/", date('d/m/Y', strtotime($data)));
        $ano = $data[2];
        $mes = $data[1];
        $dia = $data[0];
        return(abs(($this->DateToDays("1997","10","07")) - ($this->DateToDays($ano, $mes, $dia))));
    }
    /**
     * [DateToDays description]
     * @param [type] $year  [description]
     * @param [type] $month [description]
     * @param [type] $day   [description]
     */
    private function DateToDays($year, $month, $day) {
        $century = substr($year, 0, 2);
        $year    = substr($year, 2, 2);
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }
        return ( floor((  146097 * $century) /  4 ) + floor(( 1461 * $year) /  4 ) + floor(( 153 * $month +  2) /  5 ) + $day +  1721119);
    }
    /**
     * [FormataNumero description]
     * @param [type] $numero [description]
     * @param [type] $loop   [description]
     * @param [type] $insert [description]
     * @param string $tipo   [description]
     */
    private function FormataNumero($numero, $loop, $insert, $tipo = "geral") {
        switch ($tipo) {
            case 'geral':
                $numero = str_replace(",","",$numero);
                while(strlen($numero) < $loop){
                    $numero = $insert . $numero;
                }
                break;
            case 'valor':
                $numero = str_replace(",","",$numero);
                while(strlen($numero) < $loop){
                    $numero = $insert . $numero;
                }
                break;
            case 'valor':
                while(strlen($numero) < $loop){
                    $numero = $numero . $insert;
                }
                break;
        }
        return $numero;
    }
    /**
     * [DigitoVerificadorNossoNumero description]
     * @param [type] $numero [description]
     */
    private function DigitoVerificadorNossoNumero($numero) {
        $resto2 = $this->Modulo11($numero, 9, 1);
         $digito = 11 - $resto2;
         if ($digito > 9 ) {
            $dv = 0;
         } else {
            $dv = $digito;
         }
     return $dv;
    }
    /**
     * [DigitoVerificadorCampoLivre description]
     * @param [type] $numero [description]
     */
    private function DigitoVerificadorCampoLivre($numero) {
        $resto2 = $this->Modulo11($numero, 9, 1);
        if ($resto2 <=1){
            $dv = 0;
        }else{
            $dv = 11 - $resto2;
        }
         return $dv;
    }
    /**
     * [DigitoVerificadorBarra description]
     * @param [type] $numero [description]
     */
    private function DigitoVerificadorBarra($numero) {
        $resto2 = $this->Modulo11($numero, 9, 1);
        $digito = 11 - $resto2;
         if ($digito <= 1 || $digito >= 10 ) {
            $dv = 1;
         } else {
            $dv = $digito;
         }
         return $dv;
    }
    /**
     * [Modulo11 description]
     * @param [type]  $num  [description]
     * @param integer $base [description]
     * @param integer $r    [description]
     */
    private function Modulo11($num, $base=9, $r=0)  {
        $soma = 0;
        $fator = 2;
        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) {
                // restaura fator de multiplicacao para 2 
                $fator = 1;
            }
            $fator++;
        }
        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            return $digito;
        } elseif ($r == 1){
            // esta rotina sofrer algumas altera��es para ajustar no layout do SICREDI
            $r_div = (int)($soma/11);
            $digito = ($soma - ($r_div * 11));
            return $digito;
        }
    }
    /**
     * [MontaLinhaDigitavel description]
     * @param [type] $codigo [description]
     */
    private function MontaLinhaDigitavel($codigo) {
        $p1 = substr($codigo, 0, 4);
        $p2 = substr($codigo, 19, 5);
        $p3 = $this->Modulo10("$p1$p2");
        $p4 = "$p1$p2$p3";
        $p5 = substr($p4, 0, 5);
        $p6 = substr($p4, 5);
        $campo1 = "$p5.$p6";

        $p1 = substr($codigo, 24, 10);
        $p2 = $this->Modulo10($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo2 = "$p4.$p5";

        $p1 = substr($codigo, 34, 10);
        $p2 = $this->Modulo10($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo3 = "$p4.$p5";

        $campo4 = substr($codigo, 4, 1);

        $p1 = substr($codigo, 5, 4);
        $p2 = substr($codigo, 9, 10);
        $campo5 = "$p1$p2";

        return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
    }
    /**
     * [Modulo10 description]
     * @param [type] $num [description]
     */
    private function Modulo10($num) { 
        $numtotal10 = 0;
        $fator = 2;

        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo (falor 10)
            $temp = $numeros[$i] * $fator; 
            $temp0=0;
            foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
            $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2; // intercala fator de multiplicacao (modulo 10)
            }
        }
        
        $resto = $numtotal10 % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }
        
        return $digito;    
    }
}