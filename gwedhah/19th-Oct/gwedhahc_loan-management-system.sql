-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 19, 2024 at 11:17 AM
-- Server version: 10.6.19-MariaDB-cll-lve
-- PHP Version: 8.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gwedhahc_loan-management-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_loans`
--

CREATE TABLE `active_loans` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) NOT NULL,
  `guarantor1_phone` varchar(20) NOT NULL,
  `guarantor2_name` varchar(100) NOT NULL,
  `guarantor2_phone` varchar(20) NOT NULL,
  `loan_status` set('Active') NOT NULL DEFAULT 'Active',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `call_back_contacts`
--

CREATE TABLE `call_back_contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cleared_loans`
--

CREATE TABLE `cleared_loans` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) NOT NULL,
  `guarantor1_phone` varchar(20) NOT NULL,
  `guarantor2_name` varchar(100) NOT NULL,
  `guarantor2_phone` varchar(20) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL DEFAULT 'Cleared',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` varchar(10) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `county` varchar(50) NOT NULL,
  `town_centre` varchar(100) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `id_photo_front` varchar(255) NOT NULL,
  `id_photo_back` varchar(255) NOT NULL,
  `work_economic_activity` varchar(100) NOT NULL,
  `residence_nearest_building` varchar(100) NOT NULL,
  `residence_nearest_road` varchar(100) NOT NULL,
  `date_of_onboarding` date NOT NULL,
  `onboarding_officer` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `next_of_kin_name` varchar(100) DEFAULT NULL,
  `next_of_kin_phone_number` varchar(20) DEFAULT NULL,
  `next_of_kin_relation` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `next_of_kin_name_number_1` varchar(100) DEFAULT NULL,
  `next_of_kin_name_number_2` varchar(100) DEFAULT NULL,
  `next_of_kin_name_number_3` varchar(100) DEFAULT NULL,
  `next_of_kin_name_number_4` varchar(100) DEFAULT NULL,
  `next_of_kin_name_number_5` varchar(100) DEFAULT NULL,
  `next_of_kin_residence` varchar(100) DEFAULT NULL,
  `guarantor_national_id` varchar(20) DEFAULT NULL,
  `guarantor_residence_nearest_building` varchar(100) DEFAULT NULL,
  `guarantor_residence_nearest_road` varchar(100) DEFAULT NULL,
  `guarantor_id_photo_front` varchar(255) DEFAULT NULL,
  `guarantor_id_photo_back` varchar(255) DEFAULT NULL,
  `guarantor_passport_photo` varchar(255) DEFAULT NULL,
  `client_passport_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `first_name`, `last_name`, `email`, `phone_number`, `county`, `town_centre`, `national_id`, `id_photo_front`, `id_photo_back`, `work_economic_activity`, `residence_nearest_building`, `residence_nearest_road`, `date_of_onboarding`, `onboarding_officer`, `age`, `gender`, `next_of_kin_name`, `next_of_kin_phone_number`, `next_of_kin_relation`, `created_at`, `next_of_kin_name_number_1`, `next_of_kin_name_number_2`, `next_of_kin_name_number_3`, `next_of_kin_name_number_4`, `next_of_kin_name_number_5`, `next_of_kin_residence`, `guarantor_national_id`, `guarantor_residence_nearest_building`, `guarantor_residence_nearest_road`, `guarantor_id_photo_front`, `guarantor_id_photo_back`, `guarantor_passport_photo`, `client_passport_photo`) VALUES
('cl000001', 'Joseph', 'Samwa', 'joseph.lweya@outlook.com', '254717158091', 'Meru', 'Nairobi', '28351507', 'uploads/front_67064d42a5bfe.jpg', 'uploads/back_67064d42ca3ef.jpg', 'Farmer', 'Kwetu', 'Kwangu', '2024-10-09', 'admin', 27, 'Baringo', 'polinee', '0717158091', 'bro', '2024-10-09 09:30:43', NULL, NULL, NULL, NULL, NULL, 'kabete', '0717158091', 'beryl', 'lower kabs', 'uploads/guarantor-front_67064d42ec2a7.jpg', 'uploads/guarantor-back_67064d431c21b.jpg', 'uploads/guarantor-passport_67064d433daf6.jpg', 'uploads/client-passport_67064d43613ed.jpg');

--
-- Triggers `clients`
--
DELIMITER $$
CREATE TRIGGER `generate_client_id` BEFORE INSERT ON `clients` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = COALESCE((SELECT MAX(CAST(SUBSTRING(client_id, 3) AS UNSIGNED)) FROM clients), 0) + 1;
    SET NEW.client_id = CONCAT('cl', LPAD(next_id, 6, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` int(11) NOT NULL,
  `company_name` varchar(50) DEFAULT NULL,
  `company_address` varchar(50) DEFAULT NULL,
  `company_email` varchar(50) DEFAULT NULL,
  `company_website` varchar(50) DEFAULT NULL,
  `company_phone` varchar(20) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `interest_rate` decimal(5,2) DEFAULT NULL,
  `interest_billing_period` varchar(10) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `account_reference` varchar(50) DEFAULT NULL,
  `payee_name` varchar(50) DEFAULT NULL,
  `processing_fees` decimal(10,2) DEFAULT NULL,
  `insurance_fees` decimal(4,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `company_address`, `company_email`, `company_website`, `company_phone`, `tax_rate`, `interest_rate`, `interest_billing_period`, `bank_name`, `account_number`, `account_reference`, `payee_name`, `processing_fees`, `insurance_fees`) VALUES
(1, 'Gwedhah Investments Growth Limited', 'Nairobi', 'admin@gwedhah.co.ke', 'https://gwedhah.co.ke', '254900900900', 30.00, 20.00, 'Month', 'Gwedhah Investments Growth Ltd', '800900', 'Loan Account', 'Gwedhah Investments Growth Ltd', 300.00, 10.0);

-- --------------------------------------------------------

--
-- Table structure for table `defaulted_loans`
--

CREATE TABLE `defaulted_loans` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) NOT NULL,
  `guarantor1_phone` varchar(20) NOT NULL,
  `guarantor2_name` varchar(100) NOT NULL,
  `guarantor2_phone` varchar(20) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL DEFAULT 'Defaulted',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expected_payments`
--

CREATE TABLE `expected_payments` (
  `id` int(11) NOT NULL,
  `loan_id` varchar(10) NOT NULL,
  `installment_amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_status` enum('paid','not paid') NOT NULL DEFAULT 'not paid',
  `interest_income` decimal(10,2) NOT NULL,
  `principal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expected_payments`
--

INSERT INTO `expected_payments` (`id`, `loan_id`, `installment_amount`, `payment_date`, `payment_status`, `interest_income`, `principal`, `created_at`) VALUES
(185, 'ln000002', 12.00, '2024-10-16', 'paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(186, 'ln000002', 12.00, '2024-10-23', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(187, 'ln000002', 12.00, '2024-10-30', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(188, 'ln000002', 12.00, '2024-11-06', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(189, 'ln000002', 12.00, '2024-11-13', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(190, 'ln000002', 12.00, '2024-11-20', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(191, 'ln000002', 12.00, '2024-11-27', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(192, 'ln000002', 12.00, '2024-12-04', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(193, 'ln000002', 12.00, '2024-12-11', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(194, 'ln000002', 12.00, '2024-12-18', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(195, 'ln000002', 12.00, '2024-12-25', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(196, 'ln000002', 12.00, '2025-01-01', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(197, 'ln000002', 12.00, '2025-01-08', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(198, 'ln000002', 12.00, '2025-01-15', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40'),
(199, 'ln000002', 12.00, '2025-01-22', 'not paid', 5.00, 7.00, '2024-10-09 10:02:40');

--
-- Triggers `expected_payments`
--
DELIMITER $$
CREATE TRIGGER `after_expected_payments_update` AFTER UPDATE ON `expected_payments` FOR EACH ROW BEGIN
    DECLARE loan_number BIGINT;
    DECLARE interest_amount DECIMAL(25,2);
    DECLARE principal_amount DECIMAL(25,2);
    DECLARE last_entry_id BIGINT;

    -- Check if payment_status has changed from 'not paid' to 'paid'
    IF OLD.payment_status = 'not paid' AND NEW.payment_status = 'paid' THEN
        -- Extract loan number (remove 'ln' prefix)
        SET loan_number = CAST(SUBSTRING(NEW.loan_id, 3) AS UNSIGNED);
        
        -- Get interest and principal amounts
        SET interest_amount = NEW.interest_income;
        SET principal_amount = NEW.principal;

        -- Insert into oc24entries for interest payment
        INSERT INTO oc24entries (entrytype_id, number, date, dr_total, cr_total, narration)
        VALUES 
            (1, loan_number, CURRENT_DATE(), interest_amount, interest_amount, CONCAT('Payment towards earned interest for loan ', NEW.loan_id)),
            (1, loan_number, CURRENT_DATE(), principal_amount, principal_amount, CONCAT('Payment towards principal for loan ', NEW.loan_id));
        
        -- Get the last inserted entry ID
        SET last_entry_id = LAST_INSERT_ID();

        -- Insert into oc24entryitems for the interest entry
        INSERT INTO oc24entryitems (entry_id, ledger_id, amount, dc)
        VALUES 
            (last_entry_id, 11, interest_amount, 'D'),
            (last_entry_id, 13, interest_amount, 'C');

        -- Insert into oc24entryitems for the principal entry
        INSERT INTO oc24entryitems (entry_id, ledger_id, amount, dc)
        VALUES 
            (last_entry_id + 1, 11, principal_amount, 'D'),
            (last_entry_id + 1, 12, principal_amount, 'C');
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `loan_applications`
--

CREATE TABLE `loan_applications` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) DEFAULT NULL,
  `guarantor1_phone` varchar(20) DEFAULT NULL,
  `guarantor2_name` varchar(100) DEFAULT NULL,
  `guarantor2_phone` varchar(20) DEFAULT NULL,
  `signed_application_form` varchar(255) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL DEFAULT 'Pending',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `loan_applications`
--
DELIMITER $$
CREATE TRIGGER `generate_loan_id` BEFORE INSERT ON `loan_applications` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    
    -- Retrieve the last generated loan_id
    SET next_id = (SELECT last_id FROM loan_id_tracker FOR UPDATE) + 1;
    
    -- Update the tracker with the new loan_id
    UPDATE loan_id_tracker SET last_id = next_id;
    
    -- Set the new loan_id in the desired format
    SET NEW.loan_id = CONCAT('ln', LPAD(next_id, 6, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `loan_id_tracker`
--

CREATE TABLE `loan_id_tracker` (
  `id` int(11) NOT NULL,
  `last_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_id_tracker`
--

INSERT INTO `loan_id_tracker` (`id`, `last_id`) VALUES
(1, '2');

-- --------------------------------------------------------

--
-- Table structure for table `loan_info`
--

CREATE TABLE `loan_info` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) DEFAULT NULL,
  `guarantor1_phone` varchar(20) DEFAULT NULL,
  `guarantor2_name` varchar(100) DEFAULT NULL,
  `guarantor2_phone` varchar(20) DEFAULT NULL,
  `signed_application_form` varchar(255) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL DEFAULT 'Active',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_change` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_info`
--

INSERT INTO `loan_info` (`loan_id`, `client_id`, `national_id`, `requested_amount`, `loan_purpose`, `duration`, `duration_period`, `date_applied`, `collateral_name`, `collateral_value`, `collateral_pic1`, `collateral_pic2`, `guarantor1_name`, `guarantor1_phone`, `guarantor2_name`, `guarantor2_phone`, `signed_application_form`, `loan_status`, `onboarding_officer`, `created_at`, `status_change`) VALUES
('ln000002', 'cl000001', '28351507', 100.00, 'fare', 15, 'Week', '2024-10-09', 'phone', 500.00, 'uploads/pic1_67065091ac384.jpg', 'uploads/pic2_67065091d0bd8.jpg', NULL, NULL, NULL, NULL, 'uploads/signed_application_67065092012f2.jpg', 'Active', 'admin', '2024-10-09 10:02:38', '2024-10-09 10:02:38');

-- --------------------------------------------------------

--
-- Table structure for table `loan_stage`
--

CREATE TABLE `loan_stage` (
  `loan_id` varchar(10) NOT NULL,
  `stage` enum('Green Zone','Early Delinquency','Loan Loss Provision Recognised','Loan Loss Provision Unrecognised','Default Unrecognised','Default Recognised') NOT NULL DEFAULT 'Green Zone'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_status`
--

CREATE TABLE `loan_status` (
  `id` int(11) NOT NULL,
  `loan_id` varchar(10) NOT NULL,
  `national_id` varchar(50) NOT NULL,
  `collateral_status` varchar(50) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `managers`
--

CREATE TABLE `managers` (
  `manager_id` varchar(10) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `county` varchar(100) NOT NULL,
  `town_centre` varchar(100) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `id_photo_front` varchar(255) NOT NULL,
  `id_photo_back` varchar(255) NOT NULL,
  `nssf` varchar(20) NOT NULL,
  `nhif` varchar(20) NOT NULL,
  `kra_pin` varchar(20) NOT NULL,
  `date_of_onboarding` date NOT NULL,
  `residence_nearest_building` varchar(255) NOT NULL,
  `residence_nearest_road` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `next_of_kin_name` varchar(255) NOT NULL,
  `next_of_kin_phone_number` varchar(20) NOT NULL,
  `next_of_kin_relation` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `managers`
--

INSERT INTO `managers` (`manager_id`, `first_name`, `last_name`, `email`, `phone_number`, `county`, `town_centre`, `national_id`, `id_photo_front`, `id_photo_back`, `nssf`, `nhif`, `kra_pin`, `date_of_onboarding`, `residence_nearest_building`, `residence_nearest_road`, `age`, `gender`, `next_of_kin_name`, `next_of_kin_phone_number`, `next_of_kin_relation`, `created_at`) VALUES
('mn001', 'Joe', 'Lweya', 'joseph.lweya@gmail.com', '254741870560', 'Isiolo', 'Camp David', '3009146', 'uploads/front_6707833d8e656.jpg', 'uploads/back_6707833d8eb10.jpg', 'sf56785678', 'hf87658765', 'a23452345', '2024-10-10', 'Kwetu', 'Kwangu', 27, 'Baringo', 'irene', '0111296962', '0111296962', '2024-10-10 07:33:17');

--
-- Triggers `managers`
--
DELIMITER $$
CREATE TRIGGER `generate_manager_id` BEFORE INSERT ON `managers` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = COALESCE((SELECT MAX(CAST(SUBSTRING(manager_id, 3) AS UNSIGNED)) FROM managers), 0) + 1;
    SET NEW.manager_id = CONCAT('mn', LPAD(next_id, 3, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_transactions`
--

CREATE TABLE `mpesa_transactions` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `mpesa_receipt_number` varchar(20) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Completed','Failed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `heading` varchar(255) NOT NULL DEFAULT 'Topic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`, `heading`) VALUES
(166, 43, 'Hi, Joseph Samwa! Your payment of 15 has been received.', 0, '2024-10-10 08:30:48', 'Payment Received for Joseph Samwa'),
(167, 17, 'Joseph Samwa has made a payment of 15. Please review the transaction.', 0, '2024-10-10 08:30:48', 'Payment Received for Joseph Samwa');

-- --------------------------------------------------------

--
-- Table structure for table `oc24entries`
--

CREATE TABLE `oc24entries` (
  `id` bigint(18) NOT NULL,
  `tag_id` bigint(18) DEFAULT NULL,
  `entrytype_id` bigint(18) NOT NULL,
  `number` bigint(18) DEFAULT NULL,
  `date` date NOT NULL,
  `dr_total` decimal(25,2) NOT NULL DEFAULT 0.00,
  `cr_total` decimal(25,2) NOT NULL DEFAULT 0.00,
  `narration` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `oc24entries`
--

INSERT INTO `oc24entries` (`id`, `tag_id`, `entrytype_id`, `number`, `date`, `dr_total`, `cr_total`, `narration`) VALUES
(41, NULL, 1, 2, '2024-10-10', 5.00, 5.00, 'Payment towards earned interest for loan ln000002'),
(42, NULL, 1, 2, '2024-10-10', 7.00, 7.00, 'Payment towards principal for loan ln000002');

-- --------------------------------------------------------

--
-- Table structure for table `oc24entryitems`
--

CREATE TABLE `oc24entryitems` (
  `id` bigint(18) NOT NULL,
  `entry_id` bigint(18) NOT NULL,
  `ledger_id` bigint(18) NOT NULL,
  `amount` decimal(25,2) NOT NULL DEFAULT 0.00,
  `dc` char(1) NOT NULL,
  `reconciliation_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `oc24entryitems`
--

INSERT INTO `oc24entryitems` (`id`, `entry_id`, `ledger_id`, `amount`, `dc`, `reconciliation_date`) VALUES
(71, 41, 11, 5.00, 'D', NULL),
(72, 41, 13, 5.00, 'C', NULL),
(73, 42, 11, 7.00, 'D', NULL),
(74, 42, 12, 7.00, 'C', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oc24entrytypes`
--

CREATE TABLE `oc24entrytypes` (
  `id` bigint(18) NOT NULL,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `base_type` int(2) NOT NULL DEFAULT 0,
  `numbering` int(2) NOT NULL DEFAULT 1,
  `prefix` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `zero_padding` int(2) NOT NULL DEFAULT 0,
  `restriction_bankcash` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `oc24entrytypes`
--

INSERT INTO `oc24entrytypes` (`id`, `label`, `name`, `description`, `base_type`, `numbering`, `prefix`, `suffix`, `zero_padding`, `restriction_bankcash`) VALUES
(1, 'receipt', 'Receipt', 'Received in Bank account or Cash account', 1, 1, '', '', 0, 2),
(2, 'payment', 'Payment', 'Payment made from Bank account or Cash account', 1, 1, '', '', 0, 3),
(3, 'contra', 'Contra', 'Transfer between Bank account and Cash account', 1, 1, '', '', 0, 4),
(4, 'journal', 'Journal', 'Transaction that does not involve a Bank account or Cash account', 1, 1, '', '', 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `oc24groups`
--

CREATE TABLE `oc24groups` (
  `id` bigint(18) NOT NULL,
  `parent_id` bigint(18) DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `affects_gross` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `oc24groups`
--

INSERT INTO `oc24groups` (`id`, `parent_id`, `name`, `code`, `affects_gross`) VALUES
(1, NULL, 'Assets', NULL, 0),
(2, NULL, 'Liabilities and Owners Equity', NULL, 0),
(3, NULL, 'Incomes', NULL, 0),
(4, NULL, 'Expenses', NULL, 0),
(5, 1, 'Fixed Assets', NULL, 0),
(6, 1, 'Current Assets', NULL, 0),
(7, 1, 'Investments', NULL, 0),
(8, 2, 'Capital Account', NULL, 0),
(9, 2, 'Current Liabilities', NULL, 0),
(10, 2, 'Loans (Liabilities)', NULL, 0),
(11, 3, 'Direct Incomes', NULL, 1),
(12, 4, 'Direct Expenses', NULL, 1),
(13, 3, 'Indirect Incomes', NULL, 0),
(14, 4, 'Indirect Expenses', NULL, 0),
(15, 3, 'Sales', NULL, 1),
(16, 4, 'Purchases', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `oc24ledgers`
--

CREATE TABLE `oc24ledgers` (
  `id` bigint(18) NOT NULL,
  `group_id` bigint(18) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `op_balance` decimal(25,2) NOT NULL DEFAULT 0.00,
  `op_balance_dc` char(1) NOT NULL,
  `type` int(2) NOT NULL DEFAULT 0,
  `reconciliation` int(1) NOT NULL DEFAULT 0,
  `notes` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `oc24ledgers`
--

INSERT INTO `oc24ledgers` (`id`, `group_id`, `name`, `code`, `op_balance`, `op_balance_dc`, `type`, `reconciliation`, `notes`) VALUES
(1, 11, 'Interest Income on Loans', NULL, 0.00, 'C', 0, 0, ''),
(7, 14, 'Depreciation', NULL, 0.00, 'D', 0, 0, ''),
(8, 12, 'Interest Expense', NULL, 0.00, 'D', 0, 0, ''),
(9, 12, 'Commissions/Broker fees', NULL, 0.00, 'D', 0, 0, ''),
(10, 6, 'Cash In Hand', NULL, 0.00, 'D', 1, 0, ''),
(11, 6, 'Cash At Bank', NULL, 0.00, 'D', 1, 0, ''),
(12, 6, 'Loans Receivable (Current Portion)', NULL, 0.00, 'D', 0, 0, ''),
(13, 6, 'Interest Receivable', NULL, 0.00, 'D', 0, 0, ''),
(14, 6, 'Prepaid Expenses', NULL, 0.00, 'D', 0, 0, ''),
(15, 5, 'Loans Receivable (Non-Current Portion)', NULL, 0.00, 'D', 0, 0, ''),
(16, 5, 'Property and Equipment', NULL, 0.00, 'D', 0, 0, ''),
(17, 9, 'Accounts Payable', NULL, 0.00, 'C', 0, 0, ''),
(18, 9, 'Interest Payable', NULL, 0.00, 'C', 0, 0, ''),
(19, 9, 'Accrued Expenses', NULL, 0.00, 'C', 0, 0, ''),
(20, 9, 'Loan Payable (Current Portion)', NULL, 0.00, 'C', 0, 0, ''),
(21, 10, 'Loan Payable (Non-Current Portion)', NULL, 0.00, 'C', 0, 0, ''),
(22, 8, 'Owners Capital', NULL, 0.00, 'C', 0, 0, ''),
(23, 8, 'Retained Earnings', NULL, 0.00, 'C', 0, 0, ''),
(24, 11, 'Fee Income (Processing/Administrative Fees)', NULL, 0.00, 'C', 0, 0, ''),
(25, 14, 'Salaries and Wages', NULL, 0.00, 'D', 0, 0, ''),
(26, 14, 'Rent Expense', NULL, 0.00, 'D', 0, 0, ''),
(27, 14, 'Utilities Expense', NULL, 0.00, 'D', 0, 0, ''),
(28, 14, 'Office Supplies', NULL, 0.00, 'D', 0, 0, ''),
(29, 14, 'Loan Loss Provision (Reserve for Bad Debts)', NULL, 0.00, 'D', 0, 0, ''),
(30, 9, 'Allowance for Doubtful Accounts', NULL, 0.00, 'C', 0, 0, ''),
(31, 13, 'Gain on Sale of Collateral', NULL, 0.00, 'C', 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `oc24logs`
--

CREATE TABLE `oc24logs` (
  `id` bigint(18) NOT NULL,
  `date` datetime NOT NULL,
  `level` int(1) NOT NULL,
  `host_ip` varchar(25) NOT NULL,
  `user` varchar(25) NOT NULL,
  `url` varchar(255) NOT NULL,
  `user_agent` varchar(100) NOT NULL,
  `message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `oc24logs`
--

INSERT INTO `oc24logs` (`id`, `date`, `level`, `host_ip`, `user`, `url`, `user_agent`, `message`) VALUES
(1, '2024-10-09 14:48:19', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/28', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 9'),
(2, '2024-10-09 14:48:27', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/receipt/33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 2'),
(3, '2024-10-09 14:48:31', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/receipt/34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 2'),
(4, '2024-10-09 14:48:35', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/receipt/35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 2'),
(5, '2024-10-09 14:48:41', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/receipt/36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 2'),
(6, '2024-10-09 14:48:46', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/receipt/37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 2'),
(7, '2024-10-09 14:48:50', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/receipt/38', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 2'),
(8, '2024-10-09 14:48:55', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/receipt/39', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 2'),
(9, '2024-10-09 14:48:58', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/receipt/40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 2'),
(10, '2024-10-09 14:49:12', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 2'),
(11, '2024-10-09 14:49:16', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 10'),
(12, '2024-10-09 14:49:51', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 7'),
(13, '2024-10-09 14:49:57', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/25', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 6'),
(14, '2024-10-09 14:50:03', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/24', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 5'),
(15, '2024-10-09 14:50:25', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 4'),
(16, '2024-10-09 14:50:31', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 3'),
(17, '2024-10-09 14:50:35', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 8'),
(18, '2024-10-09 14:50:41', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 12'),
(19, '2024-10-09 14:50:52', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 11'),
(20, '2024-10-09 14:51:00', 1, '41.90.34.81', 'admin', 'https://portal.gwedhah.co.ke/accounts/entries/delete/payment/32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 2');

-- --------------------------------------------------------

--
-- Table structure for table `oc24settings`
--

CREATE TABLE `oc24settings` (
  `id` int(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fy_start` date NOT NULL,
  `fy_end` date NOT NULL,
  `currency_symbol` varchar(100) NOT NULL,
  `currency_format` varchar(100) NOT NULL,
  `decimal_places` int(2) NOT NULL DEFAULT 2,
  `date_format` varchar(100) NOT NULL,
  `timezone` varchar(100) NOT NULL,
  `manage_inventory` int(1) NOT NULL DEFAULT 0,
  `account_locked` int(1) NOT NULL DEFAULT 0,
  `email_use_default` int(1) NOT NULL DEFAULT 0,
  `email_protocol` varchar(10) NOT NULL,
  `email_host` varchar(255) NOT NULL,
  `email_port` int(5) NOT NULL,
  `email_tls` int(1) NOT NULL DEFAULT 0,
  `email_username` varchar(255) NOT NULL,
  `email_password` varchar(255) NOT NULL,
  `email_from` varchar(255) NOT NULL,
  `print_paper_height` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_paper_width` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_margin_top` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_margin_bottom` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_margin_left` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_margin_right` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_orientation` char(1) NOT NULL,
  `print_page_format` char(1) NOT NULL,
  `database_version` int(10) NOT NULL,
  `settings` varchar(2048) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `oc24settings`
--

INSERT INTO `oc24settings` (`id`, `name`, `address`, `email`, `fy_start`, `fy_end`, `currency_symbol`, `currency_format`, `decimal_places`, `date_format`, `timezone`, `manage_inventory`, `account_locked`, `email_use_default`, `email_protocol`, `email_host`, `email_port`, `email_tls`, `email_username`, `email_password`, `email_from`, `print_paper_height`, `print_paper_width`, `print_margin_top`, `print_margin_bottom`, `print_margin_left`, `print_margin_right`, `print_orientation`, `print_page_format`, `database_version`, `settings`) VALUES
(1, 'Gwedhah Investments Growth Limited', '', 'admin@gwedhah.co.ke', '2024-01-01', '2024-12-31', 'Kes', '###,###.##', 2, 'd-M-Y|dd-M-yy', 'UTC', 0, 0, 1, 'Smtp', '', 0, 0, '', '', '', 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 'P', 'H', 6, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oc24tags`
--

CREATE TABLE `oc24tags` (
  `id` bigint(18) NOT NULL,
  `title` varchar(255) NOT NULL,
  `color` char(6) NOT NULL,
  `background` char(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oc24wzaccounts`
--

CREATE TABLE `oc24wzaccounts` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `db_datasource` varchar(255) DEFAULT NULL,
  `db_database` varchar(255) DEFAULT NULL,
  `db_host` varchar(255) DEFAULT NULL,
  `db_port` int(11) DEFAULT NULL,
  `db_login` varchar(255) DEFAULT NULL,
  `db_password` varchar(255) DEFAULT NULL,
  `db_prefix` varchar(255) DEFAULT NULL,
  `db_persistent` varchar(255) DEFAULT NULL,
  `db_schema` varchar(255) DEFAULT NULL,
  `db_unixsocket` varchar(255) DEFAULT NULL,
  `db_settings` varchar(255) DEFAULT NULL,
  `ssl_key` varchar(255) DEFAULT NULL,
  `ssl_cert` varchar(255) DEFAULT NULL,
  `ssl_ca` varchar(255) DEFAULT NULL,
  `hidden` int(1) NOT NULL DEFAULT 0,
  `others` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `oc24wzaccounts`
--

INSERT INTO `oc24wzaccounts` (`id`, `label`, `db_datasource`, `db_database`, `db_host`, `db_port`, `db_login`, `db_password`, `db_prefix`, `db_persistent`, `db_schema`, `db_unixsocket`, `db_settings`, `ssl_key`, `ssl_cert`, `ssl_ca`, `hidden`, `others`) VALUES
(1, 'gwedhahaccounts2024', 'Database/Mysql', 'gwedhahc_loan-management-system', 'localhost', 3306, 'gwedhahc_admin', 'Invest@Gwedhah2024!', 'oc24', '0', '', NULL, '', NULL, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oc24wzsettings`
--

CREATE TABLE `oc24wzsettings` (
  `id` int(11) NOT NULL,
  `sitename` varchar(255) DEFAULT NULL,
  `drcr_toby` varchar(255) DEFAULT NULL,
  `enable_logging` int(1) NOT NULL DEFAULT 0,
  `row_count` int(11) NOT NULL DEFAULT 10,
  `user_registration` int(1) NOT NULL DEFAULT 0,
  `admin_verification` int(1) NOT NULL DEFAULT 0,
  `email_verification` int(1) NOT NULL DEFAULT 0,
  `email_protocol` varchar(255) DEFAULT NULL,
  `email_host` varchar(255) DEFAULT NULL,
  `email_port` int(11) DEFAULT 0,
  `email_tls` int(1) DEFAULT 0,
  `email_username` varchar(255) DEFAULT NULL,
  `email_password` varchar(255) DEFAULT NULL,
  `email_from` varchar(255) DEFAULT NULL,
  `others` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `oc24wzsettings`
--

INSERT INTO `oc24wzsettings` (`id`, `sitename`, `drcr_toby`, `enable_logging`, `row_count`, `user_registration`, `admin_verification`, `email_verification`, `email_protocol`, `email_host`, `email_port`, `email_tls`, `email_username`, `email_password`, `email_from`, `others`) VALUES
(1, 'Gwedhah Investments Growth Limited', 'drcr', 1, 10, 0, 1, 0, 'Smtp', 'smtp.gwedhah.co.ke', 587, 1, 'admin@gwedhah.co.ke', 'Invest@Growth2024!', 'Gwedhah Investments', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oc24wzuseraccounts`
--

CREATE TABLE `oc24wzuseraccounts` (
  `id` int(11) NOT NULL,
  `wzuser_id` int(11) NOT NULL,
  `wzaccount_id` int(11) NOT NULL,
  `role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oc24wzusers`
--

CREATE TABLE `oc24wzusers` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `verification_key` varchar(255) DEFAULT NULL,
  `email_verified` int(1) NOT NULL DEFAULT 0,
  `admin_verified` int(1) NOT NULL DEFAULT 0,
  `retry_count` int(1) NOT NULL DEFAULT 0,
  `all_accounts` int(1) NOT NULL DEFAULT 0,
  `default_account` int(11) NOT NULL DEFAULT 0,
  `others` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `oc24wzusers`
--

INSERT INTO `oc24wzusers` (`id`, `username`, `password`, `fullname`, `email`, `timezone`, `role`, `status`, `verification_key`, `email_verified`, `admin_verified`, `retry_count`, `all_accounts`, `default_account`, `others`) VALUES
(1, 'admin', '2d7ec71baef3def53b4ed3c088bb45a7ad24846f', 'Gwedhah Investments', 'admin@gwedhah.co.ke', 'UTC', 'admin', 1, '', 1, 1, 0, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `loan_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `transaction_reference` varchar(50) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `loan_id`, `national_id`, `transaction_reference`, `payment_mode`, `payment_date`, `amount`) VALUES
(22, 'ln000002', '28351507', 'gdhfgjngf', 'BANK', '2024-10-10', 15.00);

--
-- Triggers `payments`
--
DELIMITER $$
CREATE TRIGGER `update_installment_status` AFTER INSERT ON `payments` FOR EACH ROW BEGIN
    DECLARE remaining_amount DECIMAL(10,2);
    DECLARE next_installment DECIMAL(10,2);
    DECLARE next_id INT DEFAULT 0;
    DECLARE done INT DEFAULT 0;

    -- Declare a cursor to select the next unpaid installment, ordered by payment_date
    DECLARE cur CURSOR FOR
        SELECT id, installment_amount
        FROM expected_payments
        WHERE loan_id = NEW.loan_id AND payment_status = 'not paid'
        ORDER BY payment_date ASC;

    -- Handler to exit the loop when no more rows are found
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Step 1: Calculate the total sum of payments for the given loan
    SELECT IFNULL(SUM(amount), 0) INTO remaining_amount
    FROM payments
    WHERE loan_id = NEW.loan_id;

    -- Step 2: Calculate the sum of all 'paid' installments
    SELECT IFNULL(SUM(installment_amount), 0) INTO @paid_sum
    FROM expected_payments
    WHERE loan_id = NEW.loan_id AND payment_status = 'paid';

    -- Step 3: Subtract the total 'paid' installments from the total payments
    SET remaining_amount = remaining_amount - @paid_sum;

    -- Step 4: Open the cursor to loop through unpaid installments
    OPEN cur;
    
    payment_loop: LOOP
        FETCH cur INTO next_id, next_installment;

        -- Exit loop when done
        IF done THEN
            LEAVE payment_loop;
        END IF;

        -- Step 5: Check if remaining amount is greater than or equal to the next installment
        IF remaining_amount >= next_installment THEN
            -- Mark the installment as 'paid'
            UPDATE expected_payments
            SET payment_status = 'paid'
            WHERE id = next_id;

            -- Deduct the installment amount from remaining amount
            SET remaining_amount = remaining_amount - next_installment;
        ELSE
            -- If remaining amount is smaller than the next installment, stop the loop
            LEAVE payment_loop;
        END IF;
    END LOOP;
    
    -- Close the cursor after looping through unpaid installments
    CLOSE cur;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rejected_loans`
--

CREATE TABLE `rejected_loans` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) NOT NULL,
  `guarantor1_phone` varchar(20) NOT NULL,
  `guarantor2_name` varchar(100) NOT NULL,
  `guarantor2_phone` varchar(20) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL DEFAULT 'Rejected',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sqwzsettings`
--

CREATE TABLE `sqwzsettings` (
  `id` int(11) NOT NULL,
  `sitename` varchar(255) DEFAULT NULL,
  `drcr_toby` varchar(255) DEFAULT NULL,
  `enable_logging` int(1) NOT NULL DEFAULT 0,
  `row_count` int(11) NOT NULL DEFAULT 10,
  `user_registration` int(1) NOT NULL DEFAULT 0,
  `admin_verification` int(1) NOT NULL DEFAULT 0,
  `email_verification` int(1) NOT NULL DEFAULT 0,
  `email_protocol` varchar(255) DEFAULT NULL,
  `email_host` varchar(255) DEFAULT NULL,
  `email_port` int(11) DEFAULT 0,
  `email_tls` int(1) DEFAULT 0,
  `email_username` varchar(255) DEFAULT NULL,
  `email_password` varchar(255) DEFAULT NULL,
  `email_from` varchar(255) DEFAULT NULL,
  `others` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `sqwzsettings`
--

INSERT INTO `sqwzsettings` (`id`, `sitename`, `drcr_toby`, `enable_logging`, `row_count`, `user_registration`, `admin_verification`, `email_verification`, `email_protocol`, `email_host`, `email_port`, `email_tls`, `email_username`, `email_password`, `email_from`, `others`) VALUES
(1, 'Gwedhah Investments Growth Limited', 'drcr', 0, 10, 0, 1, 0, 'Smtp', 'smtp.gwedhah.co.ke', 0, 0, '', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sqwzuseraccounts`
--

CREATE TABLE `sqwzuseraccounts` (
  `id` int(11) NOT NULL,
  `wzuser_id` int(11) NOT NULL,
  `wzaccount_id` int(11) NOT NULL,
  `role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sqwzusers`
--

CREATE TABLE `sqwzusers` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `verification_key` varchar(255) DEFAULT NULL,
  `email_verified` int(1) NOT NULL DEFAULT 0,
  `admin_verified` int(1) NOT NULL DEFAULT 0,
  `retry_count` int(1) NOT NULL DEFAULT 0,
  `all_accounts` int(1) NOT NULL DEFAULT 0,
  `default_account` int(11) NOT NULL DEFAULT 0,
  `others` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `sqwzusers`
--

INSERT INTO `sqwzusers` (`id`, `username`, `password`, `fullname`, `email`, `timezone`, `role`, `status`, `verification_key`, `email_verified`, `admin_verified`, `retry_count`, `all_accounts`, `default_account`, `others`) VALUES
(1, 'admin', 'daedeb986e6f2645835bb839bbf81559813c956d', 'Gwedhah Investments', 'admin@gwedhah.co.ke', 'UTC', 'admin', 1, '', 1, 1, 0, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stk_requests`
--

CREATE TABLE `stk_requests` (
  `id` int(11) NOT NULL,
  `loan_id` varchar(10) NOT NULL,
  `merchant_request_id` varchar(255) NOT NULL,
  `checkout_request_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `stk_requests`
--

INSERT INTO `stk_requests` (`id`, `loan_id`, `merchant_request_id`, `checkout_request_id`, `created_at`) VALUES
(1, 'ln000012', 'dad6-4c34-8787-c8cb963a496d379130', 'ws_CO_09102024030917313717158091', '2024-10-09 00:07:14'),
(2, 'ln000012', '3124-481d-b706-10bdd6fbc8e2984346', 'ws_CO_09102024031933738717158091', '2024-10-09 00:17:30'),
(3, 'ln000012', '8ed5-4489-a67f-881890b925f2987060', 'ws_CO_09102024032835370717158091', '2024-10-09 00:26:32'),
(4, 'ln000012', 'dad6-4c34-8787-c8cb963a496d379852', 'ws_CO_09102024033356088717158091', '2024-10-09 00:31:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `file_no` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','client') NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `file_no`, `email`, `password`, `role`, `reset_token`, `created_at`) VALUES
(17, 'ADMIN', '001', 'ad001', 'admin@gwedhah.co.ke', '$2y$10$Ov7qrLEA2VsZic5r4Xix1ew8QGfwUVkcZsfw.i2/kyDl3uQ5.Frli', 'admin', '', '2024-09-04 11:15:01'),
(43, 'Joseph', 'Samwa', 'cl000001', 'joseph.lweya@outlook.com', '$2y$10$tT90L69o6hPpWEdLalFQpeJ3E/xA/d/l.vl76UMzk/08tlthUNnrO', 'client', NULL, '2024-10-09 09:30:43'),
(44, 'Joe', 'Lweya', 'mn001', 'joseph.lweya@gmail.com', '$2y$10$5Z.K2g/gcgV.Rfgb5tc67e9P7Tym/LL6Gh5fVhqI.dnq96xNQVbxi', 'manager', NULL, '2024-10-10 07:33:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_loans`
--
ALTER TABLE `active_loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `call_back_contacts`
--
ALTER TABLE `call_back_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cleared_loans`
--
ALTER TABLE `cleared_loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `national_id` (`national_id`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `defaulted_loans`
--
ALTER TABLE `defaulted_loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `expected_payments`
--
ALTER TABLE `expected_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_loan_id` (`loan_id`);

--
-- Indexes for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `loan_id_tracker`
--
ALTER TABLE `loan_id_tracker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_info`
--
ALTER TABLE `loan_info`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `loan_stage`
--
ALTER TABLE `loan_stage`
  ADD PRIMARY KEY (`loan_id`);

--
-- Indexes for table `loan_status`
--
ALTER TABLE `loan_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `managers`
--
ALTER TABLE `managers`
  ADD PRIMARY KEY (`manager_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD UNIQUE KEY `kra_pin` (`kra_pin`);

--
-- Indexes for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_ibfk_1` (`user_id`);

--
-- Indexes for table `oc24entries`
--
ALTER TABLE `oc24entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `tag_id` (`tag_id`),
  ADD KEY `entrytype_id` (`entrytype_id`);

--
-- Indexes for table `oc24entryitems`
--
ALTER TABLE `oc24entryitems`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `entry_id` (`entry_id`),
  ADD KEY `ledger_id` (`ledger_id`);

--
-- Indexes for table `oc24entrytypes`
--
ALTER TABLE `oc24entrytypes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD UNIQUE KEY `label` (`label`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `oc24groups`
--
ALTER TABLE `oc24groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `id` (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `oc24ledgers`
--
ALTER TABLE `oc24ledgers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `id` (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `oc24logs`
--
ALTER TABLE `oc24logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `oc24settings`
--
ALTER TABLE `oc24settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `oc24tags`
--
ALTER TABLE `oc24tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `oc24wzaccounts`
--
ALTER TABLE `oc24wzaccounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oc24wzsettings`
--
ALTER TABLE `oc24wzsettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oc24wzuseraccounts`
--
ALTER TABLE `oc24wzuseraccounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oc24wzuseraccounts_fk_check_wzuser_id` (`wzuser_id`),
  ADD KEY `oc24wzuseraccounts_fk_check_wzaccount_id` (`wzaccount_id`);

--
-- Indexes for table `oc24wzusers`
--
ALTER TABLE `oc24wzusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `rejected_loans`
--
ALTER TABLE `rejected_loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `sqwzsettings`
--
ALTER TABLE `sqwzsettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sqwzuseraccounts`
--
ALTER TABLE `sqwzuseraccounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sqwzuseraccounts_fk_check_wzuser_id` (`wzuser_id`),
  ADD KEY `sqwzuseraccounts_fk_check_wzaccount_id` (`wzaccount_id`);

--
-- Indexes for table `sqwzusers`
--
ALTER TABLE `sqwzusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stk_requests`
--
ALTER TABLE `stk_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `merchant_request_id` (`merchant_request_id`,`checkout_request_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `file_no` (`file_no`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `call_back_contacts`
--
ALTER TABLE `call_back_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expected_payments`
--
ALTER TABLE `expected_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `loan_id_tracker`
--
ALTER TABLE `loan_id_tracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loan_status`
--
ALTER TABLE `loan_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `oc24entries`
--
ALTER TABLE `oc24entries`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `oc24entryitems`
--
ALTER TABLE `oc24entryitems`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `oc24entrytypes`
--
ALTER TABLE `oc24entrytypes`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `oc24groups`
--
ALTER TABLE `oc24groups`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `oc24ledgers`
--
ALTER TABLE `oc24ledgers`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `oc24logs`
--
ALTER TABLE `oc24logs`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `oc24tags`
--
ALTER TABLE `oc24tags`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oc24wzaccounts`
--
ALTER TABLE `oc24wzaccounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `oc24wzsettings`
--
ALTER TABLE `oc24wzsettings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `oc24wzuseraccounts`
--
ALTER TABLE `oc24wzuseraccounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oc24wzusers`
--
ALTER TABLE `oc24wzusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `sqwzsettings`
--
ALTER TABLE `sqwzsettings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sqwzuseraccounts`
--
ALTER TABLE `sqwzuseraccounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sqwzusers`
--
ALTER TABLE `sqwzusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stk_requests`
--
ALTER TABLE `stk_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_loans`
--
ALTER TABLE `active_loans`
  ADD CONSTRAINT `active_loans_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `cleared_loans`
--
ALTER TABLE `cleared_loans`
  ADD CONSTRAINT `cleared_loans_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `defaulted_loans`
--
ALTER TABLE `defaulted_loans`
  ADD CONSTRAINT `defaulted_loans_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `expected_payments`
--
ALTER TABLE `expected_payments`
  ADD CONSTRAINT `fk_loan_id` FOREIGN KEY (`loan_id`) REFERENCES `loan_info` (`loan_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD CONSTRAINT `loan_applications_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `loan_info`
--
ALTER TABLE `loan_info`
  ADD CONSTRAINT `loan_info_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `loan_stage`
--
ALTER TABLE `loan_stage`
  ADD CONSTRAINT `fk_loan_stage_loan` FOREIGN KEY (`loan_id`) REFERENCES `loan_info` (`loan_id`);

--
-- Constraints for table `loan_status`
--
ALTER TABLE `loan_status`
  ADD CONSTRAINT `loan_status_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan_info` (`loan_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `oc24entries`
--
ALTER TABLE `oc24entries`
  ADD CONSTRAINT `oc24entries_fk_check_entrytype_id` FOREIGN KEY (`entrytype_id`) REFERENCES `oc24entrytypes` (`id`),
  ADD CONSTRAINT `oc24entries_fk_check_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `oc24tags` (`id`);

--
-- Constraints for table `oc24entryitems`
--
ALTER TABLE `oc24entryitems`
  ADD CONSTRAINT `oc24entryitems_fk_check_entry_id` FOREIGN KEY (`entry_id`) REFERENCES `oc24entries` (`id`),
  ADD CONSTRAINT `oc24entryitems_fk_check_ledger_id` FOREIGN KEY (`ledger_id`) REFERENCES `oc24ledgers` (`id`);

--
-- Constraints for table `oc24groups`
--
ALTER TABLE `oc24groups`
  ADD CONSTRAINT `oc24groups_fk_check_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `oc24groups` (`id`);

--
-- Constraints for table `oc24ledgers`
--
ALTER TABLE `oc24ledgers`
  ADD CONSTRAINT `oc24ledgers_fk_check_group_id` FOREIGN KEY (`group_id`) REFERENCES `oc24groups` (`id`);

--
-- Constraints for table `oc24wzuseraccounts`
--
ALTER TABLE `oc24wzuseraccounts`
  ADD CONSTRAINT `oc24wzuseraccounts_fk_check_wzaccount_id` FOREIGN KEY (`wzaccount_id`) REFERENCES `oc24wzaccounts` (`id`),
  ADD CONSTRAINT `oc24wzuseraccounts_fk_check_wzuser_id` FOREIGN KEY (`wzuser_id`) REFERENCES `oc24wzusers` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan_info` (`loan_id`);

--
-- Constraints for table `rejected_loans`
--
ALTER TABLE `rejected_loans`
  ADD CONSTRAINT `rejected_loans_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `sqwzuseraccounts`
--
ALTER TABLE `sqwzuseraccounts`
  ADD CONSTRAINT `sqwzuseraccounts_fk_check_wzaccount_id` FOREIGN KEY (`wzaccount_id`) REFERENCES `sqwzaccounts` (`id`),
  ADD CONSTRAINT `sqwzuseraccounts_fk_check_wzuser_id` FOREIGN KEY (`wzuser_id`) REFERENCES `sqwzusers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
