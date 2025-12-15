<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed roles first (required for foreign key constraints)
        $this->call('RoleSeeder');
        
        // Then seed users (depends on roles)
        $this->call('UserSeeder');
        
        // Seed medicines (if needed)
        $this->call('MedicineSeeder');
        
        // Seed doctor profiles (depends on users with doctor role)
        $this->call('DoctorSeeder');
    }
}

