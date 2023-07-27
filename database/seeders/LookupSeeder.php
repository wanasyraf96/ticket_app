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
                    ['id' => 1, 'active'],
                    ['id' => 2, 'deactive'],
                    ['id' => 3, 'complete'],
                    ['id' => 4, 'pending']
                ])
            ],
            [
                'for' => 'ticket_priority',
                'data' => json_encode([
                    ['id' => 1, 'low'],
                    ['id' => 2, 'medium'],
                    ['id' => 3, 'high'],
                    ['id' => 4, 'urgent']
                ])
            ],
        ];
        DB::table('lookup')->insert($lookup);
    }
}
