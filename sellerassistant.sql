-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2020 at 05:56 PM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `amz_pmt_details`
--

CREATE TABLE `amz_pmt_details` (
  `fin_event_grp_id` varchar(255) NOT NULL,
  `amz_ord_id` varchar(19) NOT NULL,
  `posted_date` datetime NOT NULL,
  `mp_name` varchar(15) NOT NULL,
  `ord_item_id` varchar(15) NOT NULL,
  `seller_sku` varchar(10) NOT NULL,
  `qty_shp` int(11) NOT NULL,
  `amt_type` varchar(8) NOT NULL,
  `amt_desc` varchar(35) NOT NULL COMMENT 'Amount description',
  `amt_curr` varchar(3) NOT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `amz_pmt_header`
--

CREATE TABLE `amz_pmt_header` (
  `fin_event_grp_id` varchar(255) NOT NULL,
  `fin_event_grp_start` date NOT NULL,
  `fin_event_grp_end` date NOT NULL,
  `fin_event_curr` varchar(3) NOT NULL,
  `beg_bal_amt` double NOT NULL COMMENT 'Beginning balance ',
  `deposit_amt` double NOT NULL,
  `fund_trf_date` date NOT NULL COMMENT 'Fund transfer date',
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
  `unit_of_weight` varchar(6) NOT NULL,
  `longest_side` double NOT NULL,
  `median_side` double NOT NULL,
  `shortest_side` double NOT NULL,
  `unit_of_dimension` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Indexes for table `amz_pmt_details`
--
ALTER TABLE `amz_pmt_details`
  ADD KEY `fk_amz_pmt_details_header` (`fin_event_grp_id`);

--
-- Indexes for table `amz_pmt_header`
--
ALTER TABLE `amz_pmt_header`
  ADD PRIMARY KEY (`fin_event_grp_id`),
  ADD KEY `fk_amz_pmt_header_amz_acct` (`amz_acct_id`);

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
  MODIFY `amz_acct_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `amz_accounts`
--
ALTER TABLE `amz_accounts`
  ADD CONSTRAINT `fk_amz_accts_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `amz_pmt_details`
--
ALTER TABLE `amz_pmt_details`
  ADD CONSTRAINT `fk_amz_pmt_details_header` FOREIGN KEY (`fin_event_grp_id`) REFERENCES `amz_pmt_header` (`fin_event_grp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `amz_pmt_header`
--
ALTER TABLE `amz_pmt_header`
  ADD CONSTRAINT `fk_amz_pmt_header_amz_acct` FOREIGN KEY (`amz_acct_id`) REFERENCES `amz_accounts` (`amz_acct_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fba_products`
--
ALTER TABLE `fba_products`
  ADD CONSTRAINT `fk_fba_prod_amz_acct` FOREIGN KEY (`amz_acct_id`) REFERENCES `amz_accounts` (`amz_acct_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
