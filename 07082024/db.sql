CREATE TABLE college (
    college_id INT PRIMARY KEY AUTO_INCREMENT,
    college_name VARCHAR(255) NOT NULL,
    college_code VARCHAR(50) UNIQUE NOT NULL,
    logo VARCHAR(255),  -- Path or URL to the college logo
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE departments (
    department_id INT PRIMARY KEY AUTO_INCREMENT,
    department_name VARCHAR(255) NOT NULL,
    department_code VARCHAR(50) UNIQUE NOT NULL,
    college_id INT,
    logo VARCHAR(255),  -- Path or URL to the department logo
    FOREIGN KEY (college_id) REFERENCES college(college_id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE programs (
    program_id INT PRIMARY KEY AUTO_INCREMENT,
    program_name VARCHAR(255) NOT NULL,
    program_code VARCHAR(50) UNIQUE NOT NULL,
    department_id INT,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE branches (
    branch_id INT PRIMARY KEY AUTO_INCREMENT,
    branch_name VARCHAR(255) NOT NULL,
    branch_code VARCHAR(50) UNIQUE NOT NULL,
    program_id INT,
    FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE regulations (
    regulation_id INT PRIMARY KEY AUTO_INCREMENT,
    regulation_name VARCHAR(255) NOT NULL,
    regulation_code VARCHAR(50) UNIQUE NOT NULL,
    program_id INT,
    branch_id  INT,
    FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE semesters (
    semester_id INT PRIMARY KEY AUTO_INCREMENT,
    semester_name VARCHAR(255) NOT NULL,
    regulation_id INT,
    FOREIGN KEY (regulation_id) REFERENCES regulations(regulation_id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE subject_types (
  subject_type_id INT PRIMARY KEY AUTO_INCREMENT,
  type_name VARCHAR(50) NOT NULL  -- e.g., Theory, Lab, Elective, Open Elective, Zero Credit
);
CREATE TABLE subjects (
  subject_id INT PRIMARY KEY AUTO_INCREMENT,
  subject_code VARCHAR(20) UNIQUE NOT NULL,  -- Unique code for the subject
  subject_name VARCHAR(255) NOT NULL,
  semester_id INT,  -- Optional, can be null if subject offered across semesters
  branch_id INT,
  regulation_id INT NOT NULL,  -- Adding regulation_id to map subjects to regulations
  subject_type_id INT NOT NULL,  -- Foreign key to subject types
  credits INT NOT NULL,  -- Number of credits for the subject
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (semester_id) REFERENCES semesters(semester_id) ON DELETE SET NULL,
  FOREIGN KEY (regulation_id) REFERENCES regulations(regulation_id) ON DELETE CASCADE,
  FOREIGN KEY (branch_id) REFERENCES branches(branch_id) ON DELETE CASCADE,
  FOREIGN KEY (subject_type_id) REFERENCES subject_types(subject_type_id) ON DELETE CASCADE
);

CREATE TABLE mark_types (
  mark_type_id INT PRIMARY KEY AUTO_INCREMENT,
  mark_type_name VARCHAR(50) NOT NULL  -- e.g., Internal, External, Assignment
);
CREATE TABLE marks_distribution (
  marks_distribution_id INT PRIMARY KEY AUTO_INCREMENT,
  subject_id INT NOT NULL,
  mark_type_id INT NOT NULL,
  marks INT NOT NULL,
  FOREIGN KEY (subject_id) REFERENCES subjects(subject_id) ON DELETE CASCADE,
  FOREIGN KEY (mark_type_id) REFERENCES mark_types(mark_type_id) ON DELETE CASCADE
);
CREATE TABLE elective_groups (
  elective_group_id INT PRIMARY KEY AUTO_INCREMENT,
  group_name VARCHAR(50) NOT NULL,  -- e.g., Elective-I, Open Elective-I
  semester_id INT NOT NULL,
  FOREIGN KEY (semester_id) REFERENCES semesters(semester_id) ON DELETE CASCADE
);
CREATE TABLE elective_group_subjects (
  elective_group_subject_id INT PRIMARY KEY AUTO_INCREMENT,
  elective_group_id INT NOT NULL,
  subject_id INT NOT NULL,
  FOREIGN KEY (elective_group_id) REFERENCES elective_groups(elective_group_id) ON DELETE CASCADE,
  FOREIGN KEY (subject_id) REFERENCES subjects(subject_id) ON DELETE CASCADE
);
CREATE TABLE batches (
  batch_id INT PRIMARY KEY AUTO_INCREMENT,
  batch_name VARCHAR(50) NOT NULL,
  program_id INT,
  branch_id  INT,
  start_year YEAR NOT NULL,
  end_year YEAR NOT NULL,
  FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE,
  FOREIGN KEY (branch_id) REFERENCES branches(branch_id) ON DELETE CASCADE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- students
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `admission_no` varchar(50) NOT NULL,
  `regd_no` varchar(50) ,
  `photo` varchar(255) DEFAULT NULL,
  `blood_group_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `gender_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `aadhar` varchar(12) DEFAULT NULL,
  `father_aadhar` varchar(12) DEFAULT NULL,
  `mother_aadhar` varchar(12) DEFAULT NULL,
  `father_mobile` varchar(15) DEFAULT NULL,
  `mother_mobile` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `nationality_id` int(11) NOT NULL,
  `religion_id` int(11) NOT NULL,
  `student_type_id` int(11) NOT NULL,
  `caste_id` int(11) DEFAULT NULL,
  `sub_caste_id` int(11) DEFAULT NULL,
  `batch_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `admission_no` (`admission_no`),
  UNIQUE KEY `aadhar` (`aadhar`),
  UNIQUE KEY `father_aadhar` (`father_aadhar`),
  UNIQUE KEY `mother_aadhar` (`mother_aadhar`),
  KEY `batch_id` (`batch_id`),
  KEY `blood_group_id` (`blood_group_id`),
  KEY `gender_id` (`gender_id`),
  KEY `student_type_id` (`student_type_id`),
  KEY `caste_id` (`caste_id`),
  KEY `sub_caste_id` (`sub_caste_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`batch_id`) ON DELETE CASCADE,
  CONSTRAINT `students_ibfk_2` FOREIGN KEY (`blood_group_id`) REFERENCES `blood_groups` (`blood_group_id`),
  CONSTRAINT `students_ibfk_3` FOREIGN KEY (`gender_id`) REFERENCES `gender` (`gender_id`),
  CONSTRAINT `students_ibfk_4` FOREIGN KEY (`student_type_id`) REFERENCES `student_types` (`student_type_id`),
  CONSTRAINT `students_ibfk_5` FOREIGN KEY (`nationality_id`) REFERENCES `nationality` (`nationality_id`),
  CONSTRAINT `students_ibfk_6` FOREIGN KEY (`religion_id`) REFERENCES `religion` (`religion_id`),
  CONSTRAINT `students_ibfk_7` FOREIGN KEY (`caste_id`) REFERENCES `caste` (`caste_id`),
  CONSTRAINT `students_ibfk_8` FOREIGN KEY (`sub_caste_id`) REFERENCES `sub_caste` (`sub_caste_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE blood_groups (
  blood_group_id INT PRIMARY KEY AUTO_INCREMENT,
  blood_group VARCHAR(3) NOT NULL UNIQUE,  -- E.g., 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE gender (
  gender_id INT PRIMARY KEY AUTO_INCREMENT,
  gender_name VARCHAR(10) NOT NULL UNIQUE,  -- E.g., 'Male', 'Female', 'Other'
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE student_types (
  student_type_id INT PRIMARY KEY AUTO_INCREMENT,
  student_type_name VARCHAR(50) NOT NULL UNIQUE,  -- E.g., 'Day Scholar', 'Hosteler', 'Day Scholar College Bus'
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE caste (
  caste_id INT PRIMARY KEY AUTO_INCREMENT,
  caste_name VARCHAR(50) NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE sub_caste (
  sub_caste_id INT PRIMARY KEY AUTO_INCREMENT,
  sub_caste_name VARCHAR(50) NOT NULL UNIQUE,
  caste_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (caste_id) REFERENCES caste(caste_id) ON DELETE CASCADE
);

CREATE TABLE nationality (
  nationality_id INT PRIMARY KEY AUTO_INCREMENT,
  nationality_name VARCHAR(50) NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE religion (
  religion_id INT PRIMARY KEY AUTO_INCREMENT,
  religion_name VARCHAR(50) NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE states (
  state_id INT PRIMARY KEY AUTO_INCREMENT,
  state_name VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE districts (
  district_id INT PRIMARY KEY AUTO_INCREMENT,
  district_name VARCHAR(50) NOT NULL UNIQUE,
  state_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (state_id) REFERENCES states(state_id) ON DELETE CASCADE
);
    CREATE TABLE student_educational_details (
        edu_id INT PRIMARY KEY AUTO_INCREMENT,
        edu_course_name  VARCHAR(255) NOT NULL,
        student_id INT,
        year_of_passing YEAR NOT NULL,
        class_division VARCHAR(50) NOT NULL,
        percentage_grade VARCHAR(50) NOT NULL,
        board_university VARCHAR(255) NOT NULL,
        district_id INT,
        state_id INT,
        subjects_offered TEXT NOT NULL,
        certificate_document VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
        FOREIGN KEY (district_id) REFERENCES districts(district_id) ON DELETE SET NULL,
        FOREIGN KEY (state_id) REFERENCES states(state_id) ON DELETE SET NULL
    );

    CREATE TABLE student_additional_documents (
        doc_id INT PRIMARY KEY AUTO_INCREMENT,
        document_name VARCHAR(255) NOT NULL,
        student_id INT,
        document_path VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
    );

-- 

-- faculty 
CREATE TABLE faculty (
    faculty_id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_name VARCHAR(255) NOT NULL,
    department_id INT,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- subject assignments


CREATE TABLE subject_assignment (
    assignment_id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT,
    subject_id INT,
    semester_id INT,
    FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE periods (
    period_id INT PRIMARY KEY AUTO_INCREMENT,
    period_name VARCHAR(50) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE weekdays (
    weekday_id INT PRIMARY KEY AUTO_INCREMENT,
    weekday_name ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE timetable (
    timetable_id INT PRIMARY KEY AUTO_INCREMENT,
    subject_assignment_id INT,
    weekday_id INT NOT NULL,
    period_id INT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (subject_assignment_id) REFERENCES subject_assignment(assignment_id) ON DELETE CASCADE,
    FOREIGN KEY (weekday_id) REFERENCES weekdays(weekday_id) ON DELETE CASCADE,
    FOREIGN KEY (period_id) REFERENCES periods(period_id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE attendance (
    attendance_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    subject_assignment_id INT,
    attendance_date DATE NOT NULL,
    period_id INT,
    status ENUM('Present', 'Absent') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_assignment_id) REFERENCES subject_assignment(assignment_id) ON DELETE CASCADE,
    FOREIGN KEY (period_id) REFERENCES periods(period_id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

DELIMITER //

CREATE TRIGGER prevent_duplicate_attendance
BEFORE INSERT ON attendance
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1
        FROM attendance
        WHERE student_id = NEW.student_id
          AND attendance_date = NEW.attendance_date
          AND period_id = NEW.period_id
    ) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Duplicate attendance record for the same student, date, and period is not allowed';
    END IF;
END;

//

DELIMITER ;
