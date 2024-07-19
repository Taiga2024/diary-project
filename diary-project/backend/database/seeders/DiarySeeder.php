<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Diary;

class DiarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=1; $i <= 10 ; $i++) { 
            Diary::create([
                'user_id' => $i,
                'title' => Str::random(10),
                'text' => Str::random(50),
            ]);
        }
    }
}
