<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDoctorIdToAdmissionsAndPatients extends Migration
{
    public function up()
    {
        // Add doctor_id to admissions table (for inpatients)
        $this->forge->addColumn('admissions', [
            'doctor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'patient_id',
            ],
        ]);
        
        // Add foreign key for admissions.doctor_id (if column was added successfully)
        try {
            $this->db->query('ALTER TABLE `admissions` ADD CONSTRAINT `fk_admissions_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        } catch (\Exception $e) {
            // Foreign key might already exist, ignore
        }
        
        // Add doctor_id to patients table (for outpatients)
        $this->forge->addColumn('patients', [
            'doctor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'blood_type',
            ],
        ]);
        
        // Add foreign key for patients.doctor_id (if column was added successfully)
        try {
            $this->db->query('ALTER TABLE `patients` ADD CONSTRAINT `fk_patients_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        } catch (\Exception $e) {
            // Foreign key might already exist, ignore
        }
    }

    public function down()
    {
        // Remove foreign keys first
        $this->db->query('ALTER TABLE `admissions` DROP FOREIGN KEY `fk_admissions_doctor`');
        $this->db->query('ALTER TABLE `patients` DROP FOREIGN KEY `fk_patients_doctor`');
        
        // Drop columns
        $this->forge->dropColumn('admissions', 'doctor_id');
        $this->forge->dropColumn('patients', 'doctor_id');
    }
}
