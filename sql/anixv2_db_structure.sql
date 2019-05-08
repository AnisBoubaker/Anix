-- phpMyAdmin SQL Dump
-- version 2.9.0.2
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Lundi 23 Juin 2008 à 16:52
-- Version du serveur: 5.0.51
-- Version de PHP: 5.2.5
-- 
-- Base de données: `anixv2`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `admin_admin`
-- 

DROP TABLE IF EXISTS `admin_admin`;
CREATE TABLE IF NOT EXISTS `admin_admin` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `id_group` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `email` varchar(250) collate utf8_unicode_ci NOT NULL,
  `phone1` varchar(20) collate utf8_unicode_ci NOT NULL,
  `phone2` varchar(20) collate utf8_unicode_ci NOT NULL,
  `cell` varchar(20) collate utf8_unicode_ci NOT NULL,
  `pager` varchar(20) collate utf8_unicode_ci NOT NULL,
  `login` varchar(20) collate utf8_unicode_ci NOT NULL,
  `password` varchar(50) collate utf8_unicode_ci NOT NULL,
  `id_language` tinyint(4) NOT NULL default '2',
  `locked` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Unique_login` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Anix users' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `admin_groups`
-- 

DROP TABLE IF EXISTS `admin_groups`;
CREATE TABLE IF NOT EXISTS `admin_groups` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Unique_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `admin_login`
-- 

DROP TABLE IF EXISTS `admin_login`;
CREATE TABLE IF NOT EXISTS `admin_login` (
  `id_admin` tinyint(3) unsigned NOT NULL default '0',
  `date_connection` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_logout` datetime NOT NULL default '0000-00-00 00:00:00',
  `session_expires` datetime NOT NULL default '0000-00-00 00:00:00',
  `id_session` varchar(100) collate utf8_unicode_ci NOT NULL,
  `nb_not_logout` tinyint(4) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `articles_article`
-- 

DROP TABLE IF EXISTS `articles_article`;
CREATE TABLE IF NOT EXISTS `articles_article` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_category` tinyint(3) unsigned NOT NULL default '0',
  `active` enum('Y','N','DATE','ARCHIVE') character set latin1 NOT NULL default 'Y',
  `from_date` date NOT NULL default '0000-00-00',
  `to_date` date NOT NULL default '0000-00-00',
  `home_page` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `ordering` int(10) unsigned NOT NULL default '0',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) character set latin1 NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) character set latin1 NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=72 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `articles_attachments`
-- 

DROP TABLE IF EXISTS `articles_attachments`;
CREATE TABLE IF NOT EXISTS `articles_attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_article` int(10) unsigned NOT NULL default '0',
  `id_category` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `file_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `title` varchar(250) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=72 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `articles_categories`
-- 

DROP TABLE IF EXISTS `articles_categories`;
CREATE TABLE IF NOT EXISTS `articles_categories` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` tinyint(3) unsigned NOT NULL default '0',
  `id_parent` tinyint(3) unsigned NOT NULL default '0',
  `contain_items` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `image_file_small` varchar(255) collate utf8_unicode_ci NOT NULL default 'imgcatarticle_small_no_image.jpg',
  `image_file_large` varchar(255) collate utf8_unicode_ci NOT NULL default 'imgcatarticle_large_no_image.jpg',
  `id_menu` int(11) NOT NULL default '0',
  `alias_prepend` varchar(10) character set latin1 NOT NULL default '',
  `alias_articles_prepend` varchar(10) character set latin1 NOT NULL default '',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) character set latin1 NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) character set latin1 NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `articles_info_article`
-- 

DROP TABLE IF EXISTS `articles_info_article`;
CREATE TABLE IF NOT EXISTS `articles_info_article` (
  `id_article` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `title` text collate utf8_unicode_ci NOT NULL,
  `short_desc` text collate utf8_unicode_ci NOT NULL,
  `details` text collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `unique_articles_articles_info` (`id_article`,`id_language`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `articles_info_categories`
-- 

DROP TABLE IF EXISTS `articles_info_categories`;
CREATE TABLE IF NOT EXISTS `articles_info_categories` (
  `id_article_cat` tinyint(3) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(250) character set latin1 NOT NULL default '',
  `description` text character set latin1 NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) character set latin1 NOT NULL default '',
  KEY `id_articles_cat` (`id_article_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_anix_partner`
-- 

DROP TABLE IF EXISTS `catalogue_anix_partner`;
CREATE TABLE IF NOT EXISTS `catalogue_anix_partner` (
  `id_catalogue_category` int(11) NOT NULL,
  `id_partner` int(11) NOT NULL,
  `id_partner_category` bigint(20) NOT NULL,
  PRIMARY KEY  (`id_catalogue_category`,`id_partner_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_attachments`
-- 

DROP TABLE IF EXISTS `catalogue_attachments`;
CREATE TABLE IF NOT EXISTS `catalogue_attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_product` int(10) unsigned NOT NULL default '0',
  `id_category` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `file_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `title` varchar(250) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_brands`
-- 

DROP TABLE IF EXISTS `catalogue_brands`;
CREATE TABLE IF NOT EXISTS `catalogue_brands` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(250) collate utf8_unicode_ci NOT NULL,
  `image_file_large` varchar(255) collate utf8_unicode_ci NOT NULL,
  `image_file_small` varchar(255) collate utf8_unicode_ci NOT NULL,
  `URL` varchar(255) collate utf8_unicode_ci NOT NULL,
  `customer_service_phone` varchar(20) collate utf8_unicode_ci NOT NULL,
  `customer_service_email` varchar(50) collate utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=141 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_categories`
-- 

DROP TABLE IF EXISTS `catalogue_categories`;
CREATE TABLE IF NOT EXISTS `catalogue_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ordering` int(10) unsigned NOT NULL default '0',
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `id_parent` bigint(3) unsigned NOT NULL default '0',
  `contain_products` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `reference_pattern` varchar(100) collate utf8_unicode_ci NOT NULL,
  `hide_products` enum('Y','N') collate utf8_unicode_ci NOT NULL,
  `image_file_large` varchar(255) collate utf8_unicode_ci NOT NULL default '/catalogue_images/imgcat_large_no_image.jpg',
  `image_file_small` varchar(255) collate utf8_unicode_ci NOT NULL default '/catalogue_images/imgcat_small_no_image.jpg',
  `alias_prepend` varchar(10) collate utf8_unicode_ci NOT NULL,
  `alias_prd_prepend` varchar(10) collate utf8_unicode_ci NOT NULL,
  `id_menu` int(11) NOT NULL,
  `productimg_icon_width` int(11) NOT NULL,
  `productimg_icon_height` int(11) NOT NULL,
  `productimg_small_width` int(11) NOT NULL,
  `productimg_small_height` int(11) NOT NULL,
  `productimg_large_width` int(11) NOT NULL,
  `productimg_large_height` int(11) NOT NULL,
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1140 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_extracategorysection`
-- 

DROP TABLE IF EXISTS `catalogue_extracategorysection`;
CREATE TABLE IF NOT EXISTS `catalogue_extracategorysection` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_cat` int(10) unsigned NOT NULL default '0',
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `index_idcat` (`id_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contains extra fields needed by particular category' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_extrafields`
-- 

DROP TABLE IF EXISTS `catalogue_extrafields`;
CREATE TABLE IF NOT EXISTS `catalogue_extrafields` (
  `id` int(11) NOT NULL auto_increment,
  `datatype` enum('rich','text','selection') collate utf8_unicode_ci NOT NULL default 'text',
  `id_cat` int(10) unsigned NOT NULL default '0',
  `id_product` int(10) unsigned NOT NULL default '0',
  `params` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `copy_from` int(11) NOT NULL default '0',
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `index_idcat` (`id_cat`),
  KEY `index_idproduct` (`id_product`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contains extra fields needed by particular category' AUTO_INCREMENT=124 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_extrafields_values`
-- 

DROP TABLE IF EXISTS `catalogue_extrafields_values`;
CREATE TABLE IF NOT EXISTS `catalogue_extrafields_values` (
  `id_extrafield` int(11) NOT NULL default '0',
  `id_product` int(11) NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `value` text collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `unique_prdt_field_lang` (`id_extrafield`,`id_product`,`id_language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contains the values of extra fields defined in extra_fields';

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_featured`
-- 

DROP TABLE IF EXISTS `catalogue_featured`;
CREATE TABLE IF NOT EXISTS `catalogue_featured` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_category` int(10) NOT NULL default '0',
  `id_catalogue_prd` int(10) unsigned NOT NULL default '0',
  `id_catalogue_cat` int(10) unsigned NOT NULL default '0',
  `active` enum('Y','N','DATE') collate utf8_unicode_ci NOT NULL default 'Y',
  `from_date` date NOT NULL default '0000-00-00',
  `to_date` date NOT NULL default '0000-00-00',
  `image_file_small` varchar(250) collate utf8_unicode_ci NOT NULL,
  `image_file_large` varchar(250) collate utf8_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=131 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_info_categories`
-- 

DROP TABLE IF EXISTS `catalogue_info_categories`;
CREATE TABLE IF NOT EXISTS `catalogue_info_categories` (
  `id_catalogue_cat` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(250) character set utf8 NOT NULL default '',
  `description` text character set utf8 NOT NULL,
  `alias_name` varchar(15) character set utf8 NOT NULL default '',
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  KEY `id_catalogue_cat` (`id_catalogue_cat`),
  FULLTEXT KEY `name` (`name`,`description`,`alias_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_info_choices`
-- 

DROP TABLE IF EXISTS `catalogue_info_choices`;
CREATE TABLE IF NOT EXISTS `catalogue_info_choices` (
  `id_choice` int(10) unsigned NOT NULL default '0',
  `id_language` int(10) unsigned NOT NULL default '0',
  `value` varchar(250) collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_info_extracategorysection`
-- 

DROP TABLE IF EXISTS `catalogue_info_extracategorysection`;
CREATE TABLE IF NOT EXISTS `catalogue_info_extracategorysection` (
  `id_extrasection` int(11) NOT NULL default '0',
  `id_language` int(11) NOT NULL default '0',
  `name` varchar(255) character set utf8 NOT NULL default '',
  `value` text character set utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_info_extrafields`
-- 

DROP TABLE IF EXISTS `catalogue_info_extrafields`;
CREATE TABLE IF NOT EXISTS `catalogue_info_extrafields` (
  `id_extrafield` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `description` text collate utf8_unicode_ci NOT NULL,
  `selection_values` text collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_info_featured`
-- 

DROP TABLE IF EXISTS `catalogue_info_featured`;
CREATE TABLE IF NOT EXISTS `catalogue_info_featured` (
  `id_featured` int(11) NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `title` varchar(250) collate utf8_unicode_ci NOT NULL,
  `field1` text collate utf8_unicode_ci NOT NULL,
  `field2` text collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_info_options`
-- 

DROP TABLE IF EXISTS `catalogue_info_options`;
CREATE TABLE IF NOT EXISTS `catalogue_info_options` (
  `id_option` int(10) unsigned NOT NULL default '0',
  `id_language` int(10) unsigned NOT NULL default '0',
  `name` varchar(250) character set latin1 NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_info_price_groups`
-- 

DROP TABLE IF EXISTS `catalogue_info_price_groups`;
CREATE TABLE IF NOT EXISTS `catalogue_info_price_groups` (
  `id_price_group` tinyint(4) NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(50) character set latin1 NOT NULL default '',
  `description` text character set latin1 NOT NULL,
  UNIQUE KEY `UNIQUE_name` (`id_language`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_info_products`
-- 

DROP TABLE IF EXISTS `catalogue_info_products`;
CREATE TABLE IF NOT EXISTS `catalogue_info_products` (
  `id_product` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(250) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `unique_catalogue_product_info` (`id_product`,`id_language`),
  KEY `id_product` (`id_product`),
  FULLTEXT KEY `name` (`name`,`description`,`alias_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_partner`
-- 

DROP TABLE IF EXISTS `catalogue_partner`;
CREATE TABLE IF NOT EXISTS `catalogue_partner` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `catalogue_url` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_partner_category`
-- 

DROP TABLE IF EXISTS `catalogue_partner_category`;
CREATE TABLE IF NOT EXISTS `catalogue_partner_category` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_partner` int(11) NOT NULL,
  `name` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=189 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_price_groups`
-- 

DROP TABLE IF EXISTS `catalogue_price_groups`;
CREATE TABLE IF NOT EXISTS `catalogue_price_groups` (
  `id` tinyint(4) NOT NULL auto_increment,
  `id_group` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_product_option_choices`
-- 

DROP TABLE IF EXISTS `catalogue_product_option_choices`;
CREATE TABLE IF NOT EXISTS `catalogue_product_option_choices` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_option` int(10) unsigned NOT NULL default '0',
  `default_choice` enum('Y','N') character set latin1 NOT NULL default 'N',
  `price_diff` enum('increment','decrement') character set latin1 NOT NULL default 'increment',
  `price_value` decimal(8,2) NOT NULL default '0.00',
  `price_method` enum('currency','percentage') character set latin1 NOT NULL default 'currency',
  `ordering` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_product_options`
-- 

DROP TABLE IF EXISTS `catalogue_product_options`;
CREATE TABLE IF NOT EXISTS `catalogue_product_options` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_product` int(10) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_product_prices`
-- 

DROP TABLE IF EXISTS `catalogue_product_prices`;
CREATE TABLE IF NOT EXISTS `catalogue_product_prices` (
  `id_product` int(11) NOT NULL default '0',
  `id_price_group` tinyint(4) NOT NULL default '0',
  `price` decimal(10,2) NOT NULL default '0.00',
  `is_in_special` enum('Y','N') character set latin1 NOT NULL default 'N',
  `special_price` decimal(10,2) NOT NULL default '0.00',
  KEY `INDEX_prd` (`id_product`),
  KEY `INDEX_grp` (`id_price_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contains special prices for products';

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_product_qty_price`
-- 

DROP TABLE IF EXISTS `catalogue_product_qty_price`;
CREATE TABLE IF NOT EXISTS `catalogue_product_qty_price` (
  `id_product` bigint(20) unsigned NOT NULL,
  `id_price_group` tinyint(4) NOT NULL,
  `qty` int(10) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  KEY `id_product` (`id_product`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_product_review`
-- 

DROP TABLE IF EXISTS `catalogue_product_review`;
CREATE TABLE IF NOT EXISTS `catalogue_product_review` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_product` bigint(20) NOT NULL,
  `id_customer` bigint(20) NOT NULL,
  `id_language` tinyint(4) NOT NULL,
  `reviewer_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `reviewer_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `score` tinyint(4) NOT NULL,
  `review` text collate utf8_unicode_ci NOT NULL,
  `moderated` enum('Y','N') collate utf8_unicode_ci NOT NULL,
  `review_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_product_state`
-- 

DROP TABLE IF EXISTS `catalogue_product_state`;
CREATE TABLE IF NOT EXISTS `catalogue_product_state` (
  `id_state` tinyint(3) unsigned NOT NULL,
  `id_product` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`id_state`,`id_product`),
  KEY `INDEX_PRODUCT` (`id_product`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_products`
-- 

DROP TABLE IF EXISTS `catalogue_products`;
CREATE TABLE IF NOT EXISTS `catalogue_products` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_category` int(10) unsigned NOT NULL default '0',
  `active` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `product_type` enum('good','service') collate utf8_unicode_ci NOT NULL default 'good',
  `is_in_special` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `ordering` int(10) unsigned NOT NULL default '0',
  `image_file_orig` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `image_file_large` varchar(250) collate utf8_unicode_ci NOT NULL default 'imgprd_large_no_image.jpg',
  `image_file_small` varchar(250) collate utf8_unicode_ci NOT NULL default 'imgprd_small_no_image.jpg',
  `image_file_icon` varchar(250) collate utf8_unicode_ci NOT NULL default 'imgprd_icon_no_image.jpg',
  `ref_store` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `brand` int(11) NOT NULL default '0',
  `ref_manufacturer` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `url_manufacturer` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `upc_code` varchar(250) collate utf8_unicode_ci NOT NULL,
  `dim_W` decimal(8,2) NOT NULL default '0.00',
  `dim_H` decimal(8,2) NOT NULL default '0.00',
  `dim_L` decimal(8,2) NOT NULL default '0.00',
  `weight` decimal(8,2) NOT NULL default '0.00',
  `public_price` decimal(10,2) NOT NULL default '0.00',
  `ecotaxe` decimal(10,2) NOT NULL,
  `public_special` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `special_price` decimal(10,2) NOT NULL default '0.00',
  `stock` decimal(10,2) NOT NULL default '0.00',
  `stock_alert` int(11) NOT NULL default '0',
  `restocking_delay` tinyint(4) NOT NULL,
  `on_order_qty` decimal(10,2) NOT NULL default '0.00',
  `id_supplier1` int(11) NOT NULL,
  `ref_supplier1` varchar(50) collate utf8_unicode_ci NOT NULL,
  `cost_supplier1` decimal(10,2) NOT NULL,
  `id_supplier2` int(11) NOT NULL,
  `ref_supplier2` varchar(50) collate utf8_unicode_ci NOT NULL,
  `cost_supplier2` decimal(10,2) NOT NULL,
  `id_supplier3` int(11) NOT NULL,
  `ref_supplier3` varchar(50) collate utf8_unicode_ci NOT NULL,
  `cost_supplier3` decimal(10,2) NOT NULL,
  `id_supplier4` int(11) NOT NULL,
  `ref_supplier4` varchar(50) collate utf8_unicode_ci NOT NULL,
  `cost_supplier4` decimal(10,2) NOT NULL,
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15323 ;

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

-- --------------------------------------------------------

-- 
-- Structure de la table `catalogue_review`
-- 

DROP TABLE IF EXISTS `catalogue_review`;
CREATE TABLE IF NOT EXISTS `catalogue_review` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_product` bigint(20) NOT NULL,
  `id_customer` bigint(20) NOT NULL,
  `id_language` tinyint(4) NOT NULL,
  `reviewer_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `reviewer_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `score` tinyint(4) NOT NULL,
  `review` text collate utf8_unicode_ci NOT NULL,
  `moderated` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `review_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Customers reviews of products' AUTO_INCREMENT=1 ;

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

-- --------------------------------------------------------

-- 
-- Structure de la table `content_info_menuitems`
-- 

DROP TABLE IF EXISTS `content_info_menuitems`;
CREATE TABLE IF NOT EXISTS `content_info_menuitems` (
  `id_menuitem` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(3) unsigned NOT NULL default '0',
  `title` varchar(100) character set latin1 NOT NULL default '',
  `alt_title` varchar(200) character set latin1 NOT NULL default '',
  `link` varchar(200) character set latin1 NOT NULL default '',
  `img_off` varchar(200) character set latin1 NOT NULL default '',
  `img_on` varchar(200) character set latin1 NOT NULL default '',
  `img_mover` varchar(200) character set latin1 NOT NULL default '',
  `img_click` varchar(200) character set latin1 NOT NULL default '',
  `img_release` varchar(200) character set latin1 NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `content_info_pages`
-- 

DROP TABLE IF EXISTS `content_info_pages`;
CREATE TABLE IF NOT EXISTS `content_info_pages` (
  `id_page` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(3) unsigned NOT NULL default '0',
  `title` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `short_desc` text collate utf8_unicode_ci NOT NULL,
  `content` text collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) collate utf8_unicode_ci NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `content_menuitems`
-- 

DROP TABLE IF EXISTS `content_menuitems`;
CREATE TABLE IF NOT EXISTS `content_menuitems` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_parent` tinyint(3) unsigned NOT NULL default '0',
  `id_category` tinyint(3) unsigned NOT NULL default '0',
  `type` enum('link','submenu') character set latin1 NOT NULL default 'link',
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` int(10) unsigned NOT NULL default '0',
  `txt_color_off` varchar(7) character set latin1 NOT NULL default '',
  `txt_color_on` varchar(7) character set latin1 NOT NULL default '',
  `txt_color_mover` varchar(7) character set latin1 NOT NULL default '',
  `txt_color_click` varchar(7) character set latin1 NOT NULL default '',
  `txt_color_release` varchar(7) character set latin1 NOT NULL default '',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) character set latin1 NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) character set latin1 NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=85 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `content_pages`
-- 

DROP TABLE IF EXISTS `content_pages`;
CREATE TABLE IF NOT EXISTS `content_pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_category` tinyint(4) NOT NULL default '0',
  `type` enum('page','link') collate utf8_unicode_ci NOT NULL default 'page',
  `id_menu` tinyint(4) NOT NULL default '0',
  `link_module` varchar(50) collate utf8_unicode_ci NOT NULL,
  `link_id_item` int(11) NOT NULL,
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` int(11) NOT NULL default '0',
  `created_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_address`
-- 

DROP TABLE IF EXISTS `ecomm_address`;
CREATE TABLE IF NOT EXISTS `ecomm_address` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `num` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `street1` varchar(255) collate utf8_unicode_ci NOT NULL,
  `street2` varchar(255) collate utf8_unicode_ci NOT NULL,
  `building` varchar(50) collate utf8_unicode_ci NOT NULL,
  `stairs` varchar(50) collate utf8_unicode_ci NOT NULL,
  `floor` varchar(50) collate utf8_unicode_ci NOT NULL,
  `code` varchar(50) collate utf8_unicode_ci NOT NULL,
  `city` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `province` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `id_province` int(11) NOT NULL,
  `country` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `zip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `country_code` varchar(10) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2048 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_countries`
-- 

DROP TABLE IF EXISTS `ecomm_countries`;
CREATE TABLE IF NOT EXISTS `ecomm_countries` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `code2` char(6) collate utf8_unicode_ci NOT NULL,
  `code3` char(6) collate utf8_unicode_ci NOT NULL,
  `authorized` enum('Y','N') collate utf8_unicode_ci NOT NULL,
  `default_country` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `provinces` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `ordering` int(11) NOT NULL default '255' COMMENT 'Colonne pour gÃ©rer les prioritÃ©s et les sÃ©parations dans la liste de pays (i.e. On ordonne selon ordering et quand ordering change, on doit mettre un sÃ©parateur dans la dropdown)',
  `id_tax_group` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UNIQUE_CODE` (`code2`),
  UNIQUE KEY `UNIQUE_CODE3` (`code3`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=243 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_coupon`
-- 

DROP TABLE IF EXISTS `ecomm_coupon`;
CREATE TABLE IF NOT EXISTS `ecomm_coupon` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `code` varchar(50) collate utf8_unicode_ci NOT NULL,
  `id_client` bigint(20) NOT NULL,
  `type` enum('fixed','percentage','percentage_no_transport','transport','grid') collate utf8_unicode_ci NOT NULL default 'fixed',
  `value` decimal(10,2) NOT NULL,
  `percentage` decimal(10,2) NOT NULL,
  `grid` text collate utf8_unicode_ci NOT NULL,
  `usage` enum('once','count','unlimited') collate utf8_unicode_ci NOT NULL default 'once',
  `max_usage` bigint(20) NOT NULL,
  `usage_count` bigint(20) NOT NULL,
  `valid_from` date NOT NULL default '0000-00-00',
  `valid_until` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_coupon_usage`
-- 

DROP TABLE IF EXISTS `ecomm_coupon_usage`;
CREATE TABLE IF NOT EXISTS `ecomm_coupon_usage` (
  `id_coupon` bigint(20) unsigned NOT NULL,
  `id_client` bigint(20) unsigned NOT NULL,
  `id_payment` bigint(20) unsigned NOT NULL,
  `id_order` bigint(20) unsigned NOT NULL,
  `id_invoice` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) unsigned NOT NULL,
  `usage_date` datetime NOT NULL,
  KEY `id_coupon` (`id_coupon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_customer`
-- 

DROP TABLE IF EXISTS `ecomm_customer`;
CREATE TABLE IF NOT EXISTS `ecomm_customer` (
  `id` int(11) NOT NULL auto_increment,
  `id_user_group` tinyint(4) NOT NULL default '0',
  `account_type` tinyint(4) NOT NULL default '0',
  `greating` varchar(50) collate utf8_unicode_ci NOT NULL,
  `firstname` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `lastname` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `company` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `id_address_mailing` bigint(20) NOT NULL default '0',
  `id_address_billing` bigint(20) NOT NULL default '0',
  `phone` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `cell` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `fax` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `email` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `language` tinyint(4) NOT NULL default '0',
  `billing_method` tinyint(4) NOT NULL default '0',
  `id_tax_group` tinyint(4) NOT NULL default '1',
  `id_terms` int(10) unsigned NOT NULL default '1',
  `credit_margin` decimal(10,2) NOT NULL default '0.00',
  `balance` decimal(10,2) NOT NULL default '0.00',
  `login` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `pass` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `onetimepass` varchar(200) collate utf8_unicode_ci NOT NULL,
  `state` enum('inactif','activated','suspended') collate utf8_unicode_ci NOT NULL default 'inactif',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UNIQUE_LOGIN` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2907 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_emails`
-- 

DROP TABLE IF EXISTS `ecomm_emails`;
CREATE TABLE IF NOT EXISTS `ecomm_emails` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `id_category` tinyint(3) unsigned NOT NULL,
  `fields` text collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `cc_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `bcc_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `enabled` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` tinyint(4) NOT NULL,
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `created_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  `modified_on` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_emails_sent`
-- 

DROP TABLE IF EXISTS `ecomm_emails_sent`;
CREATE TABLE IF NOT EXISTS `ecomm_emails_sent` (
  `id_email` tinyint(3) unsigned NOT NULL,
  `id_client` int(10) unsigned NOT NULL,
  `id_order` bigint(20) unsigned NOT NULL,
  `subject` varchar(255) collate utf8_unicode_ci NOT NULL,
  `sent_to` text collate utf8_unicode_ci NOT NULL,
  `sent_cc` text collate utf8_unicode_ci NOT NULL,
  `sent_bcc` text collate utf8_unicode_ci NOT NULL,
  `sent_timestamp` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contains the emails sent throught Anix''s mailer';

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_fraud_check`
-- 

DROP TABLE IF EXISTS `ecomm_fraud_check`;
CREATE TABLE IF NOT EXISTS `ecomm_fraud_check` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_order` int(10) unsigned NOT NULL,
  `method` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `result` decimal(4,2) NOT NULL,
  `info` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `check_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=188 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_groups`
-- 

DROP TABLE IF EXISTS `ecomm_groups`;
CREATE TABLE IF NOT EXISTS `ecomm_groups` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(50) character set latin1 NOT NULL default '',
  `id_price_cat` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Used to regroups users (for price privileges)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_info_countries`
-- 

DROP TABLE IF EXISTS `ecomm_info_countries`;
CREATE TABLE IF NOT EXISTS `ecomm_info_countries` (
  `id_country` int(11) unsigned NOT NULL,
  `id_language` tinyint(4) NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_info_emails`
-- 

DROP TABLE IF EXISTS `ecomm_info_emails`;
CREATE TABLE IF NOT EXISTS `ecomm_info_emails` (
  `id_email` tinyint(4) NOT NULL,
  `id_language` tinyint(4) NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `sender_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `sender_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `subject` varchar(255) collate utf8_unicode_ci NOT NULL,
  `content` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id_email`,`id_language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_info_payment_type`
-- 

DROP TABLE IF EXISTS `ecomm_info_payment_type`;
CREATE TABLE IF NOT EXISTS `ecomm_info_payment_type` (
  `id_payment_type` tinyint(3) unsigned NOT NULL default '0',
  `id_language` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `fields` text collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_info_provinces`
-- 

DROP TABLE IF EXISTS `ecomm_info_provinces`;
CREATE TABLE IF NOT EXISTS `ecomm_info_provinces` (
  `id_province` int(10) unsigned NOT NULL,
  `id_language` tinyint(4) NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_info_transporter`
-- 

DROP TABLE IF EXISTS `ecomm_info_transporter`;
CREATE TABLE IF NOT EXISTS `ecomm_info_transporter` (
  `id_transporter` int(11) NOT NULL,
  `id_language` int(11) NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_invoice`
-- 

DROP TABLE IF EXISTS `ecomm_invoice`;
CREATE TABLE IF NOT EXISTS `ecomm_invoice` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_order` bigint(20) unsigned NOT NULL default '0',
  `id_client` int(11) unsigned NOT NULL default '0',
  `billing_address` text collate utf8_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL default '0000-00-00',
  `due_date` date NOT NULL default '0000-00-00',
  `id_terms` int(10) unsigned NOT NULL default '0',
  `subtotal` decimal(10,2) NOT NULL default '0.00',
  `grandtotal` decimal(10,2) NOT NULL default '0.00',
  `payed_amount` decimal(10,2) NOT NULL default '0.00',
  `status` enum('created','issued','payed','voided','refunded') collate utf8_unicode_ci NOT NULL default 'created',
  `id_refund` int(10) unsigned NOT NULL COMMENT 'If the invoice is refunded, the ID of the refunding invoice',
  `refund` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'Whether this is a refund invoice (that refunds another invoice)',
  `id_refunded` int(10) unsigned NOT NULL COMMENT 'If this is a refund, the ID of the original invoice',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_invoice_item`
-- 

DROP TABLE IF EXISTS `ecomm_invoice_item`;
CREATE TABLE IF NOT EXISTS `ecomm_invoice_item` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_order` bigint(20) unsigned NOT NULL,
  `id_invoice` bigint(20) unsigned NOT NULL default '0',
  `reference` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `details` text collate utf8_unicode_ci NOT NULL,
  `qty` decimal(4,2) NOT NULL default '0.00',
  `unstocked_qty` decimal(4,2) NOT NULL,
  `uprice` decimal(8,2) NOT NULL default '0.00',
  `id_product` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_order`
-- 

DROP TABLE IF EXISTS `ecomm_order`;
CREATE TABLE IF NOT EXISTS `ecomm_order` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_client` int(10) unsigned NOT NULL default '0',
  `mailing_address` text collate utf8_unicode_ci NOT NULL,
  `billing_address` text collate utf8_unicode_ci NOT NULL,
  `xml_address` text collate utf8_unicode_ci NOT NULL,
  `order_date` date NOT NULL default '0000-00-00',
  `delivery_date` date NOT NULL default '0000-00-00',
  `reception_date` date NOT NULL,
  `delivery_delay` tinyint(3) unsigned NOT NULL,
  `subtotal` decimal(10,2) NOT NULL default '0.00',
  `deposit_requested` decimal(6,3) NOT NULL default '0.000',
  `deposit_amount` decimal(10,2) NOT NULL default '0.00',
  `payed_amount` decimal(8,2) NOT NULL default '0.00',
  `status` enum('stand by','ordered','invoiced','payed','voided') collate utf8_unicode_ci NOT NULL default 'ordered',
  `id_invoice` bigint(20) NOT NULL default '0',
  `order_script` text collate utf8_unicode_ci NOT NULL,
  `id_transporter` tinyint(4) NOT NULL,
  `shipping_date` date NOT NULL,
  `tracking` varchar(255) collate utf8_unicode_ci NOT NULL,
  `remote_ip` varchar(50) collate utf8_unicode_ci NOT NULL,
  `order_timestamp` datetime NOT NULL,
  `fraud_check_mode` varchar(100) collate utf8_unicode_ci NOT NULL default 'none',
  `fraud_check_result` decimal(3,2) NOT NULL,
  `fraud_check_info` text collate utf8_unicode_ci NOT NULL,
  `fraud_check_date` datetime NOT NULL,
  `fraud_check_fianet_done` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_payment`
-- 

DROP TABLE IF EXISTS `ecomm_payment`;
CREATE TABLE IF NOT EXISTS `ecomm_payment` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_client` bigint(20) NOT NULL default '0',
  `id_payment_type` tinyint(4) NOT NULL default '0',
  `reception_date` date NOT NULL default '0000-00-00',
  `amount` decimal(10,2) NOT NULL default '0.00',
  `allocated_amount` decimal(10,2) NOT NULL default '0.00',
  `to_allocate_amount` decimal(10,2) NOT NULL default '0.00',
  `field1` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `field2` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `field3` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `field4` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_payment_allocation`
-- 

DROP TABLE IF EXISTS `ecomm_payment_allocation`;
CREATE TABLE IF NOT EXISTS `ecomm_payment_allocation` (
  `id_payment` bigint(20) unsigned NOT NULL default '0',
  `id_invoice` bigint(20) unsigned NOT NULL default '0',
  `id_order` bigint(20) unsigned NOT NULL default '0',
  `amount` decimal(10,2) NOT NULL default '0.00',
  `date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id_payment`,`id_invoice`,`id_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_payment_type`
-- 

DROP TABLE IF EXISTS `ecomm_payment_type`;
CREATE TABLE IF NOT EXISTS `ecomm_payment_type` (
  `id` tinyint(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `image_file` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `ordering` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_porder`
-- 

DROP TABLE IF EXISTS `ecomm_porder`;
CREATE TABLE IF NOT EXISTS `ecomm_porder` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_supplier` int(10) unsigned NOT NULL,
  `order_date` date NOT NULL,
  `order_sent` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `sent_date` date NOT NULL,
  `expected_reception_date` date NOT NULL,
  `reception_date` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `status` enum('created','ordered','received') collate utf8_unicode_ci NOT NULL default 'created',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_porder_item`
-- 

DROP TABLE IF EXISTS `ecomm_porder_item`;
CREATE TABLE IF NOT EXISTS `ecomm_porder_item` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_porder` bigint(20) unsigned NOT NULL,
  `id_product` bigint(20) NOT NULL,
  `ref_store` varchar(50) collate utf8_unicode_ci NOT NULL,
  `ref_supplier` varchar(50) collate utf8_unicode_ci NOT NULL,
  `description` varchar(255) collate utf8_unicode_ci NOT NULL,
  `uprice` decimal(10,2) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `received_qty` decimal(10,2) NOT NULL,
  `inventory_on_order_qty` decimal(10,2) NOT NULL,
  `stocked_qty` decimal(10,2) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_porder` (`id_porder`),
  KEY `id_product` (`id_product`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_provinces`
-- 

DROP TABLE IF EXISTS `ecomm_provinces`;
CREATE TABLE IF NOT EXISTS `ecomm_provinces` (
  `id` int(11) NOT NULL auto_increment,
  `id_country` int(11) NOT NULL,
  `code` varchar(10) collate utf8_unicode_ci NOT NULL,
  `default_province` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `id_tax_group` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_shipping_destination_transporter`
-- 

DROP TABLE IF EXISTS `ecomm_shipping_destination_transporter`;
CREATE TABLE IF NOT EXISTS `ecomm_shipping_destination_transporter` (
  `id_destination` int(11) NOT NULL,
  `id_transporter` tinyint(4) NOT NULL,
  `shipping_min_fees` decimal(10,2) NOT NULL,
  `shipping_min_weight` decimal(10,2) NOT NULL,
  `shipping_max_weight` decimal(10,2) NOT NULL,
  `shipping_price_per_unit` decimal(10,2) NOT NULL,
  `shipping_flat_rate` decimal(10,2) NOT NULL,
  `shipping_table_weight` text collate utf8_unicode_ci NOT NULL,
  `shipping_table_amount` text collate utf8_unicode_ci NOT NULL,
  `insurance_min_fees` decimal(10,2) NOT NULL,
  `insurance_flat_rate` decimal(10,2) NOT NULL,
  `insurance_percentage` decimal(10,2) NOT NULL,
  `insurance_table_amount` text collate utf8_unicode_ci NOT NULL,
  KEY `id_destination` (`id_destination`,`id_transporter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_shipping_destinations`
-- 

DROP TABLE IF EXISTS `ecomm_shipping_destinations`;
CREATE TABLE IF NOT EXISTS `ecomm_shipping_destinations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(250) collate utf8_unicode_ci NOT NULL,
  `cond_country` text collate utf8_unicode_ci NOT NULL,
  `cond_city` text collate utf8_unicode_ci NOT NULL,
  `cond_postal` text collate utf8_unicode_ci NOT NULL,
  `details` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_shipping_transporters`
-- 

DROP TABLE IF EXISTS `ecomm_shipping_transporters`;
CREATE TABLE IF NOT EXISTS `ecomm_shipping_transporters` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `tracking_url` text collate utf8_unicode_ci NOT NULL,
  `method_shiping_fees` enum('flat_rate','weight','table_weight','table_amount') collate utf8_unicode_ci NOT NULL default 'flat_rate',
  `method_insurance_fees` enum('none','flat_rate','percentage','table') collate utf8_unicode_ci NOT NULL default 'flat_rate',
  `insurance_optional` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_supplier`
-- 

DROP TABLE IF EXISTS `ecomm_supplier`;
CREATE TABLE IF NOT EXISTS `ecomm_supplier` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `contact` varchar(255) collate utf8_unicode_ci NOT NULL,
  `contact_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `tel_sales` varchar(50) collate utf8_unicode_ci NOT NULL,
  `tel_support` varchar(50) collate utf8_unicode_ci NOT NULL,
  `url` varchar(255) collate utf8_unicode_ci NOT NULL,
  `delivery_delay` int(11) NOT NULL,
  `send_orders_email` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `orders_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `orders_sender` varchar(255) collate utf8_unicode_ci NOT NULL,
  `orders_sender_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `email_template` text collate utf8_unicode_ci NOT NULL,
  `email_header` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_tax_authority`
-- 

DROP TABLE IF EXISTS `ecomm_tax_authority`;
CREATE TABLE IF NOT EXISTS `ecomm_tax_authority` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_tax_group` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `method` enum('percentage','fixed') collate utf8_unicode_ci NOT NULL default 'percentage',
  `ordering` int(11) unsigned NOT NULL default '0',
  `value` decimal(6,3) unsigned NOT NULL default '0.000',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_tax_group`
-- 

DROP TABLE IF EXISTS `ecomm_tax_group`;
CREATE TABLE IF NOT EXISTS `ecomm_tax_group` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `method` enum('separate','cumulate') collate utf8_unicode_ci NOT NULL default 'separate',
  `ordering` int(10) unsigned NOT NULL default '0',
  `default` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_tax_group_authority`
-- 

DROP TABLE IF EXISTS `ecomm_tax_group_authority`;
CREATE TABLE IF NOT EXISTS `ecomm_tax_group_authority` (
  `id_tax_group` int(10) unsigned NOT NULL default '0',
  `id_tax_authority` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_tax_item`
-- 

DROP TABLE IF EXISTS `ecomm_tax_item`;
CREATE TABLE IF NOT EXISTS `ecomm_tax_item` (
  `id_invoice` bigint(20) NOT NULL default '0',
  `type` enum('credit','debit') collate utf8_unicode_ci NOT NULL default 'credit',
  `id_tax_authority` int(11) NOT NULL default '0',
  `amount` decimal(10,2) NOT NULL default '0.00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `ecomm_terms`
-- 

DROP TABLE IF EXISTS `ecomm_terms`;
CREATE TABLE IF NOT EXISTS `ecomm_terms` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `delay` int(10) unsigned NOT NULL default '0',
  `name` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `description` text collate utf8_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `default` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UNIQUE_NAME` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `faq_categories`
-- 

DROP TABLE IF EXISTS `faq_categories`;
CREATE TABLE IF NOT EXISTS `faq_categories` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` tinyint(3) unsigned NOT NULL default '0',
  `id_parent` tinyint(3) unsigned NOT NULL default '0',
  `contain_items` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `id_menu` int(11) NOT NULL default '0',
  `alias_prepend` varchar(10) character set latin1 NOT NULL default '',
  `alias_faq_prepend` varchar(10) character set latin1 NOT NULL default '',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) character set latin1 NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) character set latin1 NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `faq_faq`
-- 

DROP TABLE IF EXISTS `faq_faq`;
CREATE TABLE IF NOT EXISTS `faq_faq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_category` tinyint(3) unsigned NOT NULL default '0',
  `active` enum('Y','N') character set latin1 NOT NULL default 'Y',
  `ordering` int(10) unsigned NOT NULL default '0',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) character set latin1 NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) character set latin1 NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `faq_info_categories`
-- 

DROP TABLE IF EXISTS `faq_info_categories`;
CREATE TABLE IF NOT EXISTS `faq_info_categories` (
  `id_faq_cat` tinyint(3) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(250) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  KEY `id_news_cat` (`id_faq_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `faq_info_faq`
-- 

DROP TABLE IF EXISTS `faq_info_faq`;
CREATE TABLE IF NOT EXISTS `faq_info_faq` (
  `id_faq` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `question` text collate utf8_unicode_ci NOT NULL,
  `response` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `unique_news_news_info` (`id_faq`,`id_language`),
  KEY `id_news` (`id_faq`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `gallery_categories`
-- 

DROP TABLE IF EXISTS `gallery_categories`;
CREATE TABLE IF NOT EXISTS `gallery_categories` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` tinyint(3) unsigned NOT NULL default '0',
  `id_parent` tinyint(3) unsigned NOT NULL default '0',
  `contain_items` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `id_menu` int(11) NOT NULL default '0',
  `photo_icon_width` int(11) NOT NULL,
  `photo_icon_height` int(11) NOT NULL,
  `photo_small_width` int(11) NOT NULL,
  `photo_small_height` int(11) NOT NULL,
  `photo_large_width` int(11) NOT NULL,
  `photo_large_height` int(11) NOT NULL,
  `alias_prepend` varchar(10) character set latin1 NOT NULL default '',
  `alias_gallery_prepend` varchar(10) character set latin1 NOT NULL default '',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) character set latin1 NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) character set latin1 NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `gallery_info_categories`
-- 

DROP TABLE IF EXISTS `gallery_info_categories`;
CREATE TABLE IF NOT EXISTS `gallery_info_categories` (
  `id_gallery_cat` tinyint(3) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(250) character set latin1 NOT NULL default '',
  `description` text character set latin1 NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) character set latin1 NOT NULL default '',
  KEY `id_gallery_cat` (`id_gallery_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `gallery_info_photo`
-- 

DROP TABLE IF EXISTS `gallery_info_photo`;
CREATE TABLE IF NOT EXISTS `gallery_info_photo` (
  `id_photo` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `title` text collate utf8_unicode_ci NOT NULL,
  `date` varchar(250) collate utf8_unicode_ci NOT NULL,
  `short_desc` text collate utf8_unicode_ci NOT NULL,
  `details` text collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `unique_gallery_gallery_info` (`id_photo`,`id_language`),
  KEY `id_photo` (`id_photo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `gallery_photo`
-- 

DROP TABLE IF EXISTS `gallery_photo`;
CREATE TABLE IF NOT EXISTS `gallery_photo` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_category` tinyint(3) unsigned NOT NULL default '0',
  `active` enum('Y','N','DATE','ARCHIVE') character set latin1 NOT NULL default 'Y',
  `from_date` date NOT NULL default '0000-00-00',
  `to_date` date NOT NULL default '0000-00-00',
  `ordering` int(10) unsigned NOT NULL default '0',
  `image_file_orig` varchar(250) collate utf8_unicode_ci NOT NULL,
  `image_file_large` varchar(250) collate utf8_unicode_ci NOT NULL default 'imggallery_large_no_image.jpg',
  `image_file_small` varchar(250) collate utf8_unicode_ci NOT NULL default 'imggallery_small_no_image.jpg',
  `image_file_icon` varchar(250) collate utf8_unicode_ci NOT NULL default 'imggallery_icon_no_image.jpg',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) character set latin1 NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) character set latin1 NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `gen_bugs_reporting`
-- 

DROP TABLE IF EXISTS `gen_bugs_reporting`;
CREATE TABLE IF NOT EXISTS `gen_bugs_reporting` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `err_num` int(11) NOT NULL,
  `err_str` text collate utf8_unicode_ci NOT NULL,
  `err_file` varchar(255) collate utf8_unicode_ci NOT NULL,
  `err_line` int(11) NOT NULL,
  `err_context` text collate utf8_unicode_ci NOT NULL,
  `report_date` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `SELECT_INDEX` (`err_num`,`err_file`,`err_line`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=613 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `gen_languages`
-- 

DROP TABLE IF EXISTS `gen_languages`;
CREATE TABLE IF NOT EXISTS `gen_languages` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `image_file` varchar(255) collate utf8_unicode_ci NOT NULL,
  `language_file` varchar(255) collate utf8_unicode_ci NOT NULL,
  `default` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `used` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `locales_folder` varchar(250) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contains different languages used on the web site' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `gen_session`
-- 

DROP TABLE IF EXISTS `gen_session`;
CREATE TABLE IF NOT EXISTS `gen_session` (
  `session` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `session_expires` int(10) unsigned NOT NULL default '0',
  `session_data` text collate utf8_unicode_ci,
  PRIMARY KEY  (`session`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `links_category`
-- 

DROP TABLE IF EXISTS `links_category`;
CREATE TABLE IF NOT EXISTS `links_category` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `links_info_category`
-- 

DROP TABLE IF EXISTS `links_info_category`;
CREATE TABLE IF NOT EXISTS `links_info_category` (
  `id_category` tinyint(3) unsigned NOT NULL,
  `id_language` tinyint(3) unsigned NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id_category`,`id_language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `links_info_module`
-- 

DROP TABLE IF EXISTS `links_info_module`;
CREATE TABLE IF NOT EXISTS `links_info_module` (
  `id_module` tinyint(3) unsigned NOT NULL,
  `id_language` tinyint(3) unsigned NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `links_link`
-- 

DROP TABLE IF EXISTS `links_link`;
CREATE TABLE IF NOT EXISTS `links_link` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_category` tinyint(4) NOT NULL,
  `from_module` varchar(100) collate utf8_unicode_ci NOT NULL,
  `from_item` bigint(20) NOT NULL,
  `to_module` varchar(100) collate utf8_unicode_ci NOT NULL,
  `to_item` bigint(20) NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3789 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `links_module`
-- 

DROP TABLE IF EXISTS `links_module`;
CREATE TABLE IF NOT EXISTS `links_module` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `code` varchar(50) collate utf8_unicode_ci NOT NULL,
  `icon_file` varchar(255) collate utf8_unicode_ci NOT NULL,
  `add_link_url` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `lists_attachments`
-- 

DROP TABLE IF EXISTS `lists_attachments`;
CREATE TABLE IF NOT EXISTS `lists_attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_item` int(10) unsigned NOT NULL default '0',
  `id_category` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `file_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `title` varchar(250) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `lists_categories`
-- 

DROP TABLE IF EXISTS `lists_categories`;
CREATE TABLE IF NOT EXISTS `lists_categories` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` tinyint(3) unsigned NOT NULL default '0',
  `id_parent` tinyint(3) unsigned NOT NULL default '0',
  `contain_items` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `reference_pattern` varchar(100) collate utf8_unicode_ci NOT NULL,
  `hide_items` enum('Y','N') collate utf8_unicode_ci NOT NULL,
  `image_file_large` varchar(255) collate utf8_unicode_ci NOT NULL default 'imgflexcat_large_no_image.jpg',
  `image_file_small` varchar(255) collate utf8_unicode_ci NOT NULL default 'imgflexcat_small_no_image.jpg',
  `alias_prepend` varchar(10) collate utf8_unicode_ci NOT NULL,
  `alias_prd_prepend` varchar(10) collate utf8_unicode_ci NOT NULL,
  `id_menu` int(11) NOT NULL,
  `itemimg_icon_width` int(11) NOT NULL,
  `itemimg_icon_height` int(11) NOT NULL,
  `itemimg_small_width` int(11) NOT NULL,
  `itemimg_small_height` int(11) NOT NULL,
  `itemimg_large_width` int(11) NOT NULL,
  `itemimg_large_height` int(11) NOT NULL,
  `items_ordering` enum('manual','alpha') collate utf8_unicode_ci NOT NULL default 'manual',
  `subcats_ordering` enum('manual','alpha') collate utf8_unicode_ci NOT NULL default 'manual',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `lists_extracategorysection`
-- 

DROP TABLE IF EXISTS `lists_extracategorysection`;
CREATE TABLE IF NOT EXISTS `lists_extracategorysection` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_cat` int(10) unsigned NOT NULL default '0',
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `index_idcat` (`id_cat`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contains extra fields needed by particular category' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `lists_extrafields`
-- 

DROP TABLE IF EXISTS `lists_extrafields`;
CREATE TABLE IF NOT EXISTS `lists_extrafields` (
  `id` int(11) NOT NULL auto_increment,
  `datatype` enum('rich','text','selection','date') collate utf8_unicode_ci NOT NULL default 'text',
  `id_cat` int(10) unsigned NOT NULL default '0',
  `id_item` int(10) unsigned NOT NULL default '0',
  `params` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `copy_from` int(11) NOT NULL default '0',
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `index_idcat` (`id_cat`),
  KEY `index_iditem` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contains extra fields needed by particular category' AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `lists_extrafields_values`
-- 

DROP TABLE IF EXISTS `lists_extrafields_values`;
CREATE TABLE IF NOT EXISTS `lists_extrafields_values` (
  `id_extrafield` int(11) NOT NULL default '0',
  `id_item` int(11) NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `value` text collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `unique_prdt_field_lang` (`id_extrafield`,`id_item`,`id_language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contains the values of extra fields defined in extra_fields';

-- --------------------------------------------------------

-- 
-- Structure de la table `lists_info_categories`
-- 

DROP TABLE IF EXISTS `lists_info_categories`;
CREATE TABLE IF NOT EXISTS `lists_info_categories` (
  `id_lists_cat` tinyint(3) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(250) character set utf8 NOT NULL default '',
  `description` text character set utf8 NOT NULL,
  `alias_name` varchar(15) character set utf8 NOT NULL default '',
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  KEY `id_lists_cat` (`id_lists_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `lists_info_extracategorysection`
-- 

DROP TABLE IF EXISTS `lists_info_extracategorysection`;
CREATE TABLE IF NOT EXISTS `lists_info_extracategorysection` (
  `id_extrasection` int(11) NOT NULL default '0',
  `id_language` int(11) NOT NULL default '0',
  `name` varchar(255) character set utf8 NOT NULL default '',
  `value` text character set utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `lists_info_extrafields`
-- 

DROP TABLE IF EXISTS `lists_info_extrafields`;
CREATE TABLE IF NOT EXISTS `lists_info_extrafields` (
  `id_extrafield` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `description` text collate utf8_unicode_ci NOT NULL,
  `selection_values` text collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `lists_info_items`
-- 

DROP TABLE IF EXISTS `lists_info_items`;
CREATE TABLE IF NOT EXISTS `lists_info_items` (
  `id_item` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(250) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `unique_lists_item_info` (`id_item`,`id_language`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `lists_items`
-- 

DROP TABLE IF EXISTS `lists_items`;
CREATE TABLE IF NOT EXISTS `lists_items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_category` tinyint(3) unsigned NOT NULL default '0',
  `active` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` int(10) unsigned NOT NULL default '0',
  `image_file_orig` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `image_file_large` varchar(250) collate utf8_unicode_ci NOT NULL default 'imgflex_large_no_image.jpg',
  `image_file_small` varchar(250) collate utf8_unicode_ci NOT NULL default 'imgflex_small_no_image.jpg',
  `image_file_icon` varchar(250) collate utf8_unicode_ci NOT NULL default 'imgflex_icon_no_image.jpg',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=122 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `news_attachments`
-- 

DROP TABLE IF EXISTS `news_attachments`;
CREATE TABLE IF NOT EXISTS `news_attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_news` int(10) unsigned NOT NULL default '0',
  `id_category` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `file_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `title` varchar(250) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `news_categories`
-- 

DROP TABLE IF EXISTS `news_categories`;
CREATE TABLE IF NOT EXISTS `news_categories` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `deletable` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `ordering` tinyint(3) unsigned NOT NULL default '0',
  `id_parent` tinyint(3) unsigned NOT NULL default '0',
  `contain_items` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `id_menu` int(11) NOT NULL default '0',
  `newsimg_icon_width` int(11) NOT NULL,
  `newsimg_icon_height` int(11) NOT NULL,
  `newsimg_small_width` int(11) NOT NULL,
  `newsimg_small_height` int(11) NOT NULL,
  `newsimg_large_width` int(11) NOT NULL,
  `newsimg_large_height` int(11) NOT NULL,
  `alias_prepend` varchar(10) character set latin1 NOT NULL default '',
  `alias_news_prepend` varchar(10) character set latin1 NOT NULL default '',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) character set latin1 NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) character set latin1 NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `news_info_categories`
-- 

DROP TABLE IF EXISTS `news_info_categories`;
CREATE TABLE IF NOT EXISTS `news_info_categories` (
  `id_news_cat` tinyint(3) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `name` varchar(250) character set latin1 NOT NULL default '',
  `description` text character set latin1 NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) character set latin1 NOT NULL default '',
  KEY `id_news_cat` (`id_news_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `news_info_news`
-- 

DROP TABLE IF EXISTS `news_info_news`;
CREATE TABLE IF NOT EXISTS `news_info_news` (
  `id_news` int(10) unsigned NOT NULL default '0',
  `id_language` tinyint(4) NOT NULL default '0',
  `title` text collate utf8_unicode_ci NOT NULL,
  `date` varchar(250) collate utf8_unicode_ci NOT NULL,
  `short_desc` text collate utf8_unicode_ci NOT NULL,
  `details` text collate utf8_unicode_ci NOT NULL,
  `keywords` text collate utf8_unicode_ci NOT NULL,
  `htmltitle` varchar(255) collate utf8_unicode_ci NOT NULL,
  `htmldescription` text collate utf8_unicode_ci NOT NULL,
  `alias_name` varchar(15) collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `unique_news_news_info` (`id_news`,`id_language`),
  KEY `id_news` (`id_news`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Structure de la table `news_news`
-- 

DROP TABLE IF EXISTS `news_news`;
CREATE TABLE IF NOT EXISTS `news_news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_category` tinyint(3) unsigned NOT NULL default '0',
  `active` enum('Y','N','DATE','ARCHIVE') character set latin1 NOT NULL default 'Y',
  `from_date` date NOT NULL default '0000-00-00',
  `to_date` date NOT NULL default '0000-00-00',
  `ordering` int(10) unsigned NOT NULL default '0',
  `image_file_orig` varchar(250) collate utf8_unicode_ci NOT NULL,
  `image_file_large` varchar(250) collate utf8_unicode_ci NOT NULL default 'imgnews_large_no_image.jpg',
  `image_file_small` varchar(250) collate utf8_unicode_ci NOT NULL default 'imgnews_small_no_image.jpg',
  `image_file_icon` varchar(250) collate utf8_unicode_ci NOT NULL default 'imgnews_icon_no_image.jpg',
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) character set latin1 NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` varchar(50) character set latin1 NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=50 ;
