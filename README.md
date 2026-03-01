# 🍔 Saboroso Burger - Sistema de Delivery Full-Stack

OBS: Feito com ajuda da I.A

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)

> **Atenção:** Este é um projeto de portfólio para fins de demonstração. Nenhum pedido será processado de verdade.

### 🔗 **[Aceda à demonstração ao vivo aqui!](https://saborosobuger.com.br/)**

---

## 📖 Sobre o Projeto

O **Saboroso Burger** é um sistema web completo para uma hamburgueria, desenvolvido do zero como uma peça de portfólio para demonstrar competências em desenvolvimento **full-stack com PHP procedural**. O projeto abrange desde a experiência do cliente, com um cardápio interativo e um carrinho de compras dinâmico, até um painel administrativo completo para a gestão da loja.

---

## ✨ Principais Funcionalidades

### Para Clientes:
* **Cardápio Dinâmico:** Com filtros por categoria e busca por nome.
* **Personalização de Produtos:** Adição e remoção de ingredientes opcionais com atualização de preço em tempo real.
* **Carrinho de Compras com AJAX:** Adicione e atualize itens no carrinho sem recarregar a página, com notificações "toast".
* **Sistema de Autenticação:** Cadastro e login de utilizadores com recuperação de senha.
* **Histórico de Pedidos:** Acompanhe o status dos seus pedidos, que são atualizados em tempo real (via polling AJAX) quando o status é alterado pelo admin.
* **Sistema de Avaliação:** Clientes podem avaliar os produtos de pedidos concluídos.

### Para o Administrador:
* **Dashboard Visual:** Painel com estatísticas de vendas, pedidos pendentes e um gráfico dos últimos 7 dias de faturamento.
* **Gestão de Pedidos:** Veja os novos pedidos a chegar em tempo real, atualize o status e veja os detalhes de cada um.
* **Gestão de Produtos (CRUD):** Crie, edite e exclua produtos, incluindo a gestão de imagens e a vinculação de opcionais.
* **Gestão de Categorias e Opcionais:** Controle total sobre as categorias do menu e os ingredientes extras.
* **Configurações da Loja:** Altere facilmente o horário de funcionamento da loja.

---

## 🚀 Tecnologias Utilizadas

* **Back-end:** PHP 8 (procedural)
* **Front-end:** HTML5, CSS3, JavaScript (ES6)
* **Banco de Dados:** MySQL
* **Técnicas:** AJAX (Fetch API), Sessões, Hashes de Senha (password_hash), Segurança (CSRF Tokens, `htmlspecialchars`), Polling.
* **Bibliotecas:** Chart.js (gráficos), IMask.js (máscaras de input).

---

## 🔧 Como Executar Localmente

1.  **Clone o repositório:**
    ```bash
    git clone [https://github.com/Pereira-gu/projeto-hamburgueria.git](https://github.com/Pereira-gu/projeto-hamburgueria.git)
    ```
2.  **Importe o Banco de Dados:**
    * Crie uma base de dados no seu MySQL chamada `projeto_han`.
    * Importe o ficheiro `database/projeto_han.sql` para esta base de dados.
3.  **Configure a Conexão:**
    * Verifique se as credenciais em `app/includes/conexao.php` estão corretas (o padrão é `user: root`, `password: ''`).
    * Altere a `BASE_URL` em `app/config/config.php` para o seu caminho local (ex: `http://localhost/projeto-hamburgueria/public`).
4.  **Aceda ao site:** Inicie o seu servidor (XAMPP, etc.) e aceda ao URL que configurou.

---

## 👨‍💻 Contas de Demonstração

Para facilitar a avaliação, pode usar as seguintes contas para testar o sistema:

* **Conta de Administrador:**
    * **E-mail:** `admin@saboroso.com`
    * **Senha:** `123456`

* **Conta de Cliente:**
    * **E-mail:** `cliente@saboroso.com`
    * **Senha:** `123456`

---

## 👤 Autor

**Gustavo Pereira**

* **GitHub:** [@Pereira-gu](https://github.com/Pereira-gu)
* **LinkedIn:** [Gustavo dos Santos Pereira](www.linkedin.com/in/gustavospereira-dev)
