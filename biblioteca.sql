-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 18-Maio-2026 às 14:39
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `biblioteca`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `devolucao`
--

CREATE TABLE `devolucao` (
  `id_devolucao` int(11) NOT NULL,
  `id_emprestimo` int(11) NOT NULL,
  `data_devolucao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `devolucao`
--

INSERT INTO `devolucao` (`id_devolucao`, `id_emprestimo`, `data_devolucao`) VALUES
(1, 1, '2026-05-06'),
(2, 2, '2026-05-06'),
(3, 3, '2026-05-06');

-- --------------------------------------------------------

--
-- Estrutura da tabela `emprestimo`
--

CREATE TABLE `emprestimo` (
  `id_emprestimo` int(11) NOT NULL,
  `fk_Usuario_id_usuario` int(11) NOT NULL,
  `fk_Livro_id_livro` int(11) NOT NULL,
  `id_emprestimo_leitor` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `data_prevista` date NOT NULL,
  `data_emprestimo` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `emprestimo`
--

INSERT INTO `emprestimo` (`id_emprestimo`, `fk_Usuario_id_usuario`, `fk_Livro_id_livro`, `id_emprestimo_leitor`, `estado`, `data_prevista`, `data_emprestimo`) VALUES
(1, 2, 1, 1, 1, '2025-04-12', '2025-02-12'),
(2, 2, 4, 2, 1, '2025-04-29', '2026-05-06'),
(3, 2, 3, 1, 1, '2026-06-06', '2026-05-06');

-- --------------------------------------------------------

--
-- Estrutura da tabela `leitor`
--

CREATE TABLE `leitor` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nif` varchar(50) NOT NULL,
  `numero_telefone` int(9) NOT NULL,
  `data_leitor` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `leitor`
--

INSERT INTO `leitor` (`id`, `nome`, `email`, `nif`, `numero_telefone`, `data_leitor`) VALUES
(1, 'Marcos', 'marcos@gmail.com', '000000AA111111', 933333444, '2022-04-04'),
(2, 'Sabino', 'sabino@gmail.com', '000000AA111122', 974537081, '2006-05-19'),
(3, 'Cabral Campos', 'cabral@gmail.com', '000000AA111199', 980123467, '2026-05-06');

-- --------------------------------------------------------

--
-- Estrutura da tabela `livro`
--

CREATE TABLE `livro` (
  `id_livro` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `editora` varchar(100) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `edicao` varchar(50) NOT NULL,
  `data_livro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `livro`
--

INSERT INTO `livro` (`id_livro`, `titulo`, `autor`, `quantidade`, `editora`, `imagem`, `edicao`, `data_livro`) VALUES
(1, 'Romeu and Juliete', 'Dogma', 3, 'Sagitarius', 'img.jpg', '1 edição', '1990-11-03'),
(2, 'Lady and Bug', 'Canarius', 6, 'Panda', 'img.png', '2 edição', '1990-04-07'),
(3, 'Matemática Módulo 1', 'Pitágoras', 8, 'Harvard', 'img.png', '3 edição', '1990-11-03'),
(4, 'Física', 'A.Einstein', 2, 'Harvard', 'img.jpg', '2 edição', '1970-12-02');

-- --------------------------------------------------------

--
-- Estrutura da tabela `multa`
--

CREATE TABLE `multa` (
  `id_multa` int(11) NOT NULL,
  `id_devolucao` int(11) NOT NULL,
  `valor` decimal(7,2) NOT NULL,
  `data_multa` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `multa`
--

INSERT INTO `multa` (`id_multa`, `id_devolucao`, `valor`, `data_multa`) VALUES
(1, 1, 2500.00, '2026-05-06'),
(2, 2, 2500.00, '2026-05-06');

-- --------------------------------------------------------

--
-- Estrutura da tabela `reserva`
--

CREATE TABLE `reserva` (
  `id_reserva` int(11) NOT NULL,
  `nome_leitor` varchar(100) DEFAULT NULL,
  `prazo_reserva` date DEFAULT NULL,
  `fk_Usuario_id_usuario` int(11) DEFAULT NULL,
  `fk_Livro_id_livro` int(11) DEFAULT NULL,
  `data_reserva` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `reserva`
--

INSERT INTO `reserva` (`id_reserva`, `nome_leitor`, `prazo_reserva`, `fk_Usuario_id_usuario`, `fk_Livro_id_livro`, `data_reserva`) VALUES
(1, 'Jonas', '2026-04-01', 1, 4, '2026-02-02'),
(2, 'Sabino', '2026-04-01', 1, 3, '2026-02-02');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `contacto` int(9) NOT NULL,
  `tipo_usuario` enum('admin','bibliotecario') NOT NULL,
  `nome` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_usuario` date NOT NULL,
  `email` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `contacto`, `tipo_usuario`, `nome`, `senha`, `data_usuario`, `email`) VALUES
(1, 930400409, 'admin', 'Samuel', '12345', '2026-04-29', 'samuel@gmail.com'),
(2, 921123123, 'bibliotecario', 'Wilson Jade', '1234', '2025-05-01', 'wilson@gmail.com');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `devolucao`
--
ALTER TABLE `devolucao`
  ADD PRIMARY KEY (`id_devolucao`),
  ADD KEY `id_emprestimo` (`id_emprestimo`);

--
-- Índices para tabela `emprestimo`
--
ALTER TABLE `emprestimo`
  ADD PRIMARY KEY (`id_emprestimo`),
  ADD KEY `fk_Livro_id_livro` (`fk_Livro_id_livro`),
  ADD KEY `fk_Usuario_id_usuario` (`fk_Usuario_id_usuario`),
  ADD KEY `id_emprestimo_leitor` (`id_emprestimo_leitor`);

--
-- Índices para tabela `leitor`
--
ALTER TABLE `leitor`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `livro`
--
ALTER TABLE `livro`
  ADD PRIMARY KEY (`id_livro`);

--
-- Índices para tabela `multa`
--
ALTER TABLE `multa`
  ADD PRIMARY KEY (`id_multa`),
  ADD KEY `id_devolucao` (`id_devolucao`);

--
-- Índices para tabela `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `fk_Livro_id_livro` (`fk_Livro_id_livro`),
  ADD KEY `fk_Usuario_id_usuario` (`fk_Usuario_id_usuario`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `devolucao`
--
ALTER TABLE `devolucao`
  MODIFY `id_devolucao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `emprestimo`
--
ALTER TABLE `emprestimo`
  MODIFY `id_emprestimo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `leitor`
--
ALTER TABLE `leitor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `livro`
--
ALTER TABLE `livro`
  MODIFY `id_livro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `multa`
--
ALTER TABLE `multa`
  MODIFY `id_multa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `devolucao`
--
ALTER TABLE `devolucao`
  ADD CONSTRAINT `devolucao_ibfk_1` FOREIGN KEY (`id_emprestimo`) REFERENCES `emprestimo` (`id_emprestimo`);

--
-- Limitadores para a tabela `emprestimo`
--
ALTER TABLE `emprestimo`
  ADD CONSTRAINT `emprestimo_ibfk_4` FOREIGN KEY (`fk_Livro_id_livro`) REFERENCES `livro` (`id_livro`),
  ADD CONSTRAINT `emprestimo_ibfk_5` FOREIGN KEY (`fk_Usuario_id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `emprestimo_ibfk_6` FOREIGN KEY (`id_emprestimo_leitor`) REFERENCES `leitor` (`id`);

--
-- Limitadores para a tabela `multa`
--
ALTER TABLE `multa`
  ADD CONSTRAINT `multa_ibfk_1` FOREIGN KEY (`id_devolucao`) REFERENCES `devolucao` (`id_devolucao`);

--
-- Limitadores para a tabela `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`fk_Livro_id_livro`) REFERENCES `livro` (`id_livro`) ON DELETE CASCADE,
  ADD CONSTRAINT `reserva_ibfk_3` FOREIGN KEY (`fk_Usuario_id_usuario`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
