-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Mar 28 Novembre 2017 à 16:28
-- Version du serveur :  5.7.14
-- Version de PHP :  7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `loveletter`
--

-- --------------------------------------------------------

--
-- Structure de la table `carte`
--

CREATE TABLE `carte` (
  `id` int(11) NOT NULL,
  `nomCarte` varchar(500) NOT NULL,
  `rang` int(11) NOT NULL,
  `image` varchar(500) NOT NULL,
  `effet` varchar(500) NOT NULL,
  `nbCarte` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `carte`
--

INSERT INTO `carte` (`id`, `nomCarte`, `rang`, `image`, `effet`, `nbCarte`) VALUES
(1, 'Princess ', 8, 'Princess.png', 'Si vous défaussez cette carte, vous \r\nêtes éliminé de la manche.', 1),
(2, 'Countess', 7, 'Countess.png', 'Si vous avez cette carte en main en \r\nmême temps que le King ou le Prince, alors vous \r\ndevez défausser la carte de la Countess', 1),
(3, 'King', 6, 'King.png', 'Echangez  votre  main  avec  un  autre \r\njoueur de votre choix.', 1),
(4, 'Prince', 5, 'Prince.png', 'Choisissez  un  joueur  (y  compris \r\nvous), celui-ci défausse la carte qu\'il a en main pour \r\nen piocher une nouvelle.', 2),
(6, 'Handmaid', 4, 'Handmaid.png', 'Jusqu\'au prochain tour, vous êtes \r\nprotégé des effets des cartes des autres joueurs.', 2),
(8, 'Baron', 3, 'Baron.png', 'Comparez votre carte avec celle d\'un \r\nautre joueur, celui qui a la carte avec la plus faible \r\nvaleur est éliminé de la manche.', 2),
(10, 'Priest', 2, 'Priest.png', 'Regardez la main d\'un autre joueur.', 2),
(13, 'Guard', 1, 'Guard.png', 'Choisissez un joueur et essayez de \r\ndeviner la carte qu\'il a en main (excepté le Guard), \r\nsi  vous  tombez  juste,  le  joueur  est  éliminé  de  la \r\nmanche.', 5),
(5, 'Prince', 5, 'Prince.png', 'Choisissez  un  joueur  (y  compris \r\nvous), celui-ci défausse la carte qu\'il a en main pour \r\nen piocher une nouvelle.', 2),
(7, 'Handmaid', 4, 'Handmaid.png', 'Jusqu\'au prochain tour, vous êtes \r\nprotégé des effets des cartes des autres joueurs.', 2),
(9, 'Baron', 3, 'Baron.png', 'Comparez votre carte avec celle d\'un \r\nautre joueur, celui qui a la carte avec la plus faible \r\nvaleur est éliminé de la manche.', 2),
(11, 'Priest', 2, 'Priest.png', 'Regardez la main d\'un autre joueur.', 2),
(12, 'Guard', 1, 'Guard.png', 'Choisissez un joueur et essayez de \r\ndeviner la carte qu\'il a en main (excepté le Guard), \r\nsi  vous  tombez  juste,  le  joueur  est  éliminé  de  la \r\nmanche.', 5),
(14, 'Guard', 1, 'Guard.png', 'Choisissez un joueur et essayez de \r\ndeviner la carte qu\'il a en main (excepté le Guard), \r\nsi  vous  tombez  juste,  le  joueur  est  éliminé  de  la \r\nmanche.', 5),
(15, 'Guard', 1, 'Guard.png', 'Choisissez un joueur et essayez de \r\ndeviner la carte qu\'il a en main (excepté le Guard), \r\nsi  vous  tombez  juste,  le  joueur  est  éliminé  de  la \r\nmanche.', 5),
(16, 'Guard', 1, 'Guard.png', 'Choisissez un joueur et essayez de \r\ndeviner la carte qu\'il a en main (excepté le Guard), \r\nsi  vous  tombez  juste,  le  joueur  est  éliminé  de  la \r\nmanche.', 5);

-- --------------------------------------------------------

--
-- Structure de la table `contient`
--

CREATE TABLE `contient` (
  `idCarte` int(11) NOT NULL,
  `idPioche` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `defausse`
--

CREATE TABLE `defausse` (
  `idCarte` int(11) NOT NULL,
  `idPioche` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `joueur`
--

CREATE TABLE `joueur` (
  `id_partie` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `score` int(2) NOT NULL DEFAULT '0',
  `ordre` int(2) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `partie`
--

CREATE TABLE `partie` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `nbMax` int(2) NOT NULL DEFAULT '2',
  `createur` int(11) NOT NULL,
  `nbJoueur` int(2) NOT NULL DEFAULT '0',
  `idgagnant` int(11) DEFAULT '0',
  `joue` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `partie`
--

INSERT INTO `partie` (`id`, `nom`, `nbMax`, `createur`, `nbJoueur`, `idgagnant`, `joue`) VALUES
(1, 'r', 2, 10, 2, 9, 10);

-- --------------------------------------------------------

--
-- Structure de la table `pioche`
--

CREATE TABLE `pioche` (
  `idPioche` int(11) NOT NULL,
  `idPartie` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `pioche`
--

INSERT INTO `pioche` (`idPioche`, `idPartie`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `plateau`
--

CREATE TABLE `plateau` (
  `idCarte` int(11) NOT NULL,
  `idJoueur` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `possede`
--

CREATE TABLE `possede` (
  `idJoueur` int(11) NOT NULL,
  `idCarte` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `auth_level` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `role`
--

INSERT INTO `role` (`id`, `label`, `auth_level`) VALUES
(1, 'admin', 2),
(2, 'user', 1);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `pseudo` varchar(100) NOT NULL,
  `mdp` varchar(500) NOT NULL,
  `partieGagner` int(11) NOT NULL DEFAULT '0',
  `PartiePerdu` int(11) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL,
  `co` int(11) NOT NULL DEFAULT '0',
  `dateco` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `mail`, `pseudo`, `mdp`, `partieGagner`, `PartiePerdu`, `role_id`, `co`, `dateco`) VALUES
(11, 'chrome@gmail.com', 'chrome', '$2y$10$wSkk6ddZ/ERB8L/8YjDewuYPH66iLY808m23iGXCev1lzhkSwWwe6', 0, 0, 1, 0, NULL),
(9, 'edge@gmail.com', 'edge', '$2y$10$lqjOQNb8ST8KHj/g/PkAJ.iXF7M80dveVZ0BMlfO5vmYGSfnAgwY.', 14, 0, 1, 1, '2017-11-28 15:33:41'),
(10, 'firefox@gmail.com', 'firefox', '$2y$10$pMtmn6c9utr3kY6NkAmq../Od4I6poPo7YA0oij8Icff11BGvgNuq', 2, 6, 1, 1, '2017-11-28 15:33:21');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `carte`
--
ALTER TABLE `carte`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `joueur`
--
ALTER TABLE `joueur`
  ADD PRIMARY KEY (`id_partie`,`id_user`);

--
-- Index pour la table `partie`
--
ALTER TABLE `partie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `pioche`
--
ALTER TABLE `pioche`
  ADD PRIMARY KEY (`idPioche`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `partie`
--
ALTER TABLE `partie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `pioche`
--
ALTER TABLE `pioche`
  MODIFY `idPioche` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
