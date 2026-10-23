<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For demo, create 3 sample MCQs for each topic (if none exist)
        $topics = Topic::all();
        foreach ($topics as $topic) {
            // Skip if already has questions
            if ($topic->questions()->exists()) {
                continue;
            }
            for ($i = 1; $i <= 3; $i++) {
                Question::create([
                    'topic_id'       => $topic->id,
                    'question'       => "Sample question $i for {$topic->name}",
                    'options'        => json_encode([
                        'A' => 'Option A',
                        'B' => 'Option B',
                        'C' => 'Option C',
                        'D' => 'Option D',
                    ]),
                    'correct_option' => 'A',
                    'explanation'    => 'Explanation for the correct answer.',
                    'order'          => $i,
                ]);
            }
        }
    }
}
?>