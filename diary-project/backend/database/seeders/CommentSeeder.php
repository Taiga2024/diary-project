<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=1; $i <= 10 ; $i++) { 
            Comment::create([
                'user_id' => 1,
                'diary_id' => $i,
                'text' => Str::random(50),
            ]);
        }
    }
}
