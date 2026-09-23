



https://github.com/user-attachments/assets/68f909a0-9a76-4fff-97f1-5149b66e023f


# Gerenciador de Estacionamento

Sistema web para controle de vagas, entrada e saída de veículos e cálculo
automático do valor a pagar com base no tempo de permanência.

## Funcionalidades

| Página | Descrição |
|---|---|
| `index.php` | Painel com contadores (total, livres, ocupadas, entradas do dia) e mapa visual das vagas |
| `entrada.php` | Registra placa, modelo, cor e associa o veículo a uma vaga livre |
| `saida.php` | Busca veículo pela placa, mostra tempo decorrido em tempo real e valor estimado; ao confirmar, calcula o valor final e libera a vaga |
| `editar_registro.php` | Edita placa, modelo ou cor de um veículo que ainda está estacionado (registro ativo), sem mexer na vaga nem no horário de entrada |
| `registros.php` | Histórico de saídas finalizadas, com filtro por placa e total arrecadado |
| `vagas.php` | Cadastro e exclusão de vagas (Carro, Moto ou Caminhão) |

### CRUD por entidade

**Vagas** (tabela `vagas`)
- Create: formulário em `vagas.php`
- Read: listagem em `vagas.php` e mapa em `index.php`
- Update: status muda automaticamente ao entrar/sair um veículo
- Delete: botão **Excluir** em `vagas.php` (somente vagas livres)

**Registros de entrada/saída** (tabela `registros`)
- Create: formulário em `entrada.php`
- Read: lista de ativos em `saida.php` e histórico em `registros.php`
- Update: botão **Editar** em `saida.php`, que abre `editar_registro.php`
- Delete: finalização de saída marca o registro como `Finalizado` (não há exclusão definitiva pela interface)

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
├── editar_registro.php
├── registros.php
├── vagas.php
├── includes/
│   ├── header.php
│   └── footer.php
└── assets/
    ├── css/style.css
    └── js/script.js
```
