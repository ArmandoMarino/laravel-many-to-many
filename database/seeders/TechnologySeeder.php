<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $technologies = [
            ['label' => 'HTML', 'color' => 'text-primary'],
            ['label' => 'CSS', 'color' => 'text-secondary'],
            ['label' => 'ES6', 'color' => 'text-success'],
            ['label' => 'Bootstrap', 'color' => 'text-danger'],
            ['label' => 'Vue', 'color' => 'text-warning'],
            ['label' => 'SASS', 'color' => 'text-info'],
            ['label' => 'PHP', 'color' => 'text-light'],
            ['label' => 'SQL', 'color' => 'text-primary-emphasis'],
            ['label' => 'Laravel', 'color' => 'text-danger-emphasis'],

        ];

        foreach ($technologies as $technology) {
            $new_technology = new Technology();
            $new_technology->label = $technology['label'];
            $new_technology->color = $technology['color'];
            $new_technology->save();
        }
    }
}
