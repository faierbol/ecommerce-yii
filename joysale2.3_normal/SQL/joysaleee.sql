-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 04, 2017 at 07:36 PM
-- Server version: 5.5.58-0ubuntu0.14.04.1
-- PHP Version: 5.6.32-1+ubuntu14.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `joysale_2017`
--

-- --------------------------------------------------------

--
-- Table structure for table `hts_admins`
--

CREATE TABLE IF NOT EXISTS `hts_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `type` enum('admin','moderater') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `hts_admins`
--

INSERT INTO `hts_admins` (`id`, `name`, `password`, `email`, `type`) VALUES
(1, 'admin', 'MTIzNDU2', 'admin@joysale.com', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `hts_adspromotiondetails`
--

CREATE TABLE IF NOT EXISTS `hts_adspromotiondetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) NOT NULL,
  `promotionTime` int(11) NOT NULL,
  `promotionTranxId` int(11) NOT NULL,
  `createdDate` int(11) NOT NULL,
  PRIMARY KEY (`id`,`productId`,`promotionTranxId`),
  KEY `promotionItem` (`productId`),
  KEY `promotionTranx` (`promotionTranxId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_banners`
--

CREATE TABLE IF NOT EXISTS `hts_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bannerimage` varchar(60) NOT NULL,
  `appbannerimage` varchar(60) NOT NULL,
  `bannerurl` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_carts`
--

CREATE TABLE IF NOT EXISTS `hts_carts` (
  `cartId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `merchantId` int(11) NOT NULL,
  `productId` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `options` varchar(60) DEFAULT NULL,
  `createdDate` int(11) DEFAULT NULL,
  PRIMARY KEY (`cartId`),
  KEY `cart_user` (`userId`),
  KEY `cart_product` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_categories`
--

CREATE TABLE IF NOT EXISTS `hts_categories` (
  `categoryId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `parentCategory` int(11) DEFAULT NULL,
  `image` varchar(60) NOT NULL,
  `categoryProperty` text NOT NULL,
  `slug` varchar(50) NOT NULL,
  `createdDate` datetime DEFAULT NULL,
  PRIMARY KEY (`categoryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- Dumping data for table `hts_categories`
--

INSERT INTO `hts_categories` (`categoryId`, `name`, `parentCategory`, `image`, `categoryProperty`, `slug`, `createdDate`) VALUES
(37, 'Electronics', 0, '8870-electronic.png', '{"itemCondition":"enable","exchangetoBuy":"enable","buyNow":"enable","myOffer":"enable","contactSeller":"disable"}', 'electronic', '2017-12-02 12:29:54'),
(38, 'Fashion', 0, '6438-9028job.png', '{"itemCondition":"enable","exchangetoBuy":"enable","buyNow":"enable","myOffer":"enable","contactSeller":"disable"}', 'fashion', '2017-12-02 11:11:13'),
(39, 'Motors', 0, '1931-bike.png', '{"itemCondition":"enable","exchangetoBuy":"enable","buyNow":"enable","myOffer":"enable","contactSeller":"disable"}', 'motors', '2017-12-02 12:32:02'),
(40, 'Pets', 0, '720-49866.png', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'pets', '2017-12-02 11:12:19'),
(41, 'Property', 0, '610-7746home.png', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'property', '2017-12-02 11:12:13'),
(42, 'Services', 0, '3913-9368forsal.png', '{"itemCondition":"enable","exchangetoBuy":"enable","buyNow":"enable","myOffer":"enable","contactSeller":"disable"}', 'services', '2017-12-02 11:12:03'),
(43, 'Jobs', 0, '9774-job.png', '{"itemCondition":"enable","exchangetoBuy":"enable","buyNow":"enable","myOffer":"enable","contactSeller":"disable"}', 'jobs', '2017-12-02 12:32:48'),
(44, 'Smartphones', 0, '2163-mobiles.png', '{"itemCondition":"enable","exchangetoBuy":"enable","buyNow":"enable","myOffer":"enable","contactSeller":"disable"}', 'smartphone', '2017-12-02 12:29:41'),
(45, 'Cars', 0, '4758-8220settin.png', '{"itemCondition":"enable","exchangetoBuy":"enable","buyNow":"enable","myOffer":"enable","contactSeller":"disable"}', 'cars', '2017-12-02 11:11:40'),
(46, 'Mobile', 37, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'mobile', '2017-12-02 07:45:55'),
(47, 'Laptop', 37, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'laptop', '2017-12-02 07:49:24'),
(48, 'Television', 37, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'television', '2017-12-02 07:50:05'),
(49, 'Camera', 37, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'camera', '2017-12-02 07:50:47'),
(50, 'Men', 38, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'men', '2017-12-02 07:51:29'),
(51, 'Women', 38, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'women', '2017-12-02 07:52:56'),
(52, 'Kids', 38, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'kids', '2017-12-02 07:53:18'),
(53, 'Furniture', 41, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'furniture', '2017-12-02 07:55:00'),
(54, 'Kitchen and Dining', 41, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'kitchenand', '2017-12-02 07:56:37'),
(55, 'Home Decor', 41, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'homedecor', '2017-12-02 07:59:58'),
(56, 'Featured', 37, '', '{"itemCondition":"disable","exchangetoBuy":"disable","buyNow":"disable","myOffer":"disable","contactSeller":"disable"}', 'featured', '2017-12-02 08:22:56'),
(57, 'Truck', 39, '3598-image.png', '{"itemCondition":"enable","exchangetoBuy":"enable","buyNow":"enable","myOffer":"enable","contactSeller":"disable"}', 'truck', '2017-12-02 10:34:33'),
(58, 'Kids', 41, '8022-kids111.jpg', '{"itemCondition":"enable","exchangetoBuy":"enable","buyNow":"enable","myOffer":"enable","contactSeller":"disable"}', 'kids', '2017-12-02 14:18:38');

-- --------------------------------------------------------

--
-- Table structure for table `hts_chats`
--

CREATE TABLE IF NOT EXISTS `hts_chats` (
  `chatId` int(11) NOT NULL AUTO_INCREMENT,
  `user1` int(11) NOT NULL,
  `user2` int(11) NOT NULL,
  `lastMessage` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastToRead` int(11) NOT NULL,
  `lastContacted` int(11) NOT NULL,
  PRIMARY KEY (`chatId`),
  KEY `user1` (`user1`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_comments`
--

CREATE TABLE IF NOT EXISTS `hts_comments` (
  `commentId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `comment` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `createdDate` int(11) DEFAULT NULL,
  PRIMARY KEY (`commentId`),
  KEY `fk_comments_users1` (`userId`),
  KEY `fk_comments_product1` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_commissions`
--

CREATE TABLE IF NOT EXISTS `hts_commissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `percentage` varchar(25) NOT NULL,
  `minRate` varchar(25) NOT NULL,
  `maxRate` varchar(25) NOT NULL,
  `status` int(2) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_country`
--

CREATE TABLE IF NOT EXISTS `hts_country` (
  `countryId` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(2) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`countryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=246 ;

--
-- Dumping data for table `hts_country`
--

INSERT INTO `hts_country` (`countryId`, `code`, `country`) VALUES
(0, 'OT', 'Others'),
(1, 'AF', 'Afghanistan'),
(2, 'AL', 'Albania'),
(3, 'DZ', 'Algeria'),
(4, 'AS', 'American Samoa'),
(5, 'AD', 'Andorra'),
(6, 'AO', 'Angola'),
(7, 'AI', 'Anguilla'),
(8, 'AQ', 'Antarctica'),
(9, 'AG', 'Antigua and Barbuda'),
(10, 'AR', 'Argentina'),
(11, 'AM', 'Armenia'),
(12, 'AW', 'Aruba'),
(13, 'AU', 'Australia'),
(14, 'AT', 'Austria'),
(15, 'AZ', 'Azerbaijan'),
(16, 'BS', 'Bahamas'),
(17, 'BH', 'Bahrain'),
(18, 'BD', 'Bangladesh'),
(19, 'BB', 'Barbados'),
(20, 'BY', 'Belarus'),
(21, 'BE', 'Belgium'),
(22, 'BZ', 'Belize'),
(23, 'BJ', 'Benin'),
(24, 'BM', 'Bermuda'),
(25, 'BT', 'Bhutan'),
(26, 'BO', 'Bolivia'),
(27, 'BA', 'Bosnia and Herzegovina'),
(28, 'BW', 'Botswana'),
(29, 'BV', 'Bouvet Island'),
(30, 'BR', 'Brazil'),
(31, 'IO', 'British Indian Ocean Territory'),
(32, 'BN', 'Brunei Darussalam'),
(33, 'BG', 'Bulgaria'),
(34, 'BF', 'Burkina Faso'),
(35, 'BI', 'Burundi'),
(36, 'KH', 'Cambodia'),
(37, 'CM', 'Cameroon'),
(38, 'CA', 'Canada'),
(39, 'CV', 'Cape Verde'),
(40, 'KY', 'Cayman Islands'),
(41, 'CF', 'Central African Republic'),
(42, 'TD', 'Chad'),
(43, 'CL', 'Chile'),
(44, 'CN', 'China'),
(45, 'CX', 'Christmas Island'),
(46, 'CC', 'Cocos ('',Keeling) Islands'),
(47, 'CO', 'Colombia'),
(48, 'KM', 'Comoros'),
(49, 'CG', 'Congo'),
(50, 'CD', 'Congo, The Democratic Republic of the'),
(51, 'CK', 'Cook Islands'),
(52, 'CR', 'Costa Rica'),
(53, 'CI', 'Côte D''Ivoire'),
(54, 'HR', 'Croatia'),
(55, 'CU', 'Cuba'),
(56, 'CY', 'Cyprus'),
(57, 'CZ', 'Czech Republic'),
(58, 'DK', 'Denmark'),
(59, 'DJ', 'Djibouti'),
(60, 'DM', 'Dominica'),
(61, 'DO', 'Dominican Republic'),
(62, 'EC', 'Ecuador'),
(63, 'EG', 'Egypt'),
(64, 'SV', 'El Salvador'),
(65, 'GQ', 'Equatorial Guinea'),
(66, 'ER', 'Eritrea'),
(67, 'EE', 'Estonia'),
(68, 'ET', 'Ethiopia'),
(69, 'FK', 'Falkland Islands (Malvinas)'),
(70, 'FO', 'Faroe Islands'),
(71, 'FJ', 'Fiji'),
(72, 'FI', 'Finland'),
(73, 'FR', 'France'),
(74, 'GF', 'French Guiana'),
(75, 'PF', 'French Polynesia'),
(76, 'TF', 'French Southern Territories'),
(77, 'GA', 'Gabon'),
(78, 'GM', 'Gambia'),
(79, 'GE', 'Georgia'),
(80, 'DE', 'Germany'),
(81, 'GH', 'Ghana'),
(82, 'GI', 'Gibraltar'),
(83, 'GR', 'Greece'),
(84, 'GL', 'Greenland'),
(85, 'GD', 'Grenada'),
(86, 'GP', 'Guadeloupe'),
(87, 'GU', 'Guam'),
(88, 'GT', 'Guatemala'),
(89, 'GG', 'Guernsey'),
(90, 'GN', 'Guinea'),
(91, 'GW', 'Guinea-Bissau'),
(92, 'GY', 'Guyana'),
(93, 'HT', 'Haiti'),
(94, 'HM', 'Heard Island and McDonald Islands'),
(95, 'VA', 'Holy See (Vatican City State)'),
(96, 'HN', 'Honduras'),
(97, 'HK', 'Hong Kong'),
(98, 'HU', 'Hungary'),
(99, 'IS', 'Iceland'),
(100, 'IN', 'India'),
(101, 'ID', 'Indonesia'),
(102, 'IR', 'Iran, Islamic Republic of'),
(103, 'IQ', 'Iraq'),
(104, 'IE', 'Ireland'),
(105, 'IM', 'Isle of Man'),
(106, 'IL', 'Israel'),
(107, 'IT', 'Italy'),
(108, 'JM', 'Jamaica'),
(109, 'JP', 'Japan'),
(110, 'JE', 'Jersey'),
(111, 'JO', 'Jordan'),
(112, 'KZ', 'Kazakhstan'),
(113, 'KE', 'Kenya'),
(114, 'KI', 'Kiribati'),
(115, 'KP', 'Korea, Democratic People''s Republic of'),
(116, 'KR', 'Korea, Republic of'),
(117, 'KW', 'Kuwait'),
(118, 'KG', 'Kyrgyzstan'),
(119, 'LA', 'Lao People''s Democratic Republic'),
(120, 'LV', 'Latvia'),
(121, 'LB', 'Lebanon'),
(122, 'LS', 'Lesotho'),
(123, 'LR', 'Liberia'),
(124, 'LY', 'Libyan Arab Jamahiriya'),
(125, 'LI', 'Liechtenstein'),
(126, 'LT', 'Lithuania'),
(127, 'LU', 'Luxembourg'),
(128, 'MO', 'Macao'),
(129, 'MK', 'Macedonia, The Former Yugoslav Republic of'),
(130, 'MG', 'Madagascar'),
(131, 'MW', 'Malawi'),
(132, 'MY', 'Malaysia'),
(133, 'MV', 'Maldives'),
(134, 'ML', 'Mali'),
(135, 'MT', 'Malta'),
(136, 'MH', 'Marshall Islands'),
(137, 'MQ', 'Martinique'),
(138, 'MR', 'Mauritania'),
(139, 'MU', 'Mauritius'),
(140, 'YT', 'Mayotte'),
(141, 'MX', 'Mexico'),
(142, 'FM', 'Micronesia, Federated States of'),
(143, 'MD', 'Moldova, Republic of'),
(144, 'MC', 'Monaco'),
(145, 'MN', 'Mongolia'),
(146, 'ME', 'Montenegro'),
(147, 'MS', 'Montserrat'),
(148, 'MA', 'Morocco'),
(149, 'MZ', 'Mozambique'),
(150, 'MM', 'Myanmar'),
(151, 'NA', 'Namibia'),
(152, 'NR', 'Nauru'),
(153, 'NP', 'Nepal'),
(154, 'NL', 'Netherlands'),
(155, 'AN', 'Netherlands Antilles'),
(156, 'NC', 'New Caledonia'),
(157, 'NZ', 'New Zealand'),
(158, 'NI', 'Nicaragua'),
(159, 'NE', 'Niger'),
(160, 'NG', 'Nigeria'),
(161, 'NU', 'Niue'),
(162, 'NF', 'Norfolk Island'),
(163, 'MP', 'Northern Mariana Islands'),
(164, 'NO', 'Norway'),
(165, 'OM', 'Oman'),
(166, 'PK', 'Pakistan'),
(167, 'PW', 'Palau'),
(168, 'PS', 'Palestinian Territory, Occupied'),
(169, 'PA', 'Panama'),
(170, 'PG', 'Papua New Guinea'),
(171, 'PY', 'Paraguay'),
(172, 'PE', 'Peru'),
(173, 'PH', 'Philippines'),
(174, 'PN', 'Pitcairn'),
(175, 'PL', 'Poland'),
(176, 'PT', 'Portugal'),
(177, 'PR', 'Puerto Rico'),
(178, 'QA', 'Qatar'),
(179, 'RE', 'Reunion'),
(180, 'RO', 'Romania'),
(181, 'RU', 'Russian Federation'),
(182, 'RW', 'Rwanda'),
(183, 'BL', 'Saint Barthélemy'),
(184, 'SH', 'Saint Helena'),
(185, 'KN', 'Saint Kitts and Nevis'),
(186, 'LC', 'Saint Lucia'),
(187, 'MF', 'Saint Martin'),
(188, 'PM', 'Saint Pierre and Miquelon'),
(189, 'VC', 'Saint Vincent and the Grenadines'),
(190, 'WS', 'Samoa'),
(191, 'SM', 'San Marino'),
(192, 'ST', 'Sao Tome and Principe'),
(193, 'SA', 'Saudi Arabia'),
(194, 'SN', 'Senegal'),
(195, 'RS', 'Serbia'),
(196, 'SC', 'Seychelles'),
(197, 'SL', 'Sierra Leone'),
(198, 'SG', 'Singapore'),
(199, 'SK', 'Slovakia'),
(200, 'SI', 'Slovenia'),
(201, 'SB', 'Solomon Islands'),
(202, 'SO', 'Somalia'),
(203, 'ZA', 'South Africa'),
(204, 'GS', 'South Georgia and the South Sandwich Islands'),
(205, 'ES', 'Spain'),
(206, 'LK', 'Sri Lanka'),
(207, 'SD', 'Sudan'),
(208, 'SR', 'Suriname'),
(209, 'SJ', 'Svalbard and Jan Mayen'),
(210, 'SZ', 'Swaziland'),
(211, 'SE', 'Sweden'),
(212, 'CH', 'Switzerland'),
(213, 'SY', 'Syrian Arab Republic'),
(214, 'TW', 'Taiwan, Province Of China'),
(215, 'TJ', 'Tajikistan'),
(216, 'TZ', 'Tanzania, United Republic of'),
(217, 'TH', 'Thailand'),
(218, 'TL', 'Timor-Leste'),
(219, 'TG', 'Togo'),
(220, 'TK', 'Tokelau'),
(221, 'TO', 'Tonga'),
(222, 'TT', 'Trinidad and Tobago'),
(223, 'TN', 'Tunisia'),
(224, 'TR', 'Turkey'),
(225, 'TM', 'Turkmenistan'),
(226, 'TC', 'Turks and Caicos Islands'),
(227, 'TV', 'Tuvalu'),
(228, 'UG', 'Uganda'),
(229, 'UA', 'Ukraine'),
(230, 'AE', 'United Arab Emirates'),
(231, 'GB', 'United Kingdom'),
(232, 'US', 'United States'),
(233, 'UM', 'United States Minor Outlying Islands'),
(234, 'UY', 'Uruguay'),
(235, 'UZ', 'Uzbekistan'),
(236, 'VU', 'Vanuatu'),
(237, 'VE', 'Venezuela'),
(238, 'VN', 'Viet Nam'),
(239, 'VG', 'Virgin Islands, British'),
(240, 'VI', 'Virgin Islands, U.S.'),
(241, 'WF', 'Wallis And Futuna'),
(242, 'EH', 'Western Sahara'),
(243, 'YE', 'Yemen'),
(244, 'ZM', 'Zambia'),
(245, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `hts_coupons`
--

CREATE TABLE IF NOT EXISTS `hts_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `couponCode` varchar(15) NOT NULL,
  `sellerId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `productName` varchar(150) NOT NULL,
  `couponType` int(11) NOT NULL,
  `couponValue` int(11) NOT NULL,
  `maxAmount` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `status` int(2) NOT NULL,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_currencies`
--

CREATE TABLE IF NOT EXISTS `hts_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(50) NOT NULL,
  `currency_shortcode` varchar(10) NOT NULL,
  `currency_image` varchar(100) NOT NULL,
  `currency_symbol` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `hts_currencies`
--

INSERT INTO `hts_currencies` (`id`, `currency_name`, `currency_shortcode`, `currency_image`, `currency_symbol`) VALUES
(4, 'Euro', 'EUR', '', '€'),
(3, 'U.S. Dollar', 'USD', '', '$');

-- --------------------------------------------------------

--
-- Table structure for table `hts_exchanges`
--

CREATE TABLE IF NOT EXISTS `hts_exchanges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requestFrom` int(11) NOT NULL,
  `requestTo` int(11) NOT NULL,
  `mainProductId` int(11) NOT NULL,
  `exchangeProductId` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `slug` varchar(8) NOT NULL,
  `blockExchange` int(2) NOT NULL DEFAULT '0',
  `exchangeHistory` text NOT NULL,
  `reviewFlagSender` int(1) NOT NULL,
  `reviewFlagReceiver` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `requestFrom` (`requestFrom`),
  KEY `requestTo` (`requestTo`),
  KEY `mainProductId` (`mainProductId`),
  KEY `exchangeProductId` (`exchangeProductId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_favorites`
--

CREATE TABLE IF NOT EXISTS `hts_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_followers`
--

CREATE TABLE IF NOT EXISTS `hts_followers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `follow_userId` int(11) NOT NULL,
  `followedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_helppages`
--

CREATE TABLE IF NOT EXISTS `hts_helppages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(60) NOT NULL,
  `pageContent` longtext NOT NULL,
  `slug` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_invoices`
--

CREATE TABLE IF NOT EXISTS `hts_invoices` (
  `invoiceId` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) NOT NULL,
  `invoiceNo` varchar(20) DEFAULT NULL,
  `invoiceDate` int(11) DEFAULT NULL,
  `invoiceStatus` varchar(20) DEFAULT NULL,
  `paymentMethod` varchar(100) DEFAULT NULL,
  `paymentTranxid` mediumtext,
  PRIMARY KEY (`invoiceId`,`orderId`),
  KEY `fk_invoices_orders1` (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_logs`
--

CREATE TABLE IF NOT EXISTS `hts_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('follow','like','comment','add','admin','exchange','myoffer','promote','expromote','order','adminpayment') CHARACTER SET latin1 NOT NULL,
  `userid` int(11) NOT NULL,
  `notifyto` tinytext NOT NULL,
  `sourceid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL DEFAULT '0',
  `notifymessage` text NOT NULL,
  `notification_id` int(11) NOT NULL,
  `message` text,
  `createddate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `hts_logs`
--

INSERT INTO `hts_logs` (`id`, `type`, `userid`, `notifyto`, `sourceid`, `itemid`, `notifymessage`, `notification_id`, `message`, `createddate`) VALUES
(1, 'add', 1, '0', 1, 1, 'added a product', 0, NULL, 1512395865);

-- --------------------------------------------------------

--
-- Table structure for table `hts_messages`
--

CREATE TABLE IF NOT EXISTS `hts_messages` (
  `messageId` int(11) NOT NULL AUTO_INCREMENT,
  `chatId` int(11) NOT NULL,
  `senderId` int(11) NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sourceId` int(11) NOT NULL,
  `messageType` enum('exchange','normal','offer') NOT NULL,
  `createdDate` int(11) NOT NULL,
  PRIMARY KEY (`messageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_orderitems`
--

CREATE TABLE IF NOT EXISTS `hts_orderitems` (
  `orderitemId` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `itemName` varchar(150) DEFAULT NULL,
  `itemPrice` varchar(18) DEFAULT NULL,
  `itemSize` varchar(30) DEFAULT NULL,
  `itemQuantity` int(11) DEFAULT NULL,
  `itemunitPrice` varchar(18) DEFAULT NULL,
  `shippingPrice` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`orderitemId`),
  KEY `fk_orderitems_orders1` (`orderId`),
  KEY `fk_orderitems_products1` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_orders`
--

CREATE TABLE IF NOT EXISTS `hts_orders` (
  `orderId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `sellerId` int(11) NOT NULL,
  `totalCost` varchar(18) DEFAULT NULL,
  `totalShipping` varchar(7) DEFAULT NULL,
  `admincommission` varchar(18) NOT NULL,
  `discount` varchar(15) NOT NULL,
  `discountSource` varchar(50) NOT NULL,
  `orderDate` int(11) DEFAULT NULL,
  `shippingAddress` int(11) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `sellerPaypalId` varchar(150) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `statusDate` int(11) NOT NULL,
  `trackPayment` varchar(100) NOT NULL,
  `reviewFlag` int(1) NOT NULL,
  PRIMARY KEY (`orderId`),
  KEY `fk_orders_users1` (`userId`),
  KEY `fk_orders_users2` (`sellerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_photos`
--

CREATE TABLE IF NOT EXISTS `hts_photos` (
  `photoId` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `createdDate` int(11) NOT NULL,
  PRIMARY KEY (`photoId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `hts_photos`
--

INSERT INTO `hts_photos` (`photoId`, `productId`, `name`, `createdDate`) VALUES
(1, 1, '356b31bf87f4ecedee36f6124a302a66.jpg', 1512395865);

-- --------------------------------------------------------

--
-- Table structure for table `hts_productconditions`
--

CREATE TABLE IF NOT EXISTS `hts_productconditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `condition` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `hts_productconditions`
--

INSERT INTO `hts_productconditions` (`id`, `condition`) VALUES
(1, 'Brand New'),
(2, 'Old');

-- --------------------------------------------------------

--
-- Table structure for table `hts_products`
--

CREATE TABLE IF NOT EXISTS `hts_products` (
  `productId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `category` int(11) DEFAULT NULL,
  `subCategory` int(11) DEFAULT NULL,
  `price` double NOT NULL,
  `currency` varchar(10) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `sizeOptions` mediumtext,
  `productCondition` varchar(100) DEFAULT NULL,
  `createdDate` int(11) DEFAULT NULL,
  `likeCount` int(11) DEFAULT NULL,
  `commentCount` int(11) DEFAULT NULL,
  `chatAndBuy` int(11) NOT NULL,
  `exchangeToBuy` int(11) NOT NULL,
  `instantBuy` int(11) NOT NULL,
  `myoffer` int(11) NOT NULL,
  `paypalid` varchar(150) NOT NULL,
  `shippingTime` varchar(60) NOT NULL,
  `shippingcountry` int(11) NOT NULL,
  `shippingCost` double NOT NULL,
  `soldItem` int(2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `likes` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `reports` varchar(250) NOT NULL,
  `reportCount` int(11) NOT NULL,
  `promotionType` enum('1','2','3') NOT NULL DEFAULT '3',
  `approvedStatus` int(1) NOT NULL,
  `Initial_approve` int(1) NOT NULL,
  PRIMARY KEY (`productId`),
  KEY `fk_product_users1` (`userId`),
  KEY `fk_product_category1` (`category`),
  KEY `fk_product_category2` (`subCategory`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `hts_products`
--

INSERT INTO `hts_products` (`productId`, `userId`, `name`, `description`, `category`, `subCategory`, `price`, `currency`, `quantity`, `sizeOptions`, `productCondition`, `createdDate`, `likeCount`, `commentCount`, `chatAndBuy`, `exchangeToBuy`, `instantBuy`, `myoffer`, `paypalid`, `shippingTime`, `shippingcountry`, `shippingCost`, `soldItem`, `location`, `latitude`, `longitude`, `likes`, `views`, `reports`, `reportCount`, `promotionType`, `approvedStatus`, `Initial_approve`) VALUES
(1, 1, 'Oneplus 3T', '&lt;p&gt;Oneplus 3T is high grade smartphone&lt;/p&gt;', 44, NULL, 1200, '€-EUR', 1, NULL, 'Brand New', 1512395865, NULL, NULL, 0, 0, 0, 1, '', '', 0, 0, 0, 'London, United Kingdom', 51.5073509, -0.12775829999998223, 0, 1, '', 0, '3', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `hts_promotions`
--

CREATE TABLE IF NOT EXISTS `hts_promotions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `days` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_promotiontransaction`
--

CREATE TABLE IF NOT EXISTS `hts_promotiontransaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `promotionName` varchar(250) NOT NULL,
  `promotionPrice` int(11) NOT NULL,
  `promotionTime` int(11) NOT NULL,
  `status` enum('Live','Expired','Canceled') NOT NULL,
  `tranxId` varchar(250) NOT NULL,
  `createdDate` int(11) NOT NULL,
  `initial_check` int(1) NOT NULL,
  `approvedStatus` int(1) NOT NULL,
  PRIMARY KEY (`id`,`productId`),
  KEY `itemDetails` (`productId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `hts_promotiontransaction`
--

INSERT INTO `hts_promotiontransaction` (`id`, `productId`, `userId`, `promotionName`, `promotionPrice`, `promotionTime`, `status`, `tranxId`, `createdDate`, `initial_check`, `approvedStatus`) VALUES
(1, 113, 104, 'urgent', 30, 0, 'Live', '0JN51310370538517', 1512201303, 1, 1),
(2, 28, 1, 'urgent', 30, 0, 'Live', '9HX32713X7485773M', 1512202120, 1, 1),
(3, 30, 1, 'adds', 35, 14, 'Live', '64G61091HH510635L', 1512202616, 1, 1),
(4, 32, 10, 'urgent', 30, 0, 'Live', '3WH71057SN820672R', 1512202713, 1, 1),
(5, 35, 1, 'urgent', 30, 0, 'Live', '9U448699B8969584S', 1512203154, 1, 1),
(6, 42, 11, 'urgent', 30, 0, 'Live', '18L41153AL790081F', 1512203663, 1, 1),
(7, 46, 13, 'adds', 20, 7, 'Live', '71B55869KY200925R', 1512203823, 1, 1),
(8, 47, 1, 'adds', 20, 7, 'Live', 'as3bw485', 1512204362, 1, 1),
(9, 50, 11, 'adds', 35, 14, 'Live', '76W48340F2818245W', 1512204390, 1, 1),
(10, 68, 13, 'adds', 35, 14, 'Live', '1UK588082T978725M', 1512207213, 1, 1),
(11, 72, 16, 'urgent', 30, 0, 'Live', '3KT82163Y0793211N', 1512207984, 1, 1),
(12, 76, 15, 'urgent', 30, 0, 'Expired', '43H95119X9721511S', 1512209144, 1, 1),
(13, 77, 19, 'adds', 35, 14, 'Live', '4BT52975563991259', 1512209198, 1, 1),
(14, 78, 19, 'urgent', 30, 0, 'Live', '4T4597102C570063M', 1512209458, 1, 1),
(15, 80, 19, 'adds', 20, 7, 'Live', '9G140252YX856872U', 1512209845, 1, 1),
(16, 81, 1, 'urgent', 30, 0, 'Expired', '65116021J16132226', 1512210132, 1, 1),
(17, 82, 15, 'adds', 20, 7, 'Expired', '5C52091771772820R', 1512210214, 1, 1),
(18, 83, 20, 'adds', 35, 14, 'Canceled', '71826424XS931093S', 1512210787, 1, 1),
(19, 115, 17, 'urgent', 30, 0, 'Expired', '37B73123PT476814M', 1512215320, 1, 1),
(20, 114, 104, 'adds', 20, 7, 'Live', '7NV32304AP748740B', 1512215429, 1, 1),
(21, 119, 1, 'urgent', 30, 0, 'Live', '2ME905491L709735C', 1512219171, 1, 1),
(22, 123, 26, 'urgent', 30, 0, 'Expired', '1jtdxmg2', 1512220192, 1, 1),
(23, 83, 20, 'urgent', 30, 0, 'Expired', 'bbzn1w35', 1512220907, 1, 1),
(24, 83, 20, 'adds', 35, 14, 'Expired', 'ppy8g9xm', 1512221030, 1, 1),
(26, 126, 30, 'urgent', 30, 0, 'Expired', 'qz8h6pkg', 1512222373, 1, 1),
(27, 127, 30, 'adds', 20, 7, 'Live', 'fm5r2bhh', 1512222622, 1, 1),
(28, 128, 33, 'urgent', 30, 0, 'Live', '9KK51841Y8243683D', 1512222852, 1, 1),
(29, 22, 26, 'urgent', 30, 0, 'Live', 'cnzf3amy', 1512223929, 1, 1),
(30, 83, 20, 'urgent', 30, 0, 'Expired', 'k131r3kr', 1512223994, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `hts_resetpassword`
--

CREATE TABLE IF NOT EXISTS `hts_resetpassword` (
  `resetId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `resetData` varchar(50) NOT NULL,
  `createdDate` int(11) NOT NULL,
  PRIMARY KEY (`resetId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_reviews`
--

CREATE TABLE IF NOT EXISTS `hts_reviews` (
  `reviewId` int(11) NOT NULL AUTO_INCREMENT,
  `senderId` int(11) NOT NULL,
  `receiverId` int(11) NOT NULL,
  `reviewTitle` varchar(60) CHARACTER SET utf8 NOT NULL,
  `review` varchar(500) CHARACTER SET utf8 NOT NULL,
  `rating` int(1) NOT NULL,
  `reviewType` varchar(30) NOT NULL,
  `sourceId` int(11) NOT NULL,
  `createdDate` int(11) NOT NULL,
  PRIMARY KEY (`reviewId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_shipping`
--

CREATE TABLE IF NOT EXISTS `hts_shipping` (
  `shippingId` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `shippingCost` varchar(45) DEFAULT NULL,
  `createdDate` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`shippingId`),
  KEY `product_shipping` (`productId`),
  KEY `country_details` (`countryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_shippingaddresses`
--

CREATE TABLE IF NOT EXISTS `hts_shippingaddresses` (
  `shippingaddressId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `nickname` varchar(45) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `address1` varchar(60) DEFAULT NULL,
  `address2` varchar(60) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `state` varchar(40) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `countryCode` int(11) DEFAULT NULL,
  PRIMARY KEY (`shippingaddressId`),
  KEY `userId` (`userId`),
  KEY `countryCode` (`countryCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_sitesettings`
--

CREATE TABLE IF NOT EXISTS `hts_sitesettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smtpEmail` varchar(150) NOT NULL,
  `smtpPassword` varchar(50) NOT NULL,
  `smtpPort` varchar(10) NOT NULL,
  `smtpHost` varchar(150) NOT NULL,
  `smtpEnable` int(2) NOT NULL,
  `smtpSSL` int(11) NOT NULL,
  `signup_active` enum('yes','no') NOT NULL DEFAULT 'no',
  `socialLoginDetails` text NOT NULL,
  `logo` varchar(60) NOT NULL,
  `logoDarkVersion` varchar(60) NOT NULL,
  `sitename` varchar(40) NOT NULL,
  `metaData` text NOT NULL,
  `default_userimage` varchar(60) NOT NULL,
  `favicon` varchar(15) NOT NULL,
  `currency_priority` text NOT NULL,
  `category_priority` text NOT NULL,
  `promotionCurrency` tinytext NOT NULL,
  `urgentPrice` int(11) NOT NULL,
  `searchDistance` int(11) NOT NULL,
  `searchType` enum('miles','kilometer') NOT NULL,
  `searchList` varchar(200) NOT NULL,
  `sitepaymentmodes` varchar(250) NOT NULL,
  `commission_status` int(2) NOT NULL,
  `paypal_settings` text NOT NULL,
  `braintree_settings` varchar(250) NOT NULL,
  `api_settings` text NOT NULL,
  `footer_settings` text NOT NULL,
  `tracking_code` text NOT NULL,
  `googleapikey` varchar(100) NOT NULL,
  `account_sid` varchar(100) NOT NULL,
  `auth_token` varchar(100) NOT NULL,
  `sms_number` varchar(50) NOT NULL,
  `facebookshare` int(1) NOT NULL,
  `bannerstatus` int(1) NOT NULL,
  `promotionStatus` int(1) NOT NULL,
  `product_autoapprove` int(1) NOT NULL,
  `androidkey` varchar(100) NOT NULL,
  `bannervideoStatus` int(1) NOT NULL,
  `bannervideo` varchar(150) DEFAULT NULL,
  `bannervideoposter` varchar(150) DEFAULT NULL,
  `bannerText` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `hts_sitesettings`
--

INSERT INTO `hts_sitesettings` (`id`, `smtpEmail`, `smtpPassword`, `smtpPort`, `smtpHost`, `smtpEnable`, `smtpSSL`, `signup_active`, `socialLoginDetails`, `logo`, `logoDarkVersion`, `sitename`, `metaData`, `default_userimage`, `favicon`, `currency_priority`, `category_priority`, `promotionCurrency`, `urgentPrice`, `searchDistance`, `searchType`, `searchList`, `sitepaymentmodes`, `commission_status`, `paypal_settings`, `braintree_settings`, `api_settings`, `footer_settings`, `tracking_code`, `googleapikey`, `account_sid`, `auth_token`, `sms_number`, `facebookshare`, `bannerstatus`, `promotionStatus`, `product_autoapprove`, `androidkey`, `bannervideoStatus`, `bannervideo`, `bannervideoposter`, `bannerText`) VALUES
(1, 'fantacyclonescript@gmail.com', 'Innovation@5', '465', 'smtp.gmail.com', 1, 1, 'no', '{"facebook":{"status":"enable","appid":"1610870739211757","secret":"7024047c939f427a90fe40df30370eb2"},"twitter":{"status":"enable","appid":"jfCprDsZbv3VLf64c6YAKd356","secret":"LSFB9CERuhZhlqygOTYGzJZBE5ZIZGY81fBV3LnFGqlKYGhKZ1"},"google":{"status":"enable","appid":"70014839817-lggmbi7n0edpoq3fv2pnjke6lkeh9cln.apps.googleusercontent.com","secret":"VBZHknP06AApSOuSbnfQJLi2"}}', '3583_9247_792_app_logo.png', '5919_joysale_logo_gray.png', 'Joysale', '{"metaTitle":"Online Classifieds Platform to Buy Sell Locally.","metaDescription":"Perfect online classifieds to find stuffs to buy and sell near your locations. Keep connected with you sellers and buyers with instant chat.","metaKeywords":"online classifieds"}', '2939_default_user_icon.png', 'favicon.png', '["27","30","empty","empty","empty"]', '["37","44","38","39","41","43","42","40","45","empty"]', ' EUR-€', 30, 0, 'miles', '100', '{"exchangePaymentMode":null,"buynowPaymentMode":"0","cancelEnableStatus":"processing","sellerClimbEnableDays":"0","scrowPaymentMode":"1","buynowPlugin":"0"}', 1, '{"paypalType":"2","paypalEmailId":"rajahussain@yahoo.com","paypalApiUserId":"rajahussain_api1.yahoo.com","paypalApiPassword":"1371709929","paypalApiSignature":"AFcWxV21C7fd0v3bYYYRCpSSRl31AxJ7Xo1rZl3nqHit7WDwT9xs1q6I","paypalAppId":"APP-80W284485P519543T","paypalCcStatus":null,"paypalCcClientId":null,"paypalCcSecret":null}', '{"brainTreeType":"2","brainTreeMerchantId":"8xzz2dztkdjpf79m","brainTreePublicKey":"9j7ss27dytftp5tx","brainTreePrivateKey":"6482a53faa340b06b5b47ab678b2f8f0"}', '{"apicredential":{"default":{"username":"joySale","password":"0RWK9XM8"},"current":{"username":"joySale","password":"0RWK9XM8"}}}', '{"footerDetails":{"facebooklink":"http:\\/\\/facebook.com\\/","googlelink":"http:\\/\\/google.com\\/","twitterlink":"http:\\/\\/twitter.com\\/","androidlink":"https:\\/\\/play.google.com\\/store\\/apps\\/details?id=com.hitasoft.app.joysale&hl=en","ioslink":"https:\\/\\/itunes.apple.com\\/in\\/app\\/joysale\\/id1078268802?mt=8","footerCopyRightsDetails":"\\u00a9 Copyright 2017 Hitasoft.com Limited. All rights reserved.","socialloginheading":"Stay Connect with Joysale","applinkheading":"Download Apps","generaltextguest":"Create your Joysale online classifieds account to chat around with the sellers and buyers near you.","generaltextuser":""}}', '', 'AIzaSyCaMl_KhootfGmXzHjXOaPhA7_Qou14_0s', '', '', '', 0, 1, 1, 1, 'AIzaSyAK--ZYqqD8OjueQb_YB98llQMFIGkCYyw', 0, '3145.mp4', NULL, 'Advanced Buy and Sell Script with Web and Apps');

-- --------------------------------------------------------

--
-- Table structure for table `hts_tempaddresses`
--

CREATE TABLE IF NOT EXISTS `hts_tempaddresses` (
  `shippingaddressId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `nickname` varchar(45) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `address1` varchar(60) DEFAULT NULL,
  `address2` varchar(60) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `state` varchar(40) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `countryCode` int(11) DEFAULT NULL,
  `slug` varchar(20) NOT NULL,
  PRIMARY KEY (`shippingaddressId`),
  KEY `userId` (`userId`),
  KEY `countryCode` (`countryCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_trackingdetails`
--

CREATE TABLE IF NOT EXISTS `hts_trackingdetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) NOT NULL,
  `status` varchar(150) NOT NULL,
  `merchantid` int(11) NOT NULL,
  `buyername` varchar(250) NOT NULL,
  `buyeraddress` tinytext NOT NULL,
  `shippingdate` int(11) NOT NULL,
  `couriername` varchar(250) NOT NULL,
  `courierservice` varchar(250) DEFAULT NULL,
  `trackingid` varchar(250) NOT NULL,
  `notes` tinytext,
  PRIMARY KEY (`id`),
  KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_userdevices`
--

CREATE TABLE IF NOT EXISTS `hts_userdevices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deviceToken` varchar(200) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `badge` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `mode` int(11) NOT NULL,
  `lang_type` varchar(50) NOT NULL,
  `cdate` int(11) DEFAULT NULL,
  `deviceId` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hts_users`
--

CREATE TABLE IF NOT EXISTS `hts_users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `phonevisible` int(1) NOT NULL,
  `country` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postalcode` varchar(10) DEFAULT NULL,
  `geolocationDetails` varchar(250) NOT NULL,
  `userImage` varchar(50) DEFAULT NULL,
  `userstatus` tinyint(4) DEFAULT NULL,
  `activationStatus` tinyint(4) NOT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `facebookId` bigint(20) DEFAULT NULL,
  `fbdetails` text,
  `facebook_session` text NOT NULL,
  `twitterId` bigint(20) DEFAULT NULL,
  `googleId` varchar(50) DEFAULT NULL,
  `notificationSettings` mediumtext,
  `defaultshipping` int(11) NOT NULL,
  `createdDate` int(11) NOT NULL,
  `lastLoginDate` int(11) NOT NULL,
  `averageRating` int(1) NOT NULL,
  `recently_view_product` text,
  `mobile_verificationcode` int(11) DEFAULT NULL,
  `mobile_status` int(11) NOT NULL DEFAULT '0',
  `unreadNotification` int(11) NOT NULL,
  `country_code` int(10) NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `hts_users`
--

INSERT INTO `hts_users` (`userId`, `username`, `name`, `password`, `email`, `phone`, `phonevisible`, `country`, `city`, `state`, `postalcode`, `geolocationDetails`, `userImage`, `userstatus`, `activationStatus`, `gender`, `facebookId`, `fbdetails`, `facebook_session`, `twitterId`, `googleId`, `notificationSettings`, `defaultshipping`, `createdDate`, `lastLoginDate`, `averageRating`, `recently_view_product`, `mobile_verificationcode`, `mobile_status`, `unreadNotification`, `country_code`) VALUES
(1, 'demo', 'Test Demo User', 'MTIzNDU2', 'demo@joysale.com', '9876543210', 1, 'India', 'Chennai', 'Tamil Nadu', '600001', '', '4838_logojpg', 1, 1, 'male', 10155306851311164, '{"email":"a.arunonline@gmail.com","firstName":"Arun","lastName":"Andiselvam","phone":"","profileURL":"https:\\/\\/www.facebook.com\\/app_scoped_user_id\\/10155306851311164\\/"}', 'a:2:{s:35:"hauth_session.facebook.is_logged_in";s:4:"i:1;";s:41:"hauth_session.facebook.token.access_token";s:223:"s:214:"EAAW5FBIwle0BANFcz6pZBWdHPMzu1OilOv5pdlVbpvb5UrmFZBTZC8LHZBTVmXUyzpYeAZAfCwVuP5vxSJZBbEymR71hZBGZCX6K2WZAnEvhgRhbeXvq9RisCGdrbb1elOfUoOmnldkZBiMfl6zZAB9ghNLlmrPB41khrDoRjp5j9ezyVCOySm44MgKgFVFIv8eunbUAY32W9NXtAZDZD";";}', 291047777, NULL, '{"live":null,"comment":null,"message":null,"offer":null,"updates":null}', 5, 0, 1512395646, 2, '["1","178","177","115","176"]', 498525, 1, 0, 33);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hts_carts`
--
ALTER TABLE `hts_carts`
  ADD CONSTRAINT `cart_product` FOREIGN KEY (`productId`) REFERENCES `hts_products` (`productId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_user` FOREIGN KEY (`userId`) REFERENCES `hts_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hts_comments`
--
ALTER TABLE `hts_comments`
  ADD CONSTRAINT `fk_comments_product1` FOREIGN KEY (`productId`) REFERENCES `hts_products` (`productId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_users1` FOREIGN KEY (`userId`) REFERENCES `hts_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hts_exchanges`
--
ALTER TABLE `hts_exchanges`
  ADD CONSTRAINT `hts_exchanges_ibfk_1` FOREIGN KEY (`mainProductId`) REFERENCES `hts_products` (`productId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hts_exchanges_ibfk_2` FOREIGN KEY (`exchangeProductId`) REFERENCES `hts_products` (`productId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hts_invoices`
--
ALTER TABLE `hts_invoices`
  ADD CONSTRAINT `fk_invoices_orders1` FOREIGN KEY (`orderId`) REFERENCES `hts_orders` (`orderId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hts_orderitems`
--
ALTER TABLE `hts_orderitems`
  ADD CONSTRAINT `fk_orderitems_orders1` FOREIGN KEY (`orderId`) REFERENCES `hts_orders` (`orderId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hts_orders`
--
ALTER TABLE `hts_orders`
  ADD CONSTRAINT `fk_orders_users1` FOREIGN KEY (`userId`) REFERENCES `hts_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_users2` FOREIGN KEY (`sellerId`) REFERENCES `hts_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hts_products`
--
ALTER TABLE `hts_products`
  ADD CONSTRAINT `fk_product_category1` FOREIGN KEY (`category`) REFERENCES `hts_categories` (`categoryId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_product_category2` FOREIGN KEY (`subCategory`) REFERENCES `hts_categories` (`categoryId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_product_users1` FOREIGN KEY (`userId`) REFERENCES `hts_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hts_shipping`
--
ALTER TABLE `hts_shipping`
  ADD CONSTRAINT `country_details` FOREIGN KEY (`countryId`) REFERENCES `hts_country` (`countryId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `product_shipping` FOREIGN KEY (`productId`) REFERENCES `hts_products` (`productId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hts_shippingaddresses`
--
ALTER TABLE `hts_shippingaddresses`
  ADD CONSTRAINT `countryCode` FOREIGN KEY (`countryCode`) REFERENCES `hts_country` (`countryId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `userId` FOREIGN KEY (`userId`) REFERENCES `hts_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hts_tempaddresses`
--
ALTER TABLE `hts_tempaddresses`
  ADD CONSTRAINT `countryCode0` FOREIGN KEY (`countryCode`) REFERENCES `hts_country` (`countryId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `userId0` FOREIGN KEY (`userId`) REFERENCES `hts_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
