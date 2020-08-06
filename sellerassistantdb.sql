-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 06, 2020 at 01:01 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sellerassistant`
--

-- --------------------------------------------------------

--
-- Table structure for table `amz_accounts`
--

CREATE TABLE `amz_accounts` (
  `amz_acct_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amz_acct_name` varchar(50) NOT NULL,
  `marketplace_id` varchar(15) NOT NULL,
  `seller_id` varchar(255) NOT NULL,
  `mws_auth_token` varchar(255) NOT NULL,
  `aws_access_key_id` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `amz_accounts`
--

INSERT INTO `amz_accounts` (`amz_acct_id`, `user_id`, `amz_acct_name`, `marketplace_id`, `seller_id`, `mws_auth_token`, `aws_access_key_id`, `secret_key`) VALUES
(1, 1, 'Alpha Living US', 'ATVPDKIKX0DER', 'f0da11e526fcde7b4bae3e24a23a2b3552c87b8343bacd29ab1f54e5b3a756a10ee5f04070f4972ad555f7c580208156174b8929131b0c55709f5f23912c406bJMajd0apTaRhOBiyZXmn8aHkf0iYJXBHP2ZXgvHKQUY=', '0b16c9a0b064a00136200199385f626c6c29fb11d830b7d35c1efff625ec4ce9a9061411c0ffc6afe7ec5d3fc391611fd12119d21394f5e5257a888392a8dd46qRlusz4kQwh/1hh1qgUugBuTJ1+vZ3wA4m0RLU+i7bEyfyjlZO/YvTlGA3vbQev7y8Z+APB3gAKnxyJ9c6BZDA==', 'f6cf2c174eb99565ddadfa06f1bf0901cb722343fb9e455b3c31ad8d66f03c7ea07ecba0c78919856c4a9b56b7ef5305658de6e11f220d72f982e642166f07c76zb2E0on8Zk74Q059CmDwr1XL/ODZ0rd+FQB0u0Fc78XvLTpKJFcSJfMQu78list', 'f88aa199e1dae491bcf821db1e7493e1221d5cea820304f477bfde8f775a611e3afa107d44dfff3adb86158942c67d4f179ddb49e58eb0a2e7b56b7e7a791ce8CvTuE0xMH3wAvw4zlBRe+VdEovkFoUIm0rrZFMB6BUnx3fmySrxpnbVR5B0jX7uh/qjIwt27tD+4xQT7KyrCqQ=='),
(4, 1, 'Orient Originals US', 'ATVPDKIKX0DER', 'eaef8386b68c41cdafac935441a8f5ee8256fa9851cbd020f6ac400776a1cf4286e987bf2a859269c74bb8c3f786cbf293702100dcabfccec544deba18274198QIMmk3sVe75GfPi4apvOS5lFPcebrjwbN01d4EX0ccI=', 'b8d2cbf3f74e8c53d7e89597ecb95caf95ddd8743c4b616f4ae685503eb306cecab3bf1fb49a4f50be4da12c820fd3902d53a2320e1ef82345e602558b04f75bGGXQlc1ifONKn8DUEFVEuh399S+fHJsW6nzy1+VdnC75al6NNTNcGtyETsUL44GyYbjJx6+9POdvt+XZw+r5iA==', '70e2061661fde5eb6a9cd03d5ff44efd4c026135bf73ff251e1d791f0fdf5ff61faac1bd9ce7edb46961528768fd151b51a73ef4316c826c5d3343c771511522NWCi3ulw+D0XKDhOvwipKNWCpWDNU9rqeBa/kJsukUeXxBHHepSVgxBBRnD+kFRB', 'a29e4c7388351d053f6b6957eca171e76c91ad22c96a4bd08bd90bd15e609668824665689d28fbc86e08f9f3dda9ede3e5162de1b8378fb36761f75428f1083eZ64ZHg11IlBG79Ldmy6v+PFjyxSSmyKzjBxt01j9ve+84xng6CmLNXUD0dnrnpNOt+iPvNXDxcvnXCeZ3YdWfA==');

-- --------------------------------------------------------

--
-- Table structure for table `amz_marketplaces`
--

CREATE TABLE `amz_marketplaces` (
  `marketplace_id` varchar(15) NOT NULL,
  `marketplace_name` varchar(15) NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `marketplace_region` varchar(15) NOT NULL,
  `sales_channel` varchar(255) NOT NULL,
  `currency` varchar(35) NOT NULL,
  `curr_code` varchar(3) NOT NULL,
  `endpoint` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Amazon marketplaces';

--
-- Dumping data for table `amz_marketplaces`
--

INSERT INTO `amz_marketplaces` (`marketplace_id`, `marketplace_name`, `country_code`, `marketplace_region`, `sales_channel`, `currency`, `curr_code`, `endpoint`) VALUES
('A13V1IB3VIYZZH', 'France', 'FR', 'Europe', 'Amazon.fr', 'European Euro', 'EUR', 'https://mws-eu.amazonservices.com'),
('A1AM78C64UM0Y8', 'Mexico', 'MX', 'North America', 'Amazon.com.mx', 'Mexican Peso', 'MXN', 'https://mws.amazonservices.com'),
('A1F83G8C2ARO7P', 'United Kingdom', 'UK', 'Europe', 'Amazon.co.uk', 'Pound Sterling', 'GBP', 'https://mws-eu.amazonservices.com'),
('A1PA6795UKMFR9', 'Germany', 'DE', 'Europe', 'Amazon.de', 'European Euro', 'EUR', 'https://mws-eu.amazonservices.com'),
('A1RKKUPIHCS9HS', 'Spain', 'ES', 'Europe', 'Amazon.es', 'European Euro', 'EUR', 'https://mws-eu.amazonservices.com'),
('A1VC38T7YXB528', 'Japan', 'JP', 'Others', 'Amazon.co.jp', 'Japanese Yen', 'JPY', 'https://mws.amazonservices.jp'),
('A21TJRUUN4KGV', 'India', 'IN', 'Others', 'Amazon.in', 'Indian Ruppes', 'INR', 'https://mws.amazonservices.in'),
('A2EUQ1WTGCTBG2', 'Canada', 'CA', 'North America', 'Amazon.ca', 'Canadian Dollars', 'CAD', 'https://mws.amazonservices.com'),
('A2Q3Y263D00KWC', 'Brazil', 'BR', 'Others', 'Amazon.com.br', 'Brazilian Real', 'BRL', 'https://mws.amazonservices.com'),
('A39IBJ37TRP1C6', 'Australia', 'AU', 'Others', 'Amazon.com.au', 'Australian Dollars', 'AUD', 'https://mws.amazonservices.com.au'),
('AAHKV2X7AFYLW', 'China', 'CN', 'Others', 'Amazon.cn', 'Chinese Yuan Renminbi', 'CNY', 'https://mws.amazonservices.com.cn'),
('APJ6JRA9NG5V4', 'Italy', 'IT', 'Europe', 'Amazon.it', 'European Euro', 'EUR', 'https://mws-eu.amazonservices.com'),
('ATVPDKIKX0DER', 'United States', 'US', 'North America', 'Amazon.com', 'US Dollars', 'USD', 'https://mws.amazonservices.com');

-- --------------------------------------------------------

--
-- Table structure for table `fba_fees_comp_details`
--

CREATE TABLE `fba_fees_comp_details` (
  `fin_event_grp_id` varchar(255) NOT NULL,
  `amz_ord_id` varchar(19) NOT NULL,
  `posted_date` datetime NOT NULL,
  `mp_name` varchar(15) NOT NULL,
  `ord_item_id` varchar(15) NOT NULL,
  `seller_sku` varchar(10) NOT NULL,
  `qty_shp` int(11) NOT NULL,
  `fee_type` varchar(35) NOT NULL,
  `fee_curr` varchar(3) NOT NULL,
  `fee_amt` double NOT NULL,
  `calc_fee_amt` double NOT NULL,
  `calc_remarks` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fba_fees_comp_header`
--

CREATE TABLE `fba_fees_comp_header` (
  `fin_event_grp_id` varchar(255) NOT NULL,
  `fin_event_grp_start` date NOT NULL,
  `fin_event_grp_end` date NOT NULL,
  `amz_acct_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fba_ful_fees_usa`
--

CREATE TABLE `fba_ful_fees_usa` (
  `fba_fees_id` int(11) NOT NULL,
  `prod_size_code` varchar(5) NOT NULL,
  `first_outshp_wt` double NOT NULL COMMENT 'First outbound shipping weight.',
  `addl_outshp_wt` double NOT NULL COMMENT 'Additional outbound shipping weight.',
  `weight_unit` varchar(10) NOT NULL COMMENT 'Outbound shipping ',
  `fba_fees_first_wt` double NOT NULL COMMENT 'Fulfillment Fees on first outbound shipping weight.',
  `fba_fees_addl_wt` double NOT NULL COMMENT 'Fulfillment fees on additional outbound shipping weight.',
  `currency_code` varchar(6) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_upto` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fba_ful_fees_usa`
--

INSERT INTO `fba_ful_fees_usa` (`fba_fees_id`, `prod_size_code`, `first_outshp_wt`, `addl_outshp_wt`, `weight_unit`, `fba_fees_first_wt`, `fba_fees_addl_wt`, `currency_code`, `valid_from`, `valid_upto`) VALUES
(1, 'SS', 0, 1, 'pound', 2.41, 0, 'USD', '2017-02-22', '2017-09-30'),
(2, 'SS', 0, 1, 'pound', 2.39, 0, 'USD', '2017-10-01', '2017-12-31'),
(3, 'SS', 0, 1, 'pound', 2.41, 0, 'USD', '2018-01-01', '2018-02-21'),
(4, 'LS1', 0, 1, 'pound', 2.99, 0, 'USD', '2017-02-22', '2017-09-30'),
(5, 'LS1', 0, 1, 'pound', 2.88, 0, 'USD', '2017-10-01', '2017-12-31'),
(6, 'LS1', 0, 1, 'pound', 2.99, 0, 'USD', '2018-01-01', '2018-02-21'),
(7, 'LS2', 0, 1, 'pound', 4.18, 0, 'USD', '2017-02-22', '2017-09-30'),
(8, 'LS2', 0, 1, 'pound', 3.96, 0, 'USD', '2017-10-01', '2017-12-31'),
(9, 'LS2', 0, 1, 'pound', 4.18, 0, 'USD', '2018-01-01', '2018-02-21'),
(10, 'LS3', 2, 1, 'pound', 4.18, 0.39, 'USD', '2017-02-22', '2017-09-30'),
(11, 'LS3', 2, 1, 'pound', 3.96, 0.35, 'USD', '2017-10-01', '2017-12-31'),
(12, 'LS3', 2, 1, 'pound', 4.18, 0.39, 'USD', '2018-01-01', '2018-02-21'),
(13, 'SO', 2, 1, 'pound', 6.85, 0.39, 'USD', '2017-02-22', '2017-09-30'),
(14, 'SO', 2, 1, 'pound', 6.69, 0.35, 'USD', '2017-10-01', '2017-12-31'),
(15, 'SO', 2, 1, 'pound', 6.85, 0.39, 'USD', '2018-01-01', '2018-02-21'),
(16, 'MO', 2, 1, 'pound', 9.2, 0.39, 'USD', '2017-02-22', '2017-09-30'),
(17, 'MO', 2, 1, 'pound', 8.73, 0.35, 'USD', '2017-10-01', '2017-12-31'),
(18, 'MO', 2, 1, 'pound', 9.2, 0.39, 'USD', '2018-01-01', '2018-02-21'),
(19, 'LO', 90, 1, 'pound', 75.06, 0.8, 'USD', '2017-02-22', '2017-09-30'),
(20, 'LO', 90, 1, 'pound', 69.5, 0.76, 'USD', '2017-10-01', '2017-12-31'),
(21, 'LO', 90, 1, 'pound', 75.06, 0.8, 'USD', '2018-01-01', '2018-02-21'),
(22, 'SPO', 90, 1, 'pound', 138.08, 0.92, 'USD', '2017-02-22', '2017-09-30'),
(23, 'SPO', 90, 1, 'pound', 131.44, 0.88, 'USD', '2017-10-01', '2017-12-31'),
(24, 'SPO', 90, 1, 'pound', 138.08, 0.92, 'USD', '2018-01-01', '2018-02-21'),
(25, 'SS', 0, 1, 'pound', 2.41, 0, 'USD', '2018-02-22', '2019-02-18'),
(26, 'LS1', 0, 1, 'pound', 3.19, 0, 'USD', '2018-02-22', '2019-02-18'),
(27, 'LS2', 0, 1, 'pound', 4.71, 0, 'USD', '2018-02-22', '2019-02-18'),
(28, 'LS3', 2, 1, 'pound', 4.71, 0.38, 'USD', '2018-02-22', '2019-02-18'),
(29, 'SO', 2, 1, 'pound', 8.13, 0.38, 'USD', '2018-02-22', '2019-02-18'),
(30, 'MO', 2, 1, 'pound', 9.44, 0.38, 'USD', '2018-02-22', '2019-02-18'),
(31, 'LO', 90, 1, 'pound', 73.18, 0.79, 'USD', '2018-02-22', '2019-02-18'),
(32, 'SPO', 90, 1, 'pound', 137.32, 0.91, 'USD', '2018-02-22', '2019-02-18'),
(33, 'SS1', 0, 1, 'pound', 2.41, 0, 'USD', '2019-02-19', '2020-02-17'),
(34, 'SS2', 0, 1, 'pound', 2.48, 0, 'USD', '2019-02-19', '2020-02-17'),
(35, 'LS1', 0, 1, 'pound', 3.19, 0, 'USD', '2019-02-19', '2020-02-17'),
(36, 'LS2', 0, 1, 'pound', 3.28, 0, 'USD', '2019-02-19', '2020-02-17'),
(37, 'LS3', 0, 1, 'pound', 4.76, 0, 'USD', '2019-02-19', '2020-02-17'),
(38, 'LS4', 0, 1, 'pound', 5.26, 0, 'USD', '2019-02-19', '2020-02-17'),
(39, 'LS5', 3, 1, 'pound', 5.26, 0.38, 'USD', '2019-02-19', '2020-02-17'),
(40, 'SO', 2, 1, 'pound', 8.26, 0.38, 'USD', '2019-02-19', '2020-02-17'),
(41, 'MO', 2, 1, 'pound', 9.79, 0.39, 'USD', '2019-02-19', '2020-02-17'),
(42, 'LO', 90, 1, 'pound', 75.78, 0.79, 'USD', '2019-02-19', '2020-02-17'),
(43, 'SPO', 90, 1, 'pound', 137.32, 0.91, 'USD', '2019-02-19', '2020-02-17'),
(44, 'SS1', 0, 1, 'pound', 2.5, 0, 'USD', '2020-02-18', '2021-02-17'),
(45, 'SS2', 0, 1, 'pound', 2.63, 0, 'USD', '2020-02-18', '2021-02-17'),
(46, 'LS1', 0, 1, 'pound', 3.31, 0, 'USD', '2020-02-18', '2021-02-17'),
(47, 'LS2', 0, 1, 'pound', 3.48, 0, 'USD', '2020-02-18', '2021-02-17'),
(48, 'LS3', 0, 1, 'pound', 4.9, 0, 'USD', '2020-02-18', '2021-02-17'),
(49, 'LS4', 0, 1, 'pound', 5.42, 0, 'USD', '2020-02-18', '2021-02-17'),
(50, 'LS5', 3, 1, 'pound', 5.42, 0.38, 'USD', '2020-02-18', '2021-02-17'),
(51, 'SO', 2, 1, 'pound', 8.26, 0.38, 'USD', '2020-02-18', '2021-02-17'),
(52, 'MO', 2, 1, 'pound', 11.37, 0.39, 'USD', '2020-02-18', '2021-02-17'),
(53, 'LO', 90, 1, 'pound', 75.78, 0.79, 'USD', '2020-02-18', '2021-02-17'),
(54, 'SPO', 90, 1, 'pound', 137.32, 0.91, 'USD', '2020-02-18', '2021-02-17');

-- --------------------------------------------------------

--
-- Table structure for table `fba_products`
--

CREATE TABLE `fba_products` (
  `amz_acct_id` int(11) NOT NULL,
  `seller_sku` varchar(20) NOT NULL,
  `fnsku` varchar(20) NOT NULL,
  `asin` varchar(20) NOT NULL,
  `pkgd_prod_wt` double NOT NULL,
  `pkgd_prod_wt_uom` varchar(6) NOT NULL,
  `pkgd_prod_ls` double NOT NULL,
  `pkgd_prod_ms` double NOT NULL,
  `pkgd_prod_ss` double NOT NULL,
  `pkgd_prod_dim_uom` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fba_products`
--

INSERT INTO `fba_products` (`amz_acct_id`, `seller_sku`, `fnsku`, `asin`, `pkgd_prod_wt`, `pkgd_prod_wt_uom`, `pkgd_prod_ls`, `pkgd_prod_ms`, `pkgd_prod_ss`, `pkgd_prod_dim_uom`) VALUES
(1, '77504', '', '', 240, 'g', 5.2, 4, 2.2, 'in'),
(1, '77505', '', '', 480, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77507', '', '', 240, 'g', 5.2, 4, 2.2, 'in'),
(1, '77508', '', '', 480, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77510', '', '', 240, 'g', 5.2, 4, 2.2, 'in'),
(1, '77511', '', '', 480, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77514', '', '', 250, 'g', 6.3, 4.3, 4, 'in'),
(1, '77520', '', '', 260, 'g', 5.3, 5.2, 4.6, 'in'),
(1, '77530', '', '', 340, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77537', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77540', '', '', 320, 'g', 5.4, 4.3, 4, 'in'),
(1, '77544', '', '', 340, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77545', '', '', 230, 'g', 5.2, 4, 4, 'in'),
(1, '77546', '', '', 340, 'g', 5.2, 4, 4, 'in'),
(1, '77549', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77551', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77552', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77560', '', '', 320, 'g', 6.1, 4.2, 2.3, 'in'),
(1, '77561', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77564', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77568', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77570', '', '', 250, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77573', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77575', '', '', 260, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77579', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77582', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77591', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77593', '', '', 320, 'g', 6.1, 4.2, 2.3, 'in'),
(1, '77597', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77599', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77601', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77603', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77607', '', '', 430, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77611', '', '', 360, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77613', '', '', 180, 'g', 5.2, 4, 2.2, 'in'),
(1, '77615', '', '', 140, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77622', '', '', 450, 'g', 6.3, 4.3, 4.1, 'in'),
(1, '77627', '', '', 230, 'g', 6.3, 4.3, 2.1, 'in'),
(1, '77638', '', '', 360, 'g', 6.1, 4.2, 2.3, 'in'),
(1, '77644', '', '', 220, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77647', '', '', 640, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77650', '', '', 450, 'g', 6.3, 4.3, 4.1, 'in'),
(1, '77653', '', '', 450, 'g', 6.3, 4.3, 4.1, 'in'),
(1, '77656', '', '', 540, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77680A', '', '', 320, 'g', 6.1, 4.2, 2.3, 'in'),
(1, '77681', '', '', 540, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77685', '', '', 1220, 'g', 16.2, 12.2, 2.2, 'in'),
(1, '77686', '', '', 1220, 'g', 16.2, 12.2, 2.2, 'in'),
(1, '77687', '', '', 1220, 'g', 16.2, 12.2, 2.2, 'in'),
(1, '77688', '', '', 1220, 'g', 13.2, 12.2, 2.2, 'in'),
(1, '77689', '', '', 1220, 'g', 16.2, 12.2, 2.2, 'in'),
(1, '77690', '', '', 1220, 'g', 13.2, 12.2, 2.2, 'in'),
(1, '77691', '', '', 1220, 'g', 13.2, 12.2, 2.2, 'in'),
(1, '77693', '', '', 1220, 'g', 16.2, 12.2, 2.2, 'in'),
(1, '77694', '', '', 1220, 'g', 13.2, 12.2, 2.2, 'in'),
(1, '77695', '', '', 1220, 'g', 13.2, 12.2, 2.2, 'in'),
(1, '77700', '', '', 430, 'g', 13.3, 13.3, 1.5, 'in'),
(1, '77702', '', '', 430, 'g', 13.3, 13.3, 1.5, 'in'),
(1, '77704', '', '', 430, 'g', 13.3, 13.3, 1.5, 'in'),
(1, '77706', '', '', 430, 'g', 13.3, 13.3, 1.5, 'in'),
(1, '77707', '', '', 430, 'g', 13.3, 13.3, 1.5, 'in'),
(1, '77709', '', '', 430, 'g', 13.3, 13.3, 1.5, 'in'),
(1, '77712', '', '', 2300, 'g', 11.8, 7.8, 7.8, 'in'),
(1, '77718', '', '', 180, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77726', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77728', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77729', '', '', 1500, 'g', 13.9, 13.9, 2.4, 'in'),
(1, '77730', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77732', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77736', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77737', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77738', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77739', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77740', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77743', '', '', 480, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77745', '', '', 1220, 'g', 16.2, 12.2, 2.2, 'in'),
(1, '77746', '', '', 1000, 'g', 13.1, 11.7, 7.2, 'in'),
(1, '77747', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77748', '', '', 380, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77749', '', '', 2480, 'g', 12.7, 12.7, 6.3, 'in'),
(1, '77750', '', '', 2480, 'g', 12.7, 12.7, 6.3, 'in'),
(1, '77751', '', '', 1630, 'g', 11.8, 7.8, 7.8, 'in'),
(1, '77757', '', '', 3800, 'g', 15.9, 15.9, 12, 'in'),
(1, '77761', '', '', 280, 'g', 6.5, 6.2, 2.5, 'in'),
(1, '77765', '', '', 340, 'g', 5, 4.5, 4.2, 'in'),
(1, '77766', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77768', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77769', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77770', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77771', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77772', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77776', '', '', 540, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77778', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77779', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77786', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77787', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77788', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77789', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77792', '', '', 1650, 'g', 11.4, 11.4, 2.8, 'in'),
(1, '77794', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77795', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77796', '', '', 1270, 'g', 18.2, 15, 4.4, 'in'),
(1, '77798', '', '', 1800, 'g', 11.8, 7.8, 7.8, 'in'),
(1, '77799', '', '', 2230, 'g', 11.8, 7.8, 7.8, 'in'),
(1, '77800', '', '', 1630, 'g', 11.8, 7.8, 7.8, 'in'),
(1, '77801', '', '', 1630, 'g', 11.8, 7.8, 7.8, 'in'),
(1, '77802', '', '', 900, 'g', 17.2, 4.6, 4.6, 'in'),
(1, '77806', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77807', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77811', '', '', 340, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77812', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77813', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77814', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77815', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77817', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77818', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77819', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77820', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77824', '', '', 1100, 'g', 13.1, 7.7, 4.8, 'in'),
(1, '77825', '', '', 1650, 'g', 20.6, 8.6, 4.4, 'in'),
(1, '77826', '', '', 2220, 'g', 13.8, 11.9, 7.2, 'in'),
(1, '77827', '', '', 970, 'g', 12, 9.1, 5.6, 'in'),
(1, '77828', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77829', '', '', 1050, 'g', 14.1, 5.3, 5.3, 'in'),
(1, '77831', '', '', 2250, 'g', 12.4, 10.9, 7.8, 'in'),
(1, '77832', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77833', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77835', '', '', 1220, 'g', 16.2, 12.2, 2.2, 'in'),
(1, '77836', '', '', 1220, 'g', 13.2, 12.2, 2.2, 'in'),
(1, '77837', '', '', 1850, 'g', 13.3, 9.9, 5.1, 'in'),
(1, '77840', '', '', 230, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77841', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77842', '', '', 180, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77843', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77845', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77846', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77847', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77848', '', '', 600, 'g', 9.2, 7.6, 6.4, 'in'),
(1, '77850', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77851', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77852', '', '', 340, 'g', 5, 4.5, 4.2, 'in'),
(1, '77853', '', '', 1100, 'g', 9.2, 7.8, 4.5, 'in'),
(1, '77854', '', '', 1950, 'g', 14.5, 14.5, 5.2, 'in'),
(1, '77855', '', '', 540, 'g', 6.3, 6.3, 7.1, 'in'),
(1, '77856', '', '', 4260, 'g', 17.2, 14.1, 12.9, 'in'),
(1, '77857', '', '', 140, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77858', '', '', 140, 'g', 5.2, 3.8, 2.3, 'in'),
(1, '77859', '', '', 430, 'g', 5.2, 4.9, 4.9, 'in'),
(1, '77860', '', '', 340, 'g', 5, 4.5, 4.2, 'in'),
(1, '77861', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77862', '', '', 1000, 'g', 8.3, 6.4, 4.5, 'in'),
(1, '77863', '', '', 1000, 'g', 8.3, 6.4, 4.5, 'in'),
(1, '77864', '', '', 330, 'g', 6.2, 5.4, 2.5, 'in'),
(1, '77865', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77866', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77867', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77868', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77869', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77870', '', '', 340, 'g', 6.6, 4.9, 4.2, 'in'),
(1, '77871', '', '', 640, 'g', 6.6, 4.9, 4.2, 'in'),
(1, '77872', '', '', 550, 'g', 4.7, 4.6, 3.1, 'in'),
(1, '77873', '', '', 1060, 'g', 6.4, 5.3, 4.6, 'in'),
(1, '77874', '', '', 1080, 'g', 6.7, 4.6, 4.6, 'in'),
(1, '77875', '', '', 1190, 'g', 12.7, 9.2, 5.9, 'in'),
(1, '77876', '', '', 1000, 'g', 10.5, 10.1, 5.3, 'in'),
(1, '77877', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77878', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77879', '', '', 1270, 'g', 14.1, 10.4, 6.2, 'in'),
(1, '77880', '', '', 1220, 'g', 16.2, 12.2, 2.2, 'in'),
(1, '77881', '', '', 1220, 'g', 16.2, 12.2, 2.2, 'in'),
(1, '77882', '', '', 680, 'g', 7, 7, 1.5, 'in'),
(1, '77883', '', '', 680, 'g', 6, 6, 1.5, 'in'),
(1, '77884', '', '', 2250, 'g', 12.4, 10.9, 7.8, 'in'),
(1, '77885', '', '', 650, 'g', 15.3, 3.8, 4.6, 'in'),
(1, '77886', '', '', 950, 'g', 14.9, 7.9, 5.2, 'in'),
(1, '77887', '', '', 470, 'g', 12.3, 4.5, 3.9, 'in'),
(1, '77888', '', '', 3240, 'g', 11.3, 9.6, 5, 'in'),
(1, '77889', '', '', 830, 'g', 9.7, 4.2, 2.2, 'in'),
(1, '77890', '', '', 1630, 'g', 11.8, 7.8, 7.8, 'in'),
(1, '77891', '', '', 1630, 'g', 11.8, 7.8, 7.8, 'in'),
(1, '77892', '', '', 1630, 'g', 11.8, 7.8, 7.8, 'in'),
(1, '77893', '', '', 1630, 'g', 11.8, 7.8, 7.8, 'in'),
(1, '77894', '', '', 680, 'g', 8.8, 8.8, 2.1, 'in'),
(1, '77895', '', '', 680, 'g', 8.8, 8.8, 2, 'in'),
(1, '77896', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77897', '', '', 860, 'g', 13.3, 13.3, 2, 'in'),
(1, '77898', '', '', 300, 'g', 7.9, 4, 3, 'in'),
(1, '77899', '', '', 1700, 'g', 12, 6.3, 6.3, 'in'),
(1, '77900', '', '', 650, 'g', 5.3, 5.3, 6, 'in'),
(1, '77901', '', '', 1220, 'g', 13.2, 12.2, 2.2, 'in'),
(1, '77902', '', '', 1220, 'g', 13.2, 12.2, 2.2, 'in'),
(1, '77903', '', '', 1220, 'g', 16.2, 12.2, 2.2, 'in'),
(1, '77904', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77905', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77906', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77907', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77908', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77909', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77910', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77911', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77912', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77913', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77914', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77915', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77916', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77917', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77918', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77919', '', '', 0, 'g', 0, 0, 0, 'in'),
(1, '77920', '', '', 250, 'g', 6.3, 4.3, 4, 'in'),
(1, '77921', '', '', 540, 'g', 6.1, 4.2, 4.1, 'in'),
(1, '77922', '', '', 540, 'g', 6.1, 4.2, 4.1, 'in');

-- --------------------------------------------------------

--
-- Table structure for table `fba_prod_size_usa`
--

CREATE TABLE `fba_prod_size_usa` (
  `prod_size_id` int(11) NOT NULL,
  `prod_size_code` varchar(5) NOT NULL,
  `prod_size_tier` varchar(35) NOT NULL,
  `prod_size_type` varchar(35) NOT NULL,
  `longest_side` double NOT NULL,
  `median_side` double NOT NULL,
  `shortest_side` double NOT NULL,
  `length_and_girth` double NOT NULL,
  `dim_uom` varchar(10) NOT NULL,
  `packaging_wt` double NOT NULL COMMENT 'Amazon packaging weight.',
  `min_prod_wt` double NOT NULL,
  `max_prod_wt` double NOT NULL,
  `wt_uom` varchar(10) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_upto` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fba_prod_size_usa`
--

INSERT INTO `fba_prod_size_usa` (`prod_size_id`, `prod_size_code`, `prod_size_tier`, `prod_size_type`, `longest_side`, `median_side`, `shortest_side`, `length_and_girth`, `dim_uom`, `packaging_wt`, `min_prod_wt`, `max_prod_wt`, `wt_uom`, `valid_from`, `valid_upto`) VALUES
(1, 'SS', 'Small Standard Size', 'Standard-Size', 15, 12, 0.75, 0, 'inches', 0.25, 0, 0.75, 'pound', '2017-02-22', '2019-02-18'),
(2, 'LS1', 'Large Standard Size (0 to 1 lb)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 0, 0.75, 'pound', '2017-02-22', '2019-02-18'),
(3, 'LS2', 'Large Standard Size (1 to 2 lb)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 0.75, 1.75, 'pound', '2017-02-22', '2019-02-18'),
(4, 'LS3', 'Large Standard Size (2 to 20 lb)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 1.75, 19.75, 'pound', '2017-02-22', '2019-02-18'),
(5, 'SO', 'Small Oversize', 'Oversize', 60, 30, 0, 130, 'inches', 1, 0, 70, 'pound', '2017-02-22', '2019-02-18'),
(6, 'MO', 'Medium Oversize', 'Oversize', 108, 0, 0, 130, 'inches', 1, 0, 149, 'pound', '2017-02-22', '2019-02-18'),
(7, 'LO', 'Large Oversize', 'Oversize', 108, 0, 0, 165, 'inches', 1, 0, 149, 'pound', '2017-02-22', '2019-02-18'),
(8, 'SPO', 'Special Oversize', 'Oversize', 999999999, 0, 0, 999999999, 'inches', 1, 0, 999999999, 'pound', '2017-02-22', '2019-02-18'),
(9, 'SS1', 'Small Standard Size (10 oz or less)', 'Standard-Size', 15, 12, 0.75, 0, 'inches', 0.25, 0, 0.375, 'pound', '2019-02-19', '2020-02-17'),
(10, 'SS2', 'Small Standard Size (10 to 16 oz)', 'Standard-Size', 15, 12, 0.75, 0, 'inches', 0.25, 0.375, 0.75, 'pound', '2019-02-19', '2020-02-17'),
(11, 'LS1', 'Large Standard Size (10 oz or less)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 0, 0.375, 'pound', '2019-02-19', '2020-02-17'),
(12, 'LS2', 'Large Standard Size (10 to 16 oz)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 0.375, 0.75, 'pound', '2019-02-19', '2020-02-17'),
(13, 'LS3', 'Large Standard Size (1 to 2 lb)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 0.75, 1.75, 'pound', '2019-02-19', '2020-02-17'),
(14, 'LS4', 'Large Standard Size (2 to 3 lb)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 1.75, 2.75, 'pound', '2019-02-19', '2020-02-17'),
(15, 'LS5', 'Large Standard Size (3 to 20 lb)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 2.75, 19.75, 'pound', '2019-02-19', '2020-02-17'),
(16, 'SO', 'Small Oversize', 'Oversize', 60, 30, 0, 130, 'inches', 1, 0, 69, 'pound', '2019-02-19', '2020-02-17'),
(17, 'MO', 'Medium Oversize', 'Oversize', 108, 0, 0, 130, 'inches', 1, 0, 149, 'pound', '2019-02-19', '2020-02-17'),
(18, 'LO', 'Large Oversize', 'Oversize', 108, 0, 0, 165, 'inches', 1, 0, 149, 'pound', '2019-02-19', '2020-02-17'),
(19, 'SPO', 'Special Oversize', 'Oversize', 999999999, 0, 0, 999999999, 'inches', 1, 0, 999999999, 'pound', '2019-02-19', '2020-02-17'),
(20, 'SS1', 'Small Standard Size (10 oz or less)', 'Standard-Size', 15, 12, 0.75, 0, 'inches', 0.25, 0, 0.375, 'pound', '2020-02-18', '2021-02-18'),
(21, 'SS2', 'Small Standard Size (10 to 16 oz)', 'Standard-Size', 15, 12, 0.75, 0, 'inches', 0.25, 0.375, 0.75, 'pound', '2020-02-18', '2021-02-18'),
(22, 'LS1', 'Large Standard Size (10 oz or less)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 0, 0.375, 'pound', '2020-02-18', '2021-02-18'),
(23, 'LS2', 'Large Standard Size (10 to 16 oz)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 0.375, 0.75, 'pound', '2020-02-18', '2021-02-18'),
(24, 'LS3', 'Large Standard Size (1 to 2 lb)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 0.75, 1.75, 'pound', '2020-02-18', '2021-02-18'),
(25, 'LS4', 'Large Standard Size (2 to 3 lb)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 1.75, 2.75, 'pound', '2020-02-18', '2021-02-18'),
(26, 'LS5', 'Large Standard Size (3 to 20 lb)', 'Standard-Size', 18, 14, 8, 0, 'inches', 0.25, 2.75, 20.75, 'pound', '2020-02-18', '2021-02-18'),
(27, 'SO', 'Small Oversize', 'Oversize', 60, 30, 0, 130, 'inches', 1, 0, 70, 'pound', '2020-02-18', '2021-02-18'),
(28, 'MO', 'Medium Oversize', 'Oversize', 108, 0, 0, 130, 'inches', 1, 0, 150, 'pound', '2020-02-18', '2021-02-18'),
(29, 'LO', 'Large Oversize', 'Oversize', 108, 0, 0, 165, 'inches', 1, 0, 150, 'pound', '2020-02-18', '2021-02-18'),
(30, 'SPO', 'Special Oversize', 'Oversize', 999999999, 0, 0, 999999999, 'inches', 1, 0, 999999999, 'pound', '2020-02-18', '2021-02-18');

-- --------------------------------------------------------

--
-- Table structure for table `fba_stg_fees_usa`
--

CREATE TABLE `fba_stg_fees_usa` (
  `storage_fees_id` int(11) NOT NULL,
  `prod_size_type` varchar(35) NOT NULL,
  `storage_fees` double NOT NULL,
  `curr_code` varchar(3) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_upto` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fba_stg_fees_usa`
--

INSERT INTO `fba_stg_fees_usa` (`storage_fees_id`, `prod_size_type`, `storage_fees`, `curr_code`, `valid_from`, `valid_upto`) VALUES
(1, 'Standard-Size', 0.64, 'USD', '2017-02-22', '2017-09-30'),
(2, 'Oversize', 0.43, 'USD', '2017-02-22', '2017-09-30'),
(3, 'Standard-Size', 2.35, 'USD', '2017-10-01', '2017-12-31'),
(4, 'Oversize', 1.15, 'USD', '2017-10-01', '2017-12-31'),
(5, 'Standard-Size', 0.64, 'USD', '2018-01-01', '2018-03-31'),
(6, 'Oversize', 0.43, 'USD', '2018-01-01', '2018-03-31'),
(7, 'Standard-Size', 0.69, 'USD', '2018-04-01', '2018-09-30'),
(8, 'Oversize', 0.48, 'USD', '2018-04-01', '2018-09-30'),
(9, 'Standard-Size', 2.4, 'USD', '2018-10-01', '2018-12-31'),
(10, 'Oversize', 1.2, 'USD', '2018-10-01', '2018-12-31'),
(11, 'Standard-Size', 0.69, 'USD', '2019-01-01', '2019-09-30'),
(12, 'Oversize', 0.48, 'USD', '2019-01-01', '2019-09-30'),
(13, 'Standard-Size', 2.4, 'USD', '2019-10-01', '2019-12-31'),
(14, 'Oversize', 1.2, 'USD', '2019-10-01', '2019-12-31'),
(15, 'Standard-Size', 0.69, 'USD', '2020-01-01', '2020-02-29'),
(16, 'Standard-Size', 0.75, 'USD', '2020-03-01', '2020-09-30'),
(17, 'Oversize', 0.48, 'USD', '2020-01-01', '2020-09-30'),
(18, 'Standard-Size', 2.4, 'USD', '2020-10-01', '2020-12-31'),
(19, 'Oversize', 1.2, 'USD', '2020-10-01', '2020-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `registered_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `registered_on`) VALUES
(1, 'Tarique', 'tarique@rituraj.com', '$2y$10$YuCOcG2MKBXPoNMlzrJxCOSTvrBI643BHpxz9ZOUCCssynqxmm/9O', '2020-05-02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amz_accounts`
--
ALTER TABLE `amz_accounts`
  ADD PRIMARY KEY (`amz_acct_id`),
  ADD UNIQUE KEY `seller_mp_id` (`seller_id`,`marketplace_id`) USING BTREE,
  ADD KEY `fk_amz_accts_users` (`user_id`);

--
-- Indexes for table `amz_marketplaces`
--
ALTER TABLE `amz_marketplaces`
  ADD PRIMARY KEY (`marketplace_id`);

--
-- Indexes for table `fba_fees_comp_details`
--
ALTER TABLE `fba_fees_comp_details`
  ADD KEY `fk_fba_fees_comp_details_header` (`fin_event_grp_id`);

--
-- Indexes for table `fba_fees_comp_header`
--
ALTER TABLE `fba_fees_comp_header`
  ADD PRIMARY KEY (`fin_event_grp_id`),
  ADD KEY `fk_fba_fees_comp_header_amz_account` (`amz_acct_id`);

--
-- Indexes for table `fba_ful_fees_usa`
--
ALTER TABLE `fba_ful_fees_usa`
  ADD PRIMARY KEY (`fba_fees_id`);

--
-- Indexes for table `fba_products`
--
ALTER TABLE `fba_products`
  ADD PRIMARY KEY (`amz_acct_id`,`seller_sku`);

--
-- Indexes for table `fba_prod_size_usa`
--
ALTER TABLE `fba_prod_size_usa`
  ADD PRIMARY KEY (`prod_size_id`);

--
-- Indexes for table `fba_stg_fees_usa`
--
ALTER TABLE `fba_stg_fees_usa`
  ADD PRIMARY KEY (`storage_fees_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amz_accounts`
--
ALTER TABLE `amz_accounts`
  MODIFY `amz_acct_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `fba_ful_fees_usa`
--
ALTER TABLE `fba_ful_fees_usa`
  MODIFY `fba_fees_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
--
-- AUTO_INCREMENT for table `fba_prod_size_usa`
--
ALTER TABLE `fba_prod_size_usa`
  MODIFY `prod_size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `fba_stg_fees_usa`
--
ALTER TABLE `fba_stg_fees_usa`
  MODIFY `storage_fees_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `amz_accounts`
--
ALTER TABLE `amz_accounts`
  ADD CONSTRAINT `fk_amz_accts_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fba_fees_comp_details`
--
ALTER TABLE `fba_fees_comp_details`
  ADD CONSTRAINT `fk_fba_fees_comp_details_header` FOREIGN KEY (`fin_event_grp_id`) REFERENCES `fba_fees_comp_header` (`fin_event_grp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fba_fees_comp_header`
--
ALTER TABLE `fba_fees_comp_header`
  ADD CONSTRAINT `fk_fba_fees_comp_header_amz_account` FOREIGN KEY (`amz_acct_id`) REFERENCES `amz_accounts` (`amz_acct_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fba_products`
--
ALTER TABLE `fba_products`
  ADD CONSTRAINT `fk_fba_prod_amz_acct` FOREIGN KEY (`amz_acct_id`) REFERENCES `amz_accounts` (`amz_acct_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
