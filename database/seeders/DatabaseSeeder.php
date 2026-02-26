<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin user
        $this->call(AdminUserSeeder::class);

        // 2. Sample agents (needed before properties so agent_id is valid)
        $agents = [
            [
                'name'        => 'Sarah Mitchell',
                'title'       => 'Senior Property Consultant',
                'email'       => 'sarah.mitchell@themandatecompany.co.za',
                'phone'       => '+27 82 111 2222',
                'description' => 'Specialising in luxury coastal properties along the KZN South Coast.',
                'image'       => null,
            ],
            [
                'name'        => 'James van der Berg',
                'title'       => 'Property Specialist',
                'email'       => 'james.vdberg@themandatecompany.co.za',
                'phone'       => '+27 83 333 4444',
                'description' => 'Expert in residential and commercial mandates across the region.',
                'image'       => null,
            ],
            [
                'name'        => 'Priya Naidoo',
                'title'       => 'Rental & Sales Consultant',
                'email'       => 'priya.naidoo@themandatecompany.co.za',
                'phone'       => '+27 84 555 6666',
                'description' => 'Dedicated to finding the perfect home for every client.',
                'image'       => null,
            ],
        ];

        foreach ($agents as $agentData) {
            Agent::firstOrCreate(['email' => $agentData['email']], $agentData);
        }

        // 3. Fake properties — local dev only (Faker is a dev dependency)
        if (app()->environment('local')) {
            $this->call(PropertySeeder::class);
        }
    }
}
