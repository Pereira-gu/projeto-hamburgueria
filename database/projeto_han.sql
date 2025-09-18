-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15/09/2025 às 09:51
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `projeto_han`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `nota` int(1) NOT NULL,
  `comentario` text DEFAULT NULL,
  `data_avaliacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacoes`
--

INSERT INTO `avaliacoes` (`id`, `produto_id`, `usuario_id`, `pedido_id`, `nota`, `comentario`, `data_avaliacao`) VALUES
(1, 16, 8, 14, 5, 'asdas', '2025-09-12 19:52:17'),
(2, 19, 8, 14, 5, 'muito bom', '2025-09-12 19:52:17'),
(3, 12, 8, 18, 5, 'dsfdsvfdssfd', '2025-09-14 19:23:18'),
(4, 14, 8, 18, 4, 'xacsadgds', '2025-09-14 19:23:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `id` int(11) NOT NULL,
  `login_id` int(11) NOT NULL,
  `produtos_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `personalizacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carrinho`
--

INSERT INTO `carrinho` (`id`, `login_id`, `produtos_id`, `quantidade`, `personalizacao`) VALUES
(68, 12, 14, 2, NULL),
(69, 12, 12, 1, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(2, 'Acompanhamentos'),
(3, 'Bebidas'),
(7, 'bijoterias'),
(1, 'Burgers'),
(4, 'Sobremesas');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `chave` varchar(100) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`chave`, `valor`) VALUES
('HORARIO_ABERTURA', '09:00'),
('HORARIO_FECHAMENTO', '23:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `senha` varchar(150) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `endereco` text DEFAULT NULL,
  `telefone` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `login`
--

INSERT INTO `login` (`id`, `email`, `nome`, `senha`, `is_admin`, `endereco`, `telefone`) VALUES

-- --------------------------------------------------------

--
-- Estrutura para tabela `opcionais`
--

CREATE TABLE `opcionais` (
  `id` int(11) NOT NULL,
  `grupo` varchar(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `opcionais`
--

INSERT INTO `opcionais` (`id`, `grupo`, `nome`, `preco`) VALUES
(1, 'Adicionais', 'Bacon Extra', 3.00),
(2, 'Adicionais', 'Queijo Cheddar Extra', 3.00),
(4, 'Remover', 'Sem Cebola', 0.00),
(5, 'Remover', 'Sem Picles', 0.00),
(7, 'Ponto da Carne', 'Ao Ponto', 0.00),
(8, 'Ponto da Carne', 'Bem Passado', 0.00),
(9, 'Porção', '250g', 10.00),
(10, 'Porção', '450g', 16.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `login_id` int(11) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `endereco_entrega` text NOT NULL,
  `telefone_contato` varchar(25) NOT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Pendente',
  `tipo_entrega` varchar(50) NOT NULL DEFAULT 'delivery',
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `troco_para` decimal(10,2) DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `login_id`, `valor_total`, `endereco_entrega`, `telefone_contato`, `data_pedido`, `status`, `tipo_entrega`, `metodo_pagamento`, `troco_para`, `observacoes`) VALUES
(13, 8, 33.00, 'guau gugua guguasd', '11123123123', '2025-09-12 19:28:43', 'Pendente', 'delivery', NULL, NULL, NULL),
(14, 8, 33.00, 'guau gugua guguasd', '11123123123', '2025-09-12 19:28:51', 'Concluído', 'delivery', NULL, NULL, NULL),
(15, 8, 72.00, 'guau gugua guguasd', '11123123123', '2025-09-14 16:16:49', 'Cancelado', 'delivery', 'Dinheiro', NULL, ''),
(16, 11, 166.00, 'rua acaua 53B', '11 9555555', '2025-09-14 16:48:12', 'Concluído', 'delivery', 'Pix', NULL, 'sem gozar no pao por favor'),
(17, 12, 53.00, '123123', '123123', '2025-09-14 17:30:43', 'Concluído', 'delivery', 'Dinheiro', 123.00, '123'),
(18, 8, 50.00, 'afds', '11123123123', '2025-09-14 19:22:52', 'Concluído', 'delivery', 'Pix', NULL, 'dszvdsfdfsa');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `personalizacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_itens`
--

INSERT INTO `pedido_itens` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`, `personalizacao`) VALUES
(25, 13, 16, 1, 15.00, NULL),
(26, 13, 19, 1, 18.00, NULL),
(27, 14, 16, 1, 15.00, NULL),
(28, 14, 19, 1, 18.00, NULL),
(29, 15, 12, 2, 22.00, NULL),
(30, 15, 14, 1, 28.00, NULL),
(31, 16, 14, 3, 28.00, NULL),
(32, 16, 19, 3, 18.00, NULL),
(33, 16, 18, 3, 6.00, NULL),
(34, 16, 21, 1, 10.00, NULL),
(35, 17, 14, 1, 28.00, NULL),
(36, 17, 12, 1, 25.00, '[{\"nome\":\"Bacon Extra\",\"preco\":\"3.00\"},{\"nome\":\"Bem Passado\",\"preco\":\"0.00\"}]'),
(37, 18, 12, 1, 22.00, NULL),
(38, 18, 14, 1, 28.00, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome_produto` varchar(50) NOT NULL,
  `descricao_produto` varchar(150) NOT NULL,
  `preco_produto` float DEFAULT NULL,
  `categoria` varchar(50) NOT NULL DEFAULT 'Lanches',
  `imagem` varchar(255) DEFAULT NULL,
  `disponivel` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome_produto`, `descricao_produto`, `preco_produto`, `categoria`, `imagem`, `disponivel`) VALUES
(12, 'X-Burger Clássico', 'Pão, hambúrguer de carne bovina (120g), queijo prato derretido e maionese da casa. Simples e perfeito', 22, 'Burgers', '68c347efdc0d3_x-burger.png', 1),
(13, 'X-Salada', 'Pão, hambúrguer de carne (120g), queijo prato, alface fresquinha, tomate em rodelas e maionese da casa. O favorito de todos.', 25, 'Burgers', '68c34830c47f2_x-salada.png', 1),
(14, 'X-Bacon', 'Pão, hambúrguer de carne (120g), queijo prato e fatias de bacon bem crocantes.', 28, 'Burgers', '68c348eabf41a_x-bacon.png', 1),
(15, 'X-Tudo', 'Pão, hambúrguer de carne (120g), queijo, presunto, bacon, ovo, alface, tomate, milho e batata palha.', 32, 'Burgers', '68c34910cbf4b_x-tudo.png', 1),
(16, 'Batata Frita (Porção)', 'Porção generosa de batatas fritas sequinhas e crocantes, temperadas com sal. Acompanha maionese verde.', 15, 'Acompanhamentos', '68c34adb218c8_batata.png', 1),
(17, 'Mandioca Frita', 'Pedaços de mandioca (aipim) cozidos e depois fritos, resultando em uma casquinha crocante por fora e um interior macio.', 17, 'Acompanhamentos', '68c34d77d8360_mandioca frita.png', 1),
(18, 'Refrigerante Lata (350ml)', 'Coca-Cola, Coca-Cola Zero, Guaraná Antarctica, Fanta Laranja, Sprite.', 6, 'Bebidas', '68c34dafdeb67_68c0f45c6282a_refrigerantes.png', 1),
(19, 'Anéis de Cebola', 'Porção de anéis de cebola empanados e fritos. Crocantes e deliciosos, servidos com molho rosé.', 18, 'Acompanhamentos', '68c34e14196ad_aneis-cebola.png', 1),
(20, 'Calabresa Acebolada', 'Linguiça calabresa fatiada e frita na chapa com cebola em rodelas. Acompanha pão fatiado.', 36, 'Acompanhamentos', '68c35277a1fa7_calabresa.png', 1),
(21, 'Pudim de Leite', 'Uma fatia generosa do clássico pudim de leite condensado com bastante calda de caramelo.', 10, 'Sobremesas', '68c3530c286b7_pudim.png', 1),
(22, 'Mousse de Chocolate', 'Mousse de chocolate caseiro, com textura aerada e sabor intenso de cacau.', 12, 'Sobremesas', '68c35323909ff_mousse-chocolate.png', 1),
(23, 'Açaí na Tigela (300ml', 'Açaí tradicional batido, servido na tigela com banana fatiada e granola crocante.', 18, 'Sobremesas', '68c35339895db_acai.png', 1),
(24, 'Brigadeirão', 'Uma fatia de brigadeiro de forno, cremoso e coberto com chocolate granulado.', 11, 'Sobremesas', '68c3534c66828_brigadeirao.png', 1),
(25, 'Água Mineral', 'Garrafa de 500ml, com ou sem gás.', 4, 'Bebidas', '68c3540307174_agua.png', 1),
(26, 'Suco de Polpa (Copo 400ml)', 'Sabores: Laranja, Abacaxi, Morango ou Maracujá. Feito com polpa de fruta.', 8, 'Bebidas', '68c35417e5b0a_suco de polpa.png', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto_opcionais`
--

CREATE TABLE `produto_opcionais` (
  `produto_id` int(11) NOT NULL,
  `opcional_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto_opcionais`
--

INSERT INTO `produto_opcionais` (`produto_id`, `opcional_id`) VALUES
(12, 1),
(12, 4),
(12, 7),
(12, 8),
(16, 9),
(16, 10),
(19, 9),
(20, 5);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Índices de tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_produtos` (`login_id`,`produtos_id`),
  ADD KEY `produtos_id` (`produtos_id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`chave`);

--
-- Índices de tabela `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `opcionais`
--
ALTER TABLE `opcionais`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_id` (`login_id`);

--
-- Índices de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome_produto` (`nome_produto`);

--
-- Índices de tabela `produto_opcionais`
--
ALTER TABLE `produto_opcionais`
  ADD PRIMARY KEY (`produto_id`,`opcional_id`),
  ADD KEY `opcional_id` (`opcional_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `opcionais`
--
ALTER TABLE `opcionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `login` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `avaliacoes_ibfk_3` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`login_id`) REFERENCES `login` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrinho_ibfk_2` FOREIGN KEY (`produtos_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`login_id`) REFERENCES `login` (`id`);

--
-- Restrições para tabelas `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `produto_opcionais`
--
ALTER TABLE `produto_opcionais`
  ADD CONSTRAINT `produto_opcionais_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produto_opcionais_ibfk_2` FOREIGN KEY (`opcional_id`) REFERENCES `opcionais` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
