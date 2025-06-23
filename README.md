# 👥 Persons.me - Sistema de Gerenciamento de Pessoas

Sistema moderno de gerenciamento de pessoas desenvolvido com **Laravel 12** e **Vue.js 3**, com interface responsiva e moderna.

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 12** - Framework PHP
- **SQLite** - Banco de dados
- **PHP 8.2+** - Linguagem

### Frontend
- **Vue.js 3** - Framework JavaScript
- **Vue Router 4** - Roteamento SPA
- **Bootstrap 5** - Framework CSS
- **Axios** - Cliente HTTP
- **Laravel Mix** - Build tool

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- **PHP 8.2 ou superior**
- **Composer** (gerenciador de dependências PHP)
- **Node.js 18+ e npm** (gerenciador de dependências JavaScript)
- **Git** (para versionamento)

### Verificar instalações:
```bash
php --version
composer --version
node --version
npm --version
```

## 🛠 Instalação e Configuração

### 1. Clonar o Repositório
```bash
git clone <url-do-repositorio>
cd persons.me
```

### 2. Instalar Dependências PHP
```bash
composer install
```

### 3. Instalar Dependências JavaScript
```bash
npm install
```

### 4. Configurar Ambiente
```bash
# Copiar arquivo de configuração
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate
```

### 5. Configurar Banco de Dados
O projeto usa SQLite por padrão. O banco será criado automaticamente:

```bash
# Criar arquivo do banco (se não existir)
touch database/database.sqlite

# Executar migrações
php artisan migrate

# (Opcional) Executar seeders para dados de exemplo
php artisan db:seed
```

### 6. Compilar Assets Frontend
```bash
# Para desenvolvimento
npm run dev

# Para produção
npm run production

# Para watch mode (desenvolvimento com auto-reload)
npm run watch
```

## 🎯 Como Executar

### Opção 1: Comandos Separados (Recomendado para desenvolvimento)

**Terminal 1 - Servidor Laravel:**
```bash
php artisan serve
```

**Terminal 2 - Build Frontend (modo watch):**
```bash
npm run watch
```

### Opção 2: Comando Unificado
```bash
# Executa servidor + build simultaneamente
composer dev
```

## 📱 Funcionalidades

### 🔐 Autenticação
- ✅ Login de usuários
- ✅ Registro de novos usuários
- ✅ Logout seguro
- ✅ Proteção de rotas

### 👥 Gerenciamento de Pessoas
- ✅ **Listar pessoas** com busca e filtros
- ✅ **Adicionar pessoa** (Física ou Jurídica)
- ✅ **Editar dados** de pessoas existentes
- ✅ **Excluir pessoas** com confirmação
- ✅ **Visualizar detalhes** expandidos

### 📊 Dashboard
- ✅ **Estatísticas** de pessoas cadastradas
- ✅ **Pessoas recentes** adicionadas

### 🎨 Interface
- ✅ **Design moderno** com Bootstrap 5
- ✅ **Responsivo** para desktop e mobile
- ✅ **UX intuitiva** com emojis e feedback visual
- ✅ **SPA** com Vue Router (navegação fluida)

## 🚀 Scripts Disponíveis

### NPM Scripts
```bash
npm run dev         # Build desenvolvimento
npm run watch       # Watch mode (auto-reload)
npm run watch-poll  # Watch com polling
npm run hot         # Hot module replacement
npm run production  # Build produção otimizado
```

### Composer Scripts
```bash
composer dev        # Servidor + build simultâneos
composer test       # Executar testes
```

## 🔧 Configurações Importantes

### Configuração do Laravel (.env)
```bash
APP_NAME="Persons.me"
APP_URL=http://localhost:8000
APP_ENV=local
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### API Endpoints
- **Auth:** `/api/login`, `/api/register`, `/api/logout`
- **People:** `/api/people` (GET, POST, PUT, DELETE)
- **Search:** `/api/people/search`

## 🐛 Solução de Problemas

### Erro: "Class not found"
```bash
composer dump-autoload
```

### Erro: "Mix Manifest not found"
```bash
npm run dev
```

### Erro: "Database not found"
```bash
php artisan migrate:fresh --seed
```

### Erro: "Permission denied" (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
```

## 📚 Documentação Adicional

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Guide](https://vuejs.org/guide/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

**Desenvolvido usando Laravel + Vue.js**
