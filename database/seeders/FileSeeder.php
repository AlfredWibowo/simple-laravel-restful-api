<?php

namespace Database\Seeders;

use App\Models\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        File::create([
            'filename' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'path' => '/files/test.jpg',
        ]);
    }
}
