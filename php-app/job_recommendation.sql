-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2026 at 09:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_recommendation`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `notification_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`notification_id`, `job_id`, `application_id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 6, 18, 8, 'New application received for backend developer from nayan ', 1, '2026-06-17 02:11:36'),
(2, 409, 19, 6, 'New application received for Laravel Backend Developer from binita ', 1, '2026-06-17 02:17:18'),
(3, 417, 20, 6, 'New application received for WordPress Developer from binita ', 0, '2026-06-17 02:32:33'),
(4, 6, 21, 6, 'New application received for backend developer from binita ', 0, '2026-06-17 02:35:05'),
(5, 414, 22, 7, 'New application received for Account Executive from gita sapkota', 1, '2026-06-17 02:39:36');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `status` enum('Applied','Reviewed','Selected','Rejected') DEFAULT 'Applied',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resume_id` int(11) DEFAULT NULL,
  `match_score` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `user_id`, `job_id`, `status`, `applied_at`, `resume_id`, `match_score`) VALUES
(1, 2, 1, 'Applied', '2026-05-25 10:07:40', NULL, 0.00),
(2, 3, 2, 'Selected', '2026-05-25 10:44:34', NULL, 0.00),
(3, 1, 1, 'Applied', '2026-05-25 13:24:40', NULL, 0.00),
(4, 1, 3, 'Applied', '2026-05-26 02:13:40', NULL, 0.00),
(5, 1, 2, '', '2026-05-26 03:01:48', NULL, 0.00),
(6, 1, 4, 'Applied', '2026-05-29 07:55:16', NULL, 0.00),
(7, 6, 4, 'Applied', '2026-05-30 13:08:56', NULL, 0.00),
(8, 6, 5, 'Applied', '2026-06-01 03:33:59', NULL, 0.00),
(9, 6, 1, 'Applied', '2026-06-03 13:59:04', NULL, 0.00),
(10, 7, 1, 'Applied', '2026-06-04 01:48:29', NULL, 0.00),
(11, 8, 414, 'Rejected', '2026-06-08 15:39:29', 53, 0.00),
(12, 8, 1, 'Applied', '2026-06-08 15:39:47', 53, 0.00),
(13, 8, 3, 'Applied', '2026-06-08 15:39:55', 53, 0.00),
(14, 8, 398, 'Applied', '2026-06-11 14:29:47', 56, 40.00),
(15, 6, 392, 'Rejected', '2026-06-11 14:36:15', 58, 85.00),
(16, 6, 398, 'Selected', '2026-06-11 14:42:20', 59, 100.00),
(17, 9, 2, '', '2026-06-15 13:22:43', 61, 35.00),
(18, 8, 6, 'Applied', '2026-06-17 02:11:36', 56, 50.00),
(19, 6, 409, '', '2026-06-17 02:17:18', 59, 23.30),
(20, 6, 417, '', '2026-06-17 02:32:33', 63, 100.00),
(21, 6, 6, 'Applied', '2026-06-17 02:35:05', 64, 100.00),
(22, 7, 414, '', '2026-06-17 02:39:36', 66, 33.33);

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`message_id`, `user_id`, `sender`, `message`, `created_at`) VALUES
(1, 8, 'user-msg', '📄 CVofasmita.pdf', '2026-06-08 15:11:31'),
(2, 8, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-08 15:11:31'),
(3, 8, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>80%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'>Excel</div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add projects section., Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3>\r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>computer tech</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: computer user • 📍 banepa</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=5\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>teacher</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: vs college • 📍 bhaktapur</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        50% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=7\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Account Executive</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: KumariJob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        33.33% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=414\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Data Analyst Intern</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: Data Vision • 📍 Remote</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        25% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=3\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Data Scientist</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: JobsNepal • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        25% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=393\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        ', '2026-06-08 15:11:31'),
(4, 8, 'user-msg', 'Improve my resume', '2026-06-08 15:11:37'),
(5, 8, 'bot-msg', '\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;\'>\r\n        <h3>Resume Improvement Tips</h3>\r\n        <ul>\r\n            <li>Add a clear Skills section.</li>\r\n            <li>Mention your education and experience clearly.</li>\r\n            <li>Add projects related to your job field.</li>\r\n            <li>Include email and phone number.</li>\r\n            <li>Use job-related keywords like PHP, MySQL, Python, Excel, etc.</li>\r\n            <li>Use a text-based PDF, not scanned image PDF.</li>\r\n        </ul>\r\n    </div>', '2026-06-08 15:11:37'),
(6, 8, 'user-msg', 'Suggest jobs for me', '2026-06-08 15:11:38'),
(7, 8, 'bot-msg', '<h3>Recommended Jobs</h3>\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;\'>\r\n        <b>computer tech</b><br>\r\n        Company: computer user<br>\r\n        Location: banepa<br>\r\n        Match Score: 54.53%<br>\r\n        Missing Skills: No major missing skills<br><br>\r\n        <a href=\'apply_job.php?job_id=5\'>Apply Now</a>\r\n    </div>\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;\'>\r\n        <b>Data Analyst Intern</b><br>\r\n        Company: Data Vision<br>\r\n        Location: Remote<br>\r\n        Match Score: 33.24%<br>\r\n        Missing Skills: sql, python, data analysis<br><br>\r\n        <a href=\'apply_job.php?job_id=3\'>Apply Now</a>\r\n    </div>\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;\'>\r\n        <b>teacher</b><br>\r\n        Company: vs college<br>\r\n        Location: bhaktapur<br>\r\n        Match Score: 27.6%<br>\r\n        Missing Skills: ms-word english<br><br>\r\n        <a href=\'apply_job.php?job_id=7\'>Apply Now</a>\r\n    </div>\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;\'>\r\n        <b>Account Executive</b><br>\r\n        Company: KumariJob<br>\r\n        Location: Kathmandu<br>\r\n        Match Score: 24.01%<br>\r\n        Missing Skills: accounting, finance<br><br>\r\n        <a href=\'apply_job.php?job_id=414\'>Apply Now</a>\r\n    </div>\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;\'>\r\n        <b>Data Scientist</b><br>\r\n        Company: JobsNepal<br>\r\n        Location: Kathmandu<br>\r\n        Match Score: 23.9%<br>\r\n        Missing Skills: python, machine learning, sql<br><br>\r\n        <a href=\'apply_job.php?job_id=393\'>Apply Now</a>\r\n    </div>', '2026-06-08 15:11:38'),
(8, 8, 'user-msg', 'Check my ATS score', '2026-06-08 15:11:39'),
(9, 8, 'bot-msg', '\r\n    <div style=\'background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;border:1px solid #fed7aa;\'>\r\n        <strong>⚠ Invalid Input</strong><br><br>\r\n        Please enter your technical skills to get job recommendations.<br><br>\r\n        <b>Example:</b><br>\r\n        Python, HTML, CSS, MySQL\r\n    </div>', '2026-06-08 15:11:39'),
(10, 8, 'user-msg', '📄 Profile (6).pdf', '2026-06-08 15:11:45'),
(11, 8, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-08 15:11:45'),
(12, 8, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>80%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'></div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add projects section., Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3><div style=\'padding:20px; text-align:center; color:#6b7280; border:1px dashed #e5e7eb; border-radius:12px;\'>No direct matches found. Try updating your skills.</div>', '2026-06-08 15:11:45'),
(13, 8, 'user-msg', 'Check my ATS score', '2026-06-08 15:11:47'),
(14, 8, 'bot-msg', '\r\n    <div style=\'background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;border:1px solid #fed7aa;\'>\r\n        <strong>⚠ Invalid Input</strong><br><br>\r\n        Please enter your technical skills to get job recommendations.<br><br>\r\n        <b>Example:</b><br>\r\n        Python, HTML, CSS, MySQL\r\n    </div>', '2026-06-08 15:11:47'),
(15, 8, 'user-msg', '📄 Nayan Dheke (1).pdf', '2026-06-08 15:42:43'),
(16, 8, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-08 15:42:43'),
(17, 8, 'bot-msg', 'Resume rejected: This PDF is not accepted. Please upload a valid resume containing Education, Skills, Experience and Email.', '2026-06-08 15:42:43'),
(18, 8, 'user-msg', '📄 Nayan Dheke.pdf', '2026-06-08 15:42:48'),
(19, 8, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-08 15:42:48'),
(20, 8, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>85%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'>Python, Php, Sql, React, Node.Js, Git, Github</div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add education section.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3>\r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>DevOps Engineer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=395\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Python Django Engineer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: JobsNepal • 📍 Lalitpur</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        85% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=390\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Data Analyst Intern</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: Data Vision • 📍 Remote</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        50% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=3\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>backend developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: sajilo technology • 📍 Remote</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        50% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=6\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Data Scientist</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: JobsNepal • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        50% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=393\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>QA Automation Engineer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        50% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_job.php?job_id=400\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        ', '2026-06-08 15:42:48'),
(21, 8, 'user-msg', '📄 rupesh-3.pdf', '2026-06-11 14:06:42'),
(22, 8, 'bot-msg', 'Resume rejected: This PDF is not accepted. Please upload a valid resume containing Education, Skills, Experience and Email.', '2026-06-11 14:06:42'),
(23, 8, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-11 14:06:42'),
(24, 8, 'user-msg', '📄 Anish Resume.pdf', '2026-06-11 14:20:27'),
(25, 8, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-11 14:20:27'),
(26, 8, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>90%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'></div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3><div style=\'padding:20px; text-align:center; color:#6b7280; border:1px dashed #e5e7eb; border-radius:12px;\'>No direct matches found. Try updating your skills.</div>', '2026-06-11 14:20:27'),
(27, 8, 'user-msg', 'Suggest jobs for me', '2026-06-11 14:20:54'),
(28, 8, 'bot-msg', '\r\n    <div style=\'background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;border:1px solid #fed7aa;\'>\r\n        <strong>⚠ Invalid Input</strong><br><br>\r\n        Please enter your technical skills to get job recommendations.<br><br>\r\n        <b>Example:</b><br>\r\n        Python, HTML, CSS, MySQL\r\n    </div>', '2026-06-11 14:20:54'),
(29, 8, 'user-msg', 'Improve my resume', '2026-06-11 14:20:57'),
(30, 8, 'bot-msg', '\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;\'>\r\n        <h3>Resume Improvement Tips</h3>\r\n        <ul>\r\n            <li>Add a clear Skills section.</li>\r\n            <li>Mention your education and experience clearly.</li>\r\n            <li>Add projects related to your job field.</li>\r\n            <li>Include email and phone number.</li>\r\n            <li>Use job-related keywords like PHP, MySQL, Python, Excel, etc.</li>\r\n            <li>Use a text-based PDF, not scanned image PDF.</li>\r\n        </ul>\r\n    </div>', '2026-06-11 14:20:57'),
(31, 8, 'user-msg', '📄 rabindra-subedi.pdf', '2026-06-11 14:21:46'),
(32, 8, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-11 14:21:46'),
(33, 8, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>80%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'>Html, Mysql</div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add projects section., Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3>\r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>backend developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: sajilo technology • 📍 Remote</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        50% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=6&match_score=50\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>PHP Laravel Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Pokhara</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        50% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=392&match_score=50\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>WordPress Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: KumariJob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        50% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=417&match_score=50\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Full Stack Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        40% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=398&match_score=40\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>UI/UX Designer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: KumariJob • 📍 Bhaktapur</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        33.33% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=394&match_score=33.33\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Node.js Backend Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: JobsNepal • 📍 Lalitpur</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        33.33% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=396&match_score=33.33\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        ', '2026-06-11 14:21:46'),
(34, 6, 'user-msg', '📄 SabiwekResume.pdf', '2026-06-11 14:35:48'),
(35, 6, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-11 14:35:48'),
(36, 6, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>60%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'></div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add work experience section., Add projects section., Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3><div style=\'padding:20px; text-align:center; color:#6b7280; border:1px dashed #e5e7eb; border-radius:12px;\'>No direct matches found. Try updating your skills.</div>', '2026-06-11 14:35:48'),
(37, 6, 'user-msg', '📄 new cv.pdf', '2026-06-11 14:36:12'),
(38, 6, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-11 14:36:13'),
(39, 6, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>90%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'>Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github</div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add projects section.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3>\r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>backend developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: sajilo technology • 📍 Remote</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=6&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Mobile App Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: KumariJob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=397&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Full Stack Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=398&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>WordPress Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: KumariJob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=417&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>UI Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=418&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>PHP Laravel Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Pokhara</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        85% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=392&match_score=85\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        ', '2026-06-11 14:36:13'),
(40, 6, 'user-msg', '📄 new cv.pdf', '2026-06-11 14:37:14'),
(41, 6, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-11 14:37:14'),
(42, 6, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>90%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'>Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github</div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add projects section.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3>\r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>backend developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: sajilo technology • 📍 Remote</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=6&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Mobile App Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: KumariJob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=397&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Full Stack Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=398&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>WordPress Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: KumariJob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=417&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>UI Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=418&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>PHP Laravel Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Pokhara</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        85% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=392&match_score=85\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        ', '2026-06-11 14:37:14'),
(43, 9, 'user-msg', '📄 useragreement.pdf', '2026-06-15 13:20:08'),
(44, 9, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-15 13:20:10'),
(45, 9, 'bot-msg', 'AI server error. Please start Flask server.', '2026-06-15 13:20:10'),
(46, 9, 'user-msg', '📄 useragreement.pdf', '2026-06-15 13:20:28'),
(47, 9, 'bot-msg', 'Resume rejected: This PDF is not accepted. Please upload a text-based resume PDF.', '2026-06-15 13:20:29'),
(48, 9, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-15 13:20:29');
INSERT INTO `chat_messages` (`message_id`, `user_id`, `sender`, `message`, `created_at`) VALUES
(49, 9, 'user-msg', '📄 SabiwekResume.pdf', '2026-06-15 13:22:09'),
(50, 9, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-15 13:22:09'),
(51, 9, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>60%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'></div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add work experience section., Add projects section., Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3><div style=\'padding:20px; text-align:center; color:#6b7280; border:1px dashed #e5e7eb; border-radius:12px;\'>No direct matches found. Try updating your skills.</div>', '2026-06-15 13:22:09'),
(52, 9, 'user-msg', 'Improve my resume', '2026-06-15 13:22:17'),
(53, 9, 'bot-msg', '\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;\'>\r\n        <h3>Resume Improvement Tips</h3>\r\n        <ul>\r\n            <li>Add a clear Skills section.</li>\r\n            <li>Mention your education and experience clearly.</li>\r\n            <li>Add projects related to your job field.</li>\r\n            <li>Include email and phone number.</li>\r\n            <li>Use job-related keywords like PHP, MySQL, Python, Excel, etc.</li>\r\n            <li>Use a text-based PDF, not scanned image PDF.</li>\r\n        </ul>\r\n    </div>', '2026-06-15 13:22:17'),
(54, 9, 'user-msg', '📄 Resume_June 2024.pdf', '2026-06-15 13:22:38'),
(55, 9, 'bot-msg', '✅ Resume uploaded successfully', '2026-06-15 13:22:38'),
(56, 9, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>90%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'>Python</div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3>\r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Python Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: AI Soft • 📍 Lalitpur</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        35% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=2&match_score=35\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Python Django Engineer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: JobsNepal • 📍 Lalitpur</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        35% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=390&match_score=35\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>DevOps Engineer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        33.33% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=395&match_score=33.33\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Data Analyst Intern</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: Data Vision • 📍 Remote</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        25% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=3&match_score=25\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Data Scientist</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: JobsNepal • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        25% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=393&match_score=25\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: tech ai • 📍 bhaktapur</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        25% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=399&match_score=25\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        ', '2026-06-15 13:22:38'),
(57, 8, 'user-msg', 'Improve my resume', '2026-06-17 02:05:23'),
(58, 8, 'bot-msg', '\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;\'>\r\n        <h3>Resume Improvement Tips</h3>\r\n        <ul>\r\n            <li>Add a clear Skills section.</li>\r\n            <li>Mention your education and experience clearly.</li>\r\n            <li>Add projects related to your job field.</li>\r\n            <li>Include email and phone number.</li>\r\n            <li>Use job-related keywords like PHP, MySQL, Python, Excel, etc.</li>\r\n            <li>Use a text-based PDF, not scanned image PDF.</li>\r\n        </ul>\r\n    </div>', '2026-06-17 02:05:23'),
(59, 8, 'user-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><path d=\'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z\'/><polyline points=\'14 2 14 8 20 8\'/></svg> rabindra-subedi.pdf', '2026-06-17 02:09:59'),
(60, 8, 'bot-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><polyline points=\'20 6 9 17 4 12\'/></svg> Resume uploaded successfully', '2026-06-17 02:09:59'),
(61, 8, 'bot-msg', '<br />\n<b>Parse error</b>:  syntax error, unexpected integer &quot;12&quot;, expecting &quot;,&quot; or &quot;;&quot; in <b>C:\\xampp\\htdocs\\job-chatbot\\php-app\\upload_resume.php</b> on line <b>161</b><br />\n', '2026-06-17 02:09:59'),
(62, 6, 'user-msg', 'php', '2026-06-17 02:17:06'),
(63, 6, 'bot-msg', '<h3>Recommended Jobs</h3>\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;\'>\r\n        <b>PHP Laravel Developer</b><br>\r\n        Company: Merojob<br>\r\n        Location: Pokhara<br>\r\n        Match Score: 45.51%<br>\r\n        Missing Skills: laravel, mysql, html<br><br>\r\n        <a href=\'apply_suggested_job.php?job_id=392&match_score=45.51\'>Apply Now</a>\r\n    </div>\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;\'>\r\n        <b>backend developer</b><br>\r\n        Company: sajilo technology<br>\r\n        Location: Remote<br>\r\n        Match Score: 32.51%<br>\r\n        Missing Skills: mysql<br><br>\r\n        <a href=\'apply_suggested_job.php?job_id=6&match_score=32.51\'>Apply Now</a>\r\n    </div>\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;\'>\r\n        <b>Full Stack Developer</b><br>\r\n        Company: Merojob<br>\r\n        Location: Kathmandu<br>\r\n        Match Score: 25.74%<br>\r\n        Missing Skills: javascript, html, css, mysql<br><br>\r\n        <a href=\'apply_suggested_job.php?job_id=398&match_score=25.74\'>Apply Now</a>\r\n    </div>\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;\'>\r\n        <b>WordPress Developer</b><br>\r\n        Company: KumariJob<br>\r\n        Location: Kathmandu<br>\r\n        Match Score: 23.83%<br>\r\n        Missing Skills: mysql, css, html<br><br>\r\n        <a href=\'apply_suggested_job.php?job_id=417&match_score=23.83\'>Apply Now</a>\r\n    </div>\r\n    <div style=\'background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;\'>\r\n        <b>Laravel Backend Developer</b><br>\r\n        Company: Merojob<br>\r\n        Location: Pokhara<br>\r\n        Match Score: 23.3%<br>\r\n        Missing Skills: laravel, mysql, javascript<br><br>\r\n        <a href=\'apply_suggested_job.php?job_id=409&match_score=23.3\'>Apply Now</a>\r\n    </div>', '2026-06-17 02:17:06'),
(64, 6, 'user-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><path d=\'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z\'/><polyline points=\'14 2 14 8 20 8\'/></svg> Resume_June 2024.pdf', '2026-06-17 02:22:04'),
(65, 6, 'bot-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><polyline points=\'20 6 9 17 4 12\'/></svg> Resume uploaded successfully', '2026-06-17 02:22:04'),
(66, 6, 'bot-msg', '<br />\n<b>Parse error</b>:  syntax error, unexpected integer &quot;12&quot;, expecting &quot;,&quot; or &quot;;&quot; in <b>C:\\xampp\\htdocs\\job-chatbot\\php-app\\upload_resume.php</b> on line <b>161</b><br />\n', '2026-06-17 02:22:04'),
(67, 6, 'user-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><path d=\'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z\'/><polyline points=\'14 2 14 8 20 8\'/></svg> Sahil_NowCV.pdf', '2026-06-17 02:24:28'),
(68, 6, 'bot-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><polyline points=\'20 6 9 17 4 12\'/></svg> Resume uploaded successfully', '2026-06-17 02:24:28'),
(69, 6, 'bot-msg', '<br />\n<b>Parse error</b>:  syntax error, unexpected integer &quot;12&quot;, expecting &quot;,&quot; or &quot;;&quot; in <b>C:\\xampp\\htdocs\\job-chatbot\\php-app\\upload_resume.php</b> on line <b>161</b><br />\n', '2026-06-17 02:24:28'),
(70, 6, 'user-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><path d=\'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z\'/><polyline points=\'14 2 14 8 20 8\'/></svg> Ajay-resume-1.pdf', '2026-06-17 02:28:51'),
(71, 6, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>80%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'></div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add projects section., Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3><div style=\'padding:20px; text-align:center; color:#6b7280; border:1px dashed #e5e7eb; border-radius:12px;\'>No direct matches found. Try updating your skills.</div>', '2026-06-17 02:28:51'),
(72, 6, 'bot-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><polyline points=\'20 6 9 17 4 12\'/></svg> Resume uploaded successfully', '2026-06-17 02:28:51'),
(73, 6, 'user-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><path d=\'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z\'/><polyline points=\'14 2 14 8 20 8\'/></svg> new cv.pdf', '2026-06-17 02:29:23'),
(74, 6, 'bot-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><polyline points=\'20 6 9 17 4 12\'/></svg> Resume uploaded successfully', '2026-06-17 02:29:23'),
(75, 6, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>90%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'>Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github</div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add projects section.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3>\r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>backend developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: sajilo technology • 📍 Remote</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=6&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Mobile App Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: KumariJob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=397&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Full Stack Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=398&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>WordPress Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: KumariJob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=417&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>UI Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=418&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>PHP Laravel Developer</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: Merojob • 📍 Pokhara</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        85% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=392&match_score=85\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        ', '2026-06-17 02:29:23'),
(76, 6, 'user-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><path d=\'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z\'/><polyline points=\'14 2 14 8 20 8\'/></svg> Ajay-resume-1.pdf', '2026-06-17 02:34:03'),
(77, 6, 'bot-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><polyline points=\'20 6 9 17 4 12\'/></svg> Resume uploaded successfully', '2026-06-17 02:34:04'),
(78, 6, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>80%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'></div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add projects section., Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3><div style=\'padding:20px; text-align:center; color:#6b7280; border:1px dashed #e5e7eb; border-radius:12px;\'>No direct matches found. Try updating your skills.</div>', '2026-06-17 02:34:04'),
(79, 6, 'user-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><path d=\'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z\'/><polyline points=\'14 2 14 8 20 8\'/></svg> Ajay-resume-1.pdf', '2026-06-17 02:37:34'),
(80, 6, 'bot-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><polyline points=\'20 6 9 17 4 12\'/></svg> Resume uploaded successfully', '2026-06-17 02:37:34'),
(81, 6, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>80%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'></div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add projects section., Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3><div style=\'padding:20px; text-align:center; color:#6b7280; border:1px dashed #e5e7eb; border-radius:12px;\'>No direct matches found. Try updating your skills.</div>', '2026-06-17 02:37:34'),
(82, 7, 'user-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><path d=\'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z\'/><polyline points=\'14 2 14 8 20 8\'/></svg> Sandhya Pokharel.pdf', '2026-06-17 02:39:22'),
(83, 7, 'bot-msg', '<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><polyline points=\'20 6 9 17 4 12\'/></svg> Resume uploaded successfully', '2026-06-17 02:39:22'),
(84, 7, 'bot-msg', '\r\n    <div style=\'background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;\'>\r\n        <h3 style=\'color: #1d4ed8; margin-top:0;\'>Resume Analysis Result</h3>\r\n        <div style=\'display:flex; gap:20px; margin: 15px 0;\'>\r\n            <div style=\'background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;\'>\r\n                <div style=\'font-size:24px; font-weight:800; color:#1d4ed8;\'>80%</div>\r\n                <div style=\'font-size:12px; color:#6b7280;\'>ATS Compatibility</div>\r\n            </div>\r\n            <div style=\'background:#f0fdf4; padding:15px; border-radius:12px; flex:2;\'>\r\n                <div style=\'font-size:12px; font-weight:700; color:#166534;\'>EXTRACTED SKILLS</div>\r\n                <div style=\'font-size:13px; color:#374151; margin-top:4px;\'>Excel</div>\r\n            </div>\r\n        </div>\r\n        <div style=\'font-size:13px; color:#4b5563; line-height:1.6;\'>\r\n            <b>AI Suggestions:</b> Add projects section., Add more technical skills.\r\n        </div>\r\n    </div>\r\n<h3 style=\'margin-bottom:15px; font-size:16px;\'>AI Personalized Recommendations</h3>\r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>computer tech</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: computer user • 📍 banepa</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        100% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=5&match_score=100\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>teacher</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: vs college • 📍 bhaktapur</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        50% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=7&match_score=50\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Account Executive</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: KumariJob • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        33.33% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=414&match_score=33.33\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Data Analyst Intern</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Company: Data Vision • 📍 Remote</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#f0fdf4; color:#166534;\'>\r\n                        25% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=3&match_score=25\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        \r\n            <div style=\'background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;\'>\r\n                <div style=\'display:flex; justify-content:space-between; align-items:flex-start;\'>\r\n                    <div>\r\n                        <div style=\'font-weight:700; color:#111827; font-size:14px;\'>Data Scientist</div>\r\n                        <div style=\'font-size:12px; color:#6b7280; margin-top:2px;\'>Portal: JobsNepal • 📍 Kathmandu</div>\r\n                    </div>\r\n                    <div style=\'font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; background:#eff6ff; color:#1d4ed8;\'>\r\n                        25% Match\r\n                    </div>\r\n                </div>\r\n                <div style=\'margin-top:12px; display:flex; justify-content:flex-end;\'>\r\n                    <a href=\'apply_suggested_job.php?job_id=393&match_score=25\' style=\'font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;\'>View & Apply →</a>\r\n                </div>\r\n            </div>\r\n        ', '2026-06-17 02:39:22');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `required_skills` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `is_external` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `title`, `company`, `description`, `required_skills`, `location`, `salary`, `is_external`) VALUES
(1, 'Frontend Developer', 'Tech Nepal', 'Build responsive websites using HTML CSS JavaScript React', 'HTML,CSS,JavaScript,React', 'Kathmandu', 30000.00, 0),
(2, 'Python Developer', 'AI Soft', 'Develop backend APIs and automation tools using Python and MySQL', 'Python,Flask,MySQL,API', 'Lalitpur', 40000.00, 0),
(3, 'Data Analyst Intern', 'Data Vision', 'Analyze data using Excel SQL Python and visualization tools', 'Excel,SQL,Python,Data Analysis', 'Remote', 20000.00, 0),
(5, 'computer tech', 'computer user', 'data entry and used excel', 'excel', 'banepa', 35000.00, 0),
(6, 'backend developer', 'sajilo technology', 'haandle backend', 'php , mysql', 'Remote', 65650.00, 0),
(7, 'teacher', 'vs college', 'teaching computer ', 'excel , ms-word english  ', 'bhaktapur', 2490.00, 0),
(389, 'Senior Java Developer', 'Merojob', 'Work on high-scale systems', 'java, mysql, sql, spring', 'Kathmandu', 85000.00, 1),
(390, 'Python Django Engineer', 'JobsNepal', 'Build AI solutions', 'python, django, sql, git', 'Lalitpur', 70000.00, 1),
(391, 'Frontend React Specialist', 'KumariJob', 'Modern web interfaces', 'javascript, html, css, react', 'Remote', 60000.00, 1),
(392, 'PHP Laravel Developer', 'Merojob', 'E-commerce platforms', 'php, laravel, mysql, html', 'Pokhara', 55000.00, 1),
(393, 'Data Scientist', 'JobsNepal', 'Analyze big data', 'python, machine learning, sql, excel', 'Kathmandu', 90000.00, 1),
(394, 'UI/UX Designer', 'KumariJob', 'Creative design roles', 'photoshop, css, html', 'Bhaktapur', 45000.00, 1),
(395, 'DevOps Engineer', 'Merojob', 'Manage cloud infrastructure', 'git, python, sql', 'Kathmandu', 100000.00, 1),
(396, 'Node.js Backend Developer', 'JobsNepal', 'API development', 'javascript, node.js, mysql', 'Lalitpur', 75000.00, 1),
(397, 'Mobile App Developer', 'KumariJob', 'Flutter and Dart', 'javascript, git, css', 'Kathmandu', 65000.00, 1),
(398, 'Full Stack Developer', 'Merojob', 'Complete product lifecycle', 'php, javascript, html, css, mysql', 'Kathmandu', 80000.00, 1),
(399, 'developer', 'tech ai', 'develop website', 'python , css, html , tailwind ', 'bhaktapur', 46754.00, 0),
(400, 'QA Automation Engineer', 'Merojob', 'Ensure software quality through automated tests', 'selenium, python, git, java', 'Kathmandu', 65000.00, 1),
(401, 'React Native Developer', 'JobsNepal', 'Develop cross-platform mobile apps', 'javascript, react native, git, css', 'Remote', 75000.00, 1),
(402, 'HR Manager', 'KumariJob', 'Oversee recruitment and employee relations', 'communication, management, recruitment', 'Lalitpur', 50000.00, 1),
(403, 'Business Development Executive', 'Merojob', 'Identify new business opportunities', 'sales, marketing, communication', 'Kathmandu', 40000.00, 1),
(404, 'Network Administrator', 'JobsNepal', 'Maintain company networks and servers', 'cisco, sql, linux', 'Kathmandu', 55000.00, 1),
(405, 'Content Writer', 'KumariJob', 'Create engaging content for websites and blogs', 'content writing, marketing, communication', 'Remote', 30000.00, 1),
(406, 'Project Manager', 'Merojob', 'Lead software development projects', 'management, leadership, communication, git', 'Kathmandu', 90000.00, 1),
(407, 'Graphic Designer', 'JobsNepal', 'Design marketing materials and logos', 'photoshop, illustrator, css', 'Lalitpur', 35000.00, 1),
(408, 'Digital Marketing Specialist', 'KumariJob', 'Execute online marketing campaigns', 'marketing, social media, content writing', 'Kathmandu', 45000.00, 1),
(409, 'Laravel Backend Developer', 'Merojob', 'Build robust web applications', 'php, laravel, mysql, javascript', 'Pokhara', 60000.00, 1),
(410, 'Database Administrator', 'JobsNepal', 'Manage and optimize SQL databases', 'mysql, sql, linux', 'Kathmandu', 80000.00, 1),
(411, 'Frontend Vue.js Developer', 'KumariJob', 'Develop interactive user interfaces', 'javascript, html, css, vue.js', 'Kathmandu', 55000.00, 1),
(412, 'Machine Learning Engineer', 'Merojob', 'Develop and deploy ML models', 'python, machine learning, sql, flask', 'Kathmandu', 110000.00, 1),
(413, 'System Analyst', 'JobsNepal', 'Analyze and design software solutions', 'sql, management, communication', 'Lalitpur', 70000.00, 1),
(414, 'Account Executive', 'KumariJob', 'Manage client accounts and billing', 'excel, accounting, finance', 'Kathmandu', 38000.00, 1),
(415, 'IT Support Technician', 'Merojob', 'Provide technical assistance to users', 'computer tech, linux, cisco', 'Kathmandu', 32000.00, 1),
(416, 'Social Media Manager', 'JobsNepal', 'Manage company social media presence', 'social media, marketing, communication', 'Remote', 35000.00, 1),
(417, 'WordPress Developer', 'KumariJob', 'Build and customize WordPress websites', 'php, mysql, css, html', 'Kathmandu', 40000.00, 1),
(418, 'UI Developer', 'Merojob', 'Implement pixel-perfect designs', 'html, css, javascript, bootstrap', 'Kathmandu', 48000.00, 1),
(419, 'Cybersecurity Analyst', 'JobsNepal', 'Protect company systems from threats', 'linux, networking, sql', 'Kathmandu', 95600.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `application_id`, `message`, `is_read`, `created_at`) VALUES
(1, 6, 16, 'Your interview has been scheduled for Full Stack Developer at Merojob.', 1, '2026-06-11 15:27:45'),
(2, 6, 16, 'Your interview has been scheduled for Full Stack Developer at Merojob.', 1, '2026-06-11 15:31:42'),
(3, 6, 16, 'Congratulations! You have been selected for Full Stack Developer at Merojob.', 1, '2026-06-11 15:38:44'),
(4, 9, 17, 'Your application for Python Developer at AI Soft is now Under Review.', 1, '2026-06-15 13:23:44'),
(5, 8, 11, 'Your application for Account Executive at KumariJob was not selected this time.', 1, '2026-06-15 13:31:09'),
(6, 6, 16, 'Congratulations! You have been selected for Full Stack Developer at Merojob.', 1, '2026-06-15 13:32:25'),
(7, 3, 2, 'Congratulations! You have been selected for Python Developer at AI Soft.', 0, '2026-06-16 10:10:49'),
(8, 1, 5, 'Your interview has been scheduled for Python Developer at AI Soft.', 1, '2026-06-16 10:11:55'),
(9, 6, 19, 'Your interview has been scheduled for Laravel Backend Developer at Merojob.', 1, '2026-06-17 02:30:36'),
(10, 6, 20, 'Congratulations! You have been shortlisted for WordPress Developer at KumariJob.', 1, '2026-06-17 02:32:51'),
(11, 7, 22, 'Your application for Account Executive at KumariJob is now Under Review.', 1, '2026-06-17 02:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `recommendations`
--

CREATE TABLE `recommendations` (
  `recommendation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `match_score` float DEFAULT NULL,
  `missing_skills` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recommendations`
--

INSERT INTO `recommendations` (`recommendation_id`, `user_id`, `job_id`, `match_score`, `missing_skills`, `created_at`) VALUES
(1, 1, 1, 25, 'html, javascript, react', '2026-05-29 07:55:59'),
(2, 1, 2, 25, 'python, flask, api', '2026-05-29 07:56:29'),
(3, 1, 1, 50, 'javascript, react', '2026-05-29 07:56:43'),
(4, 1, 2, 25, 'python, flask, api', '2026-05-29 07:56:43'),
(5, 6, 1, 37.62, 'html, javascript, react', '2026-06-03 06:35:34'),
(6, 6, 2, 0, 'python, flask, mysql, api', '2026-06-03 06:35:34'),
(7, 6, 3, 0, 'excel, sql, python, data analysis', '2026-06-03 06:35:34'),
(8, 6, 4, 0, 'python', '2026-06-03 06:35:34'),
(9, 6, 5, 0, 'excel', '2026-06-03 06:35:34'),
(10, 6, 6, 0, 'php, mysql', '2026-06-03 06:35:34'),
(11, 6, 7, 0, 'excel, ms-word english', '2026-06-03 06:35:34'),
(12, 6, 1, 0, 'html, css, javascript, react', '2026-06-03 06:35:49'),
(13, 6, 2, 0, 'python, flask, mysql, api', '2026-06-03 06:35:49'),
(14, 6, 3, 0, 'excel, sql, python, data analysis', '2026-06-03 06:35:49'),
(15, 6, 4, 0, 'python', '2026-06-03 06:35:49'),
(16, 6, 5, 0, 'excel', '2026-06-03 06:35:49'),
(17, 6, 6, 0, 'php, mysql', '2026-06-03 06:35:49'),
(18, 6, 7, 0, 'excel, ms-word english', '2026-06-03 06:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `resumes`
--

CREATE TABLE `resumes` (
  `resume_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `extracted_text` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `extracted_skills` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resumes`
--

INSERT INTO `resumes` (`resume_id`, `user_id`, `file_name`, `file_path`, `extracted_text`, `uploaded_at`, `extracted_skills`) VALUES
(1, 1, '1780145275_BCA_8th_Sem_Proposal_Defense_Report_devi.pdf', 'uploads/1780145275_BCA_8th_Sem_Proposal_Defense_Report_devi.pdf', NULL, '2026-05-30 12:47:55', 'Python, Php, Html, Css, Javascript, Mysql, Machine Learning'),
(2, 1, '1780145915_Sandhya_Pokharel.pdf', 'uploads/1780145915_Sandhya_Pokharel.pdf', NULL, '2026-05-30 12:58:35', 'Excel'),
(3, 1, '1780146280_Sandhya_Pokharel.pdf', 'uploads/1780146280_Sandhya_Pokharel.pdf', NULL, '2026-05-30 13:04:40', 'Excel'),
(4, 6, '1780147009_Sandhya_Pokharel.pdf', 'uploads/1780147009_Sandhya_Pokharel.pdf', NULL, '2026-05-30 13:16:49', 'Excel'),
(5, 6, '1780147322_Sandhya_Pokharel.pdf', 'uploads/1780147322_Sandhya_Pokharel.pdf', NULL, '2026-05-30 13:22:02', 'Excel'),
(6, 6, '1780147342_Sandhya_Pokharel.pdf', 'uploads/1780147342_Sandhya_Pokharel.pdf', NULL, '2026-05-30 13:22:22', 'Excel'),
(7, 6, '1780149240_Sandhya_Pokharel.pdf', 'uploads/1780149240_Sandhya_Pokharel.pdf', NULL, '2026-05-30 13:54:00', 'Excel'),
(8, 1, '1780230191_sample_project_purposal.pdf', 'uploads/1780230191_sample_project_purposal.pdf', NULL, '2026-05-31 12:23:11', ''),
(9, 6, '1780282409_Sandhya_Pokharel.pdf', 'uploads/1780282409_Sandhya_Pokharel.pdf', NULL, '2026-06-01 02:53:29', 'Excel'),
(10, 6, '1780284690_Sandhya_Pokharel.pdf', 'uploads/1780284690_Sandhya_Pokharel.pdf', NULL, '2026-06-01 03:31:30', 'Excel'),
(11, 6, '1780284838_Sandhya_Pokharel.pdf', 'uploads/1780284838_Sandhya_Pokharel.pdf', NULL, '2026-06-01 03:33:59', 'Excel'),
(12, 6, '1780284862_Sandhya_Pokharel.pdf', 'uploads/1780284862_Sandhya_Pokharel.pdf', NULL, '2026-06-01 03:34:22', 'Excel'),
(13, 6, '1780452007_Internship_Proposal_Defense_Final_devi_sapkota_.pdf', 'uploads/1780452007_Internship_Proposal_Defense_Final_devi_sapkota_.pdf', NULL, '2026-06-03 02:00:07', 'Html, Css, Javascript, React, Git, Github'),
(14, 6, '1780466994_Sandhya_Pokharel.pdf', 'uploads/1780466994_Sandhya_Pokharel.pdf', NULL, '2026-06-03 06:09:54', 'Excel'),
(15, 6, '1780467913_Sandhya_Pokharel.pdf', 'uploads/1780467913_Sandhya_Pokharel.pdf', NULL, '2026-06-03 06:25:13', 'Excel'),
(16, 6, '1780470348_Sandhya_Pokharel.pdf', 'uploads/1780470348_Sandhya_Pokharel.pdf', NULL, '2026-06-03 07:05:48', 'Excel'),
(17, 6, '1780470902_Sandhya_Pokharel.pdf', 'uploads/1780470902_Sandhya_Pokharel.pdf', NULL, '2026-06-03 07:15:02', 'Excel'),
(18, 6, '1780472416_Sandhya_Pokharel.pdf', 'uploads/1780472416_Sandhya_Pokharel.pdf', NULL, '2026-06-03 07:40:16', 'Excel'),
(19, 6, '1780472440_Sandhya_Pokharel.pdf', 'uploads/1780472440_Sandhya_Pokharel.pdf', NULL, '2026-06-03 07:40:40', 'Excel'),
(20, 1, '1780474646_Sandhya_Pokharel.pdf', 'uploads/1780474646_Sandhya_Pokharel.pdf', NULL, '2026-06-03 08:17:26', 'Excel'),
(21, 1, '1780492580_Sandhya_Pokharel.pdf', 'uploads/1780492580_Sandhya_Pokharel.pdf', NULL, '2026-06-03 13:16:20', 'Excel'),
(22, 6, '1780492953_Sandhya_Pokharel.pdf', 'uploads/1780492953_Sandhya_Pokharel.pdf', NULL, '2026-06-03 13:22:33', 'Excel'),
(23, 6, '1780494545_Anish_Resume.pdf', 'uploads/1780494545_Anish_Resume.pdf', NULL, '2026-06-03 13:49:05', ''),
(24, 6, '1780494770_Anish_Resume.pdf', 'uploads/1780494770_Anish_Resume.pdf', NULL, '2026-06-03 13:52:50', ''),
(25, 6, '1780494823_Sandhya_Pokharel.pdf', 'uploads/1780494823_Sandhya_Pokharel.pdf', NULL, '2026-06-03 13:53:43', 'Excel'),
(26, 6, '1780494876_Sandhya_Pokharel.pdf', 'uploads/1780494876_Sandhya_Pokharel.pdf', NULL, '2026-06-03 13:54:36', 'Excel'),
(27, 6, '1780495113_new_cv.pdf', 'uploads/1780495113_new_cv.pdf', NULL, '2026-06-03 13:58:33', 'Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github'),
(28, 6, '1780495144_new_cv.pdf', 'uploads/1780495144_new_cv.pdf', NULL, '2026-06-03 13:59:04', 'Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github'),
(29, 6, '1780495686_Profile__4_.pdf', 'uploads/1780495686_Profile__4_.pdf', NULL, '2026-06-03 14:08:06', 'Github'),
(30, 6, '1780496326_Profile__5_.pdf', 'uploads/1780496326_Profile__5_.pdf', NULL, '2026-06-03 14:18:46', 'Github'),
(31, 6, '1780496426_Profile__6_.pdf', 'uploads/1780496426_Profile__6_.pdf', NULL, '2026-06-03 14:20:26', ''),
(32, 7, '1780496572_new_cv.pdf', 'uploads/1780496572_new_cv.pdf', NULL, '2026-06-03 14:22:52', 'Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github'),
(33, 7, '1780537271_new_cv.pdf', 'uploads/1780537271_new_cv.pdf', NULL, '2026-06-04 01:41:11', 'Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github'),
(34, 7, '1780537619_Nayan_Dheke.pdf', 'uploads/1780537619_Nayan_Dheke.pdf', NULL, '2026-06-04 01:47:00', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(35, 7, '1780537661_Nayan_Dheke.pdf', 'uploads/1780537661_Nayan_Dheke.pdf', NULL, '2026-06-04 01:47:41', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(36, 7, '1780537709_new_cv.pdf', 'uploads/1780537709_new_cv.pdf', NULL, '2026-06-04 01:48:29', 'Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github'),
(37, 7, '1780539529_Nayan_Dheke.pdf', 'uploads/1780539529_Nayan_Dheke.pdf', NULL, '2026-06-04 02:18:49', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(38, 8, '1780539886_Nayan_Dheke.pdf', 'uploads/1780539886_Nayan_Dheke.pdf', NULL, '2026-06-04 02:24:46', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(39, 7, '1780540297_Nayan_Dheke.pdf', 'uploads/1780540297_Nayan_Dheke.pdf', NULL, '2026-06-04 02:31:37', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(40, 7, '1780540326_Nayan_Dheke.pdf', 'uploads/1780540326_Nayan_Dheke.pdf', NULL, '2026-06-04 02:32:06', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(41, 7, '1780540880_Nayan_Dheke.pdf', 'uploads/1780540880_Nayan_Dheke.pdf', NULL, '2026-06-04 02:41:20', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(42, 7, '1780541293_Nayan_Dheke.pdf', 'uploads/1780541293_Nayan_Dheke.pdf', NULL, '2026-06-04 02:48:13', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(43, 7, '1780541320_Nayan_Dheke.pdf', 'uploads/1780541320_Nayan_Dheke.pdf', NULL, '2026-06-04 02:48:40', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(44, 7, '1780541612_Nayan_Dheke.pdf', 'uploads/1780541612_Nayan_Dheke.pdf', NULL, '2026-06-04 02:53:32', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(45, 8, '1780552723_CVofasmita.pdf', 'uploads/1780552723_CVofasmita.pdf', NULL, '2026-06-04 05:58:43', 'Excel'),
(46, 8, '1780929335_Profile__4_.pdf', 'uploads/1780929335_Profile__4_.pdf', NULL, '2026-06-08 14:35:35', 'Github'),
(47, 8, '1780929362_Nayan_Dheke.pdf', 'uploads/1780929362_Nayan_Dheke.pdf', NULL, '2026-06-08 14:36:02', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(48, 8, '1780929382_Nayan_Dheke.pdf', 'uploads/1780929382_Nayan_Dheke.pdf', NULL, '2026-06-08 14:36:22', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(49, 8, '1780930159_Profile__6_.pdf', 'uploads/1780930159_Profile__6_.pdf', NULL, '2026-06-08 14:49:19', ''),
(50, 8, '1780930898_Profile__5_.pdf', 'uploads/1780930898_Profile__5_.pdf', NULL, '2026-06-08 15:01:38', 'Github'),
(51, 8, '1780930917_Profile__6_.pdf', 'uploads/1780930917_Profile__6_.pdf', NULL, '2026-06-08 15:01:57', ''),
(52, 8, '1780931491_CVofasmita.pdf', 'uploads/1780931491_CVofasmita.pdf', NULL, '2026-06-08 15:11:31', 'Excel'),
(53, 8, '1780931505_Profile__6_.pdf', 'uploads/1780931505_Profile__6_.pdf', NULL, '2026-06-08 15:11:45', ''),
(54, 8, '1780933368_Nayan_Dheke.pdf', 'uploads/1780933368_Nayan_Dheke.pdf', NULL, '2026-06-08 15:42:48', 'Python, Php, Sql, React, Node.Js, Git, Github'),
(55, 8, '1781187627_Anish_Resume.pdf', 'uploads/1781187627_Anish_Resume.pdf', NULL, '2026-06-11 14:20:27', ''),
(56, 8, '1781187706_rabindra-subedi.pdf', 'uploads/1781187706_rabindra-subedi.pdf', NULL, '2026-06-11 14:21:46', 'Html, Mysql'),
(57, 6, '1781188548_SabiwekResume.pdf', 'uploads/1781188548_SabiwekResume.pdf', NULL, '2026-06-11 14:35:48', ''),
(58, 6, '1781188572_new_cv.pdf', 'uploads/1781188572_new_cv.pdf', NULL, '2026-06-11 14:36:13', 'Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github'),
(59, 6, '1781188634_new_cv.pdf', 'uploads/1781188634_new_cv.pdf', NULL, '2026-06-11 14:37:14', 'Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github'),
(60, 9, '1781529729_SabiwekResume.pdf', 'uploads/1781529729_SabiwekResume.pdf', NULL, '2026-06-15 13:22:09', ''),
(61, 9, '1781529758_Resume_June_2024.pdf', 'uploads/1781529758_Resume_June_2024.pdf', NULL, '2026-06-15 13:22:38', 'Python'),
(62, 6, '1781663331_Ajay-resume-1.pdf', 'uploads/1781663331_Ajay-resume-1.pdf', NULL, '2026-06-17 02:28:51', ''),
(63, 6, '1781663363_new_cv.pdf', 'uploads/1781663363_new_cv.pdf', NULL, '2026-06-17 02:29:23', 'Php, Html, Css, Javascript, Mysql, Bootstrap, Git, Github'),
(64, 6, '1781663643_Ajay-resume-1.pdf', 'uploads/1781663643_Ajay-resume-1.pdf', NULL, '2026-06-17 02:34:04', ''),
(65, 6, '1781663854_Ajay-resume-1.pdf', 'uploads/1781663854_Ajay-resume-1.pdf', NULL, '2026-06-17 02:37:34', ''),
(66, 7, '1781663962_Sandhya_Pokharel.pdf', 'uploads/1781663962_Sandhya_Pokharel.pdf', NULL, '2026-06-17 02:39:22', 'Excel');

-- --------------------------------------------------------

--
-- Table structure for table `resume_analysis`
--

CREATE TABLE `resume_analysis` (
  `analysis_id` int(11) NOT NULL,
  `resume_id` int(11) DEFAULT NULL,
  `ats_score` int(11) DEFAULT NULL,
  `missing_skills` text DEFAULT NULL,
  `suggestions` text DEFAULT NULL,
  `analyzed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resume_analysis`
--

INSERT INTO `resume_analysis` (`analysis_id`, `resume_id`, `ats_score`, `missing_skills`, `suggestions`, `analyzed_at`) VALUES
(1, 2, 80, '', 'Add projects section., Add more technical skills.', '2026-05-30 12:58:35'),
(2, 3, 80, '', 'Add projects section., Add more technical skills.', '2026-05-30 13:04:40'),
(3, 4, 80, '', 'Add projects section., Add more technical skills.', '2026-05-30 13:16:49'),
(4, 5, 80, '', 'Add projects section., Add more technical skills.', '2026-05-30 13:22:02'),
(5, 6, 80, '', 'Add projects section., Add more technical skills.', '2026-05-30 13:22:22'),
(6, 7, 80, '', 'Add projects section., Add more technical skills.', '2026-05-30 13:54:00'),
(7, 8, 40, '', 'Add email address., Add education section., Add skills section., Add more technical skills.', '2026-05-31 12:23:11'),
(8, 9, 80, '', 'Add projects section., Add more technical skills.', '2026-06-01 02:53:29'),
(9, 10, 80, '', 'Add projects section., Add more technical skills.', '2026-06-01 03:31:30'),
(10, 11, 80, '', 'Add projects section., Add more technical skills.', '2026-06-01 03:33:59'),
(11, 12, 80, '', 'Add projects section., Add more technical skills.', '2026-06-01 03:34:22'),
(12, 13, 70, '', 'Add email address., Add education section.', '2026-06-03 02:00:07'),
(13, 14, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 06:09:54'),
(14, 15, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 06:25:13'),
(15, 16, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 07:05:48'),
(16, 17, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 07:15:02'),
(17, 18, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 07:40:16'),
(18, 19, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 07:40:40'),
(19, 20, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 08:17:26'),
(20, 21, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 13:16:20'),
(21, 22, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 13:22:33'),
(22, 23, 90, '', 'Add more technical skills.', '2026-06-03 13:49:05'),
(23, 24, 90, '', 'Add more technical skills.', '2026-06-03 13:52:50'),
(24, 25, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 13:53:43'),
(25, 26, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 13:54:36'),
(26, 27, 90, '', 'Add projects section.', '2026-06-03 13:58:33'),
(27, 28, 90, '', 'Add projects section.', '2026-06-03 13:59:04'),
(28, 29, 70, '', 'Add phone number., Add projects section., Add more technical skills.', '2026-06-03 14:08:06'),
(29, 30, 60, '', 'Add work experience section., Add projects section., Add more technical skills.', '2026-06-03 14:18:46'),
(30, 31, 80, '', 'Add projects section., Add more technical skills.', '2026-06-03 14:20:26'),
(31, 32, 90, '', 'Add projects section.', '2026-06-03 14:22:52'),
(32, 33, 90, '', 'Add projects section.', '2026-06-04 01:41:11'),
(33, 34, 85, '', 'Add education section.', '2026-06-04 01:47:00'),
(34, 35, 85, '', 'Add education section.', '2026-06-04 01:47:41'),
(35, 36, 90, '', 'Add projects section.', '2026-06-04 01:48:29'),
(36, 37, 85, '', 'Add education section.', '2026-06-04 02:18:49'),
(37, 38, 85, '', 'Add education section.', '2026-06-04 02:24:46'),
(38, 39, 85, '', 'Add education section.', '2026-06-04 02:31:37'),
(39, 40, 85, '', 'Add education section.', '2026-06-04 02:32:06'),
(40, 41, 85, '', 'Add education section.', '2026-06-04 02:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`) VALUES
(1, 'devi sapkota', 'devi123@gmail.com', '$2y$10$swWrLLAfNNa8ieh/K/pOr.AsygnbMIHezdFa4yAsIUNE1ws4saoBm', 'user'),
(2, 'sita sapkota', 'sita@gmail.com', '$2y$10$P0zuJAfGpHC5lXM82URexe4mgu3JCiCboyk9Lf8s0x30bkyQ29qD2', 'user'),
(3, 'prasamsha pokharel', 'prasamsha@gmail.com', '$2y$10$aJSykLyqdExDa4mNuRskKudh6hsjJacMnyCSDr1F82KinB.1zBrIq', 'user'),
(4, 'ram', 'ram1234@gmail.com', '$2y$10$LDyk7NWsRqtnwp9Ojk61f.V2707HNI.EGSNi1KKda6lF51b5c6UnS', 'admin'),
(5, 'riya dahal', 'riya1234@gmail.com', '$2y$10$z8s80n4hLJ3A3OeO9B4EGes9JwwbHE.CyvOawGGjKALSdUGaYcmVK', 'admin'),
(6, 'binita ', 'binita@gmail.com', '$2y$10$/JrwwynAmXrdOckZQYZgJurWkGJdKyTxrlobLJpH3WnP8V2DpjyBG', 'user'),
(7, 'gita sapkota', 'gita123@gmail.com', '$2y$10$PNOSeGgZMpd9vrLBhiY23uy.hpKUpRkgMeq3g8uLRjgxN7Z3RWhzm', 'user'),
(8, 'nayan ', 'nayan@gmail.com', '$2y$10$N2ARNCeybkMPw7zkt7k1xOdhS0OSrXZ9OJKY8ZtyB3E06tLJc4q22', 'user'),
(9, 'Ambika Jha', 'ambika99@gmail.com', '$2y$10$OI.awVLxbxeDx6ijj1hv4uOTJ4rEtnMR6i7eizoiPpTMcfbv9nldW', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`recommendation_id`);

--
-- Indexes for table `resumes`
--
ALTER TABLE `resumes`
  ADD PRIMARY KEY (`resume_id`);

--
-- Indexes for table `resume_analysis`
--
ALTER TABLE `resume_analysis`
  ADD PRIMARY KEY (`analysis_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=420;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `recommendation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `resumes`
--
ALTER TABLE `resumes`
  MODIFY `resume_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `resume_analysis`
--
ALTER TABLE `resume_analysis`
  MODIFY `analysis_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
