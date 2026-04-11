# ClassFlow ERP 🎓

![Branding do Projeto](C:\Users\User\.gemini\antigravity\brain\6d1829e4-e15c-4806-a6e9-f4c0fd1afa07\classflow_hero_branding_1775878443531.png)

> **ClassFlow** é um sistema de gestão escolar (ERP) de última geração, desenvolvido para digitalizar e otimizar processos pedagógicos e administrativos em tempo real.

---

## 🚀 Visão Geral

Este projeto foi concebido para substituir os antigos diários de classe de papel e planilhas isoladas por uma plataforma unificada. O ClassFlow oferece uma experiência fluida para Diretores, Professores e Responsáveis, garantindo que a informação flua sem atritos entre a escola e a família.

### O Diferencial Técnico
Diferente de sistemas legados, o ClassFlow utiliza a **Inertia.js Stack**, proporcionando a experiência de uma Single Page Application (SPA) com a robustez e segurança de uma aplicação Server-Side (Laravel).

---

## 🛠️ Stack Tecnológica

O projeto utiliza as tecnologias mais modernas do ecossistema PHP e JavaScript (2025/2026):

- **Backend:** [Laravel 13](https://laravel.com/) (PHP 8.3+)
- **Frontend:** [Vue 3](https://vuejs.org/) (Composition API) + [Inertia.js](https://inertiajs.com/)
- **Estilização:** [Tailwind CSS v4](https://tailwindcss.com/) (Design modernista e performático)
- **Banco de Dados:** MySQL com otimização de índices.
- **Inteligência Artificial:** Integração via [Ollama](https://ollama.ai/) para assistente administrativo (SQL Analytics).
- **Interface:** Lucide Icons & Componentes customizados (estilo Shadcn).

---

## 💎 Funcionalidades Principais

### 🏛️ Módulo Administrativo (Diretor)
- **Gestão de Estrutura:** Controle total sobre anos letivos, períodos, disciplinas e turmas.
- **Matrículas Inteligentes:** Sistema simplificado para vinculação de alunos e responsáveis.
- **Analytics:** Dashboards com taxas de aprovação e evasão escolar.

### 🍎 Módulo Pedagógico (Professor)
- **Diário de Classe Digital:** Registro de frequências e conteúdos ministrados em poucos cliques.
- **Gestão de Notas:** Lançamento de avaliações com cálculos automáticos de média.
- **Planejamento:** Ferramentas para organizar o cronograma de aulas.

### 🛡️ Módulo de Monitoramento (Responsável)
- **Acompanhamento Real:** Visualização imediata de notas e faltas dos dependentes.
- **Histórico Acadêmico:** Acesso fácil ao progresso do aluno ao longo do ano.

---

## 🧠 Arquitetura e Engenharia

Para este projeto, apliquei padrões de engenharia de software que garantem escalabilidade:

1.  **RBAC (Role-Based Access Control):** Implementação de níveis de acesso rigorosos para garantir a privacidade dos dados.
2.  **AI Integration:** Um assistente capaz de traduzir perguntas em linguagem natural para consultas complexas no banco de dados (Text-to-SQL).
3.  **UI/UX Premium:** Foco em micro-interações e transições suaves (< 300ms) para alta produtividade.
4.  **Database Design:** Relacionamentos polimórficos e tabelas pivô otimizadas para performance.

---

## ⚙️ Instalação Rápida

```bash
# Clone o repositório
git clone https://github.com/FelipeOropeza/classflow.git

# Instale as dependências do PHP
composer install

# Instale as dependências do JS
npm install

# Configure o ambiente
cp .env.example .env
php artisan key:generate

# Execute as migrações e seeders
php artisan migrate --seed

# Inicie o servidor de desenvolvimento
npm run dev
# Em outro terminal
php artisan serve
```

---

## 📄 Licença

Este projeto está sob a licença [MIT](./LICENSE).

---

> Desenvolvido com foco em excelência técnica e impacto educacional. 🚀
