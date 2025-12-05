<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();

        // Find all users with doctor role (role_id = 3)
        $users = $db->table('users')
            ->select('id, first_name, last_name, username, email')
            ->where('role_id', 3)
            ->get()->getResultArray();

        if (empty($users)) {
            return;
        }

        // Existing doctor user_ids to avoid duplicates
        $existing = $db->table('doctors')->select('user_id')->get()->getResultArray();
        $existingIds = array_column($existing, 'user_id');

        $now = date('Y-m-d H:i:s');
        $rows = [];

        foreach ($users as $user) {
            if (in_array($user['id'], $existingIds, true)) {
                continue; // already has doctor profile
            }

            $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
            if ($fullName === '') {
                $fullName = $user['username'] ?: $user['email'];
            }

            $rows[] = [
                'user_id'        => $user['id'],
                'full_name'      => $fullName,
                'specialization' => null,
                'license_number' => null,
                'status'         => 'active',
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        if (! empty($rows)) {
            $db->table('doctors')->insertBatch($rows);
        }
    }
}
