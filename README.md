# Controle de Estacionamento

Este sistema de controle de estacionamento foi desenvolvido utilizando HTML, CSS, JavaScript, PHP e MySQL.

## Funcionalidades

- **Entrada de Veículos:** Registra a entrada e gera ticket com data/hora.
- **Saída de Veículos:** Registra a saída, calcula tempo e valor de pagamento.
- **Listagem de Veículos Estacionados:** Permite visualizar os veículos que ainda se encontram estacionados.
- **Configuração de Preços:** O valor da hora é configurável no banco de dados.
- **Autenticação (para futuras melhorias):** Tela de login e gerenciamento de usuários (administradores e funcionários).

## Estrutura do Projeto

parking-control-system
├── README.md
├── sql
│   └── database.sql
├── src
│   ├── css
│   │   └── styles.css
│   ├── js
│   │   └── script.js
│   ├── php
│   │   ├── api.php
│   │   ├── config
│   │   │   └── config.php
│   │   └── views
│   │       └── index.html
│   └── index.php

## Configuração

1. **Banco de Dados:**  
   Importe o arquivo `sql/database.sql` no seu MySQL para criar o banco `estacionamento` e as tabelas necessárias.

2. **Configuração da Conexão:**  
   No arquivo `src/php/config/config.php`, verifique os parâmetros de conexão (host, usuário, senha, etc.).

3. **Servidor Local:**  
   Coloque a pasta `parking-control-system` na pasta do seu servidor (por exemplo, `C:\xampp\htdocs\`).

4. **Acesso:**  
   Abra o navegador com o endereço `http://localhost/parking-control-system/src/index.php` ou ajuste conforme sua estrutura.

## Tecnologias Utilizadas

- **Front-end:** HTML, CSS e JavaScript (jQuery)
- **Back-end:** PHP (PDO)
- **Banco de Dados:** MySQL

## Observações

- Ajuste os caminhos conforme sua necessidade.
- Futuras implementações poderão incluir autenticação de usuários e gestão administrativa.
