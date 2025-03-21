<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Section; 

class SectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            ['name' => 'MIS', 'category' => 'Section'],
            ['name' => 'E-Government Section', 'category' => 'Section'],
            ['name' => 'Childrens Section', 'category' => 'Section'],
            ['name' => 'Reference Section', 'category' => 'Section'],
            ['name' => 'Filipiana Section', 'category' => 'Section'],
            ['name' => 'E Resources Section', 'category' => 'Section'],
            ['name' => 'Periodical Section', 'category' => 'Section'],
            ['name' => 'Law Research Section', 'category' => 'Section'],
            ['name' => 'Administrative Services Division', 'category' => 'Section'],
            ['name' => 'Publication Section', 'category' => 'Section'],
            ['name' => 'City Librarian Office', 'category' => 'Section'],
            ['name' => 'District Libraries Division', 'category' => 'Section'],
            ['name' => 'Recreational Educational and Social Section', 'category' => 'Section'],
            ['name' => 'Binding Preservation', 'category' => 'Section'],
            ['name' => 'Cataloging', 'category' => 'Section'],
            ['name' => 'Technical Section', 'category' => 'Section'],
            ['name' => 'Collection Development', 'category' => 'Section'],
            ['name' => 'Supplies, Inventory and Maintenance Section', 'category' => 'Section'],
            ['name' => 'Human Resource Management Section', 'category' => 'Section'],
            ['name' => 'Records Management Section', 'category' => 'Section'],
            ['name' => 'Adopt-A-Library and Library Organization', 'category' => 'Section'],
        ];

        foreach ($sections as $section) {
            Section::create($section);

        }
    }
}
