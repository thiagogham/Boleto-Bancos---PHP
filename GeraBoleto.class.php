<?php 
/*
*   Descrição:  Classe para geração de Boletos Bancos
*   Autor: Thiago R. Gham
*   Versão: 1.2  06-02-2016
	Ex Uso:

	$gerador = new GeraBoleto($cd_banco);
	$a_dados_boleto = array(
					    'imgboleto' => $nm_imagem,
					    'valor_documento' => $valor_documento,
					    'valor_acrescimo' => $vl_acrescimos,
					    'valor_desconto' => $vl_desconto,
					    'valor_deducao' => $valor_deducao,
					    'valor_multa' => $valor_multa,
					    'valor_cobrado' => $valor_documento,
					    'cd_titulo' => $cd_titulo,
					    'cd_bloqueto' => $cd_bloqueto,
					    'dt_vencimento' => $dt_vencto,
					    'dt_documento' => date('d/m/Y'),
					    'dt_processamento' => date('d/m/Y'),
					    'especie_doc' => '',
					    'outros' => '',
					    'local_pagamento' => "Pagável em Qualquer Banco Até Vencto, Após somente no $nm_banco",
					    'quantidade' => '',
					    'vl_moeda' => '',
					    'aceite' => '',
					    'carteira' => $cd_carteira,
					    'especie_moeda' => 'REAL',
					    'demonstrativo' => $historico,
					    'instr1' => $instr1,
					    'instr2' => $instr2,
					    'instr3' => $instr3,
					    'instr4' => $instr4,
					    'instr5' => $instr5,
					    'instr6' => $instr6,
					    'instr7' => $instr7,
					    'sacado' => $sacado,
					    'cpf_cnpj_sacado' => $cpf_cnpj_sacado,
					    'endereco_sacado' => $nm_endersacado,
					    'cedente' => $nm_cedente,
					    'cd_ag_cedente' => $cd_ag_cedente,
					    'cd_co_cedente' => $cd_co_cedente,
					    'cnpj_cedente' => $cnpj_cedente,
					    'endereco_cedente' => $endereco_empresa);

	$gerador->GeraBoleto($a_dados_boleto);
	$nm_arquivo = $arquivo = $gerador->EnviaBoletoEmail();
	$a_sms 	    = $gerador->EnviaBoletoSms();
*/
class GeraBoleto{

		public $imgboleto 		= '';
		public $valor_documento = '';
		public $valor_acrescimo = '';
		public $valor_desconto 	= '';
		public $valor_deducao 	= '';
		public $valor_multa 	= '';
		public $valor_cobrado 	= '';

		public $cd_titulo 		 = '';
		public $cd_bloqueto      = '';
		public $dt_vencimento 	 = '';
		public $dt_documento 	 = '';
		public $dt_processamento = '';
		public $especie_doc 	 = '';
		public $outros 			 = '';
		public $local_pagamento  = '';
		public $quantidade 		 = '';
		public $vl_moeda 		 = '';
		public $aceite 			 = '';
		public $carteira 		 = '';
		public $especie_moeda 	 = '';
		public $demonstrativo 	 = '';

		public $instr1 = '';
		public $instr2 = '';
		public $instr3 = '';
		public $instr4 = '';
		public $instr5 = '';
		public $instr6 = '';
		public $instr7 = '';

		public $sacado 			 = '';
		public $cpf_cnpj_sacado  = '';
		public $endereco_sacado  = '';

		public $cedente 		 = '';
		public $cd_ag_cedente 	 = '';
		public $cd_co_cedente 	 = '';
		public $cnpj_cedente 	 = '';
		public $endereco_cedente = '';

		public $linha_digitavel	= '';
		public $agencia_codigo	= '';
		public $codigo_barras	= '';
		public $codigo_banco	= '';
		public $nosso_numero	= '';
		
		private $BANCO;
		private $diretorio_temp = "/var/tmp";
	/*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
	function __construct($cd_banco) {
		$this->BANCO = $cd_banco;
	}
	/*
    *   Descrição: Armazena linhas do registro.
    *   @param string $linha String contendo a linha.
    *   @return 
    */
	public function GeraBoleto($a_dados){

		$this->imgboleto 		= dirname( __FILE__ )."/imagens/".$a_dados['imgboleto'];
		$this->valor_documento 	= $a_dados['valor_documento'];/* Valor do documento */
		$this->valor_acrescimo 	= $a_dados['valor_acrescimo'];/* Valor do Acrescimos*/
		$this->valor_desconto  	= $a_dados['valor_desconto'];/* Valor Descontos*/
		$this->valor_deducao 	= $a_dados['valor_deducao'];/* Valor deducoes*/
		$this->valor_multa 		= $a_dados['valor_multa'];/* Valor mora / valor_multa*/
		$this->valor_cobrado 	= $a_dados['valor_cobrado'];/* Valor Cobrado*/

		$this->cd_convenio 		= $a_dados['cd_convenio'];/* numero documento  */
		$this->cd_titulo 		= $a_dados['cd_titulo'];/* numero documento  */
		$this->cd_bloqueto     	= $a_dados['cd_bloqueto'];/* numero bloqueto nosso numero  */
		$this->dt_vencimento 	= $a_dados['dt_vencimento'];/* vencimento */
		$this->dt_documento 	= $a_dados['dt_documento'];/* data do documento */
		$this->dt_processamento = $a_dados['dt_processamento'];/* data do processamento */		
		$this->especie_doc 		= utf8_encode($a_dados['especie_doc']);/* especie documento */
		$this->outros 			= utf8_encode($a_dados['outros']);/* Outros */
		$this->local_pagamento 	= trim($a_dados['local_pagamento']);/* Local de Pagamento  */
		$this->quantidade 		= $a_dados['quantidade'];/* quantidade */
		$this->vl_moeda 		= $a_dados['vl_moeda'];/* valor da moeda */
		$this->aceite 			= utf8_encode($a_dados['aceite']);/*Aceite  */
		$this->carteira 		= utf8_encode($a_dados['carteira']);/*carteira  */
		$this->especie_moeda 	= utf8_encode($a_dados['especie_moeda']);/*Especificacao moeda  */
		$this->demonstrativo 	= utf8_encode(trim($a_dados['demonstrativo']));/* Local de Pagamento  */

		$this->instr1 			= utf8_encode(trim($a_dados['instr1']));/* instrucoes */
		$this->instr2 			= utf8_encode(trim($a_dados['instr2']));
		$this->instr3 			= utf8_encode(trim($a_dados['instr3']));
		$this->instr4 			= utf8_encode(trim($a_dados['instr4']));
		$this->instr5 			= utf8_encode(trim($a_dados['instr5']));
		$this->instr6 			= utf8_encode(trim($a_dados['instr6']));
		$this->instr7 			= utf8_encode(trim($a_dados['instr7']));

		$this->sacado 			= utf8_encode(trim($a_dados['sacado']));/* Sacado devedor Cliente*/
		$this->cpf_cnpj_sacado 	= utf8_encode(trim($a_dados['cpf_cnpj_sacado']));/* CPF_cnpj_sacado Sacado Cliente */
		$this->endereco_sacado 	= utf8_encode(trim($a_dados['endereco_sacado']));/* Endereço Sacado*/

		$this->cedente 			= utf8_encode(trim($a_dados['cedente']));/* cedente empresa*/
		$this->cd_ag_cedente 	= $a_dados['cd_ag_cedente'];/* cedente empresa*/
		$this->cd_co_cedente 	= $a_dados['cd_co_cedente'];/* cedente empresa*/
		$this->cnpj_cedente 	= utf8_encode(trim($a_dados['cnpj_cedente']));/* cnpj_cedente empresa*/
		$this->endereco_cedente = utf8_encode(trim($a_dados['endereco_cedente']));/* cnpj_cedente empresa*/

		$a_dados_boleto	= array(
            "codigo_carteira"	=> $this->carteira,
            "valor_documento"	=> $this->valor_documento, 
            "dt_vencimento"		=> $this->dt_vencimento, 
            "nosso_numero"		=> $this->cd_bloqueto,
            "agencia"			=> $this->cd_ag_cedente, 
            "conta_cedente"		=> $this->cd_co_cedente,
            "cd_convenio"		=> $this->cd_convenio
        );

        /*
		*	Identifica o Banco e Gera Dados
		*/	
		switch ($this->BANCO) {
			case '1':
				require_once dirname(__FILE__).'/classes/BoletoBancoBrasil.class.php';
				$Gerador = new BoletoBancoBrasil();
				$a_dados_banco	= $Gerador->GeraDados($a_dados_boleto);
				break;
			case '41':
				require_once dirname(__FILE__).'/classes/BoletoBanrisul.class.php';
				$Gerador = new BoletoBanrisul();
				$a_dados_banco	= $Gerador->GeraDados($a_dados_boleto);
				break;
			case '041':
				require_once dirname(__FILE__).'/classes/BoletoBanrisul.class.php';
				$Gerador = new BoletoBanrisul();
				$a_dados_banco	= $Gerador->GeraDados($a_dados_boleto);
				break;
			case '341':
				require_once dirname(__FILE__).'/classes/BoletoItau.class.php';
				$Gerador = new BoletoItau();
				$a_dados_banco	= $Gerador->GeraDados($a_dados_boleto);
				break;
			case '237':
				require_once dirname(__FILE__).'/classes/BoletoBradesco.class.php';
				$Gerador = new BoletoBradesco();
				$a_dados_banco	= $Gerador->GeraDados($a_dados_boleto);
				break;
			case '748':
				require_once dirname(__FILE__).'/classes/BoletoSicredi.class.php';
				$Gerador = new BoletoSicredi();
				$a_dados_banco	= $Gerador->GeraDados($a_dados_boleto);
				break;
			default:
				die('ERRO: Nenhum Banco Identificado.');
				break;
		}
		
		$this->linha_digitavel = $a_dados_banco["linha_digitavel"];
		$this->agencia_codigo  = $a_dados_banco["agencia_codigo"];
		$this->codigo_barras   = $a_dados_banco["codigo_barras"];
		$this->codigo_banco	   = $a_dados_banco["codigo_banco"];
		$this->nosso_numero	   = $a_dados_banco["nosso_numero"];
		$this->dt_vencimento   = $a_dados_banco["dt_vencimento"];
	}
	/*
    *   Descrição: Emprime Boleto PDF 
    *   @param 
    *   @return 
    */
	public function EmprimeBoleto(){
		ob_end_clean();
		ob_start();
		require_once dirname(__FILE__).'/mpdf/mpdf.php';
		require dirname(__FILE__).'/template.html.php';
		$mpdf = new mPDF('','', 0, '', 10, 10, 15, 8, 8, 8);
		$mpdf->WriteHTML(ob_get_contents());
		ob_end_clean();
		ob_clean();
		$retorno = new stdClass();
		$retorno->boleto 		= $mpdf;
		$retorno->nosso_numero  = $this->nosso_numero;
		return $retorno;
		exit;
	}
 	/*
    *   Descrição: Armazena dados necessários para SMS
    *   @param 
    *   @return array 
    */
	public function BoletoSms(){
		return array('cd_titulo' 	 	=> $this->cd_titulo,
					 'nosso_numero'     => $this->nosso_numero,
					 'linha_digitavel' 	=> $this->linha_digitavel,
					 'valor_documento' 	=> $this->valor_documento,
					 'dt_vencimento'	=> $this->dt_vencimento);
	}
	/*
    *   Descrição: Gera boleto e salva em PDF
    *   @param 
    *   @return texto Caminho do arquivo gerado (boleto)
    */ 
	public function BoletoEmail(){
		ob_end_clean();
		ob_start();
		require_once dirname(__FILE__).'/mpdf/mpdf.php';
		require dirname(__FILE__).'/template.html.php';
		$mpdf = new mPDF('','', 0, '', 10, 10, 15, 8, 8, 8);
		$mpdf->WriteHTML(ob_get_contents());
		ob_end_clean();
		ob_clean();
		$arquivo = dirname(__FILE__)."/boletos/T$this->cd_titulo-B$this->cd_bloqueto.pdf";
		$mpdf->Output($arquivo);
		chmod($arquivo,0777);
		$retorno = new stdClass();
		$retorno->arquivo 		= $arquivo;
		$retorno->nosso_numero  = $this->nosso_numero;
		return $retorno;
	}
	/*
    *   Descrição: Gera boleto e salva em PDF
    *   @param 
    *   @return texto Caminho do arquivo gerado (boleto)
    */
	public function BoletoArquivo(){
		ob_end_clean();
		ob_start();
		require_once dirname(__FILE__).'/mpdf/mpdf.php';
		require dirname(__FILE__).'/template.html.php';
		$mpdf = new mPDF('','', 0, '', 10, 10, 15, 8, 8, 8);
		$mpdf->WriteHTML(ob_get_contents());
		ob_end_clean();
		ob_clean();
		$arquivo = $this->diretorio_temp."/T$this->cd_titulo-B$this->cd_bloqueto.pdf";
		$mpdf->Output($arquivo);
		chmod($arquivo,0777);
		$retorno = new stdClass();
		$retorno->arquivo 		= $arquivo;
		$retorno->nosso_numero  = $this->nosso_numero;
		return $retorno;
	}
}