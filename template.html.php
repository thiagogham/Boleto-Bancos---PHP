<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Boleto</title>
<style>
*{
	padding: 1px;
	margin: 0;
}
#container{
    width:700px;
    font-family:arial;
    font-size:12px;
	border: 0;
	height:auto;
}
#container div{
	float:left;
	height: 34px auto;
	padding-left: 3px;
}
#container #recibo{
	height:auto;
}
#container #ficha_compensacao{
	height:auto;
}
#container .cabecalho{
	width:700px;
	padding: 0;
	height:auto;
}
p{
	padding:0;
	margin: 0;
	height:auto;
}
#container .cabecalho .banco_logo{
	float:left;
	width:160px;
	border: 0;
}
#container .cabecalho .banco_codigo{
	margin-top: 12px;
	float:left;
	width:70px;
	font-size:20px;
	font-weight:bold;
	text-align:center;
	border: 0;
	border-left:3px solid #000000;
	border-right:3px solid #000000;
}
#container .cabecalho .linha_digitavel{
	float:left;
	width:450px;
	font-size:12px;
	font-weight:bold; 
	text-align:right;
	margin-top: 10px;
}
#recibo .linha{
	border-left: 1px solid #000;
	border-top: 1px solid #000;
}
#recibo .item div{
	height:auto;
	padding:0;
	text-align: left !important;
}
.linha_corte{
	border-bottom:1px dotted #000;
	width:690px;
	text-align:right;
	height:0 !important;
	padding: 0 !important;
    margin: 0 !important;
}
/*Linha1*/
#recibo .linha .cedente{width:285px;border-right: 1px solid #000;}
#recibo .linha .agencia{width:130px;border-right: 1px solid #000;}
#recibo .linha .moeda{width:40px;border-right: 1px solid #000;text-align:center;}
#recibo .linha .qtd{width:30px;border-right: 1px solid #000; text-align:center;}
#recibo .linha .nosso_numero{width:180px; text-align:right; }
/*Linha2*/
#recibo .linha .num_doc{width:185px;border-right: 1px solid #000;}
#recibo .linha .cpf_cnpj{width:169px;border-right: 1px solid #000;}
#recibo .linha .vencimento{width:135px;border-right: 1px solid #000;}
#recibo .linha .valor{width:180px; text-align:right;}
/*Linha3*/
#recibo .linha .descontos{width:140px;border-right: 1px solid #000;}
#recibo .linha .outras_deducoes{width:120px;border-right: 1px solid #000;}
#recibo .linha .multa{width:90px;border-right: 1px solid #000;}
#recibo .linha .outros_acrescimos{width:135px;border-right: 1px solid #000;}
/*Linha4*/
#recibo .linha .sacado{width:690px; height: 50px;}

#recibo .linha .demonstrativo{width:480px;height:90px;}
#recibo .linha .autenticacao_mecanica{width:200px;text-align:right;}

#ficha_compensacao{
	margin-top: 25px;
	border: 0;
}
#ficha_compensacao .local_pagamento,#ficha_compensacao .mensagens,
#ficha_compensacao .cedente,        #ficha_compensacao .linha{
	width:490px;
	border-top: 1px solid #000;
	border-left: 1px solid #000;
	border-right: 1px solid #000;
}
#ficha_compensacao #colunaprincipal{
	border: 0!important;
	padding: 0;
	width:490px;
}
#ficha_compensacao .local_pagamento,#ficha_compensacao .mensagens,
#ficha_compensacao .cedente,        #ficha_compensacao .linha{
	clear:left; 
}             
#ficha_compensacao #colunaprincipal .mensagens{
	height:171px;
	border: 1px solid #000;
	padding-left: 8px;
}
#ficha_compensacao #colunaprincipal .data_doc{width:100px;border-right: 1px solid #000;}
#ficha_compensacao #colunaprincipal .num_doc{width:140px;border-right: 1px solid #000;}
#ficha_compensacao #colunaprincipal .espec_doc{width:70px;border-right: 1px solid #000;}
#ficha_compensacao #colunaprincipal .aceite{width:40px;border-right: 1px solid #000;}
#ficha_compensacao #colunaprincipal .dt_proc{width:90px;}
#ficha_compensacao #colunaprincipal .uso_banco{width:100px;border-right: 1px solid #000;}
#ficha_compensacao #colunaprincipal .carteira{width:80px;border-right: 1px solid #000;}
#ficha_compensacao #colunaprincipal .moeda{width:40px;border-right: 1px solid #000;}
#ficha_compensacao #colunaprincipal .qtd{width:70px;border-right: 1px solid #000;}
#ficha_compensacao #colunaprincipal .valor{width:110px; border:0;}
#ficha_compensacao #colunadireita{
	width:200px;
	border-top: 1px solid #000;
	padding:0; 
	margin:0;
}
#ficha_compensacao #colunadireita div{
	width:200px;
	text-align:right;
	border-bottom: 1px solid #000;
}
#ficha_compensacao #colunadireita .nos_numero{
	height: 35px;
}
#ficha_compensacao #colunadireita .valor_documento{
	height: 35px;
}
#ficha_compensacao #colunadireita p{
	text-align:left;
}
#ficha_compensacao #codigo_barras{
	width:690px;
	margin-top: 15px;
}
#ficha_compensacao #rodape{
	width:689px;
	border-bottom:1px solid #000;
	border-left: 1px solid #000;
	border-top: 1px solid #000;
	padding:0;
	margin-top: 2px;
}
#ficha_compensacao #sacado{
	width:410px;
	margin-bottom: 10px;
}
#autenticacao{
	width:275px;
	text-align:right;
}
</style>
</head>
<body>
  <div id="container">
        <!--DIV RECIBO DO SACADO-->
        <p style="font-size:8px; text-align:center;">
            <strong>
            	Instruções de Impressão<br>
                Imprima em impressora jato de tinta (ink jet) ou laser em qualidade normal ou alta (Não use modo econômico)<br>
            	Utilize folha A4 (210 x 297 mm) ou Carta (216 x 279 mm). Corte na linha indicada.<br>
            </strong>
        </p>
        <br>
        <p class="linha_corte">Recibo do Pagador</p>
        <div id="dados_vendedor" style="width: 110px; margin-top: 4px;">
                <img src="<? echo dirname( __FILE__ ); ?>/imagens/incorporasul.png" width="110"/>
        </div>
        <div style="margin-top: 4px;font-size:11px;">
                <?php echo $this->cedente; ?>
                <br>
                <?php echo $this->endereco_cedente; ?>
        </div>
        <br>
        <div id="recibo">
            <div class="cabecalho">
                <p class="banco_logo"><img width="158" src=<?php echo $this->imgboleto; ?> /></p>
                <p class="banco_codigo"><?php echo $this->codigo_banco; ?></p>
                <p class="linha_digitavel"><?php echo $this->linha_digitavel;?></p>
            </div>
            <!--Linha1-->
            <div class="linha">
                <!-- Cedente -->
                <div class="cedente item">
                    <div>Beneficiário</div>
                    <?php echo $this->cedente; ?>
                </div>
                <!-- Agência/Código do Cedente -->
                <div class="agencia item">
                    <div>Ag./Cod Beneficiário</div>
                    <?php echo $this->agencia_codigo; ?>
                </div>
                <!-- Espécie Moeda -->
                <div class="moeda item">
                    <div>Moeda</div>
                    	<?php echo $this->especie_moeda; ?>
                </div>
                <!-- Quantidade -->
                <div class="qtd item">
                    <div>Qtd.</div>
                     <?php echo $this->quantidade; ?>
                </div>
                <!-- Nosso Número -->
                <div class="nosso_numero item">
                    <div>Nosso Número</div>
                    <?php echo $this->nosso_numero; ?>
                </div>
            </div>
            <!--Linha 2-->
            <div class="linha">
                <!-- Número do Documento -->
                <div class="num_doc item">
                    <div>Número do Documento</div>
                    <?php echo $this->cd_titulo;?>
                </div>
                <!-- CPF/CNPJ -->
                <div class="cpf_cnpj item">
                    <div>CPF/CNPJ</div>
                    <?php echo $this->cnpj_cedente; ?>
                </div>
                <!-- Vencimento -->
                <div class="vencimento item">
                    <div>Vencimento</div>
                    <?php echo $this->dt_vencimento; ?>
                </div>
                <!-- Valor do Documento -->
                <div class="valor item">
                    <div>Valor do Documento</div>
                    <?php echo $this->valor_documento;?>
                </div>
            </div>
            <!--Linha 3-->
            <div class="linha">
                <div class="descontos item">
                    <div>(-) Desconto/Abatimento</div>
                </div>
                <div class="outras_deducoes item">
                    <div>(-) Outras Deduções</div>
                </div>
                <div class="multa item">
                    <div>(+) Mora/Multa</div>
                </div>
                <div class="outros_acrescimos item">
                    <div>(+) Outros Acréscimos</div>
                </div>
                <div class="valor item">
                    <div>(=) Valor Cobrado</div>
                </div>
            </div>
            <!--Linha 4-->
            <div class="linha">
                <div class="sacado item">
                    <div>Pagador</div>
                    <?php echo $this->sacado;?> - CPF/CNPJ: <?php echo $this->cpf_cnpj_sacado;?>
                </div>
            </div>
            <!--Linha 5-->
            <div class="linha">
                <div class="demonstrativo item">
                    <div>Demonstrativo</div>
                    <br>
                    <p><?php echo $this->demonstrativo;?>
                    <br>
                      <?php 
					  		if($this->codigo_banco == '041-8'){
								echo 'SAC BANRISUL-0800 646 1515 <br> OUVIDORIA BANRISUL-0800 644 2200';		
                            }
					  ?>
                    </p>
                </div>
                <div class="autenticacao_mecanica">
                    Autenticação Mecânica
                </div>
            </div>
            <p class="linha_corte">Corte na linha pontilhada</p>
        </div>
        <!--DIV FICHA DE COMPENSACAO-->
        <div id="ficha_compensacao">
            <div class="cabecalho">
                <p class="banco_logo"><img width="158" src=<?php echo $this->imgboleto; ?> /></p>
                <p class="banco_codigo"><?php echo $this->codigo_banco; ?></p>
                <p class="linha_digitavel"><?php echo $this->linha_digitavel;?></p>
            </div>   
            <div id="colunaprincipal" class="">
                <!--  linha1  -->
                    <div class="local_pagamento item">
                         <p>Local de Pagamento</p>
                         <?php 
						 		if($this->codigo_banco == '041-8'){
									echo 'Pagavel preferencialmente na rede Banrisul';
                                }else{
									echo $this->local_pagamento; 
                                }
						 ?>
                    </div>
                <!--  linha2  -->
                    <div class="cedente item">
                         <p>Beneficiário </p>
                         <?php echo $this->cedente.' - '.$this->cnpj_cedente; ?>
                    </div>
                <!--  linha3  -->
                <div class="linha">
                    <div class="data_doc item">
                        <p>Data do doc</p>
                         <?php echo $this->dt_documento; ?>
                    </div>
                    <div class="num_doc item">
                        <p>Número do documento</p>
                         <?php echo $this->cd_titulo; ?>
                    </div>
                    <div class="espec_doc item">
                        <p>Espécie Doc.</p>
                             <?php echo $this->especie_doc; ?>
                    </div>
                    <div class="aceite item">
                        <p>Aceite</p>
                            <?php echo $this->aceite; ?>
                    </div>
                    <div class="dt_proc item">
                        <p>Data proc</p>
                         <?php echo $this->dt_processamento; ?>
                    </div>
                </div>
                <!--  linha4  -->
                <div class="linha">
                    <div class="uso_banco item">
                        <p>Uso do Banco</p>
                        
                    </div>
                    <div class="carteira item">
                        <p>Carteira</p>
                         <?php echo $this->carteira; ?>
                    </div>
                    <div class="moeda item">
                        <p>Moeda</p>
                            <?php echo $this->especie_moeda; ?>
                    </div>
                    <div class="qtd item">
                        <p>Quantidade</p>
                        <?php echo $this->quantidade; ?>
                    </div>
                    <div class="valor item">
                        <p>(x) Valor</p>
                        <?php echo $this->vl_moeda; ?>
                    </div>
                </div>
                <div class="mensagens ">
                         <p>Instruções (Texto de responsabilidade do beneficiário)</p>
                         <?php
                            echo   $this->instr1.'<br>'
                                  .$this->instr2.'<br>' 
                                  .$this->instr3.'<br>' 
                                  .$this->instr4.'<br>' 
                                  .$this->instr5.'<br>'
                                  .$this->instr6.'<br>' 
                                  .$this->instr7; 
                        ?>
                </div>
            </div>
            <!--Coluna direita-->
            <div id="colunadireita" class="">
                <div class="">
                     <p>Vencimento</p>
                     <?php echo $this->dt_vencimento; ?>
                </div>
                <div class="">
                     <p>Agência / Código Beneficiário </p>
                     <?php echo $this->agencia_codigo;?>
                </div>
                <div class="nos_numero">
                     <p>Nosso número</p>
                     <?php echo $this->nosso_numero; ?>
                </div>
                <div class="valor_documento">
                     <p>(=) Valor do documento</p>
                     <?php echo $this->valor_documento; ?>
                </div>
                <div class="">
                     <p>(-) Desconto/Abatimento</p>
                     <?php echo $this->valor_desconto; ?>
                </div>
                <div class="">
                     <p>(-) Outras deduções</p>
                     
                </div>
                <div class="">
                     <p>(+) Mora/Multa</p>
                     
                </div>
                <div class="">
                     <p>(+) Outros Acréscimos</p>
                     
                </div>
                <div class="ultimo_valor">
                     <p>(=) Valor cobrado</p>
                </div>
            </div>
            <!--  sacado  -->
            <div id="rodape">
                <div id="sacado">
                    <p>Pagador</p>
                         <p style="padding-top: 5px;"><strong>
                            <?php echo $this->sacado;  ?> - CPF/CNPJ: <?php echo $this->cpf_cnpj_sacado;?>
                            <br>
                            <?php echo $this->endereco_sacado;  ?>
                         </strong></p>
                </div>
                <div id="autenticacao">Autenticação Mecânica - Ficha de Compensação</div>
                
            </div>
            <p><?php echo $this->outros; ?></p>
            <div id="codigo_barras" class="">
                    <?php
                        require_once dirname(__FILE__).'/Barcode.class.php';
                       echo Barcode::getHtml($this->codigo_barras);
                    ?>
            </div>
            <p class="linha_corte">Corte na linha pontilhada</p>
        <!--Encerra ficha de compensação-->    
        </div>
    </div>
</body>
</html>