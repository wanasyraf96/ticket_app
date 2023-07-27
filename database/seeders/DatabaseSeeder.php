<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Seed
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            LookupSeeder::class,
        ]);

        // Generate Users
        \App\Models\User::factory()->create([
            'name' => 'Staff',
            'email' => 'admin@example.com',
        ]);
        \App\Models\User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
        ]);
        \App\Models\User::factory(10)->create();


        // Generate Random tickets for each users
        \App\Models\User::where('id', '!=', 1)->get()->each(function ($user) {
            Ticket::factory()
                ->count(rand(10, 100))
                ->for($user)
                ->create([
                    'creator' => $user->id,
                ]);
        });
    }
}
