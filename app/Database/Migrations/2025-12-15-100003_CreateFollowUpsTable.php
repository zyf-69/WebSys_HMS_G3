<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFollowUpsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'doctor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'original_appointment_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Reference to the original appointment that requires follow-up',
            ],
            'follow_up_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'follow_up_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Reason for follow-up',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['scheduled', 'completed', 'cancelled', 'no_show'],
                'default'    => 'scheduled',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('patient_id');
        $this->forge->addKey('doctor_id');
        $this->forge->addKey('follow_up_date');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doctor_id', 'doctors', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('original_appointment_id', 'appointments', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('follow_ups');
    }

    public function down()
    {
        $this->forge->dropTable('follow_ups', true);
    }
}

