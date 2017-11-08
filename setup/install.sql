SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `admins` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(16) NOT NULL,
  `password` varchar(64) NOT NULL,
  `status` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `admins_permissions` (
  `admin` int(3) NOT NULL,
  `permission` tinyint(2) NOT NULL,
  PRIMARY KEY (`admin`,`permission`),
  KEY `permission` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cookies` (
  `user` varchar(9) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiration_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`token`),
  KEY `client` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `countries` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8 NOT NULL,
  `code` varchar(3) CHARACTER SET utf8 NOT NULL,
  `language_code` varchar(3) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `language_code` (`language_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=248 ;

INSERT INTO `countries` (`id`, `name`, `code`, `language_code`) VALUES
(1, 'Andorra', 'AD', 'ca'),
(2, 'United Arab Emirates', 'AE', 'ar'),
(3, 'Afghanistan', 'AF', 'ps'),
(4, 'Antigua and Barbuda', 'AG', 'en'),
(5, 'Anguilla', 'AI', 'en'),
(6, 'Albania', 'AL', 'sq'),
(7, 'Armenia', 'AM', 'hy'),
(8, 'Angola', 'AO', 'pt'),
(9, 'Argentina', 'AR', 'es'),
(10, 'American Samoa', 'AS', 'en'),
(11, 'Austria', 'AT', 'de'),
(12, 'Australia', 'AU', 'en'),
(13, 'Aruba', 'AW', 'nl'),
(14, 'Åland', 'AX', 'sv'),
(15, 'Azerbaijan', 'AZ', 'az'),
(16, 'Bosnia and Herzegovina', 'BA', 'bs'),
(17, 'Barbados', 'BB', 'en'),
(18, 'Bangladesh', 'BD', 'bn'),
(19, 'Belgium', 'BE', 'nl'),
(20, 'Burkina Faso', 'BF', 'fr'),
(21, 'Bulgaria', 'BG', 'bg'),
(22, 'Bahrain', 'BH', 'ar'),
(23, 'Burundi', 'BI', 'fr'),
(24, 'Benin', 'BJ', 'fr'),
(25, 'Saint Barthélemy', 'BL', 'fr'),
(26, 'Bermuda', 'BM', 'en'),
(27, 'Brunei', 'BN', 'ms'),
(28, 'Bolivia', 'BO', 'es'),
(29, 'Bonaire', 'BQ', 'nl'),
(30, 'Brazil', 'BR', 'pt'),
(31, 'Bahamas', 'BS', 'en'),
(32, 'Bhutan', 'BT', 'dz'),
(33, 'Botswana', 'BW', 'en'),
(34, 'Belarus', 'BY', 'be'),
(35, 'Belize', 'BZ', 'en'),
(36, 'Canada', 'CA', 'en'),
(37, 'Cocos [Keeling] Islands', 'CC', 'en'),
(38, 'Democratic Republic of the Congo', 'CD', 'fr'),
(39, 'Central African Republic', 'CF', 'fr'),
(40, 'Republic of the Congo', 'CG', 'fr'),
(41, 'Switzerland', 'CH', 'de'),
(42, 'Ivory Coast', 'CI', 'fr'),
(43, 'Cook Islands', 'CK', 'en'),
(44, 'Chile', 'CL', 'es'),
(45, 'Cameroon', 'CM', 'en'),
(46, 'China', 'CN', 'zh'),
(47, 'Colombia', 'CO', 'es'),
(48, 'Costa Rica', 'CR', 'es'),
(49, 'Cuba', 'CU', 'es'),
(50, 'Cape Verde', 'CV', 'pt'),
(51, 'Curacao', 'CW', 'nl'),
(52, 'Christmas Island', 'CX', 'en'),
(53, 'Cyprus', 'CY', 'el'),
(54, 'Czech Republic', 'CZ', 'cs'),
(55, 'Germany', 'DE', 'de'),
(56, 'Djibouti', 'DJ', 'fr'),
(57, 'Denmark', 'DK', 'da'),
(58, 'Dominica', 'DM', 'en'),
(59, 'Dominican Republic', 'DO', 'es'),
(60, 'Algeria', 'DZ', 'ar'),
(61, 'Ecuador', 'EC', 'es'),
(62, 'Estonia', 'EE', 'et'),
(63, 'Egypt', 'EG', 'ar'),
(64, 'Western Sahara', 'EH', 'es'),
(65, 'Eritrea', 'ER', 'ti'),
(66, 'Spain', 'ES', 'es'),
(67, 'Ethiopia', 'ET', 'am'),
(68, 'Finland', 'FI', 'fi'),
(69, 'Fiji', 'FJ', 'en'),
(70, 'Falkland Islands', 'FK', 'en'),
(71, 'Micronesia', 'FM', 'en'),
(72, 'Faroe Islands', 'FO', 'fo'),
(73, 'France', 'FR', 'fr'),
(74, 'Gabon', 'GA', 'fr'),
(75, 'United Kingdom', 'GB', 'en'),
(76, 'Grenada', 'GD', 'en'),
(77, 'Georgia', 'GE', 'ka'),
(78, 'French Guiana', 'GF', 'fr'),
(79, 'Guernsey', 'GG', 'en'),
(80, 'Ghana', 'GH', 'en'),
(81, 'Gibraltar', 'GI', 'en'),
(82, 'Greenland', 'GL', 'kl'),
(83, 'Gambia', 'GM', 'en'),
(84, 'Guinea', 'GN', 'fr'),
(85, 'Guadeloupe', 'GP', 'fr'),
(86, 'Equatorial Guinea', 'GQ', 'es'),
(87, 'Greece', 'GR', 'el'),
(88, 'South Georgia and the South Sandwich Islands', 'GS', 'en'),
(89, 'Guatemala', 'GT', 'es'),
(90, 'Guam', 'GU', 'en'),
(91, 'Guinea-Bissau', 'GW', 'pt'),
(92, 'Guyana', 'GY', 'en'),
(93, 'Hong Kong', 'HK', 'zh'),
(94, 'Heard Island and McDonald Islands', 'HM', 'en'),
(95, 'Honduras', 'HN', 'es'),
(96, 'Croatia', 'HR', 'hr'),
(97, 'Haiti', 'HT', 'fr'),
(98, 'Hungary', 'HU', 'hu'),
(99, 'Indonesia', 'ID', 'id'),
(100, 'Ireland', 'IE', 'ga'),
(101, 'Israel', 'IL', 'he'),
(102, 'Isle of Man', 'IM', 'en'),
(103, 'India', 'IN', 'hi'),
(104, 'British Indian Ocean Territory', 'IO', 'en'),
(105, 'Iraq', 'IQ', 'ar'),
(106, 'Iran', 'IR', 'fa'),
(107, 'Iceland', 'IS', 'is'),
(108, 'Italy', 'IT', 'it'),
(109, 'Jersey', 'JE', 'en'),
(110, 'Jamaica', 'JM', 'en'),
(111, 'Jordan', 'JO', 'ar'),
(112, 'Japan', 'JP', 'ja'),
(113, 'Kenya', 'KE', 'en'),
(114, 'Kyrgyzstan', 'KG', 'ky'),
(115, 'Cambodia', 'KH', 'km'),
(116, 'Kiribati', 'KI', 'en'),
(117, 'Comoros', 'KM', 'ar'),
(118, 'Saint Kitts and Nevis', 'KN', 'en'),
(119, 'North Korea', 'KP', 'ko'),
(120, 'South Korea', 'KR', 'ko'),
(121, 'Kuwait', 'KW', 'ar'),
(122, 'Cayman Islands', 'KY', 'en'),
(123, 'Kazakhstan', 'KZ', 'kk'),
(124, 'Laos', 'LA', 'lo'),
(125, 'Lebanon', 'LB', 'ar'),
(126, 'Saint Lucia', 'LC', 'en'),
(127, 'Liechtenstein', 'LI', 'de'),
(128, 'Sri Lanka', 'LK', 'si'),
(129, 'Liberia', 'LR', 'en'),
(130, 'Lesotho', 'LS', 'en'),
(131, 'Lithuania', 'LT', 'lt'),
(132, 'Luxembourg', 'LU', 'fr'),
(133, 'Latvia', 'LV', 'lv'),
(134, 'Libya', 'LY', 'ar'),
(135, 'Morocco', 'MA', 'ar'),
(136, 'Monaco', 'MC', 'fr'),
(137, 'Moldova', 'MD', 'ro'),
(138, 'Montenegro', 'ME', 'sr'),
(139, 'Saint Martin', 'MF', 'en'),
(140, 'Madagascar', 'MG', 'fr'),
(141, 'Marshall Islands', 'MH', 'en'),
(142, 'Macedonia', 'MK', 'mk'),
(143, 'Mali', 'ML', 'fr'),
(144, 'Myanmar [Burma]', 'MM', 'my'),
(145, 'Mongolia', 'MN', 'mn'),
(146, 'Macao', 'MO', 'zh'),
(147, 'Northern Mariana Islands', 'MP', 'en'),
(148, 'Martinique', 'MQ', 'fr'),
(149, 'Mauritania', 'MR', 'ar'),
(150, 'Montserrat', 'MS', 'en'),
(151, 'Malta', 'MT', 'mt'),
(152, 'Mauritius', 'MU', 'en'),
(153, 'Maldives', 'MV', 'dv'),
(154, 'Malawi', 'MW', 'en'),
(155, 'Mexico', 'MX', 'es'),
(156, 'Mozambique', 'MZ', 'pt'),
(157, 'Namibia', 'NA', 'en'),
(158, 'New Caledonia', 'NC', 'fr'),
(159, 'Niger', 'NE', 'fr'),
(160, 'Norfolk Island', 'NF', 'en'),
(161, 'Nigeria', 'NG', 'en'),
(162, 'Nicaragua', 'NI', 'es'),
(163, 'Netherlands', 'NL', 'nl'),
(164, 'Norway', 'NO', 'no'),
(165, 'Nepal', 'NP', 'ne'),
(166, 'Nauru', 'NR', 'en'),
(167, 'Niue', 'NU', 'en'),
(168, 'New Zealand', 'NZ', 'en'),
(169, 'Oman', 'OM', 'ar'),
(170, 'Panama', 'PA', 'es'),
(171, 'Peru', 'PE', 'es'),
(172, 'French Polynesia', 'PF', 'fr'),
(173, 'Papua New Guinea', 'PG', 'en'),
(174, 'Philippines', 'PH', 'en'),
(175, 'Pakistan', 'PK', 'en'),
(176, 'Poland', 'PL', 'pl'),
(177, 'Saint Pierre and Miquelon', 'PM', 'fr'),
(178, 'Pitcairn Islands', 'PN', 'en'),
(179, 'Puerto Rico', 'PR', 'es'),
(180, 'Palestine', 'PS', 'ar'),
(181, 'Portugal', 'PT', 'pt'),
(182, 'Palau', 'PW', 'en'),
(183, 'Paraguay', 'PY', 'es'),
(184, 'Qatar', 'QA', 'ar'),
(185, 'Réunion', 'RE', 'fr'),
(186, 'Romania', 'RO', 'ro'),
(187, 'Serbia', 'RS', 'sr'),
(188, 'Russia', 'RU', 'ru'),
(189, 'Rwanda', 'RW', 'rw'),
(190, 'Saudi Arabia', 'SA', 'ar'),
(191, 'Solomon Islands', 'SB', 'en'),
(192, 'Seychelles', 'SC', 'fr'),
(193, 'Sudan', 'SD', 'ar'),
(194, 'Sweden', 'SE', 'sv'),
(195, 'Singapore', 'SG', 'en'),
(196, 'Saint Helena', 'SH', 'en'),
(197, 'Slovenia', 'SI', 'sl'),
(198, 'Svalbard and Jan Mayen', 'SJ', 'no'),
(199, 'Slovakia', 'SK', 'sk'),
(200, 'Sierra Leone', 'SL', 'en'),
(201, 'San Marino', 'SM', 'it'),
(202, 'Senegal', 'SN', 'fr'),
(203, 'Somalia', 'SO', 'so'),
(204, 'Suriname', 'SR', 'nl'),
(205, 'South Sudan', 'SS', 'en'),
(206, 'São Tomé and Príncipe', 'ST', 'pt'),
(207, 'El Salvador', 'SV', 'es'),
(208, 'Sint Maarten', 'SX', 'nl'),
(209, 'Syria', 'SY', 'ar'),
(210, 'Swaziland', 'SZ', 'en'),
(211, 'Turks and Caicos Islands', 'TC', 'en'),
(212, 'Chad', 'TD', 'fr'),
(213, 'French Southern Territories', 'TF', 'fr'),
(214, 'Togo', 'TG', 'fr'),
(215, 'Thailand', 'TH', 'th'),
(216, 'Tajikistan', 'TJ', 'tg'),
(217, 'Tokelau', 'TK', 'en'),
(218, 'East Timor', 'TL', 'pt'),
(219, 'Turkmenistan', 'TM', 'tk'),
(220, 'Tunisia', 'TN', 'ar'),
(221, 'Tonga', 'TO', 'en'),
(222, 'Turkey', 'TR', 'tr'),
(223, 'Trinidad and Tobago', 'TT', 'en'),
(224, 'Tuvalu', 'TV', 'en'),
(225, 'Taiwan', 'TW', 'zh'),
(226, 'Tanzania', 'TZ', 'sw'),
(227, 'Ukraine', 'UA', 'uk'),
(228, 'Uganda', 'UG', 'en'),
(229, 'U.S. Minor Outlying Islands', 'UM', 'en'),
(230, 'United States', 'US', 'en'),
(231, 'Uruguay', 'UY', 'es'),
(232, 'Uzbekistan', 'UZ', 'uz'),
(233, 'Vatican City', 'VA', 'it'),
(234, 'Saint Vincent and the Grenadines', 'VC', 'en'),
(235, 'Venezuela', 'VE', 'es'),
(236, 'British Virgin Islands', 'VG', 'en'),
(237, 'U.S. Virgin Islands', 'VI', 'en'),
(238, 'Vietnam', 'VN', 'vi'),
(239, 'Vanuatu', 'VU', 'bi'),
(240, 'Wallis and Futuna', 'WF', 'fr'),
(241, 'Samoa', 'WS', 'sm'),
(242, 'Kosovo', 'XK', 'sq'),
(243, 'Yemen', 'YE', 'ar'),
(244, 'Mayotte', 'YT', 'fr'),
(245, 'South Africa', 'ZA', 'af'),
(246, 'Zambia', 'ZM', 'en'),
(247, 'Zimbabwe', 'ZW', 'en');

CREATE TABLE `languages` (
  `code` varchar(3) CHARACTER SET utf8 NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `languages` (`code`, `name`) VALUES
('aa', 'Afar'),
('ab', 'Abkhazian'),
('af', 'Afrikaans'),
('ak', 'Akan'),
('am', 'Amharic'),
('an', 'Aragonese'),
('ar', 'Arabic'),
('as', 'Assamese'),
('av', 'Avar'),
('ay', 'Aymara'),
('az', 'Azerbaijani'),
('ba', 'Bashkir'),
('be', 'Belarusian'),
('bg', 'Bulgarian'),
('bh', 'Bihari'),
('bi', 'Bislama'),
('bm', 'Bambara'),
('bn', 'Bengali'),
('bo', 'Tibetan'),
('br', 'Breton'),
('bs', 'Bosnian'),
('ca', 'Catalan'),
('ce', 'Chechen'),
('ch', 'Chamorro'),
('co', 'Corsican'),
('cr', 'Cree'),
('cs', 'Czech'),
('cu', 'Old Church Slavonic / Old Bulgarian'),
('cv', 'Chuvash'),
('cy', 'Welsh'),
('da', 'Danish'),
('de', 'German'),
('dv', 'Divehi'),
('dz', 'Dzongkha'),
('ee', 'Ewe'),
('el', 'Greek'),
('en', 'English'),
('eo', 'Esperanto'),
('es', 'Spanish'),
('et', 'Estonian'),
('eu', 'Basque'),
('fa', 'Persian'),
('ff', 'Peul'),
('fi', 'Finnish'),
('fj', 'Fijian'),
('fo', 'Faroese'),
('fr', 'French'),
('fy', 'West Frisian'),
('ga', 'Irish'),
('gd', 'Scottish Gaelic'),
('gl', 'Galician'),
('gn', 'Guarani'),
('gu', 'Gujarati'),
('gv', 'Manx'),
('ha', 'Hausa'),
('he', 'Hebrew'),
('hi', 'Hindi'),
('ho', 'Hiri Motu'),
('hr', 'Croatian'),
('ht', 'Haitian'),
('hu', 'Hungarian'),
('hy', 'Armenian'),
('hz', 'Herero'),
('ia', 'Interlingua'),
('id', 'Indonesian'),
('ie', 'Interlingue'),
('ig', 'Igbo'),
('ii', 'Sichuan Yi'),
('ik', 'Inupiak'),
('io', 'Ido'),
('is', 'Icelandic'),
('it', 'Italian'),
('iu', 'Inuktitut'),
('ja', 'Japanese'),
('jv', 'Javanese'),
('ka', 'Georgian'),
('kg', 'Kongo'),
('ki', 'Kikuyu'),
('kj', 'Kuanyama'),
('kk', 'Kazakh'),
('kl', 'Greenlandic'),
('km', 'Cambodian'),
('kn', 'Kannada'),
('ko', 'Korean'),
('kr', 'Kanuri'),
('ks', 'Kashmiri'),
('ku', 'Kurdish'),
('kv', 'Komi'),
('kw', 'Cornish'),
('ky', 'Kirghiz'),
('la', 'Latin'),
('lb', 'Luxembourgish'),
('lg', 'Ganda'),
('li', 'Limburgian'),
('ln', 'Lingala'),
('lo', 'Laotian'),
('lt', 'Lithuanian'),
('lv', 'Latvian'),
('mg', 'Malagasy'),
('mh', 'Marshallese'),
('mi', 'Maori'),
('mk', 'Macedonian'),
('ml', 'Malayalam'),
('mn', 'Mongolian'),
('mo', 'Moldovan'),
('mr', 'Marathi'),
('ms', 'Malay'),
('mt', 'Maltese'),
('my', 'Burmese'),
('na', 'Nauruan'),
('nd', 'North Ndebele'),
('ne', 'Nepali'),
('ng', 'Ndonga'),
('nl', 'Dutch'),
('nn', 'Norwegian Nynorsk'),
('no', 'Norwegian'),
('nr', 'South Ndebele'),
('nv', 'Navajo'),
('ny', 'Chichewa'),
('oc', 'Occitan'),
('oj', 'Ojibwa'),
('om', 'Oromo'),
('or', 'Oriya'),
('os', 'Ossetian / Ossetic'),
('pa', 'Panjabi / Punjabi'),
('pi', 'Pali'),
('pl', 'Polish'),
('ps', 'Pashto'),
('pt', 'Portuguese'),
('qu', 'Quechua'),
('rm', 'Raeto Romance'),
('rn', 'Kirundi'),
('ro', 'Romanian'),
('ru', 'Russian'),
('rw', 'Rwandi'),
('sa', 'Sanskrit'),
('sc', 'Sardinian'),
('sd', 'Sindhi'),
('se', 'Northern Sami'),
('sg', 'Sango'),
('sh', 'Serbo-Croatian'),
('si', 'Sinhalese'),
('sk', 'Slovak'),
('sl', 'Slovenian'),
('sm', 'Samoan'),
('sn', 'Shona'),
('so', 'Somalia'),
('sq', 'Albanian'),
('sr', 'Serbian'),
('ss', 'Swati'),
('st', 'Southern Sotho'),
('su', 'Sundanese'),
('sv', 'Swedish'),
('sw', 'Swahili'),
('ta', 'Tamil'),
('te', 'Telugu'),
('tg', 'Tajik'),
('th', 'Thai'),
('ti', 'Tigrinya'),
('tk', 'Turkmen'),
('tl', 'Tagalog / Filipino'),
('tn', 'Tswana'),
('to', 'Tonga'),
('tr', 'Turkish'),
('ts', 'Tsonga'),
('tt', 'Tatar'),
('tw', 'Twi'),
('ty', 'Tahitian'),
('ug', 'Uyghur'),
('uk', 'Ukrainian'),
('ur', 'Urdu'),
('uz', 'Uzbek'),
('ve', 'Venda'),
('vi', 'Vietnamese'),
('vo', 'Volapük'),
('wa', 'Walloon'),
('wo', 'Wolof'),
('xh', 'Xhosa'),
('yi', 'Yiddish'),
('yo', 'Yoruba'),
('za', 'Zhuang'),
('zh', 'Chinese'),
('zu', 'Zulu');

CREATE TABLE `media` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `modules` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `has_frontend` tinyint(1) NOT NULL,
  `has_backend` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

INSERT INTO `modules` (`id`, `name`, `has_frontend`, `has_backend`) VALUES
(1, 'Users', 1, 1),
(2, 'Testimonials', 1, 1),
(3, 'News', 1, 1),
(4, 'Pages', 0, 1),
(5, 'Administrators', 0, 1);

CREATE TABLE `news` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `language` varchar(3) CHARACTER SET utf8 DEFAULT 'en',
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `date_published` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `admin` int(3) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1' COMMENT '0 - Hidden, 1 - Visible',
  PRIMARY KEY (`id`),
  KEY `admin` (`admin`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `pages` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `language` varchar(3) DEFAULT 'en',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `h1` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `js` varchar(255) DEFAULT '',
  `css` varchar(255) DEFAULT '',
  `menu_text` varchar(255) DEFAULT '',
  `submenu_text` varchar(255) DEFAULT '',
  `menu_order` tinyint(3) DEFAULT '0',
  `menu_parent` tinyint(2) DEFAULT '0',
  `visible` tinyint(1) DEFAULT '1',
  `metaog` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`,`language`),
  KEY `visible` (`visible`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `passwords_reset` (
  `user` int(5) NOT NULL,
  `code` varchar(64) NOT NULL,
  `expiration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `permissions` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `permissions` (`id`, `name`) VALUES
(1, 'View users'),
(2, 'Edit users'),
(3, 'Edit pages'),
(4, 'Edit news'),
(5, 'Edit testimonials'),
(6, 'Edit administrators');

CREATE TABLE `strings` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `text` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

INSERT INTO `strings` (`id`, `text`) VALUES
(1, 'This site uses cookies. By continuing to browse the site, you are agreeing to our use of cookies.'),
(2, 'More details'),
(3, 'here'),
(4, 'Accept'),
(5, 'News'),
(6, 'Login'),
(7, 'Logout'),
(8, 'Page not found'),
(9, 'Error 404'),
(10, 'The page you requested could not be found, either contact your webmaster or try again. Use your browsers <strong>Back</strong> button to navigate to the page you have prevously come from.'),
(11, 'Or you could just press this neat little button'),
(12, 'Take Me Home'),
(13, 'You did not confirmed your account'),
(14, 'Your account is blocked'),
(15, 'No user registered with these info'),
(16, 'There is another user registered with this email address'),
(17, 'You will be redirected to confirm your email in 1 second');

CREATE TABLE `testimonials` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `company` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_bin NOT NULL,
  `short` text CHARACTER SET utf8,
  `content` text CHARACTER SET utf8 NOT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '0 - Hidden, 1 - Visible',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE `translations` (
  `string_id` int(5) NOT NULL,
  `language` varchar(3) CHARACTER SET utf8 NOT NULL,
  `translation` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`string_id`,`language`),
  KEY `string_id` (`string_id`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) CHARACTER SET utf8 NOT NULL,
  `lastname` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text,
  `city` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `country` int(3) DEFAULT '0',
  `password` varchar(64) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `settings` text,
  `cookie` varchar(255) DEFAULT '',
  `cookie_expire` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `country` (`country`),
  KEY `status` (`status`),
  KEY `cookie` (`cookie`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `user_confirm` (
  `user` int(5) NOT NULL,
  `code` varchar(64) NOT NULL,
  PRIMARY KEY (`user`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `admins_permissions`
  ADD CONSTRAINT `admins_permissions_ibfk_1` FOREIGN KEY (`admin`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `admins_permissions_ibfk_2` FOREIGN KEY (`permission`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `countries`
  ADD CONSTRAINT `countries_ibfk_1` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`admin`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `news_ibfk_2` FOREIGN KEY (`language`) REFERENCES `languages` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`language`) REFERENCES `languages` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `passwords_reset`
  ADD CONSTRAINT `passwords_reset_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `translations`
  ADD CONSTRAINT `translations_ibfk_1` FOREIGN KEY (`string_id`) REFERENCES `strings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `translations_ibfk_2` FOREIGN KEY (`language`) REFERENCES `languages` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`country`) REFERENCES `countries` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

ALTER TABLE `user_confirm`
  ADD CONSTRAINT `user_confirm_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
