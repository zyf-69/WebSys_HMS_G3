<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Ensure roles are seeded first (required for foreign key constraints)
        $rolesCount = $this->db->table('roles')->countAllResults();
        if ($rolesCount === 0) {
            $this->call('RoleSeeder');
        }

        $now = date('Y-m-d H:i:s');

        $passwords = [
            'admin'        => password_hash('Admin123!', PASSWORD_DEFAULT),
            'doctor'       => password_hash('Doctor123!', PASSWORD_DEFAULT),
            'nurse'        => password_hash('Nurse123!', PASSWORD_DEFAULT),
            'receptionist' => password_hash('Reception123!', PASSWORD_DEFAULT),
            'lab_staff'    => password_hash('Lab123!', PASSWORD_DEFAULT),
            'pharmacist'   => password_hash('Pharma123!', PASSWORD_DEFAULT),
            'accountant'   => password_hash('Account123!', PASSWORD_DEFAULT),
            'it_staff'     => password_hash('It123!', PASSWORD_DEFAULT),
        ];

        $users = [
            [
                'username'   => 'admin',
                'email'      => 'admin@example.com',
                'password'   => $passwords['admin'],
                'first_name' => 'System',
                'last_name'  => 'Administrator',
                'role_id'    => 1, // Hospital Administrator
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.smith',
                'email'      => 'doctor1@example.com',
                'password'   => $passwords['doctor'],
                'first_name' => 'John',
                'last_name'  => 'Smith',
                'role_id'    => 3, // Doctor
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'nurse.anna',
                'email'      => 'nurse1@example.com',
                'password'   => $passwords['nurse'],
                'first_name' => 'Anna',
                'last_name'  => 'Lopez',
                'role_id'    => 4, // Nurse
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'reception.maria',
                'email'      => 'reception1@example.com',
                'password'   => $passwords['receptionist'],
                'first_name' => 'Maria',
                'last_name'  => 'Santos',
                'role_id'    => 6, // Receptionist
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'lab.carlos',
                'email'      => 'lab1@example.com',
                'password'   => $passwords['lab_staff'],
                'first_name' => 'Carlos',
                'last_name'  => 'Reyes',
                'role_id'    => 7, // Laboratory Staff
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'pharma.lisa',
                'email'      => 'pharma1@example.com',
                'password'   => $passwords['pharmacist'],
                'first_name' => 'Lisa',
                'last_name'  => 'Cruz',
                'role_id'    => 5, // Pharmacist
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'acct.peter',
                'email'      => 'account1@example.com',
                'password'   => $passwords['accountant'],
                'first_name' => 'Peter',
                'last_name'  => 'Lim',
                'role_id'    => 8, // Accountant
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'it.michael',
                'email'      => 'it1@example.com',
                'password'   => $passwords['it_staff'],
                'first_name' => 'Michael',
                'last_name'  => 'Tan',
                'role_id'    => 2, // IT Staff
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($users as $user) {
            // Check if user already exists
            $exists = $this->db->table('users')
                ->where('username', $user['username'])
                ->orWhere('email', $user['email'])
                ->countAllResults();
            
            if ($exists === 0) {
                $this->db->table('users')->insert($user);
                $userId = $this->db->insertID();

                // Automatically create doctor profile for doctor-role users
                if ((int) $user['role_id'] === 3) {
                    $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                    if ($fullName === '') {
                        $fullName = $user['username'] ?? $user['email'];
                    }

                    // Check if doctor profile already exists
                    $doctorExists = $this->db->table('doctors')
                        ->where('user_id', $userId)
                        ->countAllResults();
                    
                    if ($doctorExists === 0) {
                        $doctorData = [
                            'user_id'        => $userId,
                            'full_name'      => $fullName,
                            'specialization' => null,
                            'license_number' => null,
                            'status'         => 'active',
                            'created_at'     => $now,
                            'updated_at'     => $now,
                        ];

                        $this->db->table('doctors')->insert($doctorData);
                    }
                }
            }
        }
    }
}
