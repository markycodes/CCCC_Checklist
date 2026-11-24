<?php

/*
 | BPA Prospectus template settings.
 | Change fonts/paper/curriculum here; the views read everything from this file.
 | Course row = [code, title, lecture hrs, lab hrs, credit units, pre/co-requisite]
 */
return [
    'filename' => 'BPA-Prospectus-Students-Copy.pdf',
    'paper' => [0, 0, 612, 936],   // 8.5in x 13in (long bond) in points
    'copy_label' => 'STUDENT’S COPY',

    // Same font in the browser preview and in the PDF so both look identical.
    // (DomPDF has no "Arial Narrow"; Helvetica/Arial is built in.)
    'font_family' => 'Helvetica, Arial, sans-serif',

    // Table column widths: code, title, lecture, lab, credit, pre-requisite
    'cols' => ['16%', '42%', '8%', '8%', '8%', '18%'],

    // Spacing in pt. If page 1 overflows, lower row_pad / sem_gap a little.
    'spacing' => ['line_height' => 1.05, 'row_pad' => 0.6, 'sem_gap' => 5],

    // Font sizes in pt
    'font' => [
        'republic' => 9,  'college' => 10, 'program' => 11, 'ref' => 9,
        'year' => 14,     'sem' => 11,     'colhead' => 10, 'row' => 10,
        'total' => 10,    'eval' => 10,
    ],

    // Order: Bagong Pilipinas, CHED, City Seal, College Seal (files in public/assets/img)
    'logos' => ['partner-bagong-pilipinas.png', 'partner-ched.png', 'partner-catbalogan.png', 'cccc.png'],

    'header' => [
        'republic' => ['Republic of the Philippines', 'Province of Samar'],
        'city' => 'CITY OF CATBALOGAN',
        'college' => 'CATBALOGAN CITY COMMUNITY COLLEGE',
        'program' => 'Bachelor of Public Administration (BPA)',
        'refs' => [
            'CMO No. 6 s.2010, CMO No. 20, s.2013, CMO No. 39, s.2021',
            'BOT Res. No. 25, s.2024',
            'Dated: November 20, 2024',
        ],
    ],

    'grading' => [
        [['98-100', '4.00', 'A+'], ['83-85', '2.75', 'B-']],
        [['95-97', '3.75', 'A'],  ['79-82', '2.50', 'C+']],
        [['92-94', '3.50', 'A-'], ['76-78', '2.25', 'C']],
        [['89-91', '3.25', 'B+'], ['75',    '2.00', 'C-']],
        [['86-88', '3.00', 'B'],  ['Below 75', '1.00', 'D']],
    ],
    'grading_foot' => 'AD (Authorized Dropped) | DU (Dropped Officially) | LOA (Leave of Absence)',
    'grading_res' => 'As per Board Resolution No. 18, Series 2024',

    'curriculum' => [
        [
            'label' => 'FIRST YEAR', 'page_break_before' => false,
            'semesters' => [
                ['label' => 'FIRST SEMESTER', 'courses' => [
                    ['GED 1003', 'The Contemporary World w/ Peace Educ.', 3, 0, 3, ''],
                    ['GED 1006', 'Art Appreciation', 3, 0, 3, ''],
                    ['GED 1007', 'Science, Technology and Society w/ SARS Education, HIV/AIDS', 3, 0, 3, ''],
                    ['GED 1008', 'Ethics with Logic', 3, 0, 3, ''],
                    ['GED 1009', 'The Life and Works of Rizal', 3, 0, 3, ''],
                    ['PAC 1101', 'Intro. to Public Administration', 3, 0, 3, ''],
                    ['PATHFit 1101', 'Movement Competency Training', 2, 0, 2, ''],
                    ['NSTP 1101a', 'NSTP-CWTS 1', 3, 0, 3, ''],
                ]],
                ['label' => 'SECOND SEMESTER', 'courses' => [
                    ['GED 1001', 'Understanding the Self', 3, 0, 3, ''],
                    ['GED 1002', 'Readings in the Phil. History', 3, 0, 3, ''],
                    ['GED 1004', 'Mathematics in the Modern World', 3, 0, 3, ''],
                    ['GED 1005', 'Purposive Communication', 3, 0, 3, ''],
                    ['GED 1010', 'The Entrepreneurial Mind', 3, 0, 3, ''],
                    ['PAC 1202', 'Philippine Administrative Thoughts and Institution', 3, 0, 3, ''],
                    ['PATHFit 1202', 'Exercise-based Fitness Activities', 2, 0, 2, 'PATHFit 1101'],
                    ['NSTP 1202b', 'NSTP-CWTS 2', 3, 0, 3, 'NSTP 1101a'],
                ]],
            ],
        ],
        [
            'label' => 'SECOND YEAR', 'page_break_before' => false,
            'semesters' => [
                ['label' => 'FIRST SEMESTER', 'courses' => [
                    ['GED 1011', 'Environmental Science', 3, 0, 3, ''],
                    ['GED 1012', 'Gender and Society', 3, 0, 3, ''],
                    ['PSC 2101', 'Good Governance & Social Responsibility', 3, 0, 3, ''],
                    ['PAC 2103', 'Organization and Management', 3, 0, 3, ''],
                    ['PAE 2101', 'Service Delivery System', 3, 0, 3, ''],
                    ['ISS 1001', 'Samar Studies 1: Historical and Social Dev’t', 2, 1, 3, ''],
                    ['PATHFit 2103', 'Physical Fitness Towards Health and Fitness and Dance', 2, 0, 2, 'PATHFit 1101, 1202'],
                ]],
                ['label' => 'SECOND SEMESTER', 'courses' => [
                    ['PAC 2204', 'Human Behavior in Organization', 3, 0, 3, ''],
                    ['PAC 2205', 'Office and Systems Management', 3, 0, 3, ''],
                    ['PAC 2206', 'Local and Regional Governance', 3, 0, 3, ''],
                    ['PAC 2207', 'Knowledge Management and ICT for PA', 3, 0, 3, ''],
                    ['PAC 2208', 'Ethics & Accountability in the Public Service', 3, 0, 3, ''],
                    ['ISS 1002', 'Samar Studies 2: Arts & Culture', 2, 1, 3, 'ISS 1001'],
                    ['PATHFit 2204', 'Physical Fitness Towards Health and Fitness and Sports', 2, 0, 2, 'PATHFit 1101, 1202, 2103'],
                ]],
            ],
        ],
        [
            'label' => 'THIRD YEAR', 'page_break_before' => true,
            'semesters' => [
                ['label' => 'FIRST SEMESTER', 'courses' => [
                    ['PSC 3102', 'Fundamentals of Statistics', 3, 0, 3, ''],
                    ['PAC 3109', 'Public Fiscal Administration', 3, 0, 3, ''],
                    ['PAC 3110', 'Public Accounting and Budgeting', 3, 0, 3, ''],
                    ['PAC 3111', 'Public Policy and Program Administration', 3, 0, 3, ''],
                    ['PAE 3102', 'Program and Project Dev’t and Mgt.', 3, 0, 3, ''],
                    ['PAE 3103', 'Policy Analysis', 3, 0, 3, ''],
                ]],
                ['label' => 'SECOND SEMESTER', 'courses' => [
                    ['PSC 3203', 'Basic Accounting', 3, 0, 3, ''],
                    ['PAC 3212', 'Politics and Administration', 3, 0, 3, ''],
                    ['PAC 3213', 'Administrative Law', 3, 0, 3, ''],
                    ['PAC 3214', 'Research Methods Applied in Public Administration', 3, 0, 3, ''],
                    ['PAC 3215', 'Leadership and Decision Making', 3, 0, 3, ''],
                    ['PAC 3216', 'Governance and Development', 3, 0, 3, ''],
                ]],
            ],
        ],
        [
            'label' => 'FOURTH YEAR', 'page_break_before' => false,
            'semesters' => [
                ['label' => 'FIRST SEMESTER', 'courses' => [
                    ['PSC 4104', 'Sociology', 3, 0, 3, ''],
                    ['PAC 4117', 'Public Personnel Administration', 3, 0, 3, ''],
                    ['PAC 4118', 'Special Topics in Public Administration with Seminar', 3, 0, 3, ''],
                    ['PAC 4119', 'Research Writing and Presentation', 3, 0, 3, ''],
                    ['PAE 4104', 'Environmental Management', 3, 0, 3, ''],
                ]],
                ['label' => 'SECOND SEMESTER', 'show_grand_total' => true, 'courses' => [
                    ['IFL 1003', 'Foreign Language', 3, 0, 3, ''],
                    ['PPA 4201', 'Practicum/Work Integrated Learning for Public Administration (200 hours)', 0, 0, 6, ''],
                ]],
            ],
        ],
    ],

    // type: head | row | blank | total
    'summary' => [
        ['head', 'A. GENERAL EDUCATION COURSES', 'CMO No. 6 s.2010, CMO No. 20, s.2013, CMO No. 39, s.2021', 'UNIT/S'],
        ['row', 'CORE COURSES', 'GED 1001, GED 1002, GED 1003, GED 1004, GED 1005, GED 1006, GED 1007, GED 1008', '24'],
        ['row', 'ELECTIVE COURSES', 'GED 1010, GED 1011, GED 1012', '9'],
        ['row', 'MANDATED COURSES', 'GED 1009, NSTP 1101a, NSTP 1202b, PathFit 1101, PathFit 1202, PathFit 2103, PathFit 2204', '17'],
        ['row', 'INSTITUTIONAL COURSES', 'ISS 1001, ISS 1002, IFL 1003', '9'],
        ['blank'],
        ['head', 'B. PROFESSIONAL COURSES', '', ''],
        ['row', 'PUBLIC ADMINISTRATION SUBJECTS', 'PAC 1101, 1202, 2103, 2204, 2205, 2206, 2207, 2208, 3109, 3110, 3111, 3212, 3213, 3214, 3215, 3216, 4117, 4118, 4119', '57'],
        ['row', 'SPECIAL CORE COURSES', 'PSC 2101, 3102, 3203, 4104', '12'],
        ['row', 'PUBLIC ADMINISTRATION ELECTIVES', 'PAE 2101, 3102, 3103, 4104', '12'],
        ['row', 'PRACTICUM', 'PPA 4201 (Practicum/ Work integrated Learning for Public Administration (200 hours))', '6'],
        ['total', '', 'TOTAL UNITS', '146'],
    ],
];
