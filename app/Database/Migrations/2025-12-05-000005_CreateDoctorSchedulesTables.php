<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDoctorSchedulesTables extends Migration
{
    public function up()
    {
        // Core schedule definition
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'doctor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'shift_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'e.g. Morning, Afternoon, Night',
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'valid_from' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'valid_to' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Typically within 1 year from valid_from',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('doctor_id', 'doctors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('doctor_schedules');

        // Days of availability for each schedule
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'schedule_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'day_of_week' => [
                'type'       => 'ENUM',
                'constraint' => ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'],
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('schedule_id', 'doctor_schedules', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('doctor_schedule_days');
    }

    public function down()
    {
        $this->forge->dropTable('doctor_schedule_days', true);
        $this->forge->dropTable('doctor_schedules', true);
    }
}
