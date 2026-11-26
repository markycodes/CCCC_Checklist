<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BsoaCurriculumTest extends TestCase
{
    public function test_bsoa_course_rows_match_the_reference(): void
    {
        $curriculum = require __DIR__.'/../../config/prospectus/BSOA.php';
        $actualRows = [];

        foreach ($curriculum['curriculum'] as $year) {
            foreach ($year['semesters'] as $semester) {
                foreach ($semester['courses'] as $course) {
                    $actualRows[] = $course;
                }
            }
        }

        $this->assertSame([
            ['GED 1003', 'The Contemporary World w/ Peace Educ.', 3, 0, 3, ''],
            ['GED 1006', 'Art Appreciation', 3, 0, 3, ''],
            ['GED 1007', 'Science, Technology and Society w/ SARS Education, HIV/AIDS', 3, 0, 3, ''],
            ['GED 1008', 'Ethics', 3, 0, 3, ''],
            ['GED 1009', 'Life and Works of Rizal', 3, 0, 3, ''],
            ['ABM 1001', 'Business Mathematics (For Non-ABM track)', 3, 0, 3, ''],
            ['ABM 1002', 'Fundamentals of Accounting 1 (For Non-ABM track)', 3, 0, 3, ''],
            ['PATHFit 1101', 'Movement Competency Training', 2, 0, 2, ''],
            ['NSTP 1101a', '(NSTP-CWTS 1)', 3, 0, 3, ''],
            ['GED 1001', 'Understanding the Self', 3, 0, 3, ''],
            ['GED 1002', 'Readings in the Phil. History', 3, 0, 3, ''],
            ['GED 1004', 'Mathematics in the Modern World', 3, 0, 3, ''],
            ['GED 1005', 'Purposive Communication', 3, 0, 3, ''],
            ['GED 1010a', "People and the Earth's Ecosystem", 3, 0, 3, ''],
            ['ABM 1003', 'Fundamentals of Accounting 2 (For Non-ABM track)', 3, 0, 3, 'ABM 1002'],
            ['ABM 1004', 'Business Finance (For Non-ABM track)', 3, 0, 3, ''],
            ['PATHFit 1202', 'Exercise-based Fitness Activities', 2, 0, 2, 'PATHFit 1101'],
            ['NSTP 1202b', '(NSTP-CWTS 2)', 3, 0, 3, 'NSTP 1101a'],
            ['GED 1011', 'Environmental Science', 3, 0, 3, ''],
            ['GED 1012', 'Gender and Society', 3, 0, 3, ''],
            ['OAC 2101', 'Foundations Shorthand', 3, 0, 3, ''],
            ['OAC 2102', 'Personal and Professional Development', 3, 0, 3, ''],
            ['ISS 1001', "Samar Studies 1: Historical and Social Dev't", 2, 1, 3, ''],
            ['PATHFit 2103', 'Physical Fitness Towards Health and Fitness and Dance', 2, 0, 2, 'PATHFit 1101, 1202'],
            ['OAC 2203', 'Keyboarding & Documents Processing 1', 1, 2, 3, 'OAC 2101'],
            ['OAC 2204', 'Advanced Shorthand', 3, 0, 3, 'OAC 2101'],
            ['OAC 2205', 'Customer Relations', 3, 0, 3, ''],
            ['OAC 2206', 'Integrated Software Applications (MIS Concept, Desktop Publishing, Word Processing, Spreadsheet, and Presentation 1', 1, 2, 3, ''],
            ['ISS 1002', 'Samar Studies 2: Arts & Culture', 2, 1, 3, 'ISS 1001'],
            ['PATHFit 2204', 'Physical Fitness Towards Health and Fitness and Sports', 2, 0, 2, 'PATHFit 1101,1202,1203'],
            ['OAC 3107', 'Internet Research for Business', 1, 2, 3, ''],
            ['OAC 3108', 'Administration Office Procedures and Management', 3, 0, 3, ''],
            ['OAC 3109', 'Machine Shorthand I', 1, 2, 3, 'OAC 2101'],
            ['PEC 3101', 'International Studies', 3, 0, 3, ''],
            ['CBM 3101', 'Operations Management (TQM)', 3, 0, 3, ''],
            ['OAC 3210', 'Office Administration Internship (300hrs)', 1, 2, 3, ''],
            ['OAC 3211', 'Events Management', 3, 0, 3, ''],
            ['PEC 3202', 'Legal Office Procedures', 1, 2, 3, ''],
            ['PEC 3203', 'Medical Office Procedures', 1, 2, 3, ''],
            ['CBM 3202', 'Strategic Management', 3, 0, 3, 'CBM 3101'],
            ['OAC 4112', 'Business Report Writing', 3, 0, 3, ''],
            ['OAC 4113', 'Entrepreneurial Behavior and Competencies', 3, 0, 3, ''],
            ['OAC 4114', 'Business Law', 3, 0, 3, ''],
            ['PEC 4104', 'Web Design', 1, 2, 3, ''],
            ['IFL 1003', 'Foreign Language', 3, 0, 3, ''],
            ['OAC 4215', 'Taxation', 3, 0, 3, ''],
            ['PEC 4205', 'Introduction to Project Management', 1, 2, 3, ''],
            ['PEC 4206', 'Legal Office Internship (300hrs.) or Medical Office Internship (300 hrs.)', 1, 2, 3, ''],
        ], $actualRows);

        $this->assertSame('128 Units', $curriculum['grand_total']);
        $this->assertSame('140 units', $curriculum['alternate_grand_total']);
        $this->assertSame([20, 0, 20], $curriculum['curriculum'][0]['semesters'][0]['primary_totals']);
        $this->assertSame([26, 0, 20], $curriculum['curriculum'][0]['semesters'][0]['non_abm_totals']);
        $this->assertSame([20, 0, 20], $curriculum['curriculum'][0]['semesters'][1]['primary_totals']);
        $this->assertSame([26, 0, 20], $curriculum['curriculum'][0]['semesters'][1]['non_abm_totals']);
    }

    public function test_bsoa_summary_matches_the_reference_categories(): void
    {
        $curriculum = require __DIR__.'/../../config/prospectus/BSOA.php';

        $this->assertSame([
            ['head', 'A. GENERAL EDUCATION COURSES', 'CMO No. 20, s.2013 and CMO No. 4, s.2018', 'UNIT/S'],
            ['row', 'CORE COURSES', 'GED 1001, GED 1002, GED 1003, GED 1004, GED 1005, GED 1006, GED 1007, GED 1008', '24'],
            ['row', 'ELECTIVE COURSES', 'GED 1010a, GED 1011, GED 1012', '9'],
            ['row', 'MANDATED COURSES', 'GED 1009, NSTP 1101a, NSTP 1202b, PathFit 1101, PathFit 1202, PathFit 2103, PathFit 2204', '17'],
            ['row', 'INSTITUTIONAL COURSES', 'ISS 1001, ISS 1002, IFL 1003', '9'],
            ['row', 'BRIDGING SUBJECTS (For Non-ABM track)', 'ABM 1001, ABM 1002, ABM 1003, ABM 1004', '12'],
            ['head', 'B. PROFESSIONAL COURSES', '', ''],
            ['row', 'CORE COURSES (CORE BUSINESS AND MANAGEMENT EDUCATION)', 'CBM 3101, 3202', '6'],
            ['row', 'OFFICE ADMINISTRATION CORE COURSES', 'OAC 2101, 2102, 2203, 2204, 2205, 2206, 3107, 3108, 3109, 3210, 3211, 4112, 4113, 4114, 4215', '45'],
            ['row', 'PROFESSIONAL ELECTIVE COURSES', 'PEC 3101, 3202, 3203, 4104, 4205, 4206', '18'],
            ['total', '', 'TOTAL UNITS', '128'],
            ['total', '', 'GRAND TOTAL (For NON-ABM Track)', '140'],
        ], $curriculum['summary']);
    }
}
