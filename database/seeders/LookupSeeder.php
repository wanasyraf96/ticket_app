<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lookup = [
            [
                'for' => 'ticket_status',
                'data' => json_encode([
                    ['id' => 1, 'name' => 'active'],
                    ['id' => 2, 'name' => 'deactive'],
                    ['id' => 3, 'name' => 'complete'],
                    ['id' => 4, 'name' => 'pending']
                ])
            ],
            [
                'for' => 'ticket_priority',
                'data' => json_encode([
                    ['id' => 1, 'name' => 'low'],
                    ['id' => 2, 'name' => 'medium'],
                    ['id' => 3, 'name' => 'high'],
                    ['id' => 4, 'name' => 'urgent']
                ])
            ],
        ];
        DB::table('lookup')->insert($lookup);
    }
}
