-- phpMyAdmin SQL Dump
-- version 2.9.0.2
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mardi 29 Avril 2008 à 00:01
-- Version du serveur: 5.0.51
-- Version de PHP: 5.2.5
--
-- Base de données: `anixv2`
--

-- --------------------------------------------------------

--
-- Structure de la table `catalogue_info_restocking_delay`
--

DROP TABLE IF EXISTS `catalogue_info_restocking_delay`;
CREATE TABLE IF NOT EXISTS `catalogue_info_restocking_delay` (
  `id_delay` tinyint(3) unsigned NOT NULL,
  `id_language` tinyint(4) NOT NULL,
  `name` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id_delay`,`id_language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `catalogue_info_restocking_delay`
--

INSERT INTO `catalogue_info_restocking_delay` (`id_delay`, `id_language`, `name`) VALUES
(1, 1, '3 Jours'),
(1, 2, '3 Days'),
(2, 1, '10 Jours'),
(2, 2, '10 Days'),
(3, 1, '1 à 3 semaines'),
(3, 2, '1 to 3 weeks'),
(4, 1, '2 Jours'),
(4, 2, '2 Days');

-- --------------------------------------------------------

--
-- Structure de la table `catalogue_restocking_delay`
--

DROP TABLE IF EXISTS `catalogue_restocking_delay`;
CREATE TABLE IF NOT EXISTS `catalogue_restocking_delay` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `delay_days` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Contenu de la table `catalogue_restocking_delay`
--

INSERT INTO `catalogue_restocking_delay` (`id`, `delay_days`) VALUES
(1, 3),
(2, 10),
(3, 21),
(4, 2);

-- --------------------------------------------------------

--
-- Structure de la table `catalogue_state`
--

DROP TABLE IF EXISTS `catalogue_state`;
CREATE TABLE IF NOT EXISTS `catalogue_state` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ordering` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Contenu de la table `catalogue_state`
--

INSERT INTO `catalogue_state` (`id`, `name`, `ordering`) VALUES
(1, 'Nouveau / New', 1),
(2, 'Meilleur vendeur', 2),
(3, 'Page d''accueuil', 3),
(4, 'Top affaire', 4);
