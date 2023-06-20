<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\AnswerOption;
use App\Models\AnswerEssay;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $question = [
            [
                'exam_id' => '1',
                'subject' => '<p>1 Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>',
                'type' => 'choice'
            ],
            [
                'exam_id' => '1',
                'subject' => '<p>2 Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>',
                'type' => 'choice'
            ],
            [
                'exam_id' => '1',
                'subject' => '<p>3 Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>',
                'type' => 'choice'
            ],
            [
                'exam_id' => '1',
                'subject' => '<p>4 Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>',
                'type' => 'choice'
            ],
            [
                'exam_id' => '1',
                'subject' => '<p>5 Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>',
                'type' => 'choice'
            ],
            [
                'exam_id' => '1',
                'subject' => '<p>6 Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>',
                'type' => 'essay'
            ],
            [
                'exam_id' => '1',
                'subject' => '<p>7 Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>',
                'type' => 'essay'
            ],
            [
                'exam_id' => '1',
                'subject' => '<p>8 Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>',
                'type' => 'essay'
            ],
        ];

        foreach ($question as $key => $value) {
            $data = Question::create($value);
            
            if($value['type'] == 'choice') {
                for($i = 1; $i <= 5; $i++) {
                    $answerOption = [
                        'question_id' => $data->id,
                        'subject' => "<p>" . $data->id . " $i " . "Lorem ipsum dolor sit amet</p>",
                    ];
                    
                    AnswerOption::create($answerOption);
                }
            }else{
                AnswerEssay::create([
                    'question_id' => $data->id,
                    'default_answer' => "<p>" . $data->id . " Lorem ipsum dolor sit amet</p>",
                ]);
            }
        }
    }
}
