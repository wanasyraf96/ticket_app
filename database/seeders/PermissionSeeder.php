<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'update', 'is_active' => true],
            ['name' => 'delete', 'is_active' => true],
            ['name' => 'read', 'is_active' => true],
        ];
        DB::table('permissions')->insert($permissions);
    }
}
