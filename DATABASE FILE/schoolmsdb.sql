-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2021 at 07:45 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
DROP DATABASE IF EXISTS `schoolmsdb`;
--
-- Database: `schoolmsdb`
--
CREATE DATABASE IF NOT EXISTS `schoolmsdb`;

USE schoolmsdb;
-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `email` varchar(20) NOT NULL,
  `dob` date NOT NULL,
  `hiredate` date NOT NULL,
  `address` varchar(30) NOT NULL,
  `sex` varchar(7) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `phone`, `email`, `dob`, `hiredate`, `address`, `sex`) VALUES
('ad-123-0', 'Christen', '2587416969', 'christen@example.com', '1993-11-20', '2016-01-01', 'US, Blkr St', 'female'),
('ad-123-1', 'Harry Den', '7531596969', 'harryden@gmail.com', '1995-09-22', '2018-01-05', 'US, Fairview Drive', 'Male'),
('ad-123-2', 'Bucky Barnes', '1969735220', 'barsmine@gmail.com', '1994-04-02', '2020-12-24', 'US, DownSt 12', 'Male'),
('ad-123-3', 'Steephen', '9745452220', 'stephen@gmail.com', '1991-05-02', '2014-04-24', 'AU, Parmmiza Rd', 'Male');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE IF NOT EXISTS `programs` (
  `id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `duration_years` int(2) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `name`, `duration_years`, `description`) VALUES
('P001', 'Primary Education', 5, 'Primary school education program'),
('S001', 'Science Stream', 2, 'Secondary school science program'),
('A001', 'Arts Stream', 2, 'Secondary school arts program'),
('C001', 'Commerce Stream', 2, 'Secondary school commerce program');

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE IF NOT EXISTS `academic_years` (
  `id` varchar(20) NOT NULL,
  `program_id` varchar(20) NOT NULL,
  `year_number` int(2) NOT NULL,
  `year_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `program_id`, `year_number`, `year_name`) VALUES
('PY001', 'P001', 1, 'Primary Year 1'),
('PY002', 'P001', 2, 'Primary Year 2'),
('PY003', 'P001', 3, 'Primary Year 3'),
('PY004', 'P001', 4, 'Primary Year 4'),
('PY005', 'P001', 5, 'Primary Year 5'),
('SY001', 'S001', 1, 'Science Year 1'),
('SY002', 'S001', 2, 'Science Year 2'),
('AY001', 'A001', 1, 'Arts Year 1'),
('AY002', 'A001', 2, 'Arts Year 2'),
('CY001', 'C001', 1, 'Commerce Year 1'),
('CY002', 'C001', 2, 'Commerce Year 2');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE IF NOT EXISTS `semesters` (
  `id` varchar(20) NOT NULL,
  `year_id` varchar(20) NOT NULL,
  `semester_number` int(1) NOT NULL,
  `semester_name` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `year_id`, `semester_number`, `semester_name`, `start_date`, `end_date`) VALUES
('S1PY001', 'PY001', 1, 'Primary Year 1 Semester 1', '2021-01-10', '2021-05-15'),
('S2PY001', 'PY001', 2, 'Primary Year 1 Semester 2', '2021-06-10', '2021-11-15'),
('S1PY002', 'PY002', 1, 'Primary Year 2 Semester 1', '2021-01-10', '2021-05-15'),
('S2PY002', 'PY002', 2, 'Primary Year 2 Semester 2', '2021-06-10', '2021-11-15'),
('S1SY001', 'SY001', 1, 'Science Year 1 Semester 1', '2021-01-10', '2021-05-15'),
('S2SY001', 'SY001', 2, 'Science Year 1 Semester 2', '2021-06-10', '2021-11-15'),
('S1SY002', 'SY002', 1, 'Science Year 2 Semester 1', '2021-01-10', '2021-05-15'),
('S2SY002', 'SY002', 2, 'Science Year 2 Semester 2', '2021-06-10', '2021-11-15');

-- --------------------------------------------------------

--
-- Table structure for table `course_units`
--

CREATE TABLE IF NOT EXISTS `course_units` (
  `id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `credit_hours` int(2) NOT NULL,
  `description` text,
  `program_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_units`
--

INSERT INTO `course_units` (`id`, `name`, `code`, `credit_hours`, `description`, `program_id`) VALUES
('CU001', 'Basic Mathematics', 'MATH101', 3, 'Introduction to basic mathematics', 'P001'),
('CU002', 'English Language', 'ENG101', 3, 'Basic English language skills', 'P001'),
('CU003', 'General Science', 'SCI101', 3, 'Introduction to science', 'P001'),
('CU004', 'Advanced Mathematics', 'MATH201', 4, 'Advanced mathematics concepts', 'S001'),
('CU005', 'Physics', 'PHY201', 4, 'Fundamentals of physics', 'S001'),
('CU006', 'Chemistry', 'CHE201', 4, 'Fundamentals of chemistry', 'S001'),
('CU007', 'Literature', 'LIT201', 4, 'Study of literature', 'A001'),
('CU008', 'History', 'HIS201', 4, 'World history', 'A001'),
('CU009', 'Accounting', 'ACC201', 4, 'Principles of accounting', 'C001'),
('CU010', 'Business Studies', 'BUS201', 4, 'Introduction to business', 'C001');

-- --------------------------------------------------------

--
-- Table structure for table `semester_courses`
--

CREATE TABLE IF NOT EXISTS `semester_courses` (
  `id` int(11) NOT NULL,
  `semester_id` varchar(20) NOT NULL,
  `course_unit_id` varchar(20) NOT NULL,
  `teacher_id` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `semester_courses`
--

INSERT INTO `semester_courses` (`id`, `semester_id`, `course_unit_id`, `teacher_id`) VALUES
(1, 'S1PY001', 'CU001', 'te-124-1'),
(2, 'S1PY001', 'CU002', 'te-123-1'),
(3, 'S1PY001', 'CU003', 'te-125-1'),
(4, 'S2PY001', 'CU001', 'te-124-1'),
(5, 'S2PY001', 'CU002', 'te-123-1'),
(6, 'S1SY001', 'CU004', 'te-126-1'),
(7, 'S1SY001', 'CU005', 'te-127-1'),
(8, 'S1SY001', 'CU006', 'te-125-1'),
(9, 'S2SY001', 'CU004', 'te-126-1'),
(10, 'S2SY001', 'CU005', 'te-127-1'),
(11, 'S1SY002', 'CU004', 'te-126-1'),
(12, 'S1SY002', 'CU005', 'te-127-1'),
(13, 'S1SY002', 'CU006', 'te-125-1'),
(14, 'S1AY001', 'CU007', 'te-123-1'),
(15, 'S1AY001', 'CU008', 'te-124-1'),
(16, 'S1CY001', 'CU009', 'te-126-1'),
(17, 'S1CY001', 'CU010', 'te-127-1');

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

CREATE TABLE IF NOT EXISTS `student_enrollments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `program_id` varchar(20) NOT NULL,
  `current_year_id` varchar(20) NOT NULL,
  `current_semester_id` varchar(20) NOT NULL,
  `enrollment_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student_enrollments`
--

INSERT INTO `student_enrollments` (`id`, `student_id`, `program_id`, `current_year_id`, `current_semester_id`, `enrollment_date`, `status`) VALUES
(1, 'st-123-1', 'P001', 'PY001', 'S1PY001', '2021-01-15', 'Active'),
(2, 'st-124-1', 'P001', 'PY001', 'S1PY001', '2021-01-15', 'Active'),
(3, 'st-125-1', 'S001', 'SY001', 'S1SY001', '2021-01-15', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `student_courses`
--

CREATE TABLE IF NOT EXISTS `student_courses` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `semester_course_id` int(11) NOT NULL,
  `enrollment_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Enrolled'
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student_courses`
--

INSERT INTO `student_courses` (`id`, `student_id`, `semester_course_id`, `enrollment_date`, `status`) VALUES
(1, 'st-123-1', 1, '2021-01-15', 'Enrolled'),
(2, 'st-123-1', 2, '2021-01-15', 'Enrolled'),
(3, 'st-123-1', 3, '2021-01-15', 'Enrolled'),
(4, 'st-124-1', 1, '2021-01-15', 'Enrolled'),
(5, 'st-124-1', 2, '2021-01-15', 'Enrolled'),
(6, 'st-124-1', 3, '2021-01-15', 'Enrolled'),
(7, 'st-125-1', 6, '2021-01-15', 'Enrolled'),
(8, 'st-125-1', 7, '2021-01-15', 'Enrolled');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE IF NOT EXISTS `attendance` (
`id` int(11) NOT NULL,
  `date` date NOT NULL,
  `attendedid` varchar(20) NOT NULL,
  `role` ENUM('student','teacher','staff') NOT NULL,
  `program_id` varchar(20) DEFAULT NULL,
  `year_id` varchar(20) DEFAULT NULL,
  `semester_id` varchar(20) DEFAULT NULL,
  `semester_course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `date`, `attendedid`, `semester_course_id`) VALUES
(18, '2016-05-04', 'te-123-1', 2),
(20, '2016-05-01', 'te-123-1', 2),
(21, '2016-04-12', 'te-123-1', 2),
(22, '2016-05-04', 'te-124-1', 1),
(23, '2016-04-19', 'te-124-1', 1),
(24, '2016-05-02', 'te-124-1', 1),
(25, '2016-05-04', 'sta-123-1', NULL),
(26, '2016-05-05', 'sta-123-1', NULL),
(27, '2016-04-04', 'sta-123-1', NULL),
(28, '2016-04-05', 'sta-123-1', NULL),
(29, '2021-04-06', 'te-123-1', 2),
(30, '2021-04-06', 'sta-123-1', NULL),
(31, '2021-04-06', 'st-123-1', 1),
(32, '2021-04-06', 'st-124-1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `exam_schedule`
--

CREATE TABLE IF NOT EXISTS `exam_schedule` (
  `id` varchar(20) NOT NULL,
  `examdate` date NOT NULL,
  `time` varchar(20) NOT NULL,
  `course_unit_id` varchar(20) NOT NULL,
  `semester_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `exam_schedule`
--

INSERT INTO `exam_schedule` (`id`, `examdate`, `time`, `course_unit_id`, `semester_id`) VALUES
('145', '2016-05-06', '2:00-4:00', 'CU001', 'S1PY001'),
('sh-10', '2021-04-06', '10:00 - 12:30', 'CU004', 'S1SY001'),
('sh-20', '2021-04-06', '01:00 - 03:00', 'CU005', 'S1SY001');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `id` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `sex` varchar(7) NOT NULL,
  `dob` date NOT NULL,
  `hiredate` date NOT NULL,
  `address` varchar(30) NOT NULL,
  `salary` double NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `phone`, `email`, `sex`, `dob`, `hiredate`, `address`, `salary`) VALUES
('sta-123-1', 'Scott', '1597534568', 'scootpel@gmail.com', 'Male', '1980-11-08', '2015-10-15', '2333  Cody Ridge Road', 25000),
('sta-124-1', 'Patrick', '7412531325', 'pforpat@school.com', 'Male', '1990-03-26', '2017-05-12', '321  McDonald Avenue', 19500),
('sta-125-1', 'Aaron', '2587532224', 'aarontay@gmail.com', 'Male', '1992-08-19', '2010-05-29', '4927  Water Street', 31000),
('sta-126-1', 'Peterson', '2574545888', 'peteson@gmail.com', 'Male', '2021-04-01', '2012-05-05', '2950  Parrill Court', 27000);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `id` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sex` varchar(7) NOT NULL,
  `dob` date NOT NULL,
  `addmissiondate` date NOT NULL DEFAULT CURRENT_DATE,
  `address` varchar(50) NOT NULL,
  `parentid` varchar(20) NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `phone`, `email`, `sex`, `dob`, `addmissiondate`, `address`, `parentid`) VALUES
('st-123-1', 'Wiccan', '9102457890', 'wiccan@gmail.com', 'Male', '1999-10-24', '2011-01-09', '3056  Leisure Lane', 'pa-123-1'),
('st-124-1', 'Paul', '4564564500', 'paul@gmail.com', 'Male', '2000-09-24', '2014-07-02', '1940  Prudence Street', 'pa-123-1'),
('st-125-1', 'Jacob', '8520696964', 'jacodon@gmail.com', 'Male', '2001-12-12', '2014-12-06', '2549  Simpson Avenue', 'pa-124-1');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE IF NOT EXISTS `teachers` (
  `id` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `email` varchar(20) NOT NULL,
  `address` varchar(30) NOT NULL,
  `sex` varchar(7) NOT NULL,
  `dob` date NOT NULL,
  `hiredate` date NOT NULL,
  `salary` double NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `phone`, `email`, `address`, `sex`, `dob`, `hiredate`, `salary`) VALUES
('te-123-1', 'Liiam', '1247965680', 'liam@gmail.com', '', 'Male', '1990-05-05', '2010-06-21', 36500),
('te-124-1', 'Robert', '8520000012', 'robertj@gmail,com', '1022  Neuport Lane', 'Male', '1995-12-18', '2015-12-04', 36000),
('te-125-1', 'James Rhoades', '3214569874', 'rhoadesj@gmail.com', '3464  Straford Park', 'Male', '1998-06-26', '2021-01-06', 21000),
('te-126-1', 'Maria', '9103674540', 'mariahill@gmail.com', '833  Fulton Street', 'Female', '1996-04-06', '2019-12-24', 39000),
('te-127-1', 'Darlene', '1379696969', 'darleeene@gmail.com', '2131  Glory Road', 'Female', '1994-12-25', '2017-05-25', 41000);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `noteid` INT(11) NOT NULL AUTO_INCREMENT,
  `teacherid` VARCHAR(20) NOT NULL,
  `course_unit_id` VARCHAR(20) NOT NULL,
  `semester_id` VARCHAR(20) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `filepath` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`noteid`),
  KEY `teacherid` (`teacherid`),
  KEY `course_unit_id` (`course_unit_id`),
  KEY `semester_id` (`semester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`noteid`, `teacherid`, `course_unit_id`, `semester_id`, `title`, `description`, `filepath`) VALUES
(1, 'te-123-1', 'CU002', 'S1PY001', 'Math Note', 'Algebra and Geometry notes', '/files/notes/math_note.pdf'),
(2, 'te-124-1', 'CU001', 'S1PY001', 'Science Note', 'Physics and Chemistry notes', '/files/notes/science_note.pdf'),
(3, 'te-126-1', 'CU007', 'S1AY001', 'Literature Note', 'Shakespeare and Poetry notes', '/files/notes/literature_note.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `usertype` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `password`, `usertype`) VALUES
('ad-123-0', '123', 'admin'),
('ad-123-1', '123', 'admin'),
('ad-123-2', '123', 'admin'),
('ad-123-3', '123', 'admin'),
('pa-123-1', '123', 'parent'),
('pa-124-1', '123', 'parent'),
('st-123-1', '123', 'student'),
('st-124-1', '125', 'student'),
('st-125-1', '123', 'student'),
('sta-123-1', '123', 'staff'),
('sta-124-1', '123', 'staff'),
('sta-125-1', '123', 'staff'),
('sta-126-1', '123', 'staff'),
('te-123-1', '123', 'teacher'),
('te-124-1', '124', 'teacher'),
('te-125-1', '258', 'teacher'),
('te-126-1', '258', 'teacher'),
('te-127-1', '123', 'teacher');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
 ADD PRIMARY KEY (`id`),
 ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
 ADD PRIMARY KEY (`id`),
 ADD KEY `year_id` (`year_id`);

--
-- Indexes for table `course_units`
--
ALTER TABLE `course_units`
 ADD PRIMARY KEY (`id`),
 ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `semester_courses`
--
ALTER TABLE `semester_courses`
 ADD PRIMARY KEY (`id`),
 ADD KEY `semester_id` (`semester_id`),
 ADD KEY `course_unit_id` (`course_unit_id`),
 ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
 ADD PRIMARY KEY (`id`),
 ADD KEY `student_id` (`student_id`),
 ADD KEY `program_id` (`program_id`),
 ADD KEY `current_year_id` (`current_year_id`),
 ADD KEY `current_semester_id` (`current_semester_id`);

--
-- Indexes for table `student_courses`
--
ALTER TABLE `student_courses`
 ADD PRIMARY KEY (`id`),
 ADD KEY `student_id` (`student_id`),
 ADD KEY `semester_course_id` (`semester_course_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
 ADD PRIMARY KEY (`id`),
 ADD KEY `semester_course_id` (`semester_course_id`);

--
-- Indexes for table `exam_schedule`
--
ALTER TABLE `exam_schedule`
 ADD PRIMARY KEY (`id`),
 ADD KEY `course_unit_id` (`course_unit_id`),
 ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
 ADD PRIMARY KEY (`id`),
 ADD KEY `student_id` (`student_id`),
 ADD KEY `course_unit_id` (`course_unit_id`),
 ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `retakes`
--
ALTER TABLE `retakes`
 ADD PRIMARY KEY (`id`),
 ADD KEY `student_id` (`student_id`),
 ADD KEY `course_unit_id` (`course_unit_id`),
 ADD KEY `original_semester_id` (`original_semester_id`),
 ADD KEY `retake_semester_id` (`retake_semester_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
 ADD PRIMARY KEY (`id`),
 ADD KEY `semester_course_id` (`semester_course_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
 ADD PRIMARY KEY (`id`),
 ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
 ADD PRIMARY KEY (`reportid`),
 ADD KEY `course_unit_id` (`course_unit_id`),
 ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD UNIQUE KEY `userid` (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `semester_courses`
--
ALTER TABLE `semester_courses`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `student_courses`
--
ALTER TABLE `student_courses`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `retakes`
--
ALTER TABLE `retakes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
MODIFY `reportid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;

-- Constraints for table `academic_years`
ALTER TABLE `academic_years`
ADD CONSTRAINT `academic_years_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

-- Constraints for table `semesters`
ALTER TABLE `semesters`
ADD CONSTRAINT `semesters_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE;

-- Constraints for table `course_units`
ALTER TABLE `course_units`
ADD CONSTRAINT `course_units_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

-- Constraints for table `semester_courses`
ALTER TABLE `semester_courses`
ADD CONSTRAINT `semester_courses_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `semester_courses_ibfk_2` FOREIGN KEY (`course_unit_id`) REFERENCES `course_units` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `semester_courses_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

-- Constraints for table `student_enrollments`
ALTER TABLE `student_enrollments`
ADD CONSTRAINT `student_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `student_enrollments_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `student_enrollments_ibfk_3` FOREIGN KEY (`current_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `student_enrollments_ibfk_4` FOREIGN KEY (`current_semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

-- Constraints for table `student_courses`
ALTER TABLE `student_courses`
ADD CONSTRAINT `student_courses_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `student_courses_ibfk_2` FOREIGN KEY (`semester_course_id`) REFERENCES `semester_courses` (`id`) ON DELETE CASCADE;

-- Constraints for table `attendance`
ALTER TABLE `attendance`
ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`semester_course_id`) REFERENCES `semester_courses` (`id`) ON DELETE CASCADE;

-- Constraints for table `exam_schedule`
ALTER TABLE `exam_schedule`
ADD CONSTRAINT `exam_schedule_ibfk_1` FOREIGN KEY (`course_unit_id`) REFERENCES `course_units` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `exam_schedule_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

-- Constraints for table `grades`
ALTER TABLE `grades`
ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`course_unit_id`) REFERENCES `course_units` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `grades_ibfk_3` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

-- Constraints for table `retakes`
ALTER TABLE `retakes`
ADD CONSTRAINT `retakes_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `retakes_ibfk_2` FOREIGN KEY (`course_unit_id`) REFERENCES `course_units` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `retakes_ibfk_3` FOREIGN KEY (`original_semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `retakes_ibfk_4` FOREIGN KEY (`retake_semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

-- Constraints for table `timetable`
ALTER TABLE `timetable`
ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`semester_course_id`) REFERENCES `semester_courses` (`id`) ON DELETE CASCADE;

-- Constraints for table `payment`
ALTER TABLE `payment`
ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

-- Constraints for table `report`
ALTER TABLE `report`
ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`course_unit_id`) REFERENCES `course_units` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

-- Constraints for table `notes`
ALTER TABLE `notes`
ADD CONSTRAINT `notes_teacherid_fk` FOREIGN KEY (`teacherid`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `notes_course_unit_fk` FOREIGN KEY (`course_unit_id`) REFERENCES `course_units` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `notes_semester_fk` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;