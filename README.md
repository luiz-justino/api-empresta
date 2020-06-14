## API EMPRESTA

<p align="center">
<a href="https://empresta.com.br/"><img src="https://empresta.com.br/wp-content/themes/empresta/assets/images/logo.svg"></a>
</p>

## Sobre o projeto
Este projeto foi feito para processo seletivo de Desenvolvedor Back-End PHP na empresa Empresta. O projeto consiste na criação de uma API REST para ser integrada nas aplicações web e mobiles, possibilitando simulações de empréstimos. A API deve considerar que será informado o valor desejado (campo obrigatório), quantidade de parcelas, instituições e convênios.

Foi utilizado o framework Laravel + linguagem PHP.

## Autor
Luiz Fernando Justino <http://linkedin.com.br/in/luizfernandojustino>

## Como utilizar?
Os passos abaixo contém as instruções para executar o projeto em seu computador.

1.Clone o projeto pelo link do github:

	git clone https://github.com/luiz-justino/api-empresta

2) Acesse a pasta principal do projeto 'api-empresta', criada após clonar o repositório.
Clique então com o botão direito do mouse e abra um terminal de sua preferência (Git Bash, Windows Power Shell, Prompt
de comando, Bash, etc.). Iremos agora digitar comandos para finalizar a configuração local do projeto.
Execute o comando abaixo para que sejam baixadas as dependências do projeto:

	composer install

3) Após isso as configurações finalizaram-se. Para executar o projeto acesse o caminho do seu projeto no navegador. Também é possível acessá-lo
por linha de comando executando no terminal aberto de dentro da pasta do projeto:
	php artisan serve
Após a execução ele mostrará o link de acesso: http://127.0.0.1:8000
o qual é só copiar e colar no navegador.

Aproveite o projeto!

## Funcionalidades
                    
### Estrutura de pastas
A estrutura de pastas se mantém a da estrutura padrão do framework Laravel versão 5.8, na qual o projeto foi criado.

### Rotas
A tabela abaixo contém as rotas, requisições, métodos e a funcionalidades atreladas ao projeto:

                    
|Rota                  |Requisição| Método   | Função                                                                                     |
|----------------------|----------|----------|--------------------------------------------------------------------------------------------|
|/                     |GET       |index     | Carrega a página inical 
|/api/convenios        |GET       |getAgreements            | Retorna listagem de convênios disponíveis. |
|/api/instituicoes     |GET       |getInstitutions          | Retorna listagem de instituições disponíveis. |
|/api/taxasInstituicoes|GET       |getInstitutionsFeels     | Carrega a listagem de taxas das instituições disponíveis.|
|/api/simularCredito   |POST      |creditSimulator          | Método que executa a simulação de crédito com os parâmetros informados sendo eles: valor (obrigatório), instituições, convênios e parcelas.|
