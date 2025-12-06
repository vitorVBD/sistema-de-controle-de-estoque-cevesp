# 📦 Sistema de Controle de Estoque - CEVESP

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**Sistema completo de gerenciamento de estoque com controle de lotes, movimentações, alertas inteligentes e notificações em tempo real**

[Funcionalidades](#-funcionalidades) • [Instalação](#-instalação) • [Documentação](#-documentação) • [Contribuindo](#-contribuindo)

</div>

---

## 📋 Índice

- [Sobre o Projeto](#-sobre-o-projeto)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias Utilizadas](#-tecnologias-utilizadas)
- [Requisitos do Sistema](#-requisitos-do-sistema)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Funcionalidades Detalhadas](#-funcionalidades-detalhadas)
- [Comandos Disponíveis](#-comandos-disponíveis)
- [Estrutura do Banco de Dados](#-estrutura-do-banco-de-dados)
- [Sistema de Notificações](#-sistema-de-notificações)
- [Relatórios](#-relatórios)
- [Contribuindo](#-contribuindo)
- [Licença](#-licença)

---

## 🎯 Sobre o Projeto

Sistema de controle de estoque desenvolvido para o **CEVESP** (Centro de Veterinária e Estudos de Pequenos Animais), oferecendo uma solução completa para gerenciamento de inventário com foco em:

- ✅ Controle preciso de estoque com sistema de lotes
- ✅ Gestão de validade de produtos
- ✅ Alertas inteligentes baseados em consumo (MMC)
- ✅ Notificações em tempo real via WebSockets
- ✅ Relatórios em PDF
- ✅ Interface moderna e responsiva

---

## ✨ Funcionalidades

### 📊 **Dashboard**
- Visão geral do estoque com métricas em tempo real
- Contadores de itens por status (normal, aproximando, mínimo)
- Histórico de movimentações recentes
- Acesso rápido às principais funcionalidades

### 📦 **Gestão de Itens**
- Cadastro, edição e exclusão de itens
- Controle de quantidade atual e estoque mínimo
- Unidade de medida personalizada
- Filtros por status de estoque:
  - Estoque baixo (≤ mínimo)
  - Estoque aproximando (1-2 unidades acima do mínimo)
  - Estoque normal
- Visualização de lotes por item
- Histórico de movimentações por item
- Remoção automática de lotes vencidos

### 🔄 **Movimentações**
- Registro de entradas e saídas
- Sistema FIFO (First In First Out) para saídas
- Controle automático de lotes por validade
- Rastreamento de responsável por movimentação
- Observações detalhadas
- Histórico completo de todas as movimentações

### 📅 **Controle de Lotes**
- Gestão de lotes com data de validade
- Criação automática de lotes em entradas
- Remoção automática de lotes vencidos
- Alertas de validade próxima (30 dias)
- Visualização ordenada por validade

### 🔔 **Sistema de Notificações**
- Notificações em tempo real via WebSockets (Laravel Reverb)
- Alertas de estoque mínimo
- Alertas de validade próxima
- Sugestões de estoque mínimo baseadas em MMC (Média Mensal de Consumo)
- Interface de notificações com filtros
- Marcação de notificações como lidas

### 📈 **Análise Inteligente (MMC)**
- Cálculo automático da Média Mensal de Consumo
- Sugestão de estoque mínimo ideal
- Alertas quando estoque mínimo está abaixo do sugerido
- Baseado nos últimos 90 dias de consumo

### 📄 **Relatórios em PDF**
- **Estoque Crítico**: Itens com estoque abaixo do mínimo
- **Consumo por Período**: Análise de saídas em período específico
- **Descarte e Perdas**: Movimentações de perda/descarte
- **Validade Próxima**: Lotes que vencem nos próximos 90 dias

### 👥 **Gestão de Usuários**
- Sistema de autenticação
- Controle de acesso por roles (Administrador/Usuário)
- CRUD completo de usuários (apenas administradores)
- Proteção de rotas sensíveis

---

## 🛠 Tecnologias Utilizadas

### Backend
- **Laravel 12.x** - Framework PHP
- **PHP 8.2+** - Linguagem de programação
- **SQLite** - Banco de dados (configurável para MySQL/PostgreSQL)

### Frontend
- **TailwindCSS 4.0** - Framework CSS
- **Vite 7.x** - Build tool
- **Laravel Echo** - WebSocket client
- **Axios** - Cliente HTTP

### Bibliotecas e Ferramentas
- **Laravel Reverb** - Servidor WebSocket
- **DomPDF (barryvdh/laravel-dompdf)** - Geração de PDFs
- **Carbon** - Manipulação de datas
- **Laravel Pint** - Code style fixer

---

## 📋 Requisitos do Sistema

- **PHP**: 8.2 ou superior
- **Composer**: 2.x ou superior
- **Node.js**: 18.x ou superior
- **NPM**: 9.x ou superior
- **Extensões PHP**:
  - BCMath
  - Ctype
  - cURL
  - DOM
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PCRE
  - PDO
  - Tokenizer
  - XML

---

## 🚀 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/aplicacao-controle-de-estoque-cevesp.git
cd aplicacao-controle-de-estoque-cevesp
```

### 2. Instale as dependências PHP

```bash
composer install
```

### 3. Instale as dependências Node.js

```bash
npm install
```

### 4. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure o banco de dados

Edite o arquivo `.env` e configure as variáveis de banco de dados:

```env
DB_CONNECTION=sqlite
# ou para MySQL/PostgreSQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=nome_do_banco
# DB_USERNAME=usuario
# DB_PASSWORD=senha
```

### 6. Execute as migrações

```bash
php artisan migrate
```

### 7. Compile os assets

```bash
npm run build
```

### 8. Inicie o servidor

```bash
php artisan serve
```

A aplicação estará disponível em `http://localhost:8000`

---

## ⚙️ Configuração

### Configuração do WebSocket (Laravel Reverb)

Para habilitar notificações em tempo real:

1. **Instale o Reverb** (já incluído no projeto):
```bash
php artisan reverb:install
```

2. **Configure o `.env`**:
```env
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

BROADCAST_CONNECTION=reverb

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

3. **Gere as chaves**:
```bash
php artisan reverb:generate-app-id
```

4. **Inicie o servidor Reverb** (em um terminal separado):
```bash
php artisan reverb:start
```

### Configuração de Agendamento de Tarefas

O sistema já está configurado para verificar alertas automaticamente. O comando `estoque:checar-alertas` está agendado para executar **diariamente** em `bootstrap/app.php`.

Para que o agendador funcione em produção:

1. **Adicione ao crontab** do servidor:
```bash
* * * * * cd /caminho/para/projeto && php artisan schedule:run >> /dev/null 2>&1
```

Isso garantirá que os alertas sejam verificados automaticamente todos os dias.

---

## 📁 Estrutura do Projeto

```
aplicacao-controle-de-estoque-cevesp/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── ChecarAlertasEstoque.php    # Comando para verificar alertas
│   ├── Events/
│   │   └── NotificacaoCriada.php           # Evento de notificação
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Autenticação
│   │   │   ├── DashboardController.php     # Dashboard
│   │   │   ├── ItemController.php          # Gestão de itens
│   │   │   ├── MovimentacaoController.php  # Movimentações
│   │   │   ├── NotificacaoController.php    # Notificações
│   │   │   ├── PdfController.php            # Relatórios PDF
│   │   │   └── UsuarioController.php        # Gestão de usuários
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Item.php                        # Model de itens
│   │   ├── Lote.php                        # Model de lotes
│   │   ├── Movimentacao.php                # Model de movimentações
│   │   ├── Notificacao.php                 # Model de notificações
│   │   └── User.php                        # Model de usuários
│   └── Providers/
├── config/
├── database/
│   ├── migrations/                        # Migrações do banco
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   │   ├── app.js                         # Configuração Echo/WebSocket
│   │   └── echo.js                        # Configuração Echo
│   └── views/
│       ├── auth/                          # Views de autenticação
│       ├── dashboard/                     # Views do dashboard
│       ├── itens/                         # Views de itens
│       ├── layouts/                       # Layouts principais
│       ├── movimentacoes/                  # Views de movimentações
│       ├── notificacoes/                  # Views de notificações
│       ├── relatorios/                    # Views de relatórios
│       └── usuarios/                      # Views de usuários
├── routes/
│   └── web.php                            # Rotas da aplicação
├── storage/
└── tests/
```

---

## 📖 Funcionalidades Detalhadas

### Gestão de Itens

O sistema permite gerenciar itens de estoque com as seguintes características:

- **Nome**: Identificação do item
- **Quantidade Atual**: Quantidade disponível no estoque
- **Estoque Mínimo**: Quantidade mínima para alertas
- **Unidade de Medida**: Unidade de medida do item (ex: kg, litros, unidades)

**Filtros Disponíveis**:
- Todos os itens
- Estoque baixo (quantidade ≤ estoque mínimo)
- Estoque aproximando (1-2 unidades acima do mínimo)
- Estoque normal (acima do mínimo + 2)

### Sistema de Lotes

Cada entrada de estoque cria um lote com:
- Quantidade específica
- Data de validade
- Controle automático de vencimento

**Funcionalidades**:
- Criação automática em entradas
- Remoção automática de lotes vencidos
- Visualização ordenada por validade (mais próximos primeiro)
- Alertas de validade próxima (30 dias)

### Movimentações

**Entrada**:
- Seleção do item
- Quantidade a adicionar
- Data de validade (opcional, padrão: 1 ano)
- Observações
- Criação automática de lote

**Saída**:
- Seleção do item (apenas com estoque disponível)
- Quantidade a remover
- Observações
- Sistema FIFO (remove dos lotes mais antigos primeiro)

### Análise MMC (Média Mensal de Consumo)

O sistema calcula automaticamente:

1. **MMC**: Média mensal de consumo baseada nas saídas dos últimos 90 dias
   ```
   MMC = Σ Saídas (90 dias) / 3
   ```

2. **Estoque Mínimo Sugerido**: Baseado na MMC
   ```
   Sugestão = MMC × 1.5 (cobre 1,5 mês de consumo)
   ```

3. **Alertas**: Quando o estoque mínimo atual está 25% abaixo do sugerido

---

## 🎮 Comandos Disponíveis

### Comandos Artisan

```bash
# Verificar alertas de estoque manualmente
php artisan estoque:checar-alertas

# Executar migrações
php artisan migrate

# Criar usuário (via tinker ou seeder)
php artisan tinker

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Iniciar servidor Reverb (WebSocket)
php artisan reverb:start
```

### Comandos NPM

```bash
# Desenvolvimento (com hot reload)
npm run dev

# Compilar para produção
npm run build

# Executar servidor e Vite simultaneamente
npm run serve
```

### Comandos Composer

```bash
# Instalar dependências
composer install

# Atualizar dependências
composer update

# Executar testes
composer test

# Executar setup completo
composer setup
```

---

## 🗄 Estrutura do Banco de Dados

### Tabelas Principais

#### `users`
- `id` - ID do usuário
- `name` - Nome completo
- `username` - Nome de usuário único
- `email` - E-mail único
- `password` - Senha criptografada
- `role` - Função (administrador/usuario)
- `timestamps`

#### `items`
- `id` - ID do item
- `nome` - Nome do item
- `quantidade_atual` - Quantidade disponível
- `estoque_minimo` - Quantidade mínima
- `unidade_medida` - Unidade de medida
- `timestamps`

#### `lotes`
- `id` - ID do lote
- `item_id` - Referência ao item
- `quantidade` - Quantidade do lote
- `data_validade` - Data de validade
- `timestamps`

#### `movimentacoes`
- `id` - ID da movimentação
- `item_id` - Referência ao item
- `tipo_movimentacao` - 'entrada' ou 'saida'
- `quantidade` - Quantidade movimentada
- `responsavel` - Nome do responsável
- `observacoes` - Observações da movimentação
- `timestamps`

#### `notificacoes`
- `id` - ID da notificação
- `item_id` - Referência ao item (nullable)
- `tipo_alerta` - Tipo do alerta (validade/estoque_minimo/sugestao_mmc)
- `mensagem` - Mensagem da notificação
- `is_lida` - Status de leitura
- `timestamps`

### Relacionamentos

- `Item` → `hasMany` → `Movimentacao`
- `Item` → `hasMany` → `Lote`
- `Item` → `hasMany` → `Notificacao`
- `Lote` → `belongsTo` → `Item`
- `Movimentacao` → `belongsTo` → `Item`
- `Notificacao` → `belongsTo` → `Item`

---

## 🔔 Sistema de Notificações

### Tipos de Alertas

1. **Validade Próxima**
   - Disparado quando um lote vence em ≤ 30 dias
   - Verificado diariamente pelo comando `estoque:checar-alertas`

2. **Estoque Mínimo**
   - Disparado quando `quantidade_atual ≤ estoque_minimo`
   - Verificado diariamente

3. **Sugestão MMC**
   - Disparado quando `estoque_minimo < (MMC × 1.5 × 0.75)`
   - Verificado diariamente
   - Apenas para itens com consumo histórico

### Notificações em Tempo Real

O sistema utiliza **Laravel Reverb** para notificações em tempo real via WebSockets:

- **Canal**: `notificacoes`
- **Evento**: `notificacao.criada`
- **Cliente**: Laravel Echo no frontend
- **Atualização automática**: Sem necessidade de recarregar a página

### Configuração

Ver seção [Configuração do WebSocket](#configuração-do-websocket-laravel-reverb) acima.

---

## 📄 Relatórios

### Estoque Crítico
Lista todos os itens onde `quantidade_atual ≤ estoque_minimo`, ordenados por urgência.

### Consumo por Período
Análise detalhada de todas as saídas em um período específico, agrupadas por item com totais.

### Descarte e Perdas
Movimentações de saída que contenham palavras-chave indicando perda/descarte:
- descarte
- perda
- vencido
- estragado
- danificado
- quebrado
- perdido

### Validade Próxima
Lista de lotes que vencem nos próximos 90 dias, agrupados por item.

**Todos os relatórios são gerados em PDF** usando DomPDF.

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Siga os passos abaixo:

1. **Fork** o projeto
2. Crie uma **branch** para sua feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. **Push** para a branch (`git push origin feature/AmazingFeature`)
5. Abra um **Pull Request**

### Padrões de Código

O projeto utiliza **Laravel Pint** para formatação de código:

```bash
composer pint
```

---

## 📝 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

---

## 👨‍💻 Autor

Desenvolvido para **CEVESP** (Centro de Cirurgia Minimamente Invasiva)

---

<div align="center">

**⭐ Se este projeto foi útil, considere dar uma estrela! ⭐**

Feito com ❤️ usando Laravel

</div>
