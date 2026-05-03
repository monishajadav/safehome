-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 26, 2026 at 07:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `safehome_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$P.TakTm90QhhQqxa2SpCruss02T/Eue5Q2IR6SoMYSBARdXVWbOkW', 'admin@safehome.org', '2026-02-10 13:16:05');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `message`) VALUES
(1, 'monisha', 'moni25jadav@gmail.com', '8310415946', 'hi'),
(2, 'Monisha Jadav A.S', 'monisha25jadav@gmail.com', '8310415946', 'Subject: Volunteer Opportunity\nVolunteer Interest: Yes\n\nhey i want to join '),
(3, 'Monisha Jadav A.S', 'monisha25jadav@gmail.com', '8310415946', 'Subject: Volunteer Opportunity\nVolunteer Interest: Yes\n\nhey i want to join '),
(4, 'Monisha Jadav A.S', 'monisha25jadav@gmail.com', '8310415946', 'Subject: Volunteer Opportunity\nVolunteer Interest: Yes\n\nhey i want to join '),
(5, 'sima', 'sima@gmail.com', '9036933294', 'Subject: General Inquiry\nVolunteer Interest: No\n\nneeded internship'),
(6, 'aishwarya', 'aishwarya@gmail.com', '987654321', 'Subject: General Inquiry\nVolunteer Interest: No\n\nkmjbdesewswesredtrftyvygvuhbu');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `donation_type` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `payment_id`, `full_name`, `email`, `phone`, `amount`, `donation_type`, `payment_method`, `message`, `status`, `created_at`) VALUES
(1, NULL, 'PAY_1776707093744_GQA4D0', NULL, NULL, 0, 'ha_jadmonis', NULL, NULL, 'hajadavas@gmail.com82481', '5813-06-04 28:31:11'),
(2, 'PAY_1777133519329_OS2RGB', 'samiksha', 'jadavmonisha418@gmail.com', '9036933294', 5000.00, 'Elder Care', 'UPI', '', 'completed', '2026-04-25 21:45:17'),
(3, 'PAY_1777133787803_1FXL8A', 'samiksha', 'jadavmonisha418@gmail.com', '9036933294', 5000.00, 'Elder Care', 'UPI', '', 'completed', '2026-04-25 21:46:30'),
(4, 'PAY_1777134173008_AMKL23', 'samiksha', 'samiimenon@gmail.com', '9036933294', 1000.00, 'Food & Nutrition', 'UPI', '', 'completed', '2026-04-25 21:52:55'),
(5, 'PAY_1777141637959_ZR6JUS', 'samiksha', 'jadavmonisha418@gmail.com', '9082800656', 500.00, 'Elder Care', 'UPI', 'jhgguf', 'completed', '2026-04-25 23:57:20');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `submission_type` varchar(20) NOT NULL,
  `message` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `time_spent` varchar(100) DEFAULT NULL,
  `enjoyed_most` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT 'General',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `image_path`, `category`, `uploaded_at`) VALUES
(1, 'Donation', 'Our orphan and old age home gladly accepts donations from generous individuals to support and care for our residents.', 'uploads/gallery/1775789569_69d866010e17f.jpg', 'General', '2026-04-10 02:52:49'),
(2, 'games', 'qwerr', 'uploads/gallery/1776676558_69e5eece25302.jpg', 'Child Welfare', '2026-04-20 09:15:58'),
(3, 'elder', 'elders disscussion', 'uploads/gallery/1777142396_69ed0a7c06e7f.png', 'Elder Care', '2026-04-25 18:39:56'),
(4, 'ngo', 'discussion', 'uploads/gallery/1777223583_69ee479f2001e.jpg', 'General', '2026-04-26 17:13:03');

-- --------------------------------------------------------

--
-- Table structure for table `guidelines`
--

CREATE TABLE `guidelines` (
  `id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `guideline_title` varchar(200) NOT NULL,
  `guideline_content` text NOT NULL,
  `icon` varchar(50) DEFAULT 'bi-info-circle',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guidelines`
--

INSERT INTO `guidelines` (`id`, `category`, `guideline_title`, `guideline_content`, `icon`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'General', 'Code of Conduct', 'All participants must treat everyone with respect and dignity. Discrimination, harassment, or inappropriate behavior of any kind will not be tolerated. We maintain a safe, inclusive environment for all community members regardless of age, gender, race, religion, or background.', 'bi-shield-check', 1, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(2, 'General', 'Confidentiality & Privacy', 'Respect the privacy of all individuals. Do not share personal information, photographs, or stories about beneficiaries, volunteers, or staff without explicit written consent. All information encountered during your involvement must be kept confidential.', 'bi-lock-fill', 2, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(3, 'General', 'Safety & Security', 'Always follow safety protocols and emergency procedures. Report any unsafe conditions, accidents, or incidents immediately to staff. Ensure all doors and windows are secured. Be aware of fire exits and emergency contact numbers.', 'bi-exclamation-triangle-fill', 3, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(4, 'Volunteer', 'Attendance & Punctuality', 'Arrive on time for your scheduled shifts. If you cannot attend, notify us at least 24 hours in advance. Consistent attendance is crucial for maintaining program quality and beneficiary care. Three unexcused absences may result in termination of volunteer status.', 'bi-clock-fill', 4, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(5, 'Volunteer', 'Dress Code', 'Wear modest, comfortable, and appropriate clothing. Avoid revealing or offensive attire. Closed-toe shoes are required for safety. Remove any jewelry that could pose a safety risk. Name badges must be worn visibly at all times during your shift.', 'bi-person-badge-fill', 5, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(6, 'Volunteer', 'Professional Boundaries', 'Maintain appropriate professional relationships with beneficiaries. Do not exchange personal contact information or gifts. Never be alone with a child in a private area. All interactions should be observable and transparent.', 'bi-people-fill', 6, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(7, 'Volunteer', 'Supervision & Training', 'Complete all required training before starting volunteer work. Follow instructions from staff supervisors. Ask questions if unsure about any task. Participate in ongoing training and feedback sessions. Report concerns through proper channels.', 'bi-book-fill', 7, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(8, 'Donation', 'Accepted Donations', 'We accept monetary donations, new or gently used clothing, non-perishable food items, educational materials, and medical supplies. All items must be clean, functional, and in good condition. We reserve the right to refuse items that do not meet our standards.', 'bi-bag-check-fill', 8, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(9, 'Donation', 'Restricted Items', 'We cannot accept expired medications, used mattresses or pillows, broken electronics, opened cosmetics, alcohol, tobacco products, weapons, or any items that could pose health or safety risks. Used undergarments are not accepted for hygiene reasons.', 'bi-x-circle-fill', 9, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(11, 'Donation', 'Donation Process', 'Monetary donations can be made online through our website or by check. For in-kind donations, please contact us to schedule a drop-off time. Large donations may require pickup service (advance notice required). All donations become property of Safe & Home Foundation.', 'bi-credit-card-fill', 11, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(12, 'Visitor', 'Visiting Hours', 'Visitor hours are Monday to Saturday, 9:00 AM to 5:00 PM. Sundays and holidays require prior appointment. Check in at reception and receive a visitor badge. All visitors must be accompanied by staff during facility tours. Extended visits require advance approval.', 'bi-calendar-check-fill', 12, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(13, 'Visitor', 'Photography Policy', 'Photography and videography require written permission from management. Never photograph beneficiaries without consent forms signed by legal guardians. Media representatives must coordinate with our communications team. Social media posts must not reveal identifiable information.', 'bi-camera-fill', 13, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(14, 'Visitor', 'Health Requirements', 'Do not visit if you are experiencing symptoms of illness (fever, cough, cold). Hand sanitization is mandatory upon entry. Follow all hygiene protocols. Inform staff immediately if you feel unwell during your visit. Some areas may require additional protective equipment.', 'bi-heart-pulse-fill', 14, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(15, 'Child Welfare', 'Child Protection', 'Never be alone with a child in an enclosed space. All interactions must be transparent and observable. Use appropriate language and touch (high-fives, shoulder pats only). Report any concerning behavior immediately. Background checks are mandatory for anyone working with children.', 'bi-shield-shaded', 15, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(16, 'Child Welfare', 'Educational Support', 'Encourage learning through age-appropriate activities. Never use physical punishment or harsh language. Reward positive behavior with praise and encouragement. Support homework and reading activities. Maintain consistent rules and expectations.', 'bi-mortarboard-fill', 16, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(17, 'Elder Care', 'Dignity & Respect', 'Always address elders respectfully using appropriate titles. Listen patiently and speak clearly. Respect their independence and choices. Never infantilize or patronize elderly residents. Include them in decision-making about their care whenever possible.', 'bi-heart-fill', 17, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(18, 'Elder Care', 'Physical Assistance', 'Never attempt to lift or move residents without proper training. Always ask before providing physical assistance. Use assistive devices as instructed. Report any changes in mobility or health status. Follow individual care plans strictly.', 'bi-person-walking', 18, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(19, 'Elder Care', 'Medication Safety', 'Only trained and authorized staff may handle medications. Never give residents any medication without proper authorization. Report all medication errors immediately. Keep medications in locked cabinets. Follow precise dosing schedules.', 'bi-capsule', 19, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(20, 'Emergency', 'Emergency Contacts', 'Emergency Services: 112 | Foundation Office: +91 98765 43210 | Director: +91 91234 56789 | Medical Emergency: Contact on-duty staff immediately | Fire: Follow evacuation plan posted in each room', 'bi-telephone-fill', 20, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(21, 'Emergency', 'Evacuation Procedures', 'In case of fire or emergency, remain calm. Follow EXIT signs to nearest safe exit. Assist those with mobility issues. Gather at designated assembly point outside. Do not use elevators during fire emergencies. Wait for staff instructions before re-entering.', 'bi-sign-turn-right-fill', 21, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22'),
(22, 'Emergency', 'Incident Reporting', 'Report ALL incidents (accidents, injuries, inappropriate behavior, safety concerns) to staff immediately. Complete incident report forms within 24 hours. Provide detailed, factual information. Never discuss incidents with media or on social media.', 'bi-file-earmark-text-fill', 22, 1, '2026-02-21 17:23:22', '2026-02-21 17:23:22');

-- --------------------------------------------------------

--
-- Table structure for table `internship_applications`
--

CREATE TABLE `internship_applications` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `college` varchar(150) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year` varchar(20) NOT NULL,
  `area` varchar(100) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `applied_at` datetime DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internship_applications`
--

INSERT INTO `internship_applications` (`id`, `full_name`, `email`, `phone`, `college`, `course`, `year`, `area`, `duration`, `message`, `created_at`, `applied_at`, `status`) VALUES
(1, 'samiksha', 'samiimenon@gmail.com', '9082800656', 'alvas', 'bca', '3rd Year', 'Content & Media', '2 Months', 'good ngo', '2026-04-26 17:24:14', '2026-04-26 22:54:14', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `terms_conditions`
--

CREATE TABLE `terms_conditions` (
  `id` int(11) NOT NULL,
  `section_title` varchar(200) NOT NULL,
  `section_content` text NOT NULL,
  `section_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `terms_conditions`
--

INSERT INTO `terms_conditions` (`id`, `section_title`, `section_content`, `section_order`, `is_active`, `updated_at`) VALUES
(1, 'Introduction', 'Welcome to Safe & Home Foundation. By accessing our website and using our services, you agree to comply with and be bound by the following terms and conditions. Please read them carefully before using our services.', 1, 1, '2026-02-21 09:34:48'),
(2, 'Use of Website', 'This website is provided for informational purposes about our foundation, programs, and activities. You may not use this site for any illegal or unauthorized purpose. You must not transmit any worms, viruses, or any code of a destructive nature.', 2, 1, '2026-02-21 09:34:48'),
(3, 'Donations', 'All donations made to Safe & Home Foundation are voluntary and non-refundable. We ensure complete transparency in the use of donated funds for charitable activities including elder care, child welfare, and community programs. Donation receipts will be provided for tax purposes as per applicable laws.', 3, 1, '2026-02-21 09:34:48'),
(4, 'User Accounts', 'When you create an account with us, you must provide accurate and complete information. You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.', 4, 1, '2026-02-21 09:34:48'),
(5, 'Privacy Policy', 'We are committed to protecting your personal information. Any data collected through our website will be used solely for the purpose of our charitable activities and will not be shared with third parties without your consent. Please refer to our Privacy Policy for more details.', 5, 1, '2026-02-21 09:34:48'),
(6, 'Volunteer and Internship Programs', 'Participation in our volunteer and internship programs is subject to application approval. Volunteers must adhere to our code of conduct and safety guidelines. The foundation reserves the right to terminate participation if guidelines are violated.', 6, 1, '2026-02-21 09:34:48'),
(7, 'Intellectual Property', 'All content on this website, including text, graphics, logos, images, and software, is the property of Safe & Home Foundation and is protected by copyright laws. You may not reproduce, distribute, or use any content without our written permission.', 7, 1, '2026-02-21 09:34:48'),
(8, 'Limitation of Liability', 'Safe & Home Foundation shall not be liable for any direct, indirect, incidental, consequential, or punitive damages arising from your use of the website or services. We do not guarantee the accuracy or completeness of any information on this site.', 8, 1, '2026-02-21 09:34:48'),
(9, 'Changes to Terms', 'We reserve the right to modify these terms and conditions at any time. Changes will be effective immediately upon posting to the website. Your continued use of the site after changes constitutes acceptance of the modified terms.', 9, 1, '2026-02-21 09:34:48'),
(10, 'Contact Information', 'If you have any questions about these Terms and Conditions, please contact us at: Email: info@safehome.org | Phone: +91 98765 43210 | Address: #12, Hope Street, Bengaluru, Karnataka – 560001, India', 10, 1, '2026-02-21 09:34:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `user_type` enum('user','volunteer') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phone`, `user_type`, `created_at`, `reset_token`, `reset_expiry`, `profile_picture`) VALUES
(1, 'monisha', 'moni25jadav@gmail.com', '$2y$10$q0CUrNSeCKdHkfhkkAzeYevzyrk.1D5ysPmwTIiavjae66nuLcXQO', '8310415946', 'user', '2026-02-08 10:04:16', '663fab121283960463debc024587dae607f644b6ba8747d106ded33261316c28', '2026-02-11 08:56:19', NULL),
(2, 'krishnaveni', 'krishna@gmail.com', '$2y$10$PE2J1L/HtHKTtZaz5CV.AOSYomzBa5XvsdD9dW636S20RIhdUEi0K', '8310415946', 'user', '2026-02-21 04:15:56', NULL, NULL, NULL),
(3, 'prarthana', 'prarthana@gmail.com', '$2y$10$oXXj26jufu.A8z0VoaWRHuGIlGQn9bOm6OonM3CuVRqbQ2SG46ZIS', '8310415946', 'user', '2026-02-21 04:24:50', NULL, NULL, NULL),
(4, 'aishwarya', 'aishwarya@gmail.com', '$2y$10$twh.LOwr0WIn3YpkfEXnvOXMDI9ZinNUC1rPpoqh109zfr2yKqg0W', '987654321', 'user', '2026-02-22 15:33:11', NULL, NULL, NULL),
(5, 'xyz', 'xyz@gmail.com', '$2y$10$msBTPXQFKpf1LZMY8xjxFe9I48Sg640fV1.FFq/tdjk43nvcUMUKy', '8310415946', 'user', '2026-02-22 15:52:18', NULL, NULL, NULL),
(6, 'dilip', 'dilip@gmail.com', '$2y$10$YjEJFWPMk7T51dEI.olT4OJUnVnC4D8c7vuhUQA8qXnwxrYb5J5UO', '8248162685', 'user', '2026-04-04 11:16:25', NULL, NULL, NULL),
(8, 'nishas', 'moni@gmail.com', '$2y$10$wU1ni.Z5f//vqMH202.7yuxc9fIoaZwt/WPY7hHbwmv0V9FmVnzUy', '8310415946', 'user', '2026-04-18 08:35:08', '0b86816011b63ed4857c61977ec1d70f6dfd97ff506458af5659c8f47596ab54', '2026-04-18 12:36:16', NULL),
(9, 'monisha_jad', 'monishajadavas@gmail.com', '$2y$10$L4QZJfT2OEkqNys671C2ouItICQtXTcrr9VDi4PQOCGB31IrVmuzi', '8310415946', 'user', '2026-04-19 16:39:33', NULL, NULL, NULL),
(11, 'aishwaryaa', 'aishhuuu22@gmail.com', '$2y$10$lNBKWOYQaJEDMv9B1XUfMOwgbL4S3GMfOp0HscVzChPfCzer9h0H2', '9082800656', 'user', '2026-04-20 10:50:09', NULL, NULL, NULL),
(13, 'shivani', 'monishajadav7@gmail.com', '$2y$10$dlqsPkeUl0QFIwnnPbYHVu1s08AgQXYASkM.CrOs7j4JdrCvB9Pve', '8310415946', 'user', '2026-04-24 15:43:45', NULL, NULL, NULL),
(14, 'nikhil', 'jadavmonisha418@gmail.com', '$2y$10$skrcr.dk9ptfjvgMaWxKKe/GeogymvSanabZB2v9NNqp0RR41lP7.', '9036933294', 'user', '2026-04-25 09:44:22', NULL, NULL, NULL),
(15, 'samiksha', 'samiimenon@gmail.com', '$2y$10$ytmZ8UhxOy3HNFT1Xb3YbO/SX5.J4QvKycmmRmXjJyH1DsHwT2VtS', '', 'user', '2026-04-25 15:26:18', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_applications`
--

CREATE TABLE `volunteer_applications` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `care_area` varchar(50) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer_applications`
--

INSERT INTO `volunteer_applications` (`id`, `name`, `email`, `phone`, `care_area`, `message`, `created_at`, `status`) VALUES
(1, 'samiksha', 'samiimenon@gmail.com', '9082800656', 'Orphan Care', 'i am very interested in serving people', '2026-04-25 16:07:14', 'pending'),
(2, 'Monisha Jadav', 'jadavmonisha418@gmail.com', '8248162685', 'Both', '', '2026-04-26 16:47:08', 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guidelines`
--
ALTER TABLE `guidelines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `internship_applications`
--
ALTER TABLE `internship_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terms_conditions`
--
ALTER TABLE `terms_conditions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `volunteer_applications`
--
ALTER TABLE `volunteer_applications`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `guidelines`
--
ALTER TABLE `guidelines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `internship_applications`
--
ALTER TABLE `internship_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `terms_conditions`
--
ALTER TABLE `terms_conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `volunteer_applications`
--
ALTER TABLE `volunteer_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
