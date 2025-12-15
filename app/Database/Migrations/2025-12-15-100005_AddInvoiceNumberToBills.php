<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInvoiceNumberToBills extends Migration
{
    public function up()
    {
        $this->forge->addColumn('bills', [
            'invoice_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'unique'     => true,
                'after'      => 'id',
            ],
        ]);
        
        // Add index for faster lookups
        $this->forge->addKey('invoice_number');
    }

    public function down()
    {
        $this->forge->dropColumn('bills', 'invoice_number');
    }
}

