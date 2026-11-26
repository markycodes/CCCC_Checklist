<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BscrimCurriculumTest extends TestCase
{
    public function test_bscrim_curriculum_matches_the_190_unit_total(): void
    {
        $curriculum = require __DIR__.'/../../config/prospectus/BSCRIM.php';
        $courseCount = 0;
        $unitCount = 0;

        foreach ($curriculum['curriculum'] as $year) {
            foreach ($year['semesters'] as $semester) {
                foreach ($semester['courses'] as $course) {
                    $courseCount++;
                    $unitCount += $course[4];
                }
            }
        }

        $this->assertSame(190, $unitCount);
        $this->assertGreaterThan(0, $courseCount);
    }

    public function test_bscrim_course_rows_match_the_reference(): void
    {
        $curriculum = require __DIR__.'/../../config/prospectus/BSCRIM.php';
        $actualRows = [];

        foreach ($curriculum['curriculum'] as $year) {
            foreach ($year['semesters'] as $semester) {
                foreach ($semester['courses'] as $course) {
                    $actualRows[] = $course;
                }
            }
        }

        $this->assertSame([
            ['GED 1001', 'Understanding the Self', 3, 0, 3, ''],
            ['GED 1002', 'Readings in the Phil. History', 3, 0, 3, ''],
            ['GED 1004', 'Mathematics in the Modern World', 3, 0, 3, ''],
            ['GED 1005', 'Purposive Communication', 3, 0, 3, ''],
            ['GED 1010', 'The Entrepreneurial Mind', 3, 0, 3, ''],
            ['CRM 1101', 'Introduction to Criminology', 3, 0, 3, ''],
            ['PE PATHFit 1101', 'Competency Movement Training', 2, 0, 2, ''],
            ['PE DEFTAC 1101', 'Fundamentals of Martial Arts', 2, 0, 2, ''],
            ['NSTP 1101', "Reserve Officers' Training Corps 1 (Military Science 1)", 3, 0, 3, ''],
            ['GED 1003', 'The Contemporary World with Peace Education', 3, 0, 3, ''],
            ['GED 1006', 'Art Appreciation', 3, 0, 3, ''],
            ['GED 1007', 'Science, Technology and Society with SARS Education, HIV/AIDS', 3, 0, 3, ''],
            ['GED 1008', 'Ethics', 3, 0, 3, ''],
            ['GED 1009', 'Life and Works of Rizal', 3, 0, 3, ''],
            ['CLJ 1201', 'Intro.to Philippine Criminal Justice Sys.', 3, 0, 3, ''],
            ['LEA 1101', "Law Enforcement Org. & Administ'n", 4, 0, 4, ''],
            ['PE PATHFit 1202', 'Exercise Based Fitness', 2, 0, 2, 'PE PATHFit 1101'],
            ['PE DEFTAC 1202', 'Arnis and Disarming Techniques', 2, 0, 2, 'PE DefTac 1101'],
            ['NSTP 1201', "Reserve Officers' Training Corps 2 (Military Science 2)", 3, 0, 3, 'NSTP 1101'],
            ['GED 1011', 'Environmental Science', 3, 0, 3, ''],
            ['GED 1012', 'Gender and Society', 3, 0, 3, ''],
            ['CDI 2101', "Fund's of Criminal Inv. and Intel.", 4, 0, 4, ''],
            ['CAC 2101', 'Institutional Corrections', 3, 0, 3, ''],
            ['LEA 2102', 'Comparative Models in Policing', 3, 0, 3, 'LEA 1101'],
            ['CRM 2102', 'Theories of Crime Causation', 3, 0, 3, 'CRM 1101'],
            ['GGC 1000', 'General Chemistry (Organic)', 2, 3, 3, ''],
            ['ISS 1001', 'Samar Stud.1: Hist. & Local Heroes', 2, 1, 3, ''],
            ['PE DefTac 2103', 'First Aid & Water Safety with Disaster Risk Reduction Management (DRRM)', 2, 0, 2, 'PE DefTac 1202'],
            ['CAC 2202', 'Non-Institutional Corrections', 3, 0, 3, 'CAC2101'],
            ['FRC 2201', 'Forensic Photography', 2, 1, 3, ''],
            ['CFC 2101', 'Character Formation, Nationalism and Patriotism', 3, 0, 3, ''],
            ['CDI 2202', 'Specialized Crime Investigation 1 w/ Legal Medicine', 3, 0, 3, 'CDI 2101'],
            ['FRC 2202', 'Personal Identification Techniques', 3, 1, 3, 'FRC 2201 (co-requisite)'],
            ['CRM 2203', 'Human Behavior and Victimology', 3, 0, 3, 'CRM 2102'],
            ['LEA 2203', 'Introduction to Industrial Sec. Concepts', 2, 0, 3, 'LEA 2102'],
            ['ISS 1002', 'Samar Studies 2: Culture and Arts', 2, 1, 3, 'ISS 1001'],
            ['PE DefTac 2204', "DEF TACT 4 – Fund'ls of Marksmanship", 2, 0, 2, 'PE DefTac 2103'],
            ['CRM 3104', 'Prof Conduct and Ethical Standards', 3, 0, 3, 'CRM 2203'],
            ['CLJ 3102', 'Human Rights Education', 3, 0, 3, 'CLJ 2101'],
            ['CFC 3102', "Character Formation w/ Leadership, Decision Making, Mgt. & Administ'n", 3, 0, 3, 'CFC 2101'],
            ['CDI 3103', "Specialized Crime Investigation 2 w/ Simulat'n on Interrogat'n and Interview", 3, 0, 3, 'CDI 2202'],
            ['FRC 3103', 'Forensic Chemistry and Toxicology', 3, 2, 5, 'FRC 2202'],
            ['CLJ 3103', 'Criminal Law (Book1)', 3, 0, 3, 'CLJ 3102 (co-requisite)'],
            ['LEA 3104', 'Law Enforcement Operations and Planning with Crime Mapping', 3, 0, 3, 'LEA 2203'],
            ['CDI 3104', 'Traffic Mgt. & Accident Inv. w/ Driving', 3, 0, 3, 'CDI 3103 (co-requisite)'],
            ['CRM 3105', 'Criminological Research 1 (Research Methods w/ Applied Statistics)', 3, 0, 3, ''],
            ['CRM 3206', 'Criminological Research 2 (Thesis Writing and Presentation)', 3, 0, 3, 'CRM 3105'],
            ['CRM 3207', 'Juvenile Delinquency and Juvenile Justice System', 3, 0, 3, 'CRM 3104'],
            ['CRM 3208', 'Dispute Res. & Crisis/Incidents Mgt.', 3, 0, 3, 'CRM 3207 (co-requisite)'],
            ['CAC 3202', 'Therapeutic Modalities', 2, 0, 2, 'CAC 2202'],
            ['CLJ 3204', 'Criminal Law (Book 2)', 4, 0, 4, 'CLJ 3103'],
            ['FRC 3204', 'Questioned Documents Examination', 2, 1, 3, 'FRC 3103'],
            ['FRC 3205', 'Lie Detection Techniques', 2, 1, 3, 'FRC 3204 (co-requisite)'],
            ['CDI 3205', 'Technical English 1 (Technical Report Writing and Presentation)', 2, 1, 3, 'CDI 3104'],
            ['CDI 3206', 'Fire Protection and Arson Investigation', 3, 0, 3, 'CDI 3205 (co-requisite)'],
            ['CBA 3201', 'Competency-Based Assessment I', '(3)', 0, 0, ''],
            ['IFL 1003', 'Foreign Language', 3, 0, 3, ''],
            ['CDI 4107', 'Technical English 2 (Legal Forms)', 2, 1, 3, 'CDI 3205'],
            ['CDI 4108', 'Vice and Drug Education and Control', 3, 0, 3, 'CDI 3206'],
            ['CDI 4109', 'Intro.to Cybercrime and Environmental Laws and Protection', 2, 1, 3, ''],
            ['CLJ 4105', 'Evidence', 3, 0, 3, 'CLJ 3204'],
            ['CLJ 4106', 'Criminal Procedure and Court Testimony', 3, 0, 3, 'CLJ 4105'],
            ['FRC 4106', 'Forensic Ballistics', 2, 1, 3, 'FRC 3205'],
            ['CBA 4102', 'Competency-Based Assessment II', '(3)', 0, 0, ''],
            ['CPC 4201', 'Internship (On-the-Job Training)', 6, '540 Hours', 6, 'All Major Courses from 1st Yr - 4th Yr, 1st Semester'],
            ['CBA 4203', 'Competency-Based Assessment III', '(3)', 0, 0, ''],
        ], $actualRows);
    }
}
