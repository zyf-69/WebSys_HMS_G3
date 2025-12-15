<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'           => 1,
                'name'         => 'admin',
                'display_name' => 'Hospital Administrator',
                'description'  => 'Full control, user management, reports, and branch integration',
                'level'        => 100,
                'is_active'    => 1,
            ],
            [
                'id'           => 2,
                'name'         => 'it_staff',
                'display_name' => 'IT Staff',
                'description'  => 'System maintenance, security, and backups',
                'level'        => 80,
                'is_active'    => 1,
            ],
            [
                'id'           => 3,
                'name'         => 'doctor',
                'display_name' => 'Doctor',
                'description'  => 'Access/update patient records, create prescriptions, request tests',
                'level'        => 60,
                'is_active'    => 1,
            ],
            [
                'id'           => 4,
                'name'         => 'nurse',
                'display_name' => 'Nurse',
                'description'  => 'Patient monitoring and treatment updates',
                'level'        => 40,
                'is_active'    => 1,
            ],
            [
                'id'           => 5,
                'name'         => 'pharmacist',
                'display_name' => 'Pharmacist',
                'description'  => 'Track and dispense medicines',
                'level'        => 30,
                'is_active'    => 1,
            ],
            [
                'id'           => 6,
                'name'         => 'receptionist',
                'display_name' => 'Receptionist',
                'description'  => 'Patient registration and appointment booking',
                'level'        => 20,
                'is_active'    => 1,
            ],
            [
                'id'           => 7,
                'name'         => 'lab_staff',
                'display_name' => 'Laboratory Staff',
                'description'  => 'Manage test requests and enter results',
                'level'        => 15,
                'is_active'    => 1,
            ],
            [
                'id'           => 8,
                'name'         => 'accountant',
                'display_name' => 'Accountant',
                'description'  => 'Handle billing, payments, and insurance claims',
                'level'        => 10,
                'is_active'    => 1,
            ],
        ];

        // Check if roles already exist and only insert new ones
        foreach ($data as $role) {
            $exists = $this->db->table('roles')
                ->where('id', $role['id'])
                ->countAllResults();
            
            if ($exists === 0) {
                $this->db->table('roles')->insert($role);
            }
        }
    }
}
