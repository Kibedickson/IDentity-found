<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'ID Card',
            'Passport',
            'Driving License',
            'Voter ID',
            'Birth Certificate',
            'Title Deed',
            'KRA Pin',
            'ATM Card',
            'Credit Card',
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
