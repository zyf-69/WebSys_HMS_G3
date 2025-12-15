<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedByToDoctorSchedules extends Migration
{
    public function up()
    {
        $this->forge->addColumn('doctor_schedules', [
            'created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => 'admin',
                'comment'    => 'admin or doctor',
                'after'      => 'valid_to',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('doctor_schedules', 'created_by');
    }
}
