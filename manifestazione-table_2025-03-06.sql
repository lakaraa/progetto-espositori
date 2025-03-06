-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Creato il: Mar 06, 2025 alle 21:05
-- Versione del server: 8.2.0
-- Versione PHP: 8.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `espositori`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `manifestazione`
--

CREATE TABLE `manifestazione` (
  `Id_Manifestazione` int NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `Descrizione` text,
  `Luogo` varchar(100) NOT NULL,
  `Durata` int NOT NULL,
  `Data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `manifestazione`
--

INSERT INTO `manifestazione` (`Id_Manifestazione`, `Nome`, `Descrizione`, `Luogo`, `Durata`, `Data`) VALUES
(1, 'Festival della Tecnologia', 'Manifestazione dedicata alle nuove tecnologie.', 'Palazzo della Cultura', 3, '2025-06-15'),
(2, 'Arte Contemporanea Expo', 'Esposizione di arte contemporanea.', 'Centro Arte Moderna', 5, '2025-06-20'),
(3, 'Fiera della Moda', 'Evento dedicato alla moda del futuro.', 'Fiera di Milano', 4, '2025-07-01'),
(4, 'Festival della Gastronomia', 'Evento culinario che esplora la gastronomia mondiale.', 'Sala Grande', 2, '2025-07-05'),
(5, 'Scienza e Innovazione', 'Esposizione delle ultime scoperte scientifiche.', 'Museo delle Scienze', 4, '2025-08-10'),
(6, 'Cinema Futuro', 'Eventi e discussioni sul futuro del cinema.', 'Teatro Nuovo', 3, '2025-08-15'),
(7, 'Arte Rurale e Tradizioni', 'Esposizione su arte e tradizioni rurali.', 'Palazzo Storico', 6, '2025-09-01'),
(8, 'Invenzioni e Innovazioni', 'Esposizione di invenzioni e tecnologie emergenti.', 'Centro Invenzioni', 3, '2025-09-10'),
(9, 'Fiera del Design', 'Manifestazione sul design sostenibile.', 'Fiera di Roma', 4, '2025-09-20'),
(10, 'Festival della Musica', 'Evento che celebra la musica in tutte le sue forme.', 'Teatro Nazionale', 5, '2025-10-01'),
(11, 'Evento Educazione e Cultura', 'Un evento di discussione sulla cultura e l’educazione.', 'Centro Universitario', 3, '2025-10-10'),
(12, 'Festival della Sostenibilità', 'Un festival per sensibilizzare sulla sostenibilità.', 'Parco della Natura', 2, '2025-10-15'),
(13, 'Esposizione di Scultura', 'Mostra di scultura contemporanea.', 'Galleria Arte', 3, '2025-11-01'),
(14, 'Festival della Scienza', 'Un evento che celebra le scienze naturali e la tecnologia.', 'Laboratorio Scientifico', 5, '2025-11-05'),
(15, 'Fotografia e Storia', 'Mostra fotografica storica.', 'Museo di Fotografia', 3, '2025-11-15'),
(16, 'Arte e Natura', 'Evento sull’interazione tra arte e natura.', 'Giardino Botanico', 2, '2025-12-01'),
(17, 'Design e Innovazione', 'Esposizione delle ultime tendenze del design.', 'Centro Design', 4, '2025-12-05'),
(18, 'Incontri con l’Arte', 'Esposizione artistica internazionale.', 'Palazzo dei Congressi', 6, '2025-12-10'),
(19, 'Innovazioni nel Cinema', 'Evento per discutere l’evoluzione del cinema.', 'Cineplex', 2, '2025-12-20'),
(20, 'Storia e Cultura', 'Mostra di arte e storia culturale mondiale.', 'Museo Storico', 5, '2025-12-25');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `manifestazione`
--
ALTER TABLE `manifestazione`
  ADD PRIMARY KEY (`Id_Manifestazione`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `manifestazione`
--
ALTER TABLE `manifestazione`
  MODIFY `Id_Manifestazione` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
