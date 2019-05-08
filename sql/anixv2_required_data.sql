-- phpMyAdmin SQL Dump
-- version 2.9.0.2
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Dimanche 25 Mai 2008 à 17:29
-- Version du serveur: 5.0.51
-- Version de PHP: 5.2.5
-- 
-- Base de données: `anixv2`
-- 

-- 
-- Contenu de la table `admin_admin`
-- 

INSERT INTO `admin_admin` (`id`, `id_group`, `name`, `email`, `phone1`, `phone2`, `cell`, `pager`, `login`, `password`, `id_language`, `locked`) VALUES 
(1, 5, 'Anis Boubaker', 'a.boubaker@cibaxion.com', '58226472', '5822647', '5822647', '5822647', 'anis.boubaker', 'anlycH5fmyLjc', 1, 'N'),
(4, 5, 'Rowena Agouri', 'r.agouri@cibaxion.com', '', '', '', '', 'rowena.agouri', 'roC0CCh/8sKrM', 2, 'N'),
(5, 0, '', '', '', '', '', '', '', '', 2, 'N');

-- 
-- Contenu de la table `admin_groups`
-- 

INSERT INTO `admin_groups` (`id`, `name`, `description`) VALUES 
(3, 'Administrateurs', 'Utilisateurs administrateurs'),
(5, '&Eacute;diteurs de produits', 'Ajoutent, modifient et suppriment des produits.'),
(6, '', '');

-- 
-- Contenu de la table `admin_login`
-- 

INSERT INTO `admin_login` (`id_admin`, `date_connection`, `date_logout`, `session_expires`, `id_session`, `nb_not_logout`) VALUES 
(1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2008-05-25 18:04:35', 't9sf1lrhi6q5oet8l5sfhivk33', 0);

-- 
-- Contenu de la table `ecomm_countries`
-- 

INSERT INTO `ecomm_countries` (`id`, `code2`, `code3`, `name_fr`, `name_en`, `authorized`, `ordering`) VALUES 
(1, 'AF', 'AFG', 'Afghanistan', 'Afghanistan', 'N', 255),
(2, 'ZA', 'ZAF', 'Afrique Du Sud', 'South Africa', 'N', 255),
(3, 'AL', 'ALB', 'Albanie', 'Albania', 'Y', 255),
(4, 'DZ', 'DZA', 'Alg&eacute;rie', 'Algeria', 'Y', 255),
(5, 'DE', 'DEU', 'Allemagne', 'Germany', 'Y', 2),
(6, 'AD', 'AND', 'Andorre', 'Andorra', 'N', 255),
(7, 'AO', 'AGO', 'Angola', 'Angola', 'N', 255),
(8, 'AI', 'AIA', 'Anguilla', 'Anguilla', 'N', 255),
(9, 'AQ', 'ATA', 'Antarctique', 'Antarctica', 'N', 255),
(10, 'AG', 'ATG', 'Antigua-et-barbuda', 'Antigua And Barbuda', 'N', 255),
(11, 'AN', 'ANT', 'Antilles N&eacute;erlandaises', 'Netherlands Antilles', 'N', 255),
(12, 'SA', 'SAU', 'Arabie Saoudite', 'Saudi Arabia', 'N', 255),
(13, 'AR', 'ARG', 'Argentine', 'Argentina', 'N', 255),
(14, 'AM', 'ARM', 'Arm&eacute;nie', 'Armenia', 'N', 255),
(15, 'AW', 'ABW', 'Aruba', 'Aruba', 'N', 255),
(16, 'AU', 'AUS', 'Australie', 'Australia', 'Y', 255),
(17, 'AT', 'AUT', 'Autriche', 'Austria', 'Y', 255),
(18, 'AZ', 'AZE', 'Azerba&iuml;djan', 'Azerbaijan', 'N', 255),
(19, 'BS', 'BHS', 'Bahamas', 'Bahamas', 'N', 255),
(20, 'BH', 'BHR', 'Bahre&iuml;n', 'Bahrain', 'N', 255),
(21, 'BD', 'BGD', 'Bangladesh', 'Bangladesh', 'N', 255),
(22, 'BB', 'BRB', 'Barbade', 'Barbados', 'N', 255),
(23, 'BY', 'BLR', 'B&eacute;larus', 'Belarus', 'Y', 255),
(24, 'BE', 'BEL', 'Belgique', 'Belgium', 'Y', 2),
(25, 'BZ', 'BLZ', 'Belize', 'Belize', 'N', 255),
(26, 'BJ', 'BEN', 'B&eacute;nin', 'Benin', 'N', 255),
(27, 'BM', 'BMU', 'Bermudes', 'Bermuda', 'N', 255),
(28, 'BT', 'BTN', 'Bhoutan', 'Bhutan', 'N', 255),
(29, 'BO', 'BOL', 'Bolivie', 'Bolivia', 'N', 255),
(30, 'BA', 'BIH', 'Bosnie-herz&eacute;govine', 'Bosnia And Herzegovina', 'Y', 255),
(31, 'BW', 'BWA', 'Botswana', 'Botswana', 'N', 255),
(32, 'BV', 'BVT', 'Bouvet, &Icirc;le', 'Bouvet Island', 'N', 255),
(33, 'BR', 'BRA', 'Bresil', 'Brazil', 'Y', 255),
(34, 'BN', 'BRN', 'Brun&eacute;i Darussalam', 'Brunei Darussalam', 'N', 255),
(35, 'BG', 'BGR', 'Bulgarie', 'Bulgaria', 'Y', 255),
(36, 'BF', 'BFA', 'Burkina Faso', 'Burkina Faso', 'N', 255),
(37, 'BI', 'BDI', 'Burundi', 'Burundi', 'N', 255),
(38, 'KY', 'CYM', 'Ca&iuml;manes, &Icirc;les', 'Cayman Islands', 'N', 255),
(39, 'KF', 'KHM', 'Cambodge', 'Cambodia', 'N', 255),
(40, 'CM', 'CMR', 'Cameroun', 'Cameroon', 'N', 255),
(41, 'CA', 'CAN', 'Canada', 'Canada', 'Y', 255),
(42, 'CV', 'CPV', 'Cap-vert', 'Cape Verde', 'N', 255),
(43, 'CF', 'CAF', 'Centrafricaine, R&eacute;publique', 'Central African Republic', 'N', 255),
(44, 'CL', 'CHL', 'Chili', 'Chile', 'N', 255),
(45, 'CN', 'CHN', 'Chine', 'China', 'N', 255),
(46, 'CX', 'CXR', 'Christmas, &Icirc;le', 'Christmas Island', 'N', 255),
(47, 'CY', 'CYP', 'Chypre', 'Cyprus', 'N', 255),
(48, 'CC', 'CCK', 'Cocos (keeling), &Icirc;les', 'Cocos (keeling) Islands', 'N', 255),
(49, 'CO', 'COL', 'Colombie', 'Colombia', 'N', 255),
(50, 'KM', 'COM', 'Comores', 'Comoros', 'N', 255),
(51, 'CG', 'COG', 'Congo', 'Congo', 'N', 255),
(52, 'CD', 'COD', 'Congo, La R&eacute;publique D&eacute;mocratique Du', 'Congo, The Democratic Republic Of The', 'N', 255),
(53, 'CK', 'COK', 'Cook, &Icirc;les', 'Cook Islands', 'N', 255),
(54, 'KR', 'KOR', 'Cor&eacute;e, R&eacute;publique De', 'Korea, Republic Of', 'N', 255),
(55, 'KP', 'PRK', 'Cor&eacute;e, R&eacute;publique Populaire D&eacute;mocratique De', 'Korea, Democratic People&#039;s Republic Of', 'N', 255),
(56, 'CR', 'CRI', 'Costa Rica', 'Costa Rica', 'N', 255),
(57, 'CI', 'CIV', 'C&ocirc;te D&#039;ivoire', 'Cote D&#039;ivoire', 'N', 255),
(58, 'HR', 'HRV', 'Croatie', 'Croatia', 'Y', 255),
(59, 'CU', 'CUB', 'Cuba', 'Cuba', 'N', 255),
(60, 'DK', 'DNK', 'Danemark', 'Denmark', 'Y', 255),
(61, 'DJ', 'DJI', 'Djibouti', 'Djibouti', 'N', 255),
(62, 'DO', 'DOM', 'Dominicaine, R&eacute;publique', 'Dominican Republic', 'N', 255),
(63, 'DM', 'DMA', 'Dominique', 'Dominica', 'N', 255),
(64, 'EG', 'EGY', '&Eacute;gypte', 'Egypt', 'N', 255),
(65, 'SV', 'SLV', 'El Salvador', 'El Salvador', 'N', 255),
(66, 'AE', 'ARE', '&Eacute;mirats Arabes Unis', 'United Arab Emirates', 'N', 255),
(67, 'EC', 'ECU', '&Eacute;quateur', 'Ecuador', 'N', 255),
(68, 'ER', 'ERI', '&Eacute;rythr&eacute;e', 'Eritrea', 'N', 255),
(69, 'ES', 'ESP', 'Espagne (hors Canaries)', 'Spain (except Canaries Islands)', 'Y', 2),
(70, 'ES-ICA', 'ESP', 'Espagne - &Icirc;les Canaries', 'Spain - Canari Islands', 'Y', 255),
(71, 'EE', 'EST', 'Estonie', 'Estonia', 'Y', 255),
(72, 'US', 'USA', 'Etats-unis', 'United States', 'Y', 255),
(73, 'ET', 'ETH', '&Eacute;thiopie', 'Ethiopia', 'N', 255),
(74, 'FK', 'FLK', 'Falkland, &Icirc;les (malvinas)', 'Falkland Islands (malvinas)', 'N', 255),
(75, 'FO', 'FRO', 'F&eacute;ro&eacute;, &Icirc;les', 'Faroe Islands', 'N', 255),
(76, 'FJ', 'FJI', 'Fidji', 'Fiji', 'N', 255),
(77, 'FI', 'FIN', 'Finlande', 'Finland', 'Y', 255),
(78, 'FR', 'FRA', 'France Métropolitaine', 'France (Metropolitan Area)', 'Y', 1),
(79, 'GA', 'GAB', 'Gabon', 'Gabon', 'N', 255),
(80, 'GM', 'GMB', 'Gambie', 'Gambia', 'N', 255),
(81, 'GE', 'GEO', 'G&eacute;orgie', 'Georgia', 'N', 255),
(82, 'GS', 'SGS', 'G&eacute;orgie Du Sud Et Les &Icirc;les Sandwich Du Sud', 'South Georgia And The South Sandwich Islands', 'N', 255),
(83, 'GH', 'GHA', 'Ghana', 'Ghana', 'N', 255),
(84, 'GI', 'GIB', 'Gibraltar', 'Gibraltar', 'N', 255),
(85, 'GR', 'GRC', 'Gr&egrave;ce', 'Greece', 'Y', 255),
(86, 'GD', 'GRD', 'Grenade', 'Grenada', 'N', 255),
(87, 'GL', 'GRL', 'Groenland', 'Greenland', 'N', 255),
(88, 'GP', 'GLP', 'Guadeloupe', 'Guadeloupe', 'Y', 255),
(89, 'GU', 'GUM', 'Guam', 'Guam', 'N', 255),
(90, 'GT', 'GTM', 'Guatemala', 'Guatemala', 'N', 255),
(91, 'GN', 'GIN', 'Guin&eacute;e', 'Guinea', 'N', 255),
(92, 'GQ', 'GNQ', 'Guin&eacute;e &Eacute;quatoriale', 'Equatorial Guinea', 'N', 255),
(93, 'GW', 'GNB', 'Guin&eacute;e-bissau', 'Guinea-bissau', 'N', 255),
(94, 'GY', 'GUY', 'Guyana', 'Guyana', 'N', 255),
(95, 'GF', 'GUF', 'Guyane Fran&ccedil;aise', 'French Guiana', 'Y', 255),
(96, 'HT', 'HTI', 'Ha&iuml;ti', 'Haiti', 'N', 255),
(97, 'HM', 'HMD', 'Heard, &Icirc;le Et Mcdonald, &Icirc;les', 'Heard Island And Mcdonald Islands', 'N', 255),
(98, 'HN', 'HND', 'Honduras', 'Honduras', 'N', 255),
(99, 'HK', 'HKG', 'Hong-kong', 'Hong Kong', 'N', 255),
(100, 'HU', 'HUN', 'Hongrie', 'Hungary', 'Y', 255),
(101, 'UM', 'UMI', '&Icirc;les Mineures &Eacute;loign&eacute;es Des &Eacute;tats-unis', 'United States Minor Outlying Islands', 'N', 255),
(102, 'VG', 'VGB', '&Icirc;les Vierges Britanniques', 'Virgin Islands, British', 'N', 255),
(103, 'VI', 'VIR', '&Icirc;les Vierges Des &Eacute;tats-unis', 'Virgin Islands, U.s.', 'N', 255),
(104, 'IN', 'IND', 'Inde', 'India', 'N', 255),
(105, 'ID', 'IDN', 'Indon&eacute;sie', 'Indonesia', 'N', 255),
(106, 'IR', 'IRN', 'Iran, R&eacute;publique Islamique D&#039;', 'Iran, Islamic Republic Of', 'N', 255),
(107, 'IQ', 'IRQ', 'Iraq', 'Iraq', 'N', 255),
(108, 'IE', 'IRL', 'Irlande', 'Ireland', 'Y', 2),
(109, 'IS', 'ISL', 'Islande', 'Iceland', 'Y', 255),
(110, 'IL', 'ISR', 'Isra&euml;l', 'Israel', 'N', 255),
(111, 'IT', 'ITA', 'Italie', 'Italy', 'Y', 2),
(112, 'JM', 'JAM', 'Jama&iuml;que', 'Jamaica', 'N', 255),
(113, 'JP', 'JPN', 'Japon', 'Japan', 'N', 255),
(114, 'JO', 'JOR', 'Jordanie', 'Jordan', 'N', 255),
(115, 'KZ', 'KAZ', 'Kazakhstan', 'Kazakhstan', 'N', 255),
(116, 'KE', 'KEN', 'Kenya', 'Kenya', 'N', 255),
(117, 'KG', 'KGZ', 'Kirghizistan', 'Kyrgyzstan', 'N', 255),
(118, 'KI', 'KIR', 'Kiribati', 'Kiribati', 'N', 255),
(119, 'KW', 'KWT', 'Kowe&iuml;t', 'Kuwait', 'N', 255),
(120, 'LA', 'LAO', 'Lao, R&eacute;publique D&eacute;mocratique Populaire', 'Lao People&#039;s Democratic Republic', 'N', 255),
(121, 'LS', 'LSO', 'Lesotho', 'Lesotho', 'N', 255),
(122, 'LV', 'LVA', 'Lettonie', 'Latvia', 'Y', 255),
(123, 'LB', 'LBN', 'Liban', 'Lebanon', 'N', 255),
(124, 'LR', 'LBR', 'Lib&eacute;ria', 'Liberia', 'N', 255),
(125, 'LY', 'LBY', 'Libyenne, Jamahiriya Arabe', 'Libyan Arab Jamahiriya', 'Y', 255),
(126, 'LI', 'LIE', 'Liechtenstein', 'Liechtenstein', 'N', 255),
(127, 'LT', 'LTU', 'Lituanie', 'Lithuania', 'Y', 255),
(128, 'LU', 'LUX', 'Luxembourg', 'Luxembourg', 'Y', 2),
(129, 'MO', 'MAC', 'Macao', 'Macao', 'N', 255),
(130, 'MK', 'MKD', 'Mac&eacute;doine, L&#039;ex-r&eacute;publique Yougoslave De', 'Macedonia, The Former Yugoslav Republic Of', 'Y', 255),
(131, 'MG', 'MDG', 'Madagascar', 'Madagascar', 'N', 255),
(132, 'MY', 'MYS', 'Malaisie', 'Malaysia', 'N', 255),
(133, 'MW', 'MWI', 'Malawi', 'Malawi', 'N', 255),
(134, 'MV', 'MDV', 'Maldives', 'Maldives', 'N', 255),
(135, 'ML', 'MLI', 'Mali', 'Mali', 'N', 255),
(136, 'MT', 'MLT', 'Malte', 'Malta', 'N', 255),
(137, 'MP', 'MNP', 'Mariannes Du Nord, &Icirc;les', 'Northern Mariana Islands', 'N', 255),
(138, 'MA', 'MAR', 'Maroc', 'Morocco', 'Y', 255),
(139, 'MH', 'MHL', 'Marshall, &Icirc;les', 'Marshall Islands', 'N', 255),
(140, 'MQ', 'MTQ', 'Martinique', 'Martinique', 'Y', 255),
(141, 'MU', 'MUS', 'Maurice', 'Mauritius', 'N', 255),
(142, 'MR', 'MRT', 'Mauritanie', 'Mauritania', 'Y', 255),
(143, 'YT', 'MYT', 'Mayotte', 'Mayotte', 'Y', 255),
(144, 'MX', 'MEX', 'Mexique', 'Mexico', 'N', 255),
(145, 'FM', 'FSM', 'Micron&eacute;sie, &Eacute;tats F&eacute;d&eacute;r&eacute;s De', 'Micronesia, Federated States Of', 'N', 255),
(146, 'MD', 'MDA', 'Moldova, R&eacute;publique De', 'Moldova, Republic Of', 'Y', 255),
(147, 'MC', 'MCO', 'Monaco', 'Monaco', 'N', 255),
(148, 'MN', 'MNG', 'Mongolie', 'Mongolia', 'N', 255),
(149, 'MS', 'MSR', 'Montserrat', 'Montserrat', 'N', 255),
(150, 'MZ', 'MOZ', 'Mozambique', 'Mozambique', 'N', 255),
(151, 'MM', 'MMR', 'Myanmar', 'Myanmar', 'N', 255),
(152, 'NA', 'NAM', 'Namibie', 'Namibia', 'N', 255),
(153, 'NR', 'NRU', 'Nauru', 'Nauru', 'N', 255),
(154, 'NP', 'NPL', 'N&eacute;pal', 'Nepal', 'N', 255),
(155, 'NI', 'NIC', 'Nicaragua', 'Nicaragua', 'N', 255),
(156, 'NE', 'NER', 'Niger', 'Niger', 'N', 255),
(157, 'NG', 'NGA', 'Nig&eacute;ria', 'Nigeria', 'N', 255),
(158, 'NU', 'NIU', 'Niu&eacute;', 'Niue', 'N', 255),
(159, 'NF', 'NFK', 'Norfolk, &Icirc;le', 'Norfolk Island', 'N', 255),
(160, 'NO', 'NOR', 'Norv&egrave;ge', 'Norway', 'Y', 2),
(161, 'NC', 'NCL', 'Nouvelle-cal&eacute;donie', 'New Caledonia', 'Y', 255),
(162, 'NZ', 'NZL', 'Nouvelle-z&eacute;lande', 'New Zealand', 'Y', 255),
(163, 'IO', 'IOT', 'Oc&eacute;an Indien, Territoire Britannique De L&#039;', 'British Indian Ocean Territory', 'N', 255),
(164, 'OM', 'OMN', 'Oman', 'Oman', 'N', 255),
(165, 'UG', 'UGA', 'Ouganda', 'Uganda', 'N', 255),
(166, 'UZ', 'UZB', 'Ouzb&eacute;kistan', 'Uzbekistan', 'N', 255),
(167, 'PK', 'PAK', 'Pakistan', 'Pakistan', 'N', 255),
(168, 'PW', 'PLW', 'Palaos', 'Palau', 'N', 255),
(169, 'PS', 'PSE', 'Palestinien Occup&eacute;, Territoire', 'Palestinian Territory, Occupied', 'N', 255),
(170, 'PA', 'PAN', 'Panama', 'Panama', 'N', 255),
(171, 'PG', 'PNG', 'Papouasie-nouvelle-guin&eacute;e', 'Papua New Guinea', 'N', 255),
(172, 'PY', 'PRY', 'Paraguay', 'Paraguay', 'N', 255),
(173, 'NL', 'NLD', 'Pays-bas', 'Netherlands', 'Y', 2),
(174, 'PE', 'PER', 'P&eacute;rou', 'Peru', 'N', 255),
(175, 'PH', 'PHL', 'Philippines', 'Philippines', 'N', 255),
(176, 'PN', 'PCN', 'Pitcairn', 'Pitcairn', 'N', 255),
(177, 'PL', 'POL', 'Pologne', 'Poland', 'Y', 255),
(178, 'PF', 'PYF', 'Polyn&eacute;sie Fran&ccedil;aise', 'French Polynesia', 'Y', 255),
(179, 'PR', 'PRI', 'Porto Rico', 'Puerto Rico', 'N', 255),
(180, 'PT', 'PRT', 'Portugal (hors AÃ§ores et MadÃ¨re)', 'Portugal (except Azores and Madere)', 'Y', 2),
(181, 'PT-AZO', 'PRT', 'Portugal - A&ccedil;ores', 'Portugal - A&ccedil;ores', 'Y', 255),
(182, 'PT-MDR', 'PRT', 'Portugal - Mad&egrave;re', 'Portugal - Mad&egrave;re', 'Y', 255),
(183, 'QA', 'QAT', 'Qatar', 'Qatar', 'N', 255),
(184, 'RE', 'REU', 'R&eacute;union', 'Reunion', 'Y', 255),
(185, 'RO', 'ROM', 'Roumanie', 'Romania', 'Y', 255),
(186, 'GB', 'GBR', 'Royaume-uni', 'United Kingdom', 'Y', 2),
(187, 'RU', 'RUS', 'Russie, F&eacute;d&eacute;ration De', 'Russian Federation', 'Y', 255),
(188, 'RW', 'RWA', 'Rwanda', 'Rwanda', 'N', 255),
(189, 'EH', 'ESH', 'Sahara Occidental', 'Western Sahara', 'N', 255),
(190, 'KN', 'KNA', 'Saint-kitts-et-nevis', 'Saint Kitts And Nevis', 'N', 255),
(191, 'SM', 'SMR', 'Saint-marin', 'San Marino', 'N', 255),
(192, 'PM', 'SPM', 'Saint-pierre-et-miquelon', 'Saint Pierre And Miquelon', 'Y', 255),
(193, 'VA', 'VAT', 'Saint-si&egrave;ge (&eacute;tat De La Cit&eacute; Du Vatican)', 'Holy See (vatican City State)', 'N', 255),
(194, 'VC', 'VCT', 'Saint-vincent-et-les Grenadines', 'Saint Vincent And The Grenadines', 'N', 255),
(195, 'SH', 'SHN', 'Sainte-h&eacute;l&egrave;ne', 'Saint Helena', 'N', 255),
(196, 'LC', 'LCA', 'Sainte-lucie', 'Saint Lucia', 'N', 255),
(197, 'SB', 'SLB', 'Salomon, &Icirc;les', 'Solomon Islands', 'N', 255),
(198, 'WS', 'WSM', 'Samoa', 'Samoa', 'N', 255),
(199, 'AS', 'ASM', 'Samoa Am&eacute;ricaines', 'American Samoa', 'N', 255),
(200, 'ST', 'STP', 'Sao Tom&eacute;-et-principe', 'Sao Tome And Principe', 'N', 255),
(201, 'SN', 'SEN', 'S&eacute;n&eacute;gal', 'Senegal', 'N', 255),
(202, 'CS', 'SYC', 'Seychelles', 'Seychelles', 'N', 255),
(203, 'SC', 'SLE', 'Sierra Leone', 'Sierra Leone', 'N', 255),
(204, 'SG', 'SGP', 'Singapour', 'Singapore', 'N', 255),
(205, 'SK', 'SVK', 'Slovaquie', 'Slovakia', 'Y', 255),
(206, 'SI', 'SVN', 'Slov&eacute;nie', 'Slovenia', 'Y', 255),
(207, 'SO', 'SOM', 'Somalie', 'Somalia', 'N', 255),
(208, 'SD', 'SDN', 'Soudan', 'Sudan', 'N', 255),
(209, 'LK', 'LKA', 'Sri Lanka', 'Sri Lanka', 'N', 255),
(210, 'SE', 'SWE', 'Su&egrave;de', 'Sweden', 'Y', 255),
(211, 'CH', 'CHE', 'Suisse', 'Switzerland', 'Y', 2),
(212, 'SR', 'SUR', 'Suriname', 'Suriname', 'N', 255),
(213, 'SJ', 'SJM', 'Svalbard Et &Icirc;le Jan Mayen', 'Svalbard And Jan Mayen', 'N', 255),
(214, 'SZ', 'SWZ', 'Swaziland', 'Swaziland', 'N', 255),
(215, 'SY', 'SYR', 'Syrienne, R&eacute;publique Arabe', 'Syrian Arab Republic', 'N', 255),
(216, 'TJ', 'TJK', 'Tadjikistan', 'Tajikistan', 'N', 255),
(217, 'TW', 'TWN', 'Ta&iuml;wan, Province De Chine', 'Taiwan, Province Of China', 'N', 255),
(218, 'TZ', 'TZA', 'Tanzanie, R&eacute;publique-unie De', 'Tanzania, United Republic Of', 'N', 255),
(219, 'TD', 'TCD', 'Tchad', 'Chad', 'N', 255),
(220, 'CZ', 'CZE', 'Tch&egrave;que, R&eacute;publique', 'Czech Republic', 'Y', 255),
(221, 'TF', 'ATF', 'Terres Australes Fran&ccedil;aises', 'French Southern Territories', 'N', 255),
(222, 'TH', 'THA', 'Tha&iuml;lande', 'Thailand', 'N', 255),
(223, 'TL', 'TMP', 'Timor-leste', 'Timor-leste', 'N', 255),
(224, 'TG', 'TGO', 'Togo', 'Togo', 'N', 255),
(225, 'TK', 'TKL', 'Tokelau', 'Tokelau', 'N', 255),
(226, 'TO', 'TON', 'Tonga', 'Tonga', 'N', 255),
(227, 'TT', 'TTO', 'Trinit&eacute;-et-tobago', 'Trinidad And Tobago', 'N', 255),
(228, 'TN', 'TUN', 'Tunisie', 'Tunisia', 'Y', 255),
(229, 'TM', 'TKM', 'Turkm&eacute;nistan', 'Turkmenistan', 'N', 255),
(230, 'TC', 'TCA', 'Turks Et Ca&iuml;ques, &Icirc;les', 'Turks And Caicos Islands', 'N', 255),
(231, 'TR', 'TUR', 'Turquie', 'Turkey', 'N', 255),
(232, 'TV', 'TUV', 'Tuvalu', 'Tuvalu', 'N', 255),
(233, 'UA', 'UKR', 'Ukraine', 'Ukraine', 'N', 255),
(234, 'UY', 'URY', 'Uruguay', 'Uruguay', 'N', 255),
(235, 'VU', 'VUT', 'Vanuatu', 'Vanuatu', 'N', 255),
(236, 'VE', 'VEN', 'Venezuela', 'Venezuela', 'N', 255),
(237, 'VN', 'VNM', 'Viet Nam', 'Viet Nam', 'N', 255),
(238, 'WF', 'WLF', 'Wallis Et Futuna', 'Wallis And Futuna', 'Y', 255),
(239, 'YE', 'YEM', 'Y&eacute;men', 'Yemen', 'N', 255),
(240, 'YU', 'YUG', 'Yougoslavie', 'Yugoslavia', 'N', 255),
(241, 'ZM', 'ZMB', 'Zambie', 'Zambia', 'N', 255),
(242, 'ZW', 'ZWE', 'Zimbabwe', 'Zimbabwe', 'N', 255);

-- 
-- Contenu de la table `ecomm_info_payment_type`
-- 

INSERT INTO `ecomm_info_payment_type` (`id_payment_type`, `id_language`, `name`, `fields`) VALUES 
(1, 1, 'VISA', 'Num. Confirmation'),
(1, 2, 'VISA', 'Confirmation Num.'),
(2, 1, 'MASTER CARD', 'Num. Confirmation'),
(2, 2, 'MASTER CARD', 'Confirmation Num.'),
(3, 1, 'AMERICAIN EXPRESS', 'Num. Confirmation'),
(3, 2, 'AMERICAIN EXPRESS', 'Confirmation Num.'),
(4, 1, 'Paypal', 'Num. Confirmation'),
(4, 2, 'Paypal', 'Confirmation Num.'),
(5, 1, 'ChÃ¨que', 'Num. Chèque;Date d''émission;Signataire'),
(5, 2, 'Check', 'Check Num.;Issue date;Signature'),
(6, 1, 'Argent comptant', ''),
(6, 2, 'Cash', ''),
(7, 1, 'Carte Bancaire (VISA, MC, CB)', 'Num. Transaction;Autorisation;Carte;Nombre d''échéances'),
(7, 2, 'Bank Card (VISA, MC, CB)', 'Transaction Num.;Auth.;Card;Nb. of payments'),
(8, 1, 'Receive & Pay', 'Num. Transaction'),
(8, 2, 'Receive & Pay', 'Transaction ID'),
(0, 0, '', '');

-- 
-- Contenu de la table `ecomm_payment_type`
-- 

INSERT INTO `ecomm_payment_type` (`id`, `name`, `image_file`, `ordering`) VALUES 
(3, 'AMEX', 'amex.png', 6),
(4, 'PAYPAL', 'paypal.png', 2),
(5, 'CHECK', 'check.png', 4),
(6, 'CASH', 'cash.png', 5),
(7, 'Carte Bancaire', 'carte-bancaire.png', 1),
(8, 'Receive & Pay', 'rnp.png', 3),
(9, '', '', 0);

-- 
-- Contenu de la table `ecomm_shipping_destination_transporter`
-- 

INSERT INTO `ecomm_shipping_destination_transporter` (`id_destination`, `id_transporter`, `shipping_min_fees`, `shipping_min_weight`, `shipping_max_weight`, `shipping_price_per_unit`, `shipping_flat_rate`, `shipping_table_weight`, `shipping_table_amount`, `insurance_min_fees`, `insurance_flat_rate`, `insurance_percentage`, `insurance_table_amount`) VALUES 
(1, 1, 0.00, 199.99, 30000.00, 0.00, 0.00, '499=6.90;750=8.11;1000=8.59;1500=9.07;2000=9.49;3000=10.21;4000=11.04;5000=11.76;6000=12.48;7000=12.96;10000=14.21;15000=16.37;30000=22.11', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(1, 2, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', 0.00, 0.00, 0.00, ''),
(1, 3, 3.80, 0.00, 199.99, 0.00, 3.80, '', '', 0.00, 0.00, 0.00, ''),
(0, 4, 4.16, 200.00, 499.00, 0.00, 4.16, '', '', 0.00, 0.00, 0.00, ''),
(2, 5, 0.00, 0.00, 30000.00, 0.00, 0.00, '500=13.95;1000=17.42;2000=22.33;3000=28.31;4000=34.29;5000=40.27;6000=46.25;7000=52.23;8000=58.21;9000=64.19;10000=70.17;15000=91.1;20000=121;25000=150.9;30000=180.8', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(3, 5, 0.00, 0.00, 10000.00, 0.00, 0.00, '500=20.53;1000=24.12;2000=37.75;3000=51.39;4000=65.02;5000=78.66;6000=92.29;7000=105.93;8000=119.56;9000=133.2;10000=146.83', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(4, 6, 0.00, 0.00, 30000.00, 0.00, 0.00, '1000=13.36;2000=13.95;3000=14.55;4000=15.15;5000=15.75;6000=16.35;7000=16.94;8000=17.54;9000=18.14;10000=18.74;15000=21.73;20000=24.72;25000=27.71;30000=30.70', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(5, 6, 0.00, 0.00, 30000.00, 0.00, 0.00, '1000=16.11;2000=16.82;3000=17.54;4000=18.26;5000=18.98;6000=19.69;7000=20.41;8000=21.13;9000=21.85;10000=22.57;15000=26.15;20000=29.74;25000=33.33;30000=36.92', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(6, 6, 0.00, 0.00, 30000.00, 0.00, 0.00, '1000=16.59;2000=17.9;3000=19.22;4000=20.53;5000=21.85;6000=23.16;7000=24.48;8000=25.79;9000=27.11;10000=28.43;15000=35;20000=41.58;25000=48.16;30000=54.74', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(7, 6, 0.00, 0.00, 30000.00, 0.00, 0.00, '1000=21.37;2000=22.57;3000=23.76;4000=24.96;5000=26.15;6000=27.35;7000=28.55;8000=29.74;9000=30.94;10000=32.13;15000=38.11;20000=44.09;25000=50.07;30000=56.05', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(8, 6, 0.00, 0.00, 30000.00, 0.00, 0.00, '1000=21.73;2000=25.67;3000=29.62;4000=33.57;5000=37.52;6000=41.46;7000=45.41;8000=49.36;9000=53.3;10000=57.25;15000=71.6;20000=85.95;25000=100.31;30000=114.66', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(9, 6, 0.00, 0.00, 30000.00, 0.00, 0.00, '1000=21.85;2000=26.03;3000=30.22;4000=34.41;5000=38.59;6000=42.78;7000=46.96;8000=51.15;9000=55.34;10000=59.52;15000=75.07;20000=90.62;25000=106.17;30000=121.71', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(10, 6, 0.00, 0.00, 30000.00, 0.00, 0.00, '1000=26.51;2000=34.88;3000=43.26;4000=51.63;5000=60;6000=68.37;7000=76.74;8000=85.12;9000=93.49;10000=101.86;15000=131.76;20000=161.66;25000=191.56;30000=221.46', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(11, 6, 0.00, 0.00, 30000.00, 0.00, 0.00, '1000=28.9;2000=40.27;3000=51.63;4000=62.99;5000=74.35;6000=85.71;7000=97.08;8000=108.44;9000=119.8;10000=131.16;15000=167.04;20000=202.92;25000=238.8;30000=274.68', '', 0.00, 0.00, 0.00, '150=1.08;300=2.16;450=3.24;600=4.32;750=5.4;900=6.48;1050=7.56;1200=8.64;1350=9.72;1500=10.8'),
(1, 8, 35.00, 30000.00, 80000.00, 0.00, 35.00, '', '', 0.00, 0.00, 0.00, ''),
(0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', 0.00, 0.00, 0.00, '');

-- 
-- Contenu de la table `ecomm_shipping_destinations`
-- 

INSERT INTO `ecomm_shipping_destinations` (`id`, `name`, `cond_country`, `cond_city`, `cond_postal`, `details`) VALUES 
(1, 'FRANCE', 'FR', '', '', ''),
(2, 'DOM / Mayotte / St-Pierre et Miquelon', 'GF;GP;MQ;RE;YT;PM', '', '', 'Martinique, Guyane FranÃ§aise, Guadeloupe, RÃ©union, Mayotte, Sait-Pierre & Miquelon'),
(3, 'TOM', 'NC;PF;WF', '', '', 'Nouvelle CalÃ©donie, PolynÃ©sie FranÃ§aise, Wallis & Futuna'),
(4, 'International Zone1', 'DE;BE;NL;LU', '', '', 'Allemagne(DE), Belgique(BE), Pays-Bas(NL); Luxembourg(LU)'),
(5, 'International Zone2', 'GB;IT;ES', '', '', 'Grande Bretagne(GB), Italie(IT), Espagne(ES)'),
(6, 'International Zone3', 'AT;DK;IE;PT', '', '', 'Autriche(AT), Danemark(DK), Irlande(IE), Portugal(PT)'),
(7, 'International Zone4', 'FI;NO;SE;CH;PT-AZO;PT-MDR;ES-ICA', '', '', 'Finlande(FI), NorvÃ¨ge(NO), SuÃ¨de(SE), Suisse(CH), AÃ§ores - Portugal (PT-AZO), MadÃ¨re - Portugal(PT-MDR), Ã®les Canaries - Espagne(ES-ICA)'),
(8, 'International Zone5', 'GR;HU;IS;PL;CZ;SK;SI', '', '', 'GrÃ¨ce(GR), Hongrie(HU), Islande(IS), Pologne(PL), RÃ©publique TchÃ¨que(CZ), Slovaquie(SK), SlovÃ©nie(SI)'),
(9, 'International Zone6', 'BY;EE;LV;LT;UK;RO;MD;RU;AL;BA;BG;HR;MK;ME;RS;TN;MA;DZ;LY;MR', '', '', 'BiÃ©lorussie(BY) , Estonie(EE), Lettonie(LV), Lituanie(LT), Ukraine(UK), Roumanie(RO), Moldavie(MD), Russie(RU), Albanie(AL), Bosnie-HerzÃ©govine(BA), Bulgarie(BG), Croatie(HR), MacÃ©doine(MK), MontÃ©nÃ©gro(ME), Serbie(RS), Tunisie(TN), Maroc(MA), AlgÃ©rie(DZ), Lybie(LY), Mauritanie(MR)'),
(10, 'International Zone7', 'CA;US', '', '', 'USA(US), Canada(CA)'),
(11, 'International Zone8', 'AU;NZ;BR', '', '', 'Australie(AU); Nouvelle ZÃ©lande(NZ); BrÃ©zil'),
(12, '', '', '', '', '');

-- 
-- Contenu de la table `ecomm_shipping_transporters`
-- 

INSERT INTO `ecomm_shipping_transporters` (`id`, `name`, `tracking_url`, `method_shiping_fees`, `method_insurance_fees`, `insurance_optional`, `ordering`) VALUES 
(1, 'COLISSIMO (48h ouvrables)', 'http://www.coliposte.net/gp/services/main.jsp?m=10003005&colispart=%%TRACKINGID%%', 'table_weight', 'table', 'Y', 3),
(2, 'Retrait Paris 13e', '', 'flat_rate', 'none', 'Y', 20),
(3, 'Distingo Suivi a bulles', 'http://www.numeridog.com/trackingDistingo.php?trackingId=%%TRACKINGID%%', 'flat_rate', 'flat_rate', 'N', 1),
(4, 'Distingo suivi sans bulles', 'http://www.numeridog.com/trackingDistingo.php?trackingId=%%TRACKINGID%%', 'flat_rate', 'flat_rate', 'N', 2),
(5, 'COLISSIMO Expert OM', 'http://www.coliposte.net/gp/services/main.jsp?m=10003005&colispart=%%TRACKINGID%%', 'table_weight', 'table', 'Y', 4),
(6, 'COLISSIMO Expert I', 'http://www.coliposte.net/gp/services/main.jsp?m=10003005&colispart=%%TRACKINGID%%', 'table_weight', 'table', 'Y', 5),
(8, 'Gros colis (72h)', '', 'flat_rate', 'flat_rate', 'N', 6),
(9, '', '', 'flat_rate', 'flat_rate', 'Y', 0);

-- 
-- Contenu de la table `ecomm_tax_authority`
-- 

INSERT INTO `ecomm_tax_authority` (`id`, `id_tax_group`, `name`, `method`, `ordering`, `value`) VALUES 
(1, 1, 'TVA 19.6%', 'percentage', 1, 19.600),
(3, 0, '', 'percentage', 0, 0.000);

-- 
-- Contenu de la table `ecomm_tax_group`
-- 

INSERT INTO `ecomm_tax_group` (`id`, `name`, `method`, `ordering`, `default`) VALUES 
(1, 'TVA 19.6%', 'separate', 1, 'Y'),
(2, 'TVA 0%', 'cumulate', 2, 'N'),
(4, '', 'separate', 0, 'N');

-- 
-- Contenu de la table `ecomm_tax_group_authority`
-- 

INSERT INTO `ecomm_tax_group_authority` (`id_tax_group`, `id_tax_authority`) VALUES 
(1, 1),
(0, 0);

-- 
-- Contenu de la table `gen_languages`
-- 

INSERT INTO `gen_languages` (`id`, `name`, `image_file`, `language_file`, `default`, `used`, `locales_folder`) VALUES 
(1, 'Français', 'french.gif', '', 'N', 'Y', 'fr_CA'),
(2, 'English', 'english.jpg', '', 'Y', 'Y', 'en_CA');
