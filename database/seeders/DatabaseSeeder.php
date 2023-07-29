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
            'email' => 'staff@example.com',
        ])->each(function ($user) {
            $user->roles()->attach([1, 2]);
        });
        \App\Models\User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
        ])->each(function ($user) {
            if ($user->id !== 1)
                $user->roles()->attach(1);
        });
        \App\Models\User::factory(10)->create();


        echo "Generating Randoms Ticket for each users\n";
        // Generate Random tickets for each users
        \App\Models\User::where('id', '!=', 1)->get()->each(function ($user) {
            $user->roles()->attach(1);
            Ticket::factory()
                ->count(rand(10, 100))
                ->for($user)
                ->create([
                    'creator' => $user->id,
                ]);
        });

        echo "Generating Randoms Comment for each tickets\n";
        // Generate Random comments;
        \App\Models\Ticket::all()->each(function ($ticket) {
            \App\Models\Comment::factory(rand(20, 100))->create(['ticket_id' => $ticket->id]);
        });
    }
}
