<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BsismCurriculumTest extends TestCase
{
    public function test_bsism_course_rows_match_the_reference(): void
    {
        $curriculum = require __DIR__.'/../../config/prospectus/BSISM.php';
        $actualRows = [];
        $unitCount = 0;

        foreach ($curriculum['curriculum'] as $year) {
            foreach ($year['semesters'] as $semester) {
                foreach ($semester['courses'] as $course) {
                    $actualRows[] = $course;
                    $unitCount += $course[4];
                }
            }
        }

        $this->assertSame([
            ['GED 1003', 'The Contemporary World w/ Peace Education', 3, 0, 3, ''],
            ['GED 1006', 'Art Appreciation', 3, 0, 3, ''],
            ['GED 1007', 'Science, Technology and Society w/ SARS Education, HIV/AIDS', 3, 0, 3, ''],
            ['GED 1008', 'Ethics', 3, 0, 3, ''],
            ['GED 1009', 'Life and Works of Rizal', 3, 0, 3, ''],
            ['PFS 1101', 'Fundamentals of Industrial Security', 3, 0, 3, ''],
            ['PE PATHFit 1101', 'Competency Movement Training', 2, 0, 2, ''],
            ['PE DEFTACT 1101', 'Fundamentals of Martial Arts', 2, 0, 2, ''],
            ['NSTP 1101', "Reserve Officers' Training Corps 1 (Military Science 1)", 3, 0, 3, ''],
            ['GED 1001', 'Understanding the Self', 3, 0, 3, ''],
            ['GED 1002', 'Readings in the Philippine History', 3, 0, 3, ''],
            ['GED 1004', 'Mathematics in the Modern World', 3, 0, 3, ''],
            ['GED 1005', 'Purposive Communication', 3, 0, 3, ''],
            ['GED 1010', 'The Entrepreneurial Mind', 3, 0, 3, ''],
            ['PFS 1202', 'Personnel Security', 3, 0, 3, ''],
            ['PE PATHFit 1202', 'Exercise Based Fitness', 2, 0, 2, 'PE PATHFit 1101'],
            ['PE DEFTACT 1202', 'Arnis and Disarming Techniques', 2, 0, 2, 'PE DefTac 1101'],
            ['NSTP 1201', "Reserve Officers' Training Corps 2 (Military Science 2)", 3, 0, 3, 'NSTP 1101'],
            ['PMN 2101', 'Introduction to Human Resource Management w/ Labor Laws', 3, 0, 3, ''],
            ['PMN 2102', 'Intro. to Management & Leadership', 3, 0, 3, ''],
            ['PFS 2103', 'Information Security', 3, 0, 3, ''],
            ['PFS 2104', 'Physical Security Systems', 3, 0, 3, ''],
            ['CLJ 2101', 'Intro.to Phil. Criminal Justice System', 3, 0, 3, ''],
            ['PIS 2201', 'Professional Conduct and Ethical Standards', 3, 0, 3, ''],
            ['ISS 1001', "Samar Studies 1: Historical and Social Dev't", 2, 1, 3, ''],
            ['PE DEFTACT 2103', 'First Aid and Water Safety w/ Disaster Risk Reduction Management (DRRM)', 2, 0, 2, 'PE DefTac 1201'],
            ['GED 1011', 'Environmental Science', 3, 0, 3, ''],
            ['GED 1012', 'Gender and Society', 3, 0, 3, ''],
            ['AGC 1001', 'Logic (Deductive & Inductive Reasoning)', 3, 0, 3, ''],
            ['PFS 2205', 'Intro.to Security Administration & Mgt.', 3, 0, 3, ''],
            ['PFS 2206', "Fund'ls of Investigation and Intelligence", 3, 0, 3, ''],
            ['PMN 2203', 'Introduction to Statistics', 3, 0, 3, ''],
            ['ISS 1002', 'Samar Studies 2: Arts & Culture', 2, 1, 3, ''],
            ['PE DEFTACT 2204', 'Fundamentals of Marksmanship', 2, 0, 2, 'PE DefTac 2103'],
            ['PIS 3102', 'Introduction to Cyber Crime', 3, 0, 3, ''],
            ['PIS 3103', 'Executive Protection Management', 3, 0, 3, ''],
            ['PIS 3104', 'Corporate and Security Investigation and Report Writing', 3, 0, 3, ''],
            ['PIS 3105', 'Emergency Planning & Disaster Mgt.', 3, 0, 3, ''],
            ['PIS 3106', 'Vice and Drug Education & Control', 3, 0, 3, ''],
            ['CLJ 3102', 'Human Rights Education', 3, 0, 3, 'CLJ 2101'],
            ['CLJ 3103', 'Criminal Law (Book I)', 3, 0, 3, 'CLJ 3102 (co-requisite)'],
            ['PMN 3104', 'Fundamentals of Financial Management', 3, 0, 3, ''],
            ['PMN 3105', 'Introduction to Forensic Science', 3, 0, 3, ''],
            ['PIS 3207', 'Security of Critical Infrastructures', 3, 0, 3, ''],
            ['PIS 3208', 'Asset Protection Management and Loss Prevention & Control', 3, 0, 3, ''],
            ['PIS 3209', 'Basic Occupational Safety, Health & Environmental Protection', 3, 0, 3, ''],
            ['PIS 3210', 'Risk Assessment and Management', 3, 0, 3, ''],
            ['CLJ 3204', 'Criminal Law (Book II)', 4, 0, 4, 'CLJ 3103'],
            ['PMN 3206', "Fund'ls of Operations Management", 3, 0, 3, ''],
            ['PIE 3201', 'Security Automation & Sys. Integration', 3, 0, 3, ''],
            ['PIE 3202', 'Traffic Mgt. and Accident Investigation', 3, 0, 3, ''],
            ['IFL 1003', 'Foreign Language', 3, 0, 3, ''],
            ['PIS 4111', 'Real-Estate Sec. Administration & Mgt.', 3, 0, 3, ''],
            ['PIS 4112', 'Business and Competitive Intelligence in Industrial Security', 3, 0, 3, ''],
            ['PIS 4113', 'Crisis Mgt. & Business Continuity Plan', 3, 0, 3, ''],
            ['PIS 4114', 'Crime Prevention, Detection & Control', 3, 0, 3, ''],
            ['PIS 4115', 'Crime Scene Protection and Preservation', 3, 0, 3, ''],
            ['CLJ 4105', 'Evidence', 3, 0, 3, 'CLJ 3204'],
            ['CLJ 4106', 'Criminal Procedure & Court Testimony', 3, 0, 3, 'CLJ 3204'],
            ['ISP 4202', 'Practicum 1 (Private Institutions)', 6, '540 Hours', 6, 'Junior Standing'],
        ], $actualRows);

        $this->assertSame(178, $unitCount);
    }

    public function test_bsism_summary_matches_the_reference_categories(): void
    {
        $curriculum = require __DIR__.'/../../config/prospectus/BSISM.php';

        $this->assertSame([
            ['head', 'A. GENERAL EDUCATION COURSES', 'CMO No. 20, s.2013 and CMO No. 4, s.2018', 'UNIT/S'],
            ['row', 'CORE COURSES', 'GED 1001, GED 1002, GED 1003, GED 1004, GED 1005, GED 1006, GED 1007, GED 1008', '24'],
            ['row', 'ELECTIVE COURSES', 'GED 1010, GED 1011, GED 1012', '9'],
            ['row', 'ADDITIONAL GEN.ED. COURSE', 'AGC 1001', '3'],
            ['row', 'MANDATED COURSES', 'GED 1009, NSTP 1101, NSTP 1201, PEDeftact1101, PEDeftact1202, PathFit 1101a, PathFit 1202b, PathFit 2103c, PathFit 2204d', '21'],
            ['row', 'INSTITUTIONAL COURSES', 'ISS 1001, ISS 1002, IFL 1003', '9'],
            ['head', 'B. PROFESSIONAL COURSES', '', ''],
            ['row', 'CORE COURSES', 'CLJ2101, CLJ3102, CLJ3103, CLJ3204, CLJ4105, CLJ4206', '19'],
            ['row', 'MAJOR COURSES', 'PFS1101, PFS1202, PFS2103, PFS2104, PFS2205, PFS2206, PMN2101, PMN2102, PMN2203, PMN3104, PMN3105, PMN3206, PIS2101, PIS3102, PIS3103, PIS3104, PIS3105, PIS3106, PIS3207, PIS3208, PIS3209, PIS3210, PIS4111, PIS4112, PIS4113, PIS4114, PIS4115, ISP4201', '87'],
            ['row', 'ELECTIVE COURSES', 'PIE3201, PIE3202', '6'],
            ['total', '', 'TOTAL UNITS', '178'],
        ], $curriculum['summary']);
    }
}
