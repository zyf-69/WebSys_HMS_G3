<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentDateToPaymentsTable extends Migration
{
    public function up()
    {
        $fields = [
            'payment_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'amount',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
            ],
        ];

        $this->forge->addColumn('payments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('payments', ['payment_date', 'updated_at']);
    }
}

