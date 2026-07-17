SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `caixa_diario` (
  `id` int(11) NOT NULL,
  `data_dia` date NOT NULL,
  `valor_abertura` decimal(10,2) DEFAULT 0.00,
  `valor_fechamento` decimal(10,2) DEFAULT 0.00,
  `status_caixa` enum('aberto','fechado') DEFAULT 'aberto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `caixa_diario` (`id`, `data_dia`, `valor_abertura`, `valor_fechamento`, `status_caixa`) VALUES
(8, '2026-07-15', 0.00, 51.00, 'fechado');



CREATE TABLE `cardapio` (
  `id` int(11) NOT NULL,
  `nome_prato` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `disponivel` tinyint(1) DEFAULT 1,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `estoque` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `cardapio` (`id`, `nome_prato`, `descricao`, `preco`, `categoria`, `disponivel`, `status`, `estoque`) VALUES
(1, 'Carpaccio de Mignon', 'Finas fatias de filé mignon, molho de mostarda em grãos, alcaparras, lascas de parmesão e rúcula. Acompanha torradas artesanais.', 48.00, 'Entrada', 1, 'ativo', 23),
(2, 'Bruschetta di Parma', 'Pão italiano tostado no azeite, tomate concassé, manjericão fresco, presunto de Parma e redução de balsâmico.', 42.00, 'Entrada', 1, 'ativo', 45),
(3, 'Dadinhos de Tapioca com Geleia de Pimenta', 'Dadinhos crocantes de tapioca com queijo coalho, servidos com geleia artesanal de pimenta defumada.', 36.00, 'Entrada', 1, 'ativo', 75),
(4, 'Camarões Alho e Óleo Premium', 'Camarões rosa salteados no azeite extra virgem, alho laminado e salsa fresca. Servido com fatias de pão levain.', 68.00, 'Entrada', 1, 'ativo', 20),
(5, 'Tartar de Salmão e Abacate', 'Salmão fresco picado na ponta da faca, temperado com limão siciliano, cebola roxa e cubos de abacate grelhado.', 54.00, 'Entrada', 1, 'ativo', 53),
(6, 'Filé Mignon das Chefinhas', 'Medalhão de filé mignon grelhado ao molho de vinho Merlot, acompanhado de risoto de cogumelos frescos e aspargos.', 92.00, 'Prato Principal', 1, 'ativo', 30),
(7, 'Salmão em Crosta de Ervas', 'Filé de salmão grelhado com crosta de ervas finas, servido com purê de mandioquinha ao perfume de trufas e legumes salteados.', 88.00, 'Prato Principal', 1, 'ativo', 20),
(8, 'Risoto de Camarão com Limão Siciliano', 'Risoto cremoso com camarões selecionados, finalizado com raspas e suco de limão siciliano e queijo grana padano.', 85.00, 'Prato Principal', 1, 'ativo', 45),
(9, 'Gnocchi de Mandioca com Ragu de Costela', 'Gnocchi artesanal de mandioca dourado na manteiga de garrafa, servido com ragu de costela bovina cozida lentamente.', 72.00, 'Prato Principal', 1, 'ativo', 31),
(10, 'Ancho Grelhado com Batatas Rústicas', 'Corte nobre de bife ancho (300g) grelhado na brasa, acompanhado de batatas rústicas com alecrim e molho chimichurri caseiro.', 95.00, 'Prato Principal', 1, 'ativo', 52),
(11, 'Ravioli de Brie com Damasco', 'Massa fresca recheada com queijo brie e damasco, ao molho cremoso de nozes e folhas de sálvia.', 76.00, 'Prato Principal', 1, 'ativo', 59),
(12, 'Polvo Grelhado na Brasa', 'Tentáculos de polvo grelhados, acompanhados de batatas ao murro, cebola roxa assada, tomates-cereja e azeite de ervas.', 115.00, 'Prato Principal', 1, 'ativo', 17),
(13, 'Água Mineral Premium Sem Gás', 'Garrafa de vidro 300ml.', 7.00, 'Bebida', 1, 'ativo', 46),
(14, 'Água Mineral Premium Com Gás', 'Garrafa de vidro 300ml.', 7.50, 'Bebida', 1, 'ativo', 20),
(15, 'Refrigerante Lata', 'Coca-Cola, Coca-Cola Zero ou Guaraná Antarctica 350ml.', 8.00, 'Bebida', 1, 'ativo', 21),
(16, 'Suco Natural de Frutas Vermelhas', 'Suco natural feito na hora com morango, amora e framboesa (400ml).', 14.50, 'Bebida', 1, 'ativo', 52),
(17, 'Soda Italiana de Maçã Verde', 'Bebida refrescante feita com xarope francês de maçã verde, água gaseificada e gelo.', 16.00, 'Bebida', 1, 'ativo', 22),
(19, 'Vinho Tinto Cabernet Sauvignon (Taça)', 'Taça de vinho selecionado da casa (150ml).', 28.00, 'Bebida', 1, 'ativo', 16),
(20, 'Cerveja Artesanal IPA', 'Garrafa 500ml de produção local.', 22.00, 'Bebida', 1, 'ativo', 69),
(21, 'Petit Gâteau de Doce de Leite', 'Bolinho quente de doce de leite com recheio cremoso, acompanhado de sorvete de baunilha artesanal.', 28.00, 'Sobremesa', 1, 'ativo', 35),
(22, 'Mil Folhas de Baunilha', 'Massa folhada super crocante intercalada com creme de confeiteiro com fava de baunilha e açúcar de confeiteiro.', 32.00, 'Sobremesa', 1, 'ativo', 44),
(23, 'Tiramisù Tradicional', 'Sobremesa italiana clássica com biscoito champagne embebido em café expresso, creme de mascarpone e cacau em pó.', 34.00, 'Sobremesa', 1, 'ativo', 56),
(24, 'Grand Gateau das Chefinhas', 'Bolinho de chocolate belga quente, picolé de chocolate artesanal espetado, morangos frescos e calda de Nutella.', 38.00, 'Sobremesa', 1, 'ativo', 46),
(25, 'Cheesecake de Frutas Vermelhas', 'Base de biscoito amanteigado, creme de cream cheese leve e cobertura de calda caseira de frutas vermelhas.', 26.00, 'Sobremesa', 1, 'ativo', 55),
(29, 'Churrasco ', 'vinagrete, arroz ', 70.00, 'Prato Principal', 1, 'ativo', 50),
(31, 'picanha', 'arroz', 99.98, 'Prato Principal', 1, 'inativo', 13);



CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `clientes` (`id`, `nome`, `telefone`, `criado_em`) VALUES
(1, 'Ester Rodrigues', NULL, '2026-07-04 21:22:41'),
(2, 'Sara Souza', NULL, '2026-07-04 21:23:51'),
(3, 'Carlos Mendes', NULL, '2026-07-04 21:41:23'),
(4, 'Eduardo Souza', NULL, '2026-07-04 23:38:22'),
(5, 'Sofia Carol', NULL, '2026-07-04 23:43:22'),
(6, 'Sara', NULL, '2026-07-05 00:01:45'),
(7, 'Ana Vitoria ', NULL, '2026-07-05 00:15:24'),
(8, 'Ana Vitoria', NULL, '2026-07-05 00:16:47'),
(9, 'Eduarda', NULL, '2026-07-05 00:35:12'),
(10, 'sara', NULL, '2026-07-05 00:45:12'),
(11, 'Izabela', NULL, '2026-07-05 00:49:00'),
(12, 'Soraia', NULL, '2026-07-05 00:58:02'),
(13, 'soso', NULL, '2026-07-06 01:03:59'),
(15, 'oi', NULL, '2026-07-05 01:28:08'),
(16, 'LALA', NULL, '2026-07-05 01:48:04'),
(17, 'SARA', NULL, '2026-07-05 01:59:09'),
(18, 'Luis', NULL, '2026-07-05 02:02:02'),
(19, 'lorena', NULL, '2026-07-05 02:03:49'),
(20, 'kiki', NULL, '2026-07-05 02:13:48'),
(21, 'sara', NULL, '2026-07-05 03:02:11'),
(22, 'Julia', NULL, '2026-07-05 03:03:28'),
(23, 'Juliana Miranda', NULL, '2026-07-05 03:32:19'),
(24, 'Juliana Castro', NULL, '2026-07-05 03:38:37'),
(25, 'Ana', '123456789002', '2026-07-05 12:52:39'),
(26, 'Rosa', NULL, '2026-07-05 12:56:01'),
(27, 'José Fernando', NULL, '2026-07-06 20:37:06'),
(28, 'Sara Rodrigues', NULL, '2026-07-06 20:48:34'),
(29, 'Riquelmi Lima', NULL, '2026-07-07 13:51:00'),
(30, 'Ana Maria Oliveira', '89994338456', '2026-07-07 14:58:55'),
(31, 'Thiago Rodrigues', '89994338400', '2026-07-07 15:08:57'),
(32, 'Thiago Rodrigues', '89994338400', '2026-07-07 15:14:58'),
(33, 'Thiago Souza', '89994338410', '2026-07-07 15:15:15'),
(34, 'Thiago Souza', '89994338410', '2026-07-07 15:15:17'),
(35, 'Thiago Souza', '89994338410', '2026-07-07 15:15:18'),
(36, 'Thiago Souza', '89994338410', '2026-07-07 15:15:18'),
(37, 'Thiago Souza', '89994338410', '2026-07-07 15:15:19'),
(38, 'Thiago Souza', '89994338451', '2026-07-07 15:15:43'),
(39, 'Thiago Souza Lima', '89994338451', '2026-07-07 15:21:28'),
(40, 'Jônio Castro', '12345678901', '2026-07-07 16:54:23'),
(41, 'Luzitânia Jacobina', '89994338456', '2026-07-12 17:20:49'),
(42, 'José Miranda', '022265852636', '2026-07-13 20:20:07'),
(43, 'Thiago Rodrigues', '', '2026-07-13 20:23:09'),
(44, 'Lulú', '89994338425', '2026-07-15 23:34:30'),
(46, 'Ana Luiza Moreira', '8966666-0000', '2026-07-16 20:01:00'),
(47, 'Heloisa Rodrigues', '89994338400', '2026-07-16 20:42:57');



CREATE TABLE `despesas` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_despesa` date NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `despesas` (`id`, `descricao`, `valor`, `data_despesa`, `categoria`, `criado_em`) VALUES
(1, 'salario ', 1621.00, '2026-07-05', 'Salários', '2026-07-05 00:07:51'),
(2, 'Luz de Julho', 120.00, '2026-07-13', 'Luz/Água', '2026-07-13 20:31:25'),
(3, 'luz agosto', 111.00, '2026-07-15', 'Outros', '2026-07-15 23:49:03'),
(4, 'arroz', 200.00, '2026-07-16', 'Ingredientes', '2026-07-16 19:30:23');



CREATE TABLE `itens_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `cardapio_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `itens_pedido` (`id`, `pedido_id`, `cardapio_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 16, 1, 14.50),
(2, 1, 12, 1, 115.00),
(3, 2, 14, 1, 7.50),
(4, 3, 1, 1, 48.00),
(5, 4, 15, 1, 8.00),
(6, 5, 22, 1, 32.00),
(7, 5, 24, 1, 38.00),
(8, 5, 4, 1, 68.00),
(9, 5, 19, 1, 28.00),
(10, 6, 16, 1, 14.50),
(11, 7, 3, 1, 36.00),
(12, 7, 9, 1, 72.00),
(13, 7, 11, 1, 76.00),
(14, 7, 12, 1, 115.00),
(15, 8, 14, 9, 7.50),
(16, 8, 20, 3, 22.00),
(17, 9, 13, 1, 7.00),
(18, 9, 1, 1, 48.00),
(19, 9, 4, 1, 68.00),
(20, 10, 14, 4, 7.50),
(21, 11, 10, 1, 95.00),
(22, 11, 8, 1, 85.00),
(23, 11, 7, 1, 88.00),
(24, 11, 20, 6, 22.00),
(25, 11, 15, 1, 8.00),
(26, 11, 14, 1, 7.50),
(30, 15, 14, 1, 7.50),
(31, 16, 14, 1, 7.50),
(32, 17, 15, 1, 8.00),
(33, 18, 14, 1, 7.50),
(36, 21, 20, 1, 22.00),
(37, 22, 15, 1, 8.00),
(38, 23, 15, 1, 8.00),
(39, 23, 3, 1, 36.00),
(40, 23, 1, 1, 48.00),
(41, 24, 6, 1, 92.00),
(42, 24, 17, 1, 16.00),
(43, 25, 14, 1, 7.50),
(44, 26, 4, 1, 68.00),
(45, 27, 20, 1, 22.00),
(46, 28, 7, 1, 88.00),
(47, 28, 15, 1, 8.00),
(48, 29, 11, 1, 76.00),
(49, 29, 12, 1, 115.00),
(50, 30, 14, 1, 7.50),
(51, 31, 8, 1, 85.00),
(52, 32, 13, 1, 7.00),
(53, 33, 19, 1, 28.00),
(54, 34, 7, 1, 88.00),
(55, 35, 13, 1, 7.00),
(56, 36, 13, 1, 7.00),
(57, 36, 20, 1, 22.00),
(58, 37, 13, 1, 7.00),
(59, 37, 20, 1, 22.00),
(60, 38, 15, 1, 8.00),
(61, 38, 6, 1, 92.00),
(62, 39, 25, 1, 26.00),
(63, 39, 22, 1, 32.00),
(64, 40, 1, 1, 48.00),
(65, 41, 16, 1, 14.50),
(66, 42, 17, 1, 16.00),
(67, 42, 13, 1, 7.00),
(68, 43, 19, 1, 28.00),
(69, 44, 13, 1, 7.00),
(70, 45, 13, 1, 7.00),
(71, 46, 6, 1, 92.00),
(72, 47, 15, 1, 8.00),
(73, 48, 21, 1, 28.00);


CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `numero_mesa` int(11) NOT NULL,
  `status` enum('disponivel','ocupada') DEFAULT 'disponivel',
  `status_mesa` varchar(20) DEFAULT 'livre',
  `cliente_atual` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `mesas` (`id`, `numero_mesa`, `status`, `status_mesa`, `cliente_atual`) VALUES
(1, 1, 'disponivel', 'livre', NULL),
(2, 2, 'disponivel', 'livre', NULL),
(3, 3, 'disponivel', 'livre', NULL),
(4, 4, 'disponivel', 'livre', NULL),
(5, 5, 'disponivel', 'livre', NULL),
(6, 6, 'disponivel', 'livre', NULL),
(7, 7, 'disponivel', 'livre', NULL),
(8, 8, 'disponivel', 'livre', NULL),
(9, 9, 'disponivel', 'livre', NULL),
(10, 10, 'disponivel', 'livre', NULL),
(11, 11, 'disponivel', 'livre', NULL),
(12, 12, 'disponivel', 'livre', NULL),
(13, 13, 'disponivel', 'livre', NULL),
(14, 14, 'disponivel', 'livre', NULL),
(15, 15, 'disponivel', 'livre', NULL),
(16, 16, 'disponivel', 'livre', NULL),
(17, 17, 'disponivel', 'livre', NULL),
(18, 18, 'disponivel', 'livre', NULL),
(19, 19, 'disponivel', 'livre', NULL),
(20, 20, 'disponivel', 'livre', NULL),
(24, 21, 'disponivel', 'livre', NULL),
(25, 23, 'disponivel', 'livre', NULL);



CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `garcom_id` int(11) NOT NULL,
  `mensagem` varchar(255) NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `notificacoes` (`id`, `pedido_id`, `garcom_id`, `mensagem`, `lida`, `criado_em`) VALUES
(1, 1, 2, 'O pedido da Mesa 13 mudou para: *em preparo*', 0, '2026-07-04 21:33:11'),
(2, 2, 2, 'O pedido da Mesa 14 mudou para: *em preparo*', 0, '2026-07-04 21:33:15'),
(3, 1, 2, 'O pedido da Mesa 13 mudou para: *finalizado*', 0, '2026-07-04 21:40:34'),
(4, 1, 2, 'O pedido da Mesa 13 mudou para: *entregue*', 0, '2026-07-04 21:41:49'),
(5, 3, 2, 'O pedido da Mesa 3 mudou para: *em preparo*', 0, '2026-07-04 21:41:53'),
(6, 2, 2, 'O pedido da Mesa 14 mudou para: *entregue*', 0, '2026-07-04 21:53:06'),
(7, 3, 2, 'O pedido da Mesa 3 mudou para: *finalizado*', 0, '2026-07-04 21:53:09'),
(8, 3, 2, 'O pedido da Mesa 3 mudou para: *entregue*', 0, '2026-07-04 21:53:12'),
(9, 4, 2, 'O pedido da Mesa 6 mudou para: *em preparo*', 0, '2026-07-04 23:39:23'),
(10, 4, 2, 'O pedido da Mesa 6 mudou para: *finalizado*', 0, '2026-07-04 23:39:31'),
(11, 4, 2, 'O pedido da Mesa 6 mudou para: *entregue*', 0, '2026-07-04 23:39:43'),
(12, 5, 2, 'O pedido da Mesa 1 mudou para: *em preparo*', 0, '2026-07-05 00:02:53'),
(13, 5, 2, 'O pedido da Mesa 1 mudou para: *finalizado*', 0, '2026-07-05 00:03:02'),
(14, 5, 2, 'O pedido da Mesa 1 mudou para: *entregue*', 0, '2026-07-05 00:03:05'),
(15, 6, 2, 'O pedido da Mesa 1 mudou para: *em preparo*', 0, '2026-07-05 00:03:28'),
(16, 6, 2, 'O pedido da Mesa 1 mudou para: *finalizado*', 0, '2026-07-05 00:17:19'),
(17, 6, 2, 'O pedido da Mesa 1 mudou para: *entregue*', 0, '2026-07-05 00:17:21'),
(18, 7, 2, 'O pedido da Mesa 4 mudou para: *entregue*', 0, '2026-07-05 00:17:24'),
(19, 8, 2, 'O pedido da Mesa 2 mudou para: *entregue*', 0, '2026-07-05 00:17:26'),
(20, 9, 2, 'O pedido da Mesa 5 mudou para: *em preparo*', 0, '2026-07-05 00:35:35'),
(21, 9, 2, 'O pedido da Mesa 5 mudou para: *finalizado*', 0, '2026-07-05 00:35:37'),
(22, 9, 2, 'O pedido da Mesa 5 mudou para: *entregue*', 0, '2026-07-05 00:35:40'),
(23, 10, 2, 'O pedido da Mesa 6 mudou para: *em preparo*', 0, '2026-07-05 00:45:39'),
(24, 10, 2, 'O pedido da Mesa 6 mudou para: *finalizado*', 0, '2026-07-05 00:45:41'),
(25, 10, 2, 'O pedido da Mesa 6 mudou para: *entregue*', 0, '2026-07-05 00:45:43'),
(26, 11, 2, 'O pedido da Mesa 10 mudou para: *em preparo*', 0, '2026-07-05 00:58:18'),
(27, 11, 2, 'O pedido da Mesa 10 mudou para: *finalizado*', 0, '2026-07-05 00:58:20'),
(31, 11, 2, 'O pedido da Mesa 10 mudou para: *entregue*', 0, '2026-07-05 00:58:29'),
(38, 15, 2, 'O pedido da Mesa 8 mudou para: *em preparo*', 0, '2026-07-05 01:28:49'),
(39, 15, 2, 'O pedido da Mesa 8 mudou para: *finalizado*', 0, '2026-07-05 01:28:52'),
(40, 15, 2, 'O pedido da Mesa 8 mudou para: *entregue*', 0, '2026-07-05 01:29:16'),
(41, 16, 2, 'O pedido da Mesa 4 mudou para: *em preparo*', 0, '2026-07-05 01:48:24'),
(42, 16, 2, 'O pedido da Mesa 4 mudou para: *finalizado*', 0, '2026-07-05 01:48:27'),
(43, 16, 2, 'O pedido da Mesa 4 mudou para: *entregue*', 0, '2026-07-05 01:48:29'),
(44, 17, 2, 'O pedido da Mesa 6 mudou para: *em preparo*', 0, '2026-07-05 02:00:57'),
(45, 17, 2, 'O pedido da Mesa 6 mudou para: *finalizado*', 0, '2026-07-05 02:00:58'),
(46, 17, 2, 'O pedido da Mesa 6 mudou para: *entregue*', 0, '2026-07-05 02:01:00'),
(47, 18, 2, 'O pedido da Mesa 17 mudou para: *em preparo*', 0, '2026-07-05 02:02:19'),
(48, 18, 2, 'O pedido da Mesa 17 mudou para: *finalizado*', 0, '2026-07-05 02:02:20'),
(49, 18, 2, 'O pedido da Mesa 17 mudou para: *entregue*', 0, '2026-07-05 02:02:22'),
(56, 21, 2, 'O pedido da Mesa 3 mudou para: *em preparo*', 0, '2026-07-05 03:02:33'),
(57, 21, 2, 'O pedido da Mesa 3 mudou para: *finalizado*', 0, '2026-07-05 03:02:35'),
(58, 21, 2, 'O pedido da Mesa 3 mudou para: *entregue*', 0, '2026-07-05 03:02:38'),
(59, 22, 2, 'O pedido da Mesa 8 mudou para: *em preparo*', 0, '2026-07-05 03:03:43'),
(60, 22, 2, 'O pedido da Mesa 8 mudou para: *finalizado*', 0, '2026-07-05 03:03:45'),
(61, 22, 2, 'O pedido da Mesa 8 mudou para: *entregue*', 0, '2026-07-05 03:03:47'),
(62, 23, 2, 'O pedido da Mesa 19 mudou para: *em preparo*', 0, '2026-07-05 03:32:44'),
(63, 23, 2, 'O pedido da Mesa 19 mudou para: *finalizado*', 0, '2026-07-05 03:32:45'),
(64, 23, 2, 'O pedido da Mesa 19 mudou para: *entregue*', 0, '2026-07-05 03:32:50'),
(65, 24, 6, 'O pedido da Mesa 3 mudou para: *em preparo*', 0, '2026-07-05 03:39:02'),
(66, 24, 6, 'O pedido da Mesa 3 mudou para: *finalizado*', 0, '2026-07-05 03:39:03'),
(67, 24, 6, 'O pedido da Mesa 3 mudou para: *entregue*', 0, '2026-07-05 03:39:04'),
(68, 25, 6, 'O pedido da Mesa 10 mudou para: *em preparo*', 0, '2026-07-05 12:55:19'),
(69, 25, 6, 'O pedido da Mesa 10 mudou para: *finalizado*', 0, '2026-07-05 12:55:23'),
(70, 25, 6, 'O pedido da Mesa 10 mudou para: *entregue*', 0, '2026-07-05 12:55:28'),
(71, 26, 6, 'O pedido da Mesa 12 mudou para: *em preparo*', 0, '2026-07-05 12:57:54'),
(72, 26, 6, 'O pedido da Mesa 12 mudou para: *finalizado*', 0, '2026-07-05 12:57:55'),
(73, 26, 6, 'O pedido da Mesa 12 mudou para: *em preparo*', 0, '2026-07-05 12:57:57'),
(74, 26, 6, 'O pedido da Mesa 12 mudou para: *pendente*', 0, '2026-07-05 12:57:59'),
(75, 26, 6, 'O pedido da Mesa 12 mudou para: *em preparo*', 0, '2026-07-05 12:58:02'),
(76, 26, 6, 'O pedido da Mesa 12 mudou para: *pendente*', 0, '2026-07-05 13:05:33'),
(77, 26, 6, 'O pedido da Mesa 12 mudou para: *finalizado*', 0, '2026-07-05 13:05:35'),
(78, 26, 6, 'O pedido da Mesa 12 mudou para: *em preparo*', 0, '2026-07-05 13:05:36'),
(79, 26, 6, 'O pedido da Mesa 12 mudou para: *pendente*', 0, '2026-07-05 13:05:38'),
(80, 26, 6, 'O pedido da Mesa 12 mudou para: *em preparo*', 0, '2026-07-05 13:09:57'),
(81, 26, 6, 'O pedido da Mesa 12 mudou para: *pendente*', 0, '2026-07-05 13:09:59'),
(82, 26, 6, 'O pedido da Mesa 12 mudou para: *em preparo*', 0, '2026-07-05 13:13:25'),
(83, 26, 6, 'O pedido da Mesa 12 mudou para: *finalizado*', 0, '2026-07-06 20:37:57'),
(84, 26, 6, 'O pedido da Mesa 12 mudou para: *entregue*', 0, '2026-07-06 20:37:59'),
(85, 27, 6, 'O pedido da Mesa 11 mudou para: *em preparo*', 0, '2026-07-06 20:38:01'),
(86, 27, 6, 'O pedido da Mesa 11 mudou para: *finalizado*', 0, '2026-07-06 20:38:02'),
(87, 27, 6, 'O pedido da Mesa 11 mudou para: *entregue*', 0, '2026-07-06 20:38:03'),
(88, 28, 6, 'O pedido da Mesa 13 mudou para: *em preparo*', 0, '2026-07-06 20:49:48'),
(89, 28, 6, 'O pedido da Mesa 13 mudou para: *pendente*', 0, '2026-07-06 20:49:49'),
(90, 28, 6, 'O pedido da Mesa 13 mudou para: *em preparo*', 0, '2026-07-06 20:49:53'),
(91, 28, 6, 'O pedido da Mesa 13 mudou para: *finalizado*', 0, '2026-07-06 20:50:02'),
(92, 28, 6, 'O pedido da Mesa 13 mudou para: *entregue*', 0, '2026-07-06 20:50:06'),
(93, 29, 6, 'O pedido da Mesa 9 mudou para: *em preparo*', 0, '2026-07-07 13:53:30'),
(94, 29, 6, 'O pedido da Mesa 9 mudou para: *finalizado*', 0, '2026-07-07 13:53:42'),
(95, 29, 6, 'O pedido da Mesa 9 mudou para: *entregue*', 0, '2026-07-07 13:53:55'),
(96, 30, 2, 'O pedido da Mesa 13 mudou para: *em preparo*', 0, '2026-07-07 15:09:32'),
(97, 30, 2, 'O pedido da Mesa 13 mudou para: *finalizado*', 0, '2026-07-07 15:22:38'),
(98, 30, 2, 'O pedido da Mesa 13 mudou para: *entregue*', 0, '2026-07-07 15:22:40'),
(99, 31, 2, 'O pedido da Mesa 5 mudou para: *em preparo*', 0, '2026-07-07 15:22:41'),
(100, 31, 2, 'O pedido da Mesa 5 mudou para: *finalizado*', 0, '2026-07-07 15:22:42'),
(101, 31, 2, 'O pedido da Mesa 5 mudou para: *entregue*', 0, '2026-07-07 15:22:44'),
(102, 32, 2, 'O pedido da Mesa 17 mudou para: *em preparo*', 0, '2026-07-07 15:54:49'),
(103, 32, 2, 'O pedido da Mesa 17 mudou para: *finalizado*', 0, '2026-07-07 15:54:51'),
(104, 32, 2, 'O pedido da Mesa 17 mudou para: *entregue*', 0, '2026-07-07 15:54:53'),
(105, 33, 2, 'O pedido da Mesa 16 mudou para: *em preparo*', 0, '2026-07-07 16:07:10'),
(106, 33, 2, 'O pedido da Mesa 16 mudou para: *finalizado*', 0, '2026-07-07 16:07:13'),
(107, 33, 2, 'O pedido da Mesa 16 mudou para: *entregue*', 0, '2026-07-07 16:07:15'),
(108, 34, 2, 'O pedido da Mesa 12 mudou para: *em preparo*', 0, '2026-07-07 17:15:41'),
(109, 34, 2, 'O pedido da Mesa 12 mudou para: *finalizado*', 0, '2026-07-07 17:15:49'),
(110, 35, 2, 'O pedido da Mesa 8 mudou para: *em preparo*', 0, '2026-07-09 21:09:16'),
(111, 35, 2, 'O pedido da Mesa 8 mudou para: *finalizado*', 0, '2026-07-09 21:09:17'),
(112, 35, 2, 'O pedido da Mesa 8 mudou para: *entregue*', 0, '2026-07-09 21:09:19'),
(113, 34, 2, 'O pedido da Mesa 12 mudou para: *entregue*', 0, '2026-07-12 17:23:28'),
(114, 36, 2, 'O pedido da Mesa 9 mudou para: *em preparo*', 0, '2026-07-12 17:23:54'),
(115, 36, 2, 'O pedido da Mesa 9 mudou para: *finalizado*', 0, '2026-07-12 17:23:57'),
(116, 36, 2, 'O pedido da Mesa 9 mudou para: *entregue*', 0, '2026-07-12 17:24:04'),
(117, 39, 2, 'O pedido da Mesa 15 mudou para: *em preparo*', 0, '2026-07-12 17:24:52'),
(118, 39, 2, 'O pedido da Mesa 15 mudou para: *finalizado*', 0, '2026-07-12 17:24:54'),
(119, 37, 2, 'O pedido da Mesa 5 mudou para: *em preparo*', 0, '2026-07-13 20:24:09'),
(120, 37, 2, 'O pedido da Mesa 5 mudou para: *finalizado*', 0, '2026-07-13 20:24:16'),
(121, 37, 2, 'O pedido da Mesa 5 mudou para: *entregue*', 0, '2026-07-13 20:24:28'),
(122, 38, 2, 'O pedido da Mesa 9 mudou para: *em preparo*', 0, '2026-07-13 20:24:31'),
(123, 38, 2, 'O pedido da Mesa 9 mudou para: *finalizado*', 0, '2026-07-13 20:24:33'),
(124, 38, 2, 'O pedido da Mesa 9 mudou para: *entregue*', 0, '2026-07-13 20:24:34'),
(125, 39, 2, 'O pedido da Mesa 15 mudou para: *em preparo*', 0, '2026-07-13 20:24:36'),
(126, 39, 2, 'O pedido da Mesa 15 mudou para: *entregue*', 0, '2026-07-13 20:24:37'),
(127, 40, 2, 'O pedido da Mesa 10 mudou para: *em preparo*', 0, '2026-07-13 20:24:40'),
(128, 40, 2, 'O pedido da Mesa 10 mudou para: *finalizado*', 0, '2026-07-13 20:24:41'),
(129, 41, 2, 'O pedido da Mesa 10 mudou para: *em preparo*', 0, '2026-07-13 20:24:49'),
(130, 40, 2, 'O pedido da Mesa 10 mudou para: *entregue*', 0, '2026-07-15 23:05:59'),
(131, 41, 2, 'O pedido da Mesa 10 mudou para: *entregue*', 0, '2026-07-15 23:06:02'),
(132, 42, 2, 'O pedido da Mesa 12 mudou para: *em preparo*', 0, '2026-07-15 23:08:00'),
(133, 42, 2, 'O pedido da Mesa 12 mudou para: *finalizado*', 0, '2026-07-15 23:08:03'),
(134, 42, 2, 'O pedido da Mesa 12 mudou para: *entregue*', 0, '2026-07-15 23:10:55'),
(135, 43, 2, 'O pedido da Mesa 9 mudou para: *em preparo*', 0, '2026-07-15 23:36:58'),
(136, 43, 2, 'O pedido da Mesa 9 mudou para: *finalizado*', 0, '2026-07-15 23:37:05'),
(137, 43, 2, 'O pedido da Mesa 9 mudou para: *entregue*', 0, '2026-07-15 23:38:52'),
(138, 44, 2, 'O pedido da Mesa 13 mudou para: *em preparo*', 0, '2026-07-16 20:53:13'),
(139, 44, 2, 'O pedido da Mesa 13 mudou para: *finalizado*', 0, '2026-07-16 20:53:18'),
(140, 44, 2, 'O pedido da Mesa 13 mudou para: *entregue*', 0, '2026-07-16 20:53:21'),
(141, 45, 2, 'O pedido da Mesa 15 mudou para: *em preparo*', 0, '2026-07-16 20:53:24'),
(142, 45, 2, 'O pedido da Mesa 15 mudou para: *em preparo*', 0, '2026-07-16 20:53:25'),
(143, 45, 2, 'O pedido da Mesa 15 mudou para: *pendente*', 0, '2026-07-16 20:53:26'),
(144, 45, 2, 'O pedido da Mesa 15 mudou para: *em preparo*', 0, '2026-07-16 20:53:28'),
(145, 45, 2, 'O pedido da Mesa 15 mudou para: *finalizado*', 0, '2026-07-16 22:22:08'),
(146, 45, 2, 'O pedido da Mesa 15 mudou para: *entregue*', 0, '2026-07-16 22:22:10'),
(147, 46, 2, 'O pedido da Mesa 10 mudou para: *finalizado*', 0, '2026-07-16 22:22:14'),
(148, 46, 2, 'O pedido da Mesa 10 mudou para: *pendente*', 0, '2026-07-17 00:02:55'),
(149, 47, 2, 'O pedido da Mesa 5 mudou para: *em preparo*', 0, '2026-07-17 00:03:00'),
(150, 47, 2, 'O pedido da Mesa 5 mudou para: *finalizado*', 0, '2026-07-17 00:03:03'),
(151, 47, 2, 'O pedido da Mesa 5 mudou para: *entregue*', 0, '2026-07-17 00:03:09'),
(152, 48, 2, 'O pedido da Mesa 1 mudou para: *em preparo*', 0, '2026-07-17 00:45:02'),
(153, 48, 2, 'O pedido da Mesa 1 mudou para: *finalizado*', 0, '2026-07-17 00:45:04'),
(154, 48, 2, 'O pedido da Mesa 1 mudou para: *entregue*', 0, '2026-07-17 00:45:08'),
(155, 46, 2, 'O pedido da Mesa 10 mudou para: *em preparo*', 0, '2026-07-17 00:46:19'),
(156, 46, 2, 'O pedido da Mesa 10 mudou para: *finalizado*', 0, '2026-07-17 00:46:22'),
(157, 46, 2, 'O pedido da Mesa 10 mudou para: *finalizado*', 0, '2026-07-17 00:46:23'),
(158, 46, 2, 'O pedido da Mesa 10 mudou para: *entregue*', 0, '2026-07-17 00:46:24');


CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `mesa_id` int(11) NOT NULL,
  `usuario_garcom_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `status_pedido` enum('pendente','em preparo','finalizado','entregue') DEFAULT 'pendente',
  `status_pagamento` varchar(20) DEFAULT 'pendente',
  `forma_pagamento` enum('dinheiro','cartao_credito','cartao_debito','pix') NOT NULL,
  `valor_total` decimal(10,2) DEFAULT 0.00,
  `data_horario` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `pedidos` (`id`, `mesa_id`, `usuario_garcom_id`, `cliente_id`, `status_pedido`, `status_pagamento`, `forma_pagamento`, `valor_total`, `data_horario`) VALUES
(1, 13, 2, 1, 'entregue', 'pendente', 'pix', 129.50, '2026-07-04 21:22:41'),
(2, 14, 2, 2, 'entregue', 'pendente', 'pix', 7.50, '2026-07-04 21:23:51'),
(3, 3, 2, 3, 'entregue', 'pendente', 'cartao_debito', 48.00, '2026-07-04 21:41:23'),
(4, 6, 2, 4, 'entregue', 'pendente', 'pix', 8.00, '2026-07-04 23:38:22'),
(5, 1, 2, 5, 'entregue', 'pendente', 'cartao_credito', 166.00, '2026-07-04 23:43:22'),
(6, 1, 2, 6, 'entregue', 'pendente', 'dinheiro', 14.50, '2026-07-05 00:01:45'),
(7, 4, 2, 7, 'entregue', 'pendente', 'cartao_debito', 299.00, '2026-07-05 00:15:24'),
(8, 2, 2, 8, 'entregue', 'pendente', 'cartao_credito', 133.50, '2026-07-05 00:16:47'),
(9, 5, 2, 9, 'entregue', 'pendente', 'dinheiro', 123.00, '2026-07-05 00:35:12'),
(10, 6, 2, 10, 'entregue', 'pendente', 'pix', 30.00, '2026-07-05 00:45:12'),
(11, 10, 2, 11, 'entregue', 'pendente', 'cartao_credito', 415.50, '2026-07-05 00:49:00'),
(15, 8, 2, 15, 'entregue', 'pendente', 'cartao_debito', 7.50, '2026-07-05 01:28:08'),
(16, 4, 2, 16, 'entregue', 'pendente', 'pix', 7.50, '2026-07-05 01:48:04'),
(17, 6, 2, 17, 'entregue', 'pendente', 'pix', 8.00, '2026-07-05 01:59:09'),
(18, 17, 2, 18, 'entregue', 'pendente', 'pix', 7.50, '2026-07-05 02:02:02'),
(21, 3, 2, 21, 'entregue', 'pendente', 'cartao_credito', 22.00, '2026-07-05 03:02:11'),
(22, 8, 2, 22, 'entregue', 'pendente', 'cartao_debito', 8.00, '2026-07-05 03:03:28'),
(23, 19, 2, 23, 'entregue', 'pendente', 'cartao_debito', 92.00, '2026-07-05 03:32:19'),
(24, 3, 6, 24, 'entregue', 'pendente', 'cartao_debito', 108.00, '2026-07-05 03:38:37'),
(25, 10, 6, 25, 'entregue', 'pendente', 'dinheiro', 7.50, '2026-07-05 12:52:39'),
(26, 12, 6, 26, 'entregue', 'pendente', 'cartao_credito', 68.00, '2026-07-05 12:56:01'),
(27, 11, 6, 27, 'entregue', 'pendente', 'dinheiro', 22.00, '2026-07-06 20:37:06'),
(28, 13, 6, 28, 'entregue', 'pendente', 'pix', 96.00, '2026-07-06 20:48:34'),
(29, 9, 6, 29, 'entregue', 'pendente', 'dinheiro', 191.00, '2026-07-07 13:51:00'),
(30, 13, 2, 28, 'entregue', 'pendente', 'cartao_credito', 7.50, '2026-07-07 15:05:43'),
(31, 5, 2, 39, 'entregue', 'pendente', 'pix', 85.00, '2026-07-07 15:21:47'),
(32, 17, 2, 29, 'entregue', 'pendente', 'dinheiro', 7.00, '2026-07-07 15:54:29'),
(33, 16, 2, 39, 'entregue', 'pendente', 'pix', 28.00, '2026-07-07 16:06:51'),
(34, 12, 2, 40, 'entregue', 'pendente', 'dinheiro', 88.00, '2026-07-07 17:14:03'),
(35, 8, 2, 27, 'entregue', 'pendente', 'pix', 7.00, '2026-07-09 21:08:41'),
(36, 9, 2, 28, 'entregue', 'pendente', 'dinheiro', 29.00, '2026-07-09 21:10:05'),
(37, 5, 2, 28, 'entregue', 'pendente', 'pix', 29.00, '2026-07-09 21:20:45'),
(38, 9, 2, 29, 'entregue', 'pendente', 'cartao_credito', 100.00, '2026-07-09 21:21:27'),
(39, 15, 2, 41, 'entregue', 'pendente', 'cartao_debito', 58.00, '2026-07-12 17:21:28'),
(40, 10, 2, 42, 'entregue', 'pendente', 'pix', 48.00, '2026-07-13 20:22:45'),
(41, 10, 2, 31, 'entregue', 'pendente', 'pix', 14.50, '2026-07-13 20:23:30'),
(42, 12, 2, 39, 'entregue', 'pendente', 'cartao_credito', 23.00, '2026-07-15 23:07:16'),
(43, 9, 2, 44, 'entregue', 'pendente', 'pix', 28.00, '2026-07-15 23:35:32'),
(44, 13, 2, 46, 'entregue', 'pago', 'cartao_debito', 7.00, '2026-07-16 20:01:11'),
(45, 15, 2, 47, 'entregue', 'pago', 'dinheiro', 7.00, '2026-07-16 20:43:11'),
(46, 10, 2, 39, 'entregue', 'pago', 'pix', 92.00, '2026-07-16 22:20:31'),
(47, 5, 2, 39, 'entregue', 'pendente', '', 8.00, '2026-07-17 00:02:11'),
(48, 1, 2, 27, 'entregue', 'pago', 'pix', 28.00, '2026-07-17 00:44:26');



CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cargo` enum('administrador','garcom','cozinheiro') NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `cargo`, `criado_em`) VALUES
(1, 'Ana Admin', 'admin@chefinhas.com', '$2y$10$7V.qEdd0XQ6TiZ90CfpZ4ut62YB3B98S7BjENlkPOfunDTPgdZ1kS', 'administrador', '2026-07-04 20:58:27'),
(2, 'Gabriel Garçom', 'garcom@chefinhas.com', '$2y$10$aLSmm9IlkdWjy1mkFbS27udJ9dtbzHCNqpaEXScSG17JRoCgXy6qa', 'garcom', '2026-07-04 20:58:27'),
(3, 'Carlos Cozinheiro', 'cozinha@chefinhas.com', '$2y$10$SH/dxpohRXlUioHSakGGEu9.kvktylC.wixANfLQJyFFCHVgkTugC', 'cozinheiro', '2026-07-04 20:58:27'),
(4, 'Sara Rodrigues', 'saracon@gmail.com', '$2y$10$VHSgGdEw71ArHTOfablHxeYQmJftUBL/RlckYekE8uZZa5llhkfMe', 'cozinheiro', '2026-07-04 21:48:09'),
(6, 'Izabela Lisboa', 'lisboa@gmail.com', '$2y$10$qIHBkxVpQVIpz1fEdeblPufk7vd7mgy6K8kkteCIQXQyZ8e.uAwH6', 'garcom', '2026-07-05 03:36:35'),
(8, 'Heloisa Rodrigues', 'heloisa@gmail.com', '$2y$10$MlNJuDXHt6vyjtFUOgsc1./.3DKFkIBhylxeERgY7OpE4JINKzqq2', 'garcom', '2026-07-16 21:50:54');


ALTER TABLE `caixa_diario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data_dia` (`data_dia`);

ALTER TABLE `cardapio`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `despesas`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `cardapio_id` (`cardapio_id`);

ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_mesa` (`numero_mesa`);

ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `garcom_id` (`garcom_id`);

ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mesa_id` (`mesa_id`),
  ADD KEY `usuario_garcom_id` (`usuario_garcom_id`),
  ADD KEY `cliente_id` (`cliente_id`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `caixa_diario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `cardapio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

ALTER TABLE `despesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `itens_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`cardapio_id`) REFERENCES `cardapio` (`id`);

ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacoes_ibfk_2` FOREIGN KEY (`garcom_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`usuario_garcom_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);
COMMIT;