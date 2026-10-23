<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Material;
use Illuminate\Support\Str;

class BcaSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create semesters 1 through 6 if they don't exist
        for ($i = 1; $i <= 6; $i++) {
            Semester::firstOrCreate(['name' => 'Semester ' . $i]);
        }

        // Define default subjects per semester (adjust as needed)
        $defaultSubjects = [
            1 => ['Mathematics I', 'Computer Fundamentals', 'Programming I', 'Digital Logic', 'Business Communication'],
            2 => ['Mathematics II', 'Data Structures', 'Programming II', 'Computer Organization', 'Business Law'],
            3 => ['Operating Systems', 'Database Management', 'Web Technologies', 'Software Engineering', 'Computer Networks'],
            4 => ['Design Patterns', 'Mobile Application Development', 'Cloud Computing', 'Artificial Intelligence', 'Elective I'],
            5 => ['Machine Learning', 'Big Data Analytics', 'Cyber Security', 'Elective II', 'Project Phase I'],
            6 => ['Project Phase II', 'Professional Ethics', 'Entrepreneurship', 'Elective III', 'Internship'],
        ];

        foreach ($defaultSubjects as $semesterNumber => $subjects) {
            $semester = Semester::where('name', 'Semester ' . $semesterNumber)->first();
            foreach ($subjects as $order => $subjectName) {
                $subject = Subject::firstOrCreate([
                    'semester_id' => $semester->id,
                    'name' => $subjectName,
                ], [
                    'description' => $subjectName . ' notes and study material',
                    'order' => $order + 1,
                ]);

                // Create placeholder PDF material if none exists
                if ($subject->materials()->count() === 0) {
                    // Ensure storage directory exists
                    $folder = storage_path('app/public/notes/semester' . $semesterNumber . '/' . Str::slug($subjectName));
                    if (!file_exists($folder)) {
                        mkdir($folder, 0755, true);
                    }
                    $pdfPath = $folder . '/placeholder.pdf';
                    if (!file_exists($pdfPath)) {
                        // Simple placeholder content; in production replace with real PDF
                        file_put_contents($pdfPath, 'Placeholder PDF for ' . $subjectName);
                    }
                    Material::create([
                        'subject_id' => $subject->id,
                        'type' => 'pdf',
                        'title' => $subjectName . ' Notes',
                        'filename' => 'placeholder.pdf',
                        'description' => 'Placeholder notes for ' . $subjectName,
                        'order' => 1,
                    ]);
                }
            }
        }
    }
}
?>
