# Gerenciador de Biblioteca TG

Um sistema robusto e profissional para gestão de bibliotecas, focado na rastreabilidade total de empréstimos, facilidade de uso administrativo e uma experiência visual premium.

## 🚀 Status do Projeto
**Em desenvolvimento**

---

## 🛠️ Funcionalidades Principais

### Gestão Administrativa
- **CRUD Completo de Livros**: Cadastro detalhado com controle de ISBN, autor, categoria e descrição.
- **Gestão de Usuários**: Sistema de perfis (Admin/Usuário) com validação de CPF, e-mail único e senhas fortes.
- **Categorias Dinâmicas**: Organização de livros por gêneros com contagem automática de exemplares vinculados.

### Sistema de Empréstimos Avançado
- **Fluxo de Vida Completo**: Controle de empréstimo, devolução e prorrogação de prazos.
- **Cálculo de Prazos Inteligente**: Sugestão automática de datas de devolução com base em dias úteis.
- **Histórico Auditável**: Registro completo de cada ação realizada no sistema, utilizando Triggers e registros históricos para auditoria futura.
- **Badges de Ação**: Identificação visual rápida de status (Emprestado, Devolvido, Prorrogado) com cores coordenadas.

### Performance e UX
- **Paginação Global**: Limite de 30 registros por página em todos os módulos para garantir performance em larga escala.
- **Filtros Avançados**: Busca em tempo real e filtros múltiplos (por livro, usuário, data e status).
- **Design Premium**: Interface moderna inspirada no Google Antigravity, com animações suaves, responsividade e tipografia limpa.

---

## 💻 Tecnologias Utilizadas

- **Linguagem**: PHP 8.x (Arquitetura MVC)
- **Banco de Dados**: MySQL (Utilizando PDO para segurança contra SQL Injection)
- **Front-end**: Vanilla CSS3 e JavaScript (ES6+)
- **Estilização**: Sistema de design proprietário com variáveis CSS modernas.
- **Servidor**: Apache (XAMPP/WAMP)

---

## 📂 Estrutura do Projeto

```text
gerenciador_biblioteca_tg/
├── app/                # Lógica de Negócio (MVC)
│   ├── Controllers/    # Controladores das rotas
│   ├── Models/         # Classes de interação com Banco de Dados
│   ├── Libraries/      # Core do System (Database, Controller, etc)
│   └── Helpers/        # Funções utilitárias e calculadoras
├── config/             # Configurações de sistema e banco
├── public/             # Arquivos públicos (CSS, JS, Imagens)
├── views/              # Templates HTML/PHP do front-end
├── database.sql        # Script de criação do banco de dados
└── .htaccess           # Configurações de URL amigáveis
```

---

## 🔧 Instalação e Execução

Para rodar o projeto localmente, siga os passos abaixo:

1. **Pré-requisitos**:
   - Ter o **XAMPP** (ou similar) instalado com PHP >= 8.0.
   - Ter o **Git** instalado.

2. **Clone o Repositório**:
   ```bash
   git clone https://github.com/matheusferrucio/gerenciador_biblioteca_tg.git
   ```

3. **Configuração do Banco de Dados**:
   - Abra o PHPMyAdmin (`localhost/phpmyadmin`).
   - Crie um banco de dados chamado `biblioteca_tg`.
   - Importe o arquivo `database.sql` incluído na raiz do projeto.

4. **Ajuste de Credenciais**:
   - Navegue até `config/config.php` e verifique se as constantes `DB_USER` e `DB_PASS` coincidem com as de seu ambiente local.

5. **Execução**:
   - Mova a pasta para `C:\xampp\htdocs\`.
   - Acesse via navegador em: `http://localhost/gerenciador_biblioteca_tg`.

---

## 🛡️ Regras de Negócio
As regras de operação, validações de empréstimo e políticas de prorrogação estão detalhadas no arquivo `rules.txt` disponível na raiz do repositório.
