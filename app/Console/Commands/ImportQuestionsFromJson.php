<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Language;
use App\Models\Question;
use Illuminate\Support\Facades\File;

/**
 * ImportQuestionsFromJson Command
 * 
 * Console command to import sample questions from the JSON file into the database.
 * This provides an alternative to the seeder for quickly populating the database
 * with sample assessment questions for evaluation purposes.
 * 
 * @package App\Console\Commands
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class ImportQuestionsFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:import {--file=database/sample-questions.json : Path to the JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import sample questions from JSON file into the database';

    /**
     * Execute the console command.
     * 
     * Reads the sample questions JSON file and imports all languages and questions
     * into the database. Provides feedback on the import progress and results.
     * 
     * @return int Command exit code (0 for success, 1 for failure)
     */
    public function handle(): int
    {
        $filePath = $this->option('file');
        
        if (!File::exists($filePath)) {
            $this->error("JSON file not found: {$filePath}");
            return 1;
        }

        $this->info("Importing questions from: {$filePath}");
        
        try {
            $data = json_decode(File::get($filePath), true);
            
            if (!$data) {
                $this->error('Invalid JSON format');
                return 1;
            }

            // Import languages
            $languageCount = 0;
            foreach ($data['languages'] as $langData) {
                $language = Language::firstOrCreate(
                    ['name' => $langData['name']],
                    ['description' => $langData['description'] ?? null]
                );
                $languageCount++;
                $this->info("✓ Language: {$language->name}");
            }

            // Import questions
            $questionCount = 0;
            foreach ($data['questions'] as $languageName => $questions) {
                $language = Language::where('name', $languageName)->first();
                
                if (!$language) {
                    $this->warn("Language '{$languageName}' not found, skipping questions");
                    continue;
                }

                foreach ($questions as $questionData) {
                    Question::create([
                        'language_id' => $language->id,
                        'question_text' => $questionData['question'],
                        'options' => $questionData['options'],
                        'correct_answer' => $questionData['correctAnswer'],
                    ]);
                    $questionCount++;
                }
                
                $this->info("✓ Imported " . count($questions) . " questions for {$languageName}");
            }

            $this->info("\n🎉 Import completed successfully!");
            $this->info("📊 Summary:");
            $this->info("   Languages: {$languageCount}");
            $this->info("   Questions: {$questionCount}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }
}
