<?php

/*
 | BSENTREP Prospectus template settings.
 | Course row = [code, title, lecture hrs, lab hrs, credit units, pre/co-requisite]
 */
return [
    'filename' => 'BSENTREP-Tourism-Hospitality-Prospectus-Students-Copy.pdf',
    'paper' => [0, 0, 612, 936],
    'copy_label' => 'DEAN’S COPY',
    'font_family' => 'Helvetica, Arial, sans-serif',
    'cols' => ['18%', '40%', '8%', '8%', '8%', '18%'],
    'spacing' => ['line_height' => 1.05, 'row_pad' => 0.5, 'sem_gap' => 5],
    'font' => [
        'republic' => 8, 'college' => 9, 'program' => 10, 'ref' => 8,
        'year' => 13, 'sem' => 10, 'colhead' => 9, 'row' => 9,
        'total' => 8.5, 'eval' => 8.5,
    ],
    'logos' => ['partner-bagong-pilipinas.png', 'partner-ched.png', 'partner-catbalogan.png', 'cccc.png'],
    'header' => [
        'republic' => ['Republic of the Philippines', 'Province of Samar'],
        'city' => 'CITY OF CATBALOGAN',
        'college' => 'CATBALOGAN CITY COMMUNITY COLLEGE',
        'program' => 'Bachelor of Science in Entrepreneurship (BSENTREP) (with Special Track in Tourism and Hospitality Business)',
        'refs' => [
            'CMO No. 18 s.2017, CMO No. 20, s.2013, CMO No. 39, s.2021',
            'BOT Res. No. 24, s. 2024',
            'Dated: November 20, 2024',
        ],
    ],
    'grading' => [
        [['98-100', '4.00', 'A+'], ['83-85', '2.75', 'B-']],
        [['95-97', '3.75', 'A'], ['79-82', '2.50', 'C+']],
        [['92-94', '3.50', 'A-'], ['76-78', '2.25', 'C']],
        [['89-91', '3.25', 'B+'], ['75', '2.00', 'C-']],
        [['86-88', '3.00', 'B'], ['Below 75', '1.00', 'D']],
    ],
    'grading_foot' => 'AD (Authorized Dropped) | DU (Dropped Officially) | LOA (Leave of Absence)',
    'grading_res' => 'As per Board Resolution No. 18, Series 2024',
    'grand_total' => '138 Units',
    'alternate_grand_total' => '150 units',
    'curriculum' => [
        [
            'label' => 'FIRST YEAR',
            'page_break_before' => false,
            'semesters' => [
                [
                    'label' => 'FIRST SEMESTER',
                    'primary_totals' => [20, 0, 20],
                    'non_abm_totals' => [26, 0, 26],
                    'courses' => [
                        ['GED 1003', 'The Contemporary World w/ Peace Educ.', 3, 0, 3, ''],
                        ['GED 1006', 'Art Appreciation', 3, 0, 3, ''],
                        ['GED 1007', 'Science, Technology, and Society w/ SARS Education, HIV/AIDS', 3, 0, 3, ''],
                        ['GED 1008', 'Ethics', 3, 0, 3, ''],
                        ['GED 1009', 'Life and Works of Rizal', 3, 0, 3, ''],
                        ['ABM 1001', 'Business Mathematics (For Non-ABM track)', 3, 0, 3, ''],
                        ['ABM 1002', 'Fundamentals of Accounting 1 (For Non-ABM track)', 3, 0, 3, ''],
                        ['PATHFit 1101', 'Movement Competency Training', 2, 0, 2, ''],
                        ['NSTP 1101a', 'Civic Welfare Training Service 1 (NSTP-CWTS 1)', 3, 0, 3, ''],
                    ],
                ],
                [
                    'label' => 'SECOND SEMESTER',
                    'primary_totals' => [20, 0, 20],
                    'non_abm_totals' => [26, 0, 20],
                    'courses' => [
                        ['GED 1001', 'Understanding the Self', 3, 0, 3, ''],
                        ['GED 1002', 'Readings in the Phil. History', 3, 0, 3, ''],
                        ['GED 1004', 'Mathematics in the Modern World', 3, 0, 3, ''],
                        ['GED 1005', 'Purposive Communication', 3, 0, 3, ''],
                        ['GED 1010a', "People and the Earth's Ecosystem", 3, 0, 3, ''],
                        ['ABM 1003', 'Fundamentals of Accounting 2 (For Non-ABM track)', 3, 0, 3, 'ABM 1002'],
                        ['ABM 1004', 'Business Finance (For Non-ABM track)', 3, 0, 3, ''],
                        ['PATHFit 1202', 'Exercise-based Fitness Activities', 2, 0, 2, 'PATHFit 1101'],
                        ['NSTP 1202b', 'Civic Welfare Training Service 2 (NSTP-CWTS 2)', 3, 0, 3, 'NSTP 1101a'],
                    ],
                ],
            ],
        ],
        [
            'label' => 'SECOND YEAR',
            'page_break_before' => false,
            'extra_spacing_before' => true,
            'semesters' => [
                ['label' => 'FIRST SEMESTER', 'courses' => [
                    ['GED 1011', 'Environmental Science', 3, 0, 3, ''],
                    ['GED 1012', 'Gender and Society', 3, 0, 3, ''],
                    ['ENT 2101', 'Entrepreneurial Behavior', 3, 0, 3, ''],
                    ['ENT 2102', 'Microeconomics', 3, 0, 3, ''],
                    ['ISS 1001', "Samar Studies 1: Historical and Social Dev't", 2, 1, 3, ''],
                    ['PATHFit 2103', 'Physical Fitness Towards Health and Fitness and Dance', 2, 0, 2, 'PATHFit 1101, 1202'],
                ]],
                ['label' => 'SECOND SEMESTER', 'courses' => [
                    ['ENT 2203', 'Opportunity Seeking', 3, 0, 3, 'ENT 2101'],
                    ['ENT 2204', 'Pricing and Costing', 3, 0, 3, 'ENT 2102'],
                    ['EEC 2201', 'Supply Chain Management', 3, 0, 3, ''],
                    ['EST 2201', 'Quality Serv. Mgt. in Tourism & Hosp. Bus.', 3, 0, 3, ''],
                    ['ISS 1002', 'Samar Studies 2: Arts & Culture', 2, 1, 3, 'ISS 1001'],
                    ['PATHFit 2204', 'Physical Fitness Towards Health and Fitness and Sports', 2, 0, 2, 'PATHFit 1101,1202,1203'],
                ]],
            ],
        ],
        [
            'label' => 'THIRD YEAR',
            'page_break_before' => true,
            'semesters' => [
                ['label' => 'FIRST SEMESTER', 'courses' => [
                    ['ENT 3105', 'Market Research & Consumer Behavior', 3, 0, 3, 'ENT 2204'],
                    ['ENT 3106', 'Human Resource Management', 3, 0, 3, ''],
                    ['ENT 3107', 'Financial Management (Financial Analysis for Decision Making)', 3, 0, 3, 'ENT 2102'],
                    ['EEC 3102', 'Entrepreneurial Marketing Strategies', 3, 0, 3, 'ENT 2204'],
                    ['EST 3102', 'Tourism and Hospitality Mktg. Strategy', 3, 0, 3, 'EST 2201'],
                    ['CBM 3101', 'Operations Management (TQM)', 3, 0, 3, ''],
                ]],
                ['label' => 'SECOND SEMESTER', 'courses' => [
                    ['ENT 3208', 'Programs and Policies on Enterprise Development', 3, 0, 3, 'ENT 3105'],
                    ['ENT 3209', 'Social Entrepreneurship', 3, 0, 3, 'ENT 3106'],
                    ['ENT 3210', 'Business Plan Preparation', 3, 0, 3, 'ENT 3105, 3107'],
                    ['ENT 3211', 'Business Law and Taxation, w/ focus on Laws Affecting Micro, Small and Medium Enterprise', 3, 0, 3, ''],
                    ['EEC 3203', 'Management of Technology', 3, 0, 3, ''],
                    ['CBM 3202', 'Strategic Management', 3, 0, 3, 'CBM 3101'],
                ]],
            ],
        ],
        [
            'label' => 'FOURTH YEAR',
            'page_break_before' => false,
            'extra_spacing_before' => true,
            'semesters' => [
                ['label' => 'FIRST SEMESTER', 'courses' => [
                    ['ENT 4112', 'International Business and Trade', 3, 0, 3, 'ENT 3208'],
                    ['ENT 4113', 'Innovation Management', 3, 0, 3, 'ENT 3107'],
                    ['ENT 4114', "Business Plan Implementation 1: Product Dev't and Market Analysis", 2, 3, 5, 'ENT 3210'],
                    ['EST 3103', 'Risk Mgt. as Applied to Safety, Security and Sanitation in Tourism and Hospitality Business', 3, 0, 3, 'EST 3102'],
                ]],
                ['label' => 'SECOND SEMESTER', 'show_grand_total' => true, 'courses' => [
                    ['IFL 1003', 'Foreign Language', 3, 0, 3, ''],
                    ['EEC 4204', 'Negotiation', 3, 0, 3, ''],
                    ['EST 4204', 'Entrepreneurship in Tourism & Hospitality', 3, 0, 3, 'EST 4103'],
                    ['ENT 4215', 'Business Plan Implementation II', 2, 3, 5, 'ENT 4114'],
                ]],
            ],
        ],
    ],
    'summary' => [
        ['head', 'A. GENERAL EDUCATION COURSES', 'CMO No. 20, s.2013 and CMO No. 4, s.2018', 'UNIT/S'],
        ['row', 'CORE COURSES', 'GED 1001, GED 1002, GED 1003, GED 1004, GED 1005, GED 1006, GED 1007, GED 1008', '24'],
        ['row', 'ELECTIVE COURSES', 'GED 1010a, GED 1011, GED 1012', '9'],
        ['row', 'MANDATED COURSES', 'GED 1009, NSTP 1101, NSTP 1202b, PathFit 1101, PathFit 1202, PathFit 2103, PathFit 2204', '17'],
        ['row', 'INSTITUTIONAL COURSES', 'ISS 1001, ISS 1002, IFL 1003', '9'],
        ['row', 'BRIDGING SUBJECTS (For Non-ABM track)', 'ABM 1001, ABM 1002, ABM 1003, ABM 1004', '12'],
        ['head', 'B. PROFESSIONAL COURSES', '', ''],
        ['row', 'CORE BUSINESS & MANAGEMENT EDUCATION COURSES', 'CBM 3101, CBM 3202', '6'],
        ['row', 'ENTREPRENEURSHIP CORE COURSES', 'ENT 2101, 2102, 2203, 2204, 3105, 3106, 3107, 3208, 3209, 3210, 3211, 4112, 4113, 4114, 4215', '49'],
        ['row', 'SPECIALIZED TRACKS COURSES', 'EST 2201, 3102, 3103, 4204', '12'],
        ['row', 'PROFESSIONAL ELECTIVE COURSES', 'EEC 2201, 3102, 3203, 4204', '12'],
        ['total', '', 'TOTAL UNITS', '138'],
        ['total', '', 'GRAND TOTAL (For NON-ABM Track)', '150'],
    ],
];
