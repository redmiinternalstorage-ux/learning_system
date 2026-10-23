<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Topic;

class BcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = [
            1 => 'Semester 1',
            2 => 'Semester 2',
            3 => 'Semester 3',
            4 => 'Semester 4',
            5 => 'Semester 5',
            6 => 'Semester 6',
        ];

        foreach ($semesters as $order => $name) {
            $semester = Semester::create(['name' => $name, 'order' => $order]);
            $subjects = $this->subjectsForSemester($order);
            foreach ($subjects as $sOrder => $subjectName) {
                $subject = Subject::create([
                    'semester_id' => $semester->id,
                    'name'        => $subjectName,
                    'order'       => $sOrder,
                ]);
                // placeholder units & topics
                for ($u = 1; $u <= 2; $u++) {
                    $unit = Unit::create([
                        'subject_id' => $subject->id,
                        'name'       => "Unit $u",
                        'order'      => $u,
                    ]);
                    for ($t = 1; $t <= 3; $t++) {
                        Topic::create([
                            'unit_id'    => $unit->id,
                            'name'       => "Topic $t",
                            'order'      => $t,
                            'description'=> 'Content Coming Soon',
                        ]);
                    }
                }
            }
        }
    }

    private function subjectsForSemester(int $semester): array
    {
        $map = [
            1 => [
                1 => 'C Programming',
                2 => 'Mathematics & Statistics',
                3 => 'Digital Logic',
                4 => 'Computer Organization',
                5 => 'English & Communication Skills',
                6 => 'Environmental Studies',
            ],
            2 => [
                1 => 'C++ Programming',
                2 => 'Data Structures & Algorithms',
                3 => 'Operating Systems',
                4 => 'Database Management Systems',
                5 => 'Software Engineering',
                6 => 'Professional Ethics',
            ],
            3 => [
                1 => 'Java Programming',
                2 => 'Computer Networks',
                3 => 'Web Development (HTML/CSS/JS)',
                4 => 'PHP & Laravel',
                5 => 'Discrete Mathematics',
                6 => 'Project Management',
            ],
            4 => [
                1 => 'Python Programming',
                2 => 'Machine Learning',
                3 => 'Cyber Security',
                4 => 'Cloud Computing',
                5 => 'Data Analytics',
                6 => 'Internship Preparation',
            ],
            5 => [
                1 => 'Artificial Intelligence',
                2 => 'Advanced DBMS',
                3 => 'Software Testing',
                4 => 'Mobile App Development',
                5 => 'Entrepreneurship',
                6 => 'Elective',
            ],
            6 => [
                1 => 'Capstone Project',
                2 => 'Industry Training',
                3 => 'Advanced Topics in Computing',
                4 => 'Research Methodology',
                5 => 'Career Roadmaps',
                6 => 'Resume Builder',
            ],
        ];
        return $map[$semester] ?? [];
    }
}
?>