<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/airline_faqs.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("FAQ JSON file not found at: $jsonPath");
            return;
        }

        $faqs = json_decode(File::get($jsonPath), true);

        foreach ($faqs as $faq) {
            DB::table('faqs')->insert([
                'categories_id' => $faq['category'],
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("✅ Successfully seeded " . count($faqs) . " FAQs.");
    }
}
