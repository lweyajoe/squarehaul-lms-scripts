-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 19, 2024 at 03:13 AM
-- Server version: 10.6.19-MariaDB-cll-lve
-- PHP Version: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `omabracr_loan-management-system`
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
('cl000004', 'Brian', 'Omachi', 'omachibrain5@gmail.com', '254712752616', 'Machakos', 'Machakos', '27871610', 'uploads/Copy of Copy of Omachi PIN (1).pdf', 'uploads/ID.docx.pdf', 'One Acre', 'Twin falls City', 'Twin falls City', '2024-10-14', 'admin', NULL, NULL, NULL, NULL, NULL, '2024-10-14 07:02:02', 'Karisa Jumwa, 254722953418', 'Anas Omware, 254722394332', 'Omabra, 254743673899', 'Paul Omachi, 254721837224', 'Isdora Anyona, 254721674483', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
(1, 'Omabra Credit', 'Nairobi', 'support@omabracredit.co.ke', 'https://omabracredit.co.ke', '254743673899', 30.00, 14.00, 'Month', 'MPESA PAYBILL', '4082073', 'Loan Account', 'Omabra Limited', 0.00, 0.0);

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
(202, 'ln000003', 34200.00, '2024-11-17', 'not paid', 4200.00, 30000.00, '2024-10-17 13:34:39'),
(203, 'ln000004', 14.00, '2024-10-24', 'paid', 4.00, 10.00, '2024-10-17 14:40:43'),
(204, 'ln000004', 14.00, '2024-10-31', 'paid', 4.00, 10.00, '2024-10-17 14:40:43'),
(205, 'ln000004', 14.00, '2024-11-07', 'paid', 4.00, 10.00, '2024-10-17 14:40:43'),
(206, 'ln000004', 14.00, '2024-11-14', 'paid', 4.00, 10.00, '2024-10-17 14:40:43'),
(207, 'ln000004', 14.00, '2024-11-21', 'paid', 4.00, 10.00, '2024-10-17 14:40:43'),
(208, 'ln000004', 14.00, '2024-11-28', 'not paid', 4.00, 10.00, '2024-10-17 14:40:43'),
(209, 'ln000004', 14.00, '2024-12-05', 'not paid', 4.00, 10.00, '2024-10-17 14:40:43'),
(210, 'ln000004', 14.00, '2024-12-12', 'not paid', 4.00, 10.00, '2024-10-17 14:40:43'),
(211, 'ln000004', 14.00, '2024-12-19', 'not paid', 4.00, 10.00, '2024-10-17 14:40:43'),
(212, 'ln000004', 14.00, '2024-12-26', 'not paid', 4.00, 10.00, '2024-10-17 14:40:43');

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
(1, '4');

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
('ln000003', 'cl000004', '27871610', 30000.00, 'fare to india', 1, 'Month', '2024-10-17', 'bike', 60000.00, 'uploads/pic1_6711117aa4e15.jpeg', 'uploads/pic2_6711117aa53aa.jpeg', NULL, NULL, NULL, NULL, 'uploads/signed_application_6711117aa57be.jpeg', 'Active', 'admin', '2024-10-17 13:34:39', '2024-10-17 13:34:39'),
('ln000004', 'cl000004', '27871610', 100.00, 'food', 10, 'Week', '2024-10-17', 'phone', 1000.00, 'uploads/pic1_671121dd9dcd9.jpg', 'uploads/pic2_671121dda1ccd.jpg', NULL, NULL, NULL, NULL, 'uploads/signed_application_671121dda5711.jpg', 'Active', 'admin', '2024-10-17 14:40:43', '2024-10-17 14:40:43');

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
(207, 49, 'Hi, Brian Omachi! Your application has been received and is awaiting approval.', 0, '2024-10-17 13:30:39', 'Loan Application for Brian Omachi'),
(208, 46, 'Loan application has been received and is awaiting approval by an authorised administrator.', 0, '2024-10-17 13:30:39', 'Loan Application for Brian Omachi'),
(209, 17, 'Loan application has been received and is awaiting approval. Please APPROVE.', 0, '2024-10-17 13:30:39', 'Loan Application for Brian Omachi'),
(210, 46, 'Loan application has been received and is awaiting approval. Please APPROVE.', 0, '2024-10-17 13:30:39', 'Loan Application for Brian Omachi'),
(211, 48, 'Loan application has been received and is awaiting approval. Please APPROVE.', 0, '2024-10-17 13:30:39', 'Loan Application for Brian Omachi'),
(212, 49, 'Welcome, Brian Omachi! Your loan account has been credited.', 0, '2024-10-17 13:34:39', 'Loan Approved!'),
(213, 49, 'Hi, Brian Omachi! Your application has been received and is awaiting approval.', 0, '2024-10-17 14:40:29', 'Loan Application for Brian Omachi'),
(214, 46, 'Loan application has been received and is awaiting approval by an authorised administrator.', 0, '2024-10-17 14:40:29', 'Loan Application for Brian Omachi'),
(215, 17, 'Loan application has been received and is awaiting approval. Please APPROVE.', 0, '2024-10-17 14:40:29', 'Loan Application for Brian Omachi'),
(216, 46, 'Loan application has been received and is awaiting approval. Please APPROVE.', 0, '2024-10-17 14:40:29', 'Loan Application for Brian Omachi'),
(217, 48, 'Loan application has been received and is awaiting approval. Please APPROVE.', 0, '2024-10-17 14:40:29', 'Loan Application for Brian Omachi'),
(218, 49, 'Welcome, Brian Omachi! Your loan account has been credited.', 0, '2024-10-17 14:40:43', 'Loan Approved!'),
(219, 49, 'Hi, Brian Omachi! Your payment of 14 has been received.', 0, '2024-10-17 15:01:46', 'Payment Received for Brian Omachi'),
(220, 17, 'Brian Omachi has made a payment of 14 . Please review the transaction.', 0, '2024-10-17 15:01:46', 'Payment Received for Brian Omachi'),
(221, 46, 'Brian Omachi has made a payment of 14 . Please review the transaction.', 0, '2024-10-17 15:01:46', 'Payment Received for Brian Omachi'),
(222, 48, 'Brian Omachi has made a payment of 14 . Please review the transaction.', 0, '2024-10-17 15:01:46', 'Payment Received for Brian Omachi'),
(223, 49, 'Hi, Brian Omachi! Your payment of 14 has been received.', 0, '2024-10-17 15:19:00', 'Payment Received for Brian Omachi'),
(224, 17, 'Brian Omachi has made a payment of 14 . Please review the transaction.', 0, '2024-10-17 15:19:00', 'Payment Received for Brian Omachi'),
(225, 46, 'Brian Omachi has made a payment of 14 . Please review the transaction.', 0, '2024-10-17 15:19:00', 'Payment Received for Brian Omachi'),
(226, 48, 'Brian Omachi has made a payment of 14 . Please review the transaction.', 0, '2024-10-17 15:19:00', 'Payment Received for Brian Omachi');

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
(46, NULL, 2, 2, '2024-10-15', 30000.00, 30000.00, 'Disbursement to client Brian Omachi for loan number ln000002'),
(47, NULL, 1, 2, '2024-10-15', 6000.00, 6000.00, 'Payment towards earned interest for loan ln000002'),
(48, NULL, 1, 2, '2024-10-15', 30000.00, 30000.00, 'Payment towards principal for loan ln000002'),
(49, NULL, 4, 2, '2024-10-15', 18000.00, 18000.00, 'recording accrued interest ln000002'),
(50, NULL, 2, 3, '2024-10-17', 30000.00, 30000.00, 'Disbursement to client Brian Omachi for loan number ln000003'),
(51, NULL, 2, 4, '2024-10-17', 100.00, 100.00, 'Disbursement to client Brian Omachi for loan number ln000004'),
(52, NULL, 1, 4, '2024-10-17', 4.00, 4.00, 'Payment towards earned interest for loan ln000004'),
(53, NULL, 1, 4, '2024-10-17', 10.00, 10.00, 'Payment towards principal for loan ln000004'),
(54, NULL, 1, 4, '2024-10-17', 4.00, 4.00, 'Payment towards earned interest for loan ln000004'),
(55, NULL, 1, 4, '2024-10-17', 10.00, 10.00, 'Payment towards principal for loan ln000004'),
(56, NULL, 1, 4, '2024-10-17', 4.00, 4.00, 'Payment towards earned interest for loan ln000004'),
(57, NULL, 1, 4, '2024-10-17', 10.00, 10.00, 'Payment towards principal for loan ln000004'),
(58, NULL, 1, 4, '2024-10-17', 4.00, 4.00, 'Payment towards earned interest for loan ln000004'),
(59, NULL, 1, 4, '2024-10-17', 10.00, 10.00, 'Payment towards principal for loan ln000004'),
(60, NULL, 1, 4, '2024-10-17', 4.00, 4.00, 'Payment towards earned interest for loan ln000004'),
(61, NULL, 1, 4, '2024-10-17', 10.00, 10.00, 'Payment towards principal for loan ln000004');

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
(81, 46, 11, 30000.00, 'C', NULL),
(82, 46, 12, 30000.00, 'D', NULL),
(83, 47, 11, 6000.00, 'D', NULL),
(84, 47, 13, 6000.00, 'C', NULL),
(85, 48, 11, 30000.00, 'D', NULL),
(86, 48, 12, 30000.00, 'C', NULL),
(87, 49, 13, 18000.00, 'D', NULL),
(88, 49, 1, 18000.00, 'C', NULL),
(89, 50, 11, 30000.00, 'C', NULL),
(90, 50, 12, 30000.00, 'D', NULL),
(91, 51, 11, 100.00, 'C', NULL),
(92, 51, 12, 100.00, 'D', NULL),
(93, 52, 11, 4.00, 'D', NULL),
(94, 52, 13, 4.00, 'C', NULL),
(95, 53, 11, 10.00, 'D', NULL),
(96, 53, 12, 10.00, 'C', NULL),
(97, 54, 11, 4.00, 'D', NULL),
(98, 54, 13, 4.00, 'C', NULL),
(99, 55, 11, 10.00, 'D', NULL),
(100, 55, 12, 10.00, 'C', NULL),
(101, 56, 11, 4.00, 'D', NULL),
(102, 56, 13, 4.00, 'C', NULL),
(103, 57, 11, 10.00, 'D', NULL),
(104, 57, 12, 10.00, 'C', NULL),
(105, 58, 11, 4.00, 'D', NULL),
(106, 58, 13, 4.00, 'C', NULL),
(107, 59, 11, 10.00, 'D', NULL),
(108, 59, 12, 10.00, 'C', NULL),
(109, 60, 11, 4.00, 'D', NULL),
(110, 60, 13, 4.00, 'C', NULL),
(111, 61, 11, 10.00, 'D', NULL),
(112, 61, 12, 10.00, 'C', NULL);

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
(22, '2024-10-14 09:26:10', 1, '41.90.40.128', 'admin', 'https://portal.omabracredit.co.ke/accounts/entries/delete/receipt/41', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 2'),
(23, '2024-10-14 09:26:17', 1, '41.90.40.128', 'admin', 'https://portal.omabracredit.co.ke/accounts/entries/delete/receipt/42', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 2'),
(24, '2024-10-15 17:32:41', 1, '41.90.40.128', 'admin', 'https://portal.omabracredit.co.ke/accounts/entries/delete/receipt/44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 4'),
(25, '2024-10-15 17:32:44', 1, '41.90.40.128', 'admin', 'https://portal.omabracredit.co.ke/accounts/entries/delete/receipt/45', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Receipt entry numbered 4'),
(26, '2024-10-15 17:32:48', 1, '41.90.40.128', 'admin', 'https://portal.omabracredit.co.ke/accounts/entries/delete/payment/43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Deleted Payment entry numbered 4'),
(27, '2024-10-15 18:22:16', 1, '41.90.40.128', 'admin', 'https://portal.omabracredit.co.ke/accounts/entries/add/journal', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Sa', 'Added Journal entry numbered 2');

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
(1, 'Omabra Credit', '', 'support@omabracredit.co.ke', '2024-01-01', '2024-12-31', 'Kes', '###,###.##', 2, 'd-M-Y|dd-M-yy', 'UTC', 0, 0, 1, 'Smtp', '', 0, 0, '', '', '', 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 'P', 'H', 6, NULL);

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
(1, 'omabraaccounts2024', 'Database/Mysql', 'omabracr_loan-management-system', 'localhost', 3306, 'omabracr', 'RA%E.~P+zHd6', 'oc24', '0', '', NULL, '', NULL, NULL, NULL, 0, NULL);

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
(1, 'Omabra Credit', 'drcr', 1, 10, 0, 1, 0, 'Smtp', 'mail.omabracredit.co.ke', 465, 0, 'notifications@omabracredit.co.ke', 'b2Ps]s-}?CB.', 'Omabra Credit', NULL);

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
(1, 'admin', '83dc30bda3e4745d34bb387d03a7593b53b019f0', 'Omabra Credit', 'notifications@omabracredit.co.ke', 'UTC', 'admin', 1, '', 1, 1, 0, 1, 0, NULL);

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
  `amount` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `loan_id`, `national_id`, `transaction_reference`, `payment_mode`, `payment_date`, `amount`, `created_at`) VALUES
(28, 'ln000004', '254712752616', 'SJH0FKP4JM', 'MPESA', '2024-10-17', 14.00, '2024-10-17 09:37:39'),
(29, 'ln000004', '254712752616', 'SJH7FO07Z1', 'MPESA', '2024-10-17', 14.00, '2024-10-17 09:37:58'),
(31, 'ln000004', '254708374149', 'LGR519G9G4', 'MPESA', '2023-10-15', 10.00, '2024-10-17 12:26:35'),
(32, 'ln000004', '254712752616', 'LGR519G9t7', 'MPESA', '2023-10-15', 10.00, '2024-10-17 12:42:34'),
(33, 'ln000004', '254712752616', 'LGR819G9t7', 'MPESA', '2023-10-15', 10.00, '2024-10-17 15:05:45'),
(34, 'ln000004', '254712752616', 'LGy819G9t7', 'MPESA', '2023-10-15', 10.00, '2024-10-17 15:08:59'),
(35, 'ln000004', '254712752616', 'kLGy819G9t7', 'MPESA', '2023-10-15', 10.00, '2024-10-17 18:24:07'),
(36, 'ln000004', '254717158091', 'SJI5H3LYC3', 'MPESA', '2024-10-18', 1.00, '2024-10-17 20:25:06');

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
(11, 'ln000003', 'feab-4d34-8ffb-457d02cd9bfc99868113', 'ws_CO_17102024163723648712752616', '2024-10-17 13:37:23'),
(12, 'ln000004', 'e1f1-482d-9538-d140c330be9b91173616', 'ws_CO_17102024174208255712752616', '2024-10-17 14:42:08'),
(13, 'ln000004', '38d5-4ca6-b9c9-0240a9781f7a67662702', 'ws_CO_17102024174342178712752616', '2024-10-17 14:43:42'),
(14, 'ln000004', 'dab3-4cb9-9110-99918efd35e372584263', 'ws_CO_17102024174922035712752616', '2024-10-17 14:49:22'),
(15, 'ln000004', '4f9d-4622-a0da-1c77977dad0c72654422', 'ws_CO_17102024174922048712752616', '2024-10-17 14:49:23'),
(16, 'ln000004', '60e4-4f14-997e-f04c8c4f586d64162253', 'ws_CO_17102024175333717712752616', '2024-10-17 14:53:33'),
(17, 'ln000004', '6771-4f62-b538-7b914275850587571003', 'ws_CO_17102024175403617717158091', '2024-10-17 14:54:03'),
(18, 'ln000004', 'dab3-4cb9-9110-99918efd35e372613688', 'ws_CO_17102024180133813717158091', '2024-10-17 15:01:33'),
(19, 'ln000004', 'dab3-4cb9-9110-99918efd35e372654152', 'ws_CO_17102024181834777717158091', '2024-10-17 15:18:34'),
(20, 'ln000004', 'e1f1-482d-9538-d140c330be9b92321974', 'ws_CO_18102024052331219717158091', '2024-10-18 02:23:30'),
(21, 'ln000004', 'c687-4e11-bbe3-3e2fd41c991165233638', 'ws_CO_18102024052449849717158091', '2024-10-18 02:24:49'),
(22, 'ln000003', 'a5e9-4493-bf74-289c5402a31885285757', 'ws_CO_18102024053743283717158091', '2024-10-18 02:37:42'),
(23, 'ln000004', '0a83-4731-ac2e-58dccc54ee5992670224', 'ws_CO_18102024061314667717158091', '2024-10-18 03:13:14');

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
(17, 'ADMIN', '001', 'ad001', 'support@omabracredit.co.ke', '$2y$10$Ov7qrLEA2VsZic5r4Xix1ew8QGfwUVkcZsfw.i2/kyDl3uQ5.Frli', 'admin', '', '2024-09-04 11:15:01'),
(46, 'Joseph', 'Samwa', 'ad002', 'joseph.lweya@gmail.com', '$2y$10$jJREKns038DT0B484QZOy.ZTPNHGa.ijB5uOblUWZ5J9tTVhdkRK.', 'admin', NULL, '2024-10-12 23:12:18'),
(48, 'Brian', 'Omachi', 'bo001', 'omachibrian5@gmail.com', '$2y$10$DBAWBkirNjUNaIjUybYh5eroeBq7qrvoM4RSd8sfEgpMt/Y3eXE.G', 'admin', NULL, '2024-10-14 06:44:13'),
(49, 'Brian', 'Omachi', 'cl000004', 'omachibrain5@gmail.com', '$2y$10$JK35qfNbnTkSFav5d0wo0eloNz5Fv4gW2VDkUtCe1I5umXXs2K2Bq', 'client', NULL, '2024-10-14 07:02:02');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=227;

--
-- AUTO_INCREMENT for table `oc24entries`
--
ALTER TABLE `oc24entries`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `oc24entryitems`
--
ALTER TABLE `oc24entryitems`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

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
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
