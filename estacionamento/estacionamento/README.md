# Gerenciador de Estacionamento

Sistema web para controle de vagas, entrada/saída de veículos e cálculo automático
do valor a pagar com base no tempo de permanência.

## Stack
- PHP 7.4+ (PDO)
- MySQL / MariaDB
- Bootstrap 5 (via CDN)
- JavaScript puro

## Instalação

1. Crie o banco de dados executando o script `banco_dados.sql`:
   ```
   mysql -u root -p < banco_dados.sql
   ```
2. Edite `config.php` com as credenciais do seu MySQL (`$DB_HOST`, `$DB_USER`, `$DB_PASS`).
3. Coloque a pasta `estacionamento/` no diretório do seu servidor (ex: `htdocs` do XAMPP,
   ou sirva com `php -S localhost:8000` dentro da pasta).
4. Acesse `index.php` no navegador.

## Funcionalidades

- **Painel (index.php)**: visão geral de vagas livres/ocupadas e mapa visual das vagas.
- **Registrar Entrada (entrada.php)**: cadastra placa, modelo, cor e associa a uma vaga livre.
- **Registrar Saída (saida.php)**: busca veículo pela placa, mostra tempo decorrido em
  tempo real (relógio JS) e valor estimado; ao confirmar, calcula o valor final e libera a vaga.
- **Histórico (registros.php)**: lista todas as saídas finalizadas, com filtro por placa
  e total arrecadado.
- **Vagas (vagas.php)**: cadastro e exclusão de vagas (tipo Carro/Moto/Caminhão).

## Regra de cobrança (tabela `tarifas`)

- Primeira hora: R$ 5,00 (valor cheio, mesmo que o veículo fique poucos minutos)
- Cada hora adicional (ou fração): R$ 3,00

Os valores podem ser alterados diretamente na tabela `tarifas` do banco de dados.

## Estrutura de pastas

```
estacionamento/
├── banco_dados.sql
├── config.php
├── index.php
├── entrada.php
├── saida.php
├── registros.php
├── vagas.php
├── includes/
│   ├── header.php
│   └── footer.php
└── assets/
    ├── css/style.css
    └── js/script.js
```
