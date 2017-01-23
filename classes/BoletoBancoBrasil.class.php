<?php 
/*
*   Descrição:  Classe para geração de Boletos para BancoBrasil
*   Autor: Marielle S. Wronka
*   Versão: 0.1  14-12-2016                                         
*/
class BoletoBancoBrasil{
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
        $cart	            = sprintf("%02d",$info["codigo_carteira"]);
        $nosso_numero       = $info["nosso_numero"];
        $cd_convenio        = $info["cd_convenio"];
        $nn                 = $nosso_numero;
        $agencia_dv         = substr($info["agencia"], -1);
        $agencia		    = substr($info["agencia"], 0, 4);
        $contacedente_dv    = substr($info["conta_cedente"], -1);
        $contacedente	    = substr($info["conta_cedente"],0,6);
        $contacedente	    = sprintf("%06d",$contacedente);
        $contacedente_sdac  = substr($contacedente, 0, strlen($contacedente)-1);
        $contacedente_sdac  = sprintf("%08d",$contacedente_sdac);
        $moeda		        = '9';
        $codbank	        = '001';
        
        $AC_ACCN           = $this->Modulo10("$agencia$contacedente_sdac$cart$nosso_numero");
        $DAC_ACC            = $this->Modulo10("$agencia$contacedente_sdac");
        $xnn                = "$agencia$contacedente_sdac$cart$nosso_numero";
        $DAC_NN             = $this->Modulo10("$agencia$contacedente_sdac$cart$nosso_numero");

        $dv = $this->Modulo11("$codbank$moeda$dv$fatorvcto$valor$cd_convenio$nosso_numero$agencia$contacedente_sdac$cart");
        $num = "$codbank$moeda$dv$fatorvcto$valor$cd_convenio$nosso_numero$agencia$contacedente_sdac$cart";

        $linha_digitavel	= $this->Montalinha($num);

        $codigo_banco		= $this->GeraCodigoBanco($codbank);
        $nosso_numero       = $this->getNossoNumero($cart, $cd_convenio, $nosso_numero);
        $agencia_codigo     = $agencia.'-'.$agencia_dv.'/'.substr($info["conta_cedente"],0,4).'-'.$contacedente_dv; 

        return array(
            "dt_vencimento"   => $dt_vencimento,
            "linha_digitavel" => $linha_digitavel,
            "agencia_codigo"  => $agencia_codigo,
            "codigo_barras"   => $num,
            "codigo_banco"    => $codigo_banco,
            "nosso_numero"    => $nosso_numero,
            "xnn"             => $xnn,
            "especie_doc"     => 'DS'
        );
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */


    public function getCampoLivre()
    {
        $length = strlen($this->getConvenio());
        $nossoNumero = $this->gerarNossoNumero();
        // Nosso número sem o DV - repare que ele só vem com DV quando o mesmo é menor que 17 caracteres
        // Então removemos o dígito (e o traço) apenas quando seu tamanho for menor que 17 caracteres
        strlen($this->getNossoNumero()) < 17 and $nossoNumero = substr($nossoNumero, 0, -2);

        // Sequencial do cliente com 17 dígitos
        // Apenas para convênio com 6 dígitos, modalidade sem registro - carteira 16 e 18 (definida para 21)
        if (strlen($this->getSequencial()) > 10) {
            if ($length == 6 and $this->getCarteira() == 21) {
                // Convênio (6) + Nosso número (17) + Carteira (2)
                return self::zeroFill($this->getConvenio(), 6) . $nossoNumero . '21';
            } else {
                throw new Exception('Só é possível criar um boleto com mais de 10 dígitos no nosso número quando a carteira é 21 e o convênio possuir 6 dígitos.');
            }
        }

        switch ($length) {
            case 4:
            case 6:
                // Nosso número (11) + Agencia (4) + Conta (8) + Carteira (2)
                return $nossoNumero . self::zeroFill($this->getAgencia(), 4) . self::zeroFill($this->getConta(), 8) . self::zeroFill($this->getCarteira(), 2);
            case 7:
                // Zeros (6) + Nosso número (17) + Carteira (2)
                return '000000' . $nossoNumero . self::zeroFill($this->getCarteira(), 2);
        }

        throw new Exception('O código do convênio precisa ter 4, 6 ou 7 dígitos!');
    }

    private function Montalinha($codigo){
        // Posição  Conteúdo
    // 1 a 3    Número do banco
    // 4        Código da Moeda - 9 para Real
    // 5        Digito verificador do Código de Barras
    // 6 a 19   Valor (12 inteiros e 2 decimais)
    // 20 a 44  Campo Livre definido por cada banco
    // 1. Campo - composto pelo código do banco, código da moéda, as cinco primeiras posições
    // do campo livre e DV (modulo10) deste campo
    $p1 = substr($codigo, 0, 4);
    $p2 = substr($codigo, 19, 5);
    $p3 = $this->Modulo10("$p1$p2");
    $p4 = "$p1$p2$p3";
    $p5 = substr($p4, 0, 5);
    $p6 = substr($p4, 5);
    $campo1 = "$p5.$p6";
    // 2. Campo - composto pelas posiçoes 6 a 15 do campo livre
    // e livre e DV (modulo10) deste campo
    $p1 = substr($codigo, 24, 10);
    $p2 = $this->Modulo10($p1);
    $p3 = "$p1$p2";
    $p4 = substr($p3, 0, 5);
    $p5 = substr($p3, 5);
    $campo2 = "$p4.$p5";
    // 3. Campo composto pelas posicoes 16 a 25 do campo livre
    // e livre e DV (modulo10) deste campo
    $p1 = substr($codigo, 34, 10);
    $p2 = $this->Modulo10($p1);
    $p3 = "$p1$p2";
    $p4 = substr($p3, 0, 5);
    $p5 = substr($p3, 5);
    $campo3 = "$p4.$p5";

    // 4. Campo - digito verificador do codigo de barras
    $campo4 = substr($codigo, 4, 1);
    // 5. Campo composto pelo valor nominal pelo valor nominal do documento, sem
    // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
    // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
    $campo5 = substr($codigo, 5, 14);
    return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
    }
    /*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
    private function DigitoVerificador($numero){
        $digito = $this->Modulo11($numero);
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
        $parte2 = $this->Modulo11($parte1, 9, 0);
        return $parte1 . "-" . $parte2;
    }
    /**
     * [formataCampoNumerico description]
     * @param  [type] $vl_campo [description]
     * @param  [type] $tamanho  [description]
     * @return [type]           [description]
     */
    private function formataCampoNumerico($vl_campo, $tamanho){
        return str_pad($vl_campo, $tamanho, '0', STR_PAD_LEFT);
    }
    /**
     * [getNossoNumero description]
     * @param  [type] $cd_convenio  [description]
     * @param  [type] $nosso_numero [description]
     * @return [type]               [description]
     */
    private function getNossoNumero($cd_carteira, $cd_convenio, $nosso_numero){
        $numero = null;
        switch (strlen($cd_convenio)) {
            case 4: // Convênio de 4 dígitos, são 11 dígitos no nosso número
                $numero = $this->formataCampoNumerico($cd_convenio, 4) . $this->formataCampoNumerico($nosso_numero, 7);
                break;
            case 6:// Convênio de 6 dígitos, são 11 dígitos no nosso número
                if ($cd_carteira == 21) {// Exceto no caso de ter a carteira 21, onde são 17 dígitos
                    $numero = $this->formataCampoNumerico($nosso_numero, 17);
                } else {
                    $numero = $this->formataCampoNumerico($cd_convenio, 6) . $this->formataCampoNumerico($nosso_numero, 5);
                }
                break;
            case 7: // Convênio de 7 dígitos, são 17 dígitos no nosso número
                $numero = $this->formataCampoNumerico($cd_convenio, 7) . $this->formataCampoNumerico($nosso_numero, 10);
                break;
            default:
                $numero = $this->formataCampoNumerico($nosso_numero, 17);
        }

        // Quando o nosso número tiver menos de 17 dígitos, colocar o dígito
        if (strlen($numero) < 17) {
            $numero .= $this->modulo11($numero);
        }

        return $numero;
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
    private function Modulo11($num, $base=9, $r=0){
        $soma = 0;
        $fator = 2;
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
        }elseif ($r == 1){
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