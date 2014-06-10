-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 06, 2014 at 02:03 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `moerin_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `etat`
--

CREATE TABLE IF NOT EXISTS `etat` (
  `id` char(2) NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `etat`
--

INSERT INTO `etat` (`id`, `libelle`) VALUES
('CL', 'Saisie clôturée'),
('CR', 'Fiche créée, saisie en cours'),
('MP', 'Mise en paiement'),
('RB', 'Remboursée'),
('VA', 'Validée');

-- --------------------------------------------------------

--
-- Table structure for table `fichefrais`
--

CREATE TABLE IF NOT EXISTS `fichefrais` (
  `idVisiteur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `nbJustificatifs` int(11) DEFAULT NULL,
  `montantValide` decimal(10,2) DEFAULT NULL,
  `dateModif` date DEFAULT NULL,
  `idEtat` char(2) DEFAULT 'CR',
  PRIMARY KEY (`idVisiteur`,`mois`),
  KEY `idEtat` (`idEtat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fichefrais`
--

INSERT INTO `fichefrais` (`idVisiteur`, `mois`, `nbJustificatifs`, `montantValide`, `dateModif`, `idEtat`) VALUES
('a131', '201312', 4, '1334.20', '2014-02-12', 'VA'),
('a131', '201401', 0, '2336.20', '2014-02-12', 'VA'),
('a131', '201402', 0, '1334.84', '2014-06-04', 'RB'),
('a131', '201404', 2, '1495.31', '2014-06-04', 'CL'),
('a131', '201405', 0, '1091.12', '2014-06-04', 'RB'),
('a131', '201406', 0, NULL, '2014-06-03', 'CR'),
('a17', '201401', 2, '439.12', '2014-06-04', 'CL'),
('a17', '201402', 0, NULL, '2014-05-11', 'CL'),
('a17', '201406', 0, NULL, '2014-06-04', 'CR'),
('a55', '201402', 0, NULL, '2014-05-11', 'CL');

-- --------------------------------------------------------

--
-- Table structure for table `fonction`
--

CREATE TABLE IF NOT EXISTS `fonction` (
  `id` int(1) NOT NULL DEFAULT '0',
  `libelleFonction` char(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fonction`
--

INSERT INTO `fonction` (`id`, `libelleFonction`) VALUES
(1, 'Visiteur'),
(2, 'Comptable');

-- --------------------------------------------------------

--
-- Table structure for table `fraisforfait`
--

CREATE TABLE IF NOT EXISTS `fraisforfait` (
  `id` char(6) NOT NULL,
  `libelle` char(40) DEFAULT NULL,
  `montant` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fraisforfait`
--

INSERT INTO `fraisforfait` (`id`, `libelle`, `montant`) VALUES
('ETP', 'Forfait Etape', '110.00'),
('KM4d', 'Frais Kilométriques 4CV Diesel', '0.52'),
('KM4e', 'Frais Kilométriques 4CV Essence', '0.62'),
('KM56d', 'Frais Kilométriques 5/6CV Diesel', '0.58'),
('KM56e', 'Frais Kilométriques 5/6CV Essence', '0.67'),
('NUI', 'Nuitée(s) Hôtel', '80.00'),
('REP', 'Repas Restaurant', '25.00');

-- --------------------------------------------------------

--
-- Table structure for table `lignefraisforfait`
--

CREATE TABLE IF NOT EXISTS `lignefraisforfait` (
  `idVisiteur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `idFraisForfait` char(6) NOT NULL,
  `quantite` int(11) DEFAULT NULL,
  PRIMARY KEY (`idVisiteur`,`mois`,`idFraisForfait`),
  KEY `idFraisForfait` (`idFraisForfait`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lignefraisforfait`
--

INSERT INTO `lignefraisforfait` (`idVisiteur`, `mois`, `idFraisForfait`, `quantite`) VALUES
('a131', '201312', 'ETP', 12),
('a131', '201312', 'KM4d', 0),
('a131', '201312', 'KM4e', 0),
('a131', '201312', 'KM56d', 0),
('a131', '201312', 'KM56e', 0),
('a131', '201312', 'NUI', 0),
('a131', '201312', 'REP', 0),
('a131', '201401', 'ETP', 8),
('a131', '201401', 'KM4d', 0),
('a131', '201401', 'KM4e', 10),
('a131', '201401', 'KM56d', 0),
('a131', '201401', 'KM56e', 0),
('a131', '201401', 'NUI', 15),
('a131', '201401', 'REP', 10),
('a131', '201402', 'ETP', 3),
('a131', '201402', 'KM4d', 12),
('a131', '201402', 'KM4e', 30),
('a131', '201402', 'KM56d', 0),
('a131', '201402', 'KM56e', 0),
('a131', '201402', 'NUI', 6),
('a131', '201402', 'REP', 20),
('a131', '201404', 'ETP', 3),
('a131', '201404', 'KM4d', 75),
('a131', '201404', 'KM4e', 0),
('a131', '201404', 'KM56d', 35),
('a131', '201404', 'KM56e', 3),
('a131', '201404', 'NUI', 3),
('a131', '201404', 'REP', 3),
('a131', '201405', 'ETP', 5),
('a131', '201405', 'KM4d', 100),
('a131', '201405', 'KM4e', 0),
('a131', '201405', 'KM56d', 0),
('a131', '201405', 'KM56e', 36),
('a131', '201405', 'NUI', 3),
('a131', '201405', 'REP', 7),
('a131', '201406', 'ETP', 2),
('a131', '201406', 'KM4d', 100),
('a131', '201406', 'KM4e', 0),
('a131', '201406', 'KM56d', 0),
('a131', '201406', 'KM56e', 150),
('a131', '201406', 'NUI', 2),
('a131', '201406', 'REP', 3),
('a17', '201401', 'ETP', 0),
('a17', '201401', 'KM4d', 49),
('a17', '201401', 'KM4e', 22),
('a17', '201401', 'KM56d', 0),
('a17', '201401', 'KM56e', 0),
('a17', '201401', 'NUI', 5),
('a17', '201401', 'REP', 0),
('a17', '201402', 'ETP', 3),
('a17', '201402', 'KM4d', 43),
('a17', '201402', 'KM4e', 0),
('a17', '201402', 'KM56d', 0),
('a17', '201402', 'KM56e', 0),
('a17', '201402', 'NUI', 5),
('a17', '201402', 'REP', 7),
('a17', '201406', 'ETP', 1),
('a17', '201406', 'KM4d', 15),
('a17', '201406', 'KM4e', 0),
('a17', '201406', 'KM56d', 12),
('a17', '201406', 'KM56e', 0),
('a17', '201406', 'NUI', 1),
('a17', '201406', 'REP', 100);

-- --------------------------------------------------------

--
-- Table structure for table `lignefraishorsforfait`
--

CREATE TABLE IF NOT EXISTS `lignefraishorsforfait` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idVisiteur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `libelle` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idVisiteur` (`idVisiteur`,`mois`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `lignefraishorsforfait`
--

INSERT INTO `lignefraishorsforfait` (`id`, `idVisiteur`, `mois`, `libelle`, `date`, `montant`) VALUES
(2, 'a131', '201312', 'Karting', '2013-12-07', '100.00'),
(3, 'a131', '201312', 'Patinoire', '2013-12-07', '2.20'),
(5, 'a131', '201312', 'Piscine', '2013-11-30', '12.00'),
(6, 'a55', '201402', 'Restaurant Docteur Simon', '2014-02-02', '50.00'),
(7, 'a131', '201405', 'REFUSÉ : Conference Doliprane', '2014-06-04', '50.00'),
(8, 'a131', '201404', 'REFUSÉ : Formation Besoin Client', '2014-06-06', '789.00'),
(30, 'a131', '201406', 'Achats Fournitures', '2014-06-03', '31.20');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` char(4) NOT NULL,
  `nom` char(30) DEFAULT NULL,
  `prenom` char(30) DEFAULT NULL,
  `login` char(20) DEFAULT NULL,
  `mdp` char(60) DEFAULT NULL,
  `adresse` char(30) DEFAULT NULL,
  `cp` char(5) DEFAULT NULL,
  `ville` char(30) DEFAULT NULL,
  `dateEmbauche` date DEFAULT NULL,
  `idFonction` int(1) DEFAULT NULL,
  `hash` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_fonction` (`idFonction`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `login`, `mdp`, `adresse`, `cp`, `ville`, `dateEmbauche`, `idFonction`, `hash`) VALUES
('a131', 'Villechalane', 'Louis', 'lvillachane', 'jux7g', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', 1, 0),
('a17', 'Andre', 'David', 'dandre', 'oppg5', '1 rue Petit', '46200', 'Lalbenque', '1998-11-23', 1, 0),
('a55', 'Bedos', 'Christian', 'cbedos', 'gmhxd', '1 rue Peranud', '46250', 'Montcuq', '1995-01-12', 1, 0),
('a93', 'Tusseau', 'Louis', 'ltusseau', 'ktp3s', '22 rue des Ternes', '46123', 'Gramat', '2000-05-01', 1, 0),
('b13', 'Bentot', 'Pascal', 'pbentot', 'doyw1', '11 allée des Cerises', '46512', 'Bessines', '1992-07-09', 1, 0),
('b16', 'Bioret', 'Luc', 'lbioret', 'hrjfs', '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11', 1, 0),
('b19', 'Bunisset', 'Francis', 'fbunisset', '4vbnd', '10 rue des Perles', '93100', 'Montreuil', '1987-10-21', 1, 0),
('b25', 'Bunisset', 'Denise', 'dbunisset', 's1y1r', '23 rue Manin', '75019', 'paris', '2010-12-05', 1, 0),
('b28', 'Cacheux', 'Bernard', 'bcacheux', 'uf7r3', '114 rue Blanche', '75017', 'Paris', '2009-11-12', 1, 0),
('b34', 'Cadic', 'Eric', 'ecadic', '6u8dc', '123 avenue de la République', '75011', 'Paris', '2008-09-23', 1, 0),
('b4', 'Charoze', 'Catherine', 'ccharoze', 'u817o', '100 rue Petit', '75019', 'Paris', '2005-11-12', 1, 0),
('b50', 'Clepkens', 'Christophe', 'cclepkens', 'bw1us', '12 allée des Anges', '93230', 'Romainville', '2003-08-11', 1, 0),
('b59', 'Cottin', 'Vincenne', 'vcottin', '2hoh9', '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18', 1, 0),
('c01', 'Maurice', 'Roger', 'mroger', 'iklo', NULL, NULL, NULL, NULL, 2, 0),
('c14', 'Daburon', 'François', 'fdaburon', '7oqpv', '13 rue de Chanzy', '94000', 'Créteil', '2002-02-11', 1, 0),
('c3', 'De', 'Philippe', 'pde', 'gk9kx', '13 rue Barthes', '94000', 'Créteil', '2010-12-14', 1, 0),
('c54', 'Debelle', 'Michel', 'mdebelle', 'od5rt', '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23', 1, 0),
('d13', 'Debelle', 'Jeanne', 'jdebelle', 'nvwqq', '134 allée des Joncs', '44000', 'Nantes', '2000-05-11', 1, 0),
('d51', 'Debroise', 'Michel', 'mdebroise', 'sghkb', '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17', 1, 0),
('e22', 'Desmarquest', 'Nathalie', 'ndesmarquest', 'f1fob', '14 Place d Arc', '45000', 'Orléans', '2005-11-12', 1, 0),
('e24', 'Desnost', 'Pierre', 'pdesnost', '4k2o5', '16 avenue des Cèdres', '23200', 'Guéret', '2001-02-05', 1, 0),
('e39', 'Dudouit', 'Frédéric', 'fdudouit', '44im8', '18 rue de l église', '23120', 'GrandBourg', '2000-08-01', 1, 0),
('e49', 'Duncombe', 'Claude', 'cduncombe', 'qf77j', '19 rue de la tour', '23100', 'La souteraine', '1987-10-10', 1, 0),
('e5', 'Enault-Pascreau', 'Céline', 'cenault', 'y2qdu', '25 place de la gare', '23200', 'Gueret', '1995-09-01', 1, 0),
('e52', 'Eynde', 'Valérie', 'veynde', 'i7sn3', '3 Grand Place', '13015', 'Marseille', '1999-11-01', 1, 0),
('f21', 'Finck', 'Jacques', 'jfinck', 'mpb3t', '10 avenue du Prado', '13002', 'Marseille', '2001-11-10', 1, 0),
('f39', 'Frémont', 'Fernande', 'ffremont', 'xs5tq', '4 route de la mer', '13012', 'Allauh', '1998-10-01', 1, 0),
('f4', 'Gest', 'Alain', 'agest', 'dywvt', '30 avenue de la mer', '13025', 'Berre', '1985-11-01', 1, 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fichefrais`
--
ALTER TABLE `fichefrais`
  ADD CONSTRAINT `fichefrais_ibfk_1` FOREIGN KEY (`idEtat`) REFERENCES `etat` (`id`),
  ADD CONSTRAINT `fichefrais_ibfk_2` FOREIGN KEY (`idVisiteur`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `lignefraisforfait`
--
ALTER TABLE `lignefraisforfait`
  ADD CONSTRAINT `lignefraisforfait_ibfk_1` FOREIGN KEY (`idVisiteur`, `mois`) REFERENCES `fichefrais` (`idVisiteur`, `mois`),
  ADD CONSTRAINT `lignefraisforfait_ibfk_2` FOREIGN KEY (`idFraisForfait`) REFERENCES `fraisforfait` (`id`);

--
-- Constraints for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `fk_fonction` FOREIGN KEY (`idFonction`) REFERENCES `fonction` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
