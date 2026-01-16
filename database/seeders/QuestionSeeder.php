<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Languages exist and get their IDs
        $js = Language::firstOrCreate(['name' => 'JavaScript']);
        $py = Language::firstOrCreate(['name' => 'Python']);

        // 2. Define the Question Bank
        $questions = [
            'JavaScript' => [
                ['q' => 'What is the output of 1 + "1" in JavaScript?', 'opts' => ['2', '11', 'NaN', 'undefined'], 'a' => '11'],
                ['q' => 'Which keyword is used to declare a constant?', 'opts' => ['var', 'let', 'constant', 'const'], 'a' => 'const'],
                ['q' => 'What does "NaN" stand for?', 'opts' => ['Not a Number', 'New and Next', 'Null and None', 'No available Name'], 'a' => 'Not a Number'],
                ['q' => 'Which method adds an element to the end of an array?', 'opts' => ['pop()', 'push()', 'shift()', 'unshift()'], 'a' => 'push()'],
                ['q' => 'What is the correct syntax for an arrow function?', 'opts' => ['() => {}', 'function => {}', '() -> {}', '=> () {}'], 'a' => '() => {}'],
                ['q' => 'Which operator checks for both value and type equality?', 'opts' => ['==', '=', '===', '!='], 'a' => '==='],
                ['q' => 'How do you write an IF statement in JavaScript?', 'opts' => ['if i = 5', 'if (i == 5)', 'if i == 5 then', 'if i = 5 then'], 'a' => 'if (i == 5)'],
                ['q' => 'What is the use of "use strict" in JS?', 'opts' => ['Enforces stricter parsing', 'Enables new features', 'Speeds up execution', 'Ignores errors'], 'a' => 'Enforces stricter parsing'],
                ['q' => 'Which of these is NOT a primitive data type?', 'opts' => ['String', 'Boolean', 'Number', 'Object'], 'a' => 'Object'],
                ['q' => 'How do you create a function in JavaScript?', 'opts' => ['function:myFunc()', 'function = myFunc()', 'function myFunc()', 'new function myFunc()'], 'a' => 'function myFunc()'],
            ],
            'Python' => [
                ['q' => 'Which keyword is used to create a function in Python?', 'opts' => ['func', 'define', 'def', 'function'], 'a' => 'def'],
                ['q' => 'What is the correct extension for Python files?', 'opts' => ['.py', '.pyt', '.pt', '.pw'], 'a' => '.py'],
                ['q' => 'How do you start a comment in Python?', 'opts' => ['//', '/*', '#', '--'], 'a' => '#'],
                ['q' => 'Which of these is a mutable data type?', 'opts' => ['Tuple', 'String', 'List', 'Integer'], 'a' => 'List'],
                ['q' => 'What is the output of print(2 ** 3)?', 'opts' => ['6', '8', '9', '16'], 'a' => '8'],
                ['q' => 'Which method is used to add an element to a list?', 'opts' => ['add()', 'append()', 'insert()', 'push()'], 'a' => 'append()'],
                ['q' => 'What is the output of print(10 / 3)?', 'opts' => ['3', '3.333...', '3.0', '3.33'], 'a' => '3.333...'],
                ['q' => 'Which keyword is used for exception handling?', 'opts' => ['except', 'catch', 'try', 'handle'], 'a' => 'try'],
                ['q' => 'What does the len() function return?', 'opts' => ['Length of an object', 'Type of an object', 'Memory size', 'Index of an element'], 'a' => 'Length of an object'],
                ['q' => 'Which of these is NOT a Python data type?', 'opts' => ['List', 'Dictionary', 'Array', 'Tuple'], 'a' => 'Array'],
            ]
        ];

        foreach ($questions['JavaScript'] as $item) {
            Question::create([
                'language_id' => $js->id,
                'question_text' => $item['q'],
                'options' => $item['opts'],
                'correct_answer' => $item['a']
            ]);
        }

        foreach ($questions['Python'] as $item) {
            Question::create([
                'language_id' => $py->id,
                'question_text' => $item['q'],
                'options' => $item['opts'],
                'correct_answer' => $item['a']
            ]);
        }
    }
}
