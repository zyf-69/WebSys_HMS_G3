<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMiddleNameAndAddressToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'middle_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'first_name',
            ],
            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'last_name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['middle_name', 'address']);
    }
}

