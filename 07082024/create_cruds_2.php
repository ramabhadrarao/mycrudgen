<?php 
$tables = [
    'college' => ['college_id', 'college_name', 'college_code', 'logo'],
    'departments' => ['department_id', 'department_name', 'department_code', 'college_id', 'logo'],
    'programs' => ['program_id', 'program_name', 'program_code', 'department_id'],
    'branches' => ['branch_id', 'branch_name', 'branch_code', 'program_id'],
    'regulations' => ['regulation_id', 'regulation_name', 'regulation_code', 'program_id', 'branch_id'],
    'semesters' => ['semester_id', 'semester_name', 'regulation_id'],
    'subject_types' => ['subject_type_id', 'type_name'],
    'subjects' => ['subject_id', 'subject_code', 'subject_name', 'semester_id', 'branch_id', 'regulation_id', 'subject_type_id', 'credits'],
    'mark_types' => ['mark_type_id', 'mark_type_name'],
    'marks_distribution' => ['marks_distribution_id', 'subject_id', 'mark_type_id', 'marks'],
    'elective_groups' => ['elective_group_id', 'group_name', 'semester_id'],
    'elective_group_subjects' => ['elective_group_subject_id', 'elective_group_id', 'subject_id'],
    'batches' => ['batch_id', 'batch_name', 'program_id', 'branch_id', 'start_year', 'end_year'],
    'students' => ['student_id', 'admission_no', 'regd_no', 'photo', 'blood_group_id', 'name', 'gender_id', 'email', 'mobile', 'father_name', 'mother_name', 'aadhar', 'father_aadhar', 'mother_aadhar', 'father_mobile', 'mother_mobile', 'address', 'nationality_id', 'religion_id', 'student_type_id', 'caste_id', 'sub_caste_id', 'batch_id'],
    'blood_groups' => ['blood_group_id', 'blood_group'],
    'gender' => ['gender_id', 'gender_name'],
    'student_types' => ['student_type_id', 'student_type_name'],
    'caste' => ['caste_id', 'caste_name'],
    'sub_caste' => ['sub_caste_id', 'sub_caste_name', 'caste_id'],
    'nationality' => ['nationality_id', 'nationality_name'],
    'religion' => ['religion_id', 'religion_name'],
    'states' => ['state_id', 'state_name'],
    'districts' => ['district_id', 'district_name', 'state_id'],
    'student_educational_details' => ['edu_id', 'edu_course_name', 'student_id', 'year_of_passing', 'class_division', 'percentage_grade', 'board_university', 'district_id', 'state_id', 'subjects_offered', 'certificate_document'],
    'student_additional_documents' => ['doc_id', 'document_name', 'student_id', 'document_path'],
    'faculty' => ['faculty_id', 'faculty_name', 'department_id'],
    'subject_assignment' => ['assignment_id', 'faculty_id', 'subject_id', 'semester_id'],
    'periods' => ['period_id', 'period_name', 'start_time', 'end_time'],
    'weekdays' => ['weekday_id', 'weekday_name'],
    'timetable' => ['timetable_id', 'subject_assignment_id', 'weekday_id', 'period_id', 'start_time', 'end_time'],
    'attendance' => ['attendance_id', 'student_id', 'subject_assignment_id', 'attendance_date', 'period_id', 'status']
];

$foreignKeys = [
    'departments' => [
        'college_id' => ['table' => 'college', 'key' => 'college_id', 'field' => 'college_name']
    ],
    'programs' => [
        'department_id' => ['table' => 'departments', 'key' => 'department_id', 'field' => 'department_name']
    ],
    'branches' => [
        'program_id' => ['table' => 'programs', 'key' => 'program_id', 'field' => 'program_name']
    ],
    'regulations' => [
        'program_id' => ['table' => 'programs', 'key' => 'program_id', 'field' => 'program_name'],
        'branch_id' => ['table' => 'branches', 'key' => 'branch_id', 'field' => 'branch_name']
    ],
    'semesters' => [
        'regulation_id' => ['table' => 'regulations', 'key' => 'regulation_id', 'field' => 'regulation_name']
    ],
    'subjects' => [
        'semester_id' => ['table' => 'semesters', 'key' => 'semester_id', 'field' => 'semester_name'],
        'branch_id' => ['table' => 'branches', 'key' => 'branch_id', 'field' => 'branch_name'],
        'regulation_id' => ['table' => 'regulations', 'key' => 'regulation_id', 'field' => 'regulation_name'],
        'subject_type_id' => ['table' => 'subject_types', 'key' => 'subject_type_id', 'field' => 'type_name']
    ],
    'marks_distribution' => [
        'subject_id' => ['table' => 'subjects', 'key' => 'subject_id', 'field' => 'subject_name'],
        'mark_type_id' => ['table' => 'mark_types', 'key' => 'mark_type_id', 'field' => 'mark_type_name']
    ],
    'elective_groups' => [
        'semester_id' => ['table' => 'semesters', 'key' => 'semester_id', 'field' => 'semester_name']
    ],
    'elective_group_subjects' => [
        'elective_group_id' => ['table' => 'elective_groups', 'key' => 'elective_group_id', 'field' => 'group_name'],
        'subject_id' => ['table' => 'subjects', 'key' => 'subject_id', 'field' => 'subject_name']
    ],
    'students' => [
        'blood_group_id' => ['table' => 'blood_groups', 'key' => 'blood_group_id', 'field' => 'blood_group'],
        'gender_id' => ['table' => 'gender', 'key' => 'gender_id', 'field' => 'gender_name'],
        'student_type_id' => ['table' => 'student_types', 'key' => 'student_type_id', 'field' => 'student_type_name'],
        'caste_id' => ['table' => 'caste', 'key' => 'caste_id', 'field' => 'caste_name'],
        'sub_caste_id' => ['table' => 'sub_caste', 'key' => 'sub_caste_id', 'field' => 'sub_caste_name'],
        'nationality_id' => ['table' => 'nationality', 'key' => 'nationality_id', 'field' => 'nationality_name'],
        'religion_id' => ['table' => 'religion', 'key' => 'religion_id', 'field' => 'religion_name'],
        'batch_id' => ['table' => 'batches', 'key' => 'batch_id', 'field' => 'batch_name']
    ],
    'sub_caste' => [
        'caste_id' => ['table' => 'caste', 'key' => 'caste_id', 'field' => 'caste_name']
    ],
    'districts' => [
        'state_id' => ['table' => 'states', 'key' => 'state_id', 'field' => 'state_name']
    ],
    'student_educational_details' => [
        'student_id' => ['table' => 'students', 'key' => 'student_id', 'field' => 'name'],
        'district_id' => ['table' => 'districts', 'key' => 'district_id', 'field' => 'district_name'],
        'state_id' => ['table' => 'states', 'key' => 'state_id', 'field' => 'state_name']
    ],
    'student_additional_documents' => [
        'student_id' => ['table' => 'students', 'key' => 'student_id', 'field' => 'name']
    ],
    'faculty' => [
        'department_id' => ['table' => 'departments', 'key' => 'department_id', 'field' => 'department_name']
    ],
    'subject_assignment' => [
        'faculty_id' => ['table' => 'faculty', 'key' => 'faculty_id', 'field' => 'faculty_name'],
        'subject_id' => ['table' => 'subjects', 'key' => 'subject_id', 'field' => 'subject_name'],
        'semester_id' => ['table' => 'semesters', 'key' => 'semester_id', 'field' => 'semester_name']
    ],
    'timetable' => [
        'subject_assignment_id' => ['table' => 'subject_assignment', 'key' => 'assignment_id', 'field' => 'subject_id'],
        'weekday_id' => ['table' => 'weekdays', 'key' => 'weekday_id', 'field' => 'weekday_name'],
        'period_id' => ['table' => 'periods', 'key' => 'period_id', 'field' => 'period_name']
    ],
    'attendance' => [
        'student_id' => ['table' => 'students', 'key' => 'student_id', 'field' => 'name'],
        'subject_assignment_id' => ['table' => 'subject_assignment', 'key' => 'assignment_id', 'field' => 'subject_id'],
        'period_id' => ['table' => 'periods', 'key' => 'period_id', 'field' => 'period_name']
    ]
];

$uniqueKeys = [
    'college' => ['college_code'],
    'departments' => ['department_code'],
    'programs' => ['program_code'],
    'branches' => ['branch_code'],
    'regulations' => ['regulation_code'],
    'subjects' => ['subject_code'],
    'students' => ['admission_no', 'aadhar', 'father_aadhar', 'mother_aadhar'],
    'blood_groups' => ['blood_group'],
    'gender' => ['gender_name'],
    'student_types' => ['student_type_name'],
    'caste' => ['caste_name'],
    'sub_caste' => ['sub_caste_name'],
    'nationality' => ['nationality_name'],
    'religion' => ['religion_name'],
    'states' => ['state_name'],
    'districts' => ['district_name']
];
// Generate CRUD files for each table
foreach ($tables as $table => $columns) {
    $foreignKeysForTable = $foreignKeys[$table] ?? [];
    $uniqueKeysForTable = $uniqueKeys[$table] ?? [];
    $generator = new CRUDGenerator($table, $columns, $foreignKeysForTable, $uniqueKeysForTable);
    $generator->generateFiles();
}
?>
