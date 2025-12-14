<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePatientsSchema extends Migration
{
    public function up()
    {
        // Core patient identity
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'patient_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'unique'     => true,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'middle_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'date_of_birth' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['male', 'female', 'other'],
                'null'       => true,
            ],
            'civil_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'place_of_birth' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'blood_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'null'       => true,
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
        $this->forge->createTable('patients');

        // Contact information (normalized from patient core)
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
            'province' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'city_municipality' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'barangay' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'street' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'phone_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'mobile_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patient_contacts');

        // Emergency contact
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
            'contact_person' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'relationship' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
            ],
            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patient_emergency_contacts');

        // Vitals – keep separated, could be extended to history later
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
            'blood_pressure' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,
            ],
            'heart_rate' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'temperature' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'height_cm' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => true,
            ],
            'weight_kg' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => true,
            ],
            'bmi' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'comment'    => 'Body Mass Index, calculated from height and weight',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patient_vitals');

        // Insurance information – a patient may have one or more policies in the future
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
            'provider_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'provider_contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'policy_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patient_insurances');

        // Clinical / medical notes – text per patient, can be extended to history later
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
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patient_notes');

        // Admission details – only used when patient is admitted (inpatient)
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
            'admission_datetime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'room_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'room_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'bed_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('admissions');
    }

    public function down()
    {
        $this->forge->dropTable('admissions', true);
        $this->forge->dropTable('patient_notes', true);
        $this->forge->dropTable('patient_insurances', true);
        $this->forge->dropTable('patient_vitals', true);
        $this->forge->dropTable('patient_emergency_contacts', true);
        $this->forge->dropTable('patient_contacts', true);
        $this->forge->dropTable('patients', true);
    }
}
