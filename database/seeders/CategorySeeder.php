<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categoryArr = [
            [
                'name'        => 'clothes',
                'description' => 'clothes',
            ],
            [
                'name'        => 'toys',
                'description' => 'toys',
            ],
            [
                'name'        => 'APPLIANCES',
                'description' => 'APPLIANCES',
            ],
            [
                'name'        => 'GADGETS',
                'description' => 'GADGETS',
            ],
            [
                'name'        => 'ACCESSORIES',
                'description' => 'ACCESSORIES',
            ]
        ];
        Category::insert($categoryArr);
    }
}
