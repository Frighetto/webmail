-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18-Mar-2024 às 02:05
-- Versão do servidor: 10.4.21-MariaDB
-- versão do PHP: 7.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `webmail`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `parametros`
--

CREATE TABLE `parametros` (
  `description` varchar(100) NOT NULL,
  `imap_server` varchar(100) NOT NULL,
  `imap_port` int(11) NOT NULL,
  `smtp_server` varchar(100) NOT NULL,
  `smtp_port` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='description;imap_host;input_door;smtp_host;output_door';

--
-- Extraindo dados da tabela `parametros`
--

INSERT INTO `parametros` (`description`, `imap_server`, `imap_port`, `smtp_server`, `smtp_port`) VALUES
('Google', 'imap.gmail.com', 993, 'smtp.gmail.com', 465),
('Outlook', 'outlook.office365.com', 993, 'smtp-mail.outlook.com', 587),
('Teleatendimento', 'mail.teleatendimento.com.br', 993, 'mail.teleatendimento.com.br', 465);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario` varchar(100) NOT NULL,
  `parametro` varchar(100) NOT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `permissao` varchar(50) NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `senha` varchar(100) DEFAULT NULL,
  `secret_identifier` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`usuario`, `parametro`, `empresa`, `permissao`, `ativo`, `senha`, `secret_identifier`) VALUES
('teste@teleatendimento.com.br', 'Teleatendimento', 'Teleatendimento', 'MASTER', 1, '&lBCEyO8,C*y', 'Y1ygdOgeYpLSS3xDyzbN65P+rGjURTK7Qzc745wERSU8QNmIrwThGqw6pRGd4UwSWvVkCDfnZVRsCO1+8hkkpJ7k/1ezYJlivLAa');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `parametros`
--
ALTER TABLE `parametros`
  ADD PRIMARY KEY (`description`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
