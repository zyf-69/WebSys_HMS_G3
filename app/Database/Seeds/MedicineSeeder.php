<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $medicines = [
            // Antibiotics
            ['medicine_name' => 'Amoxicillin 500mg', 'category' => 'Antibiotic', 'stock_quantity' => 150, 'unit_price' => 25.50, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Ciprofloxacin 500mg', 'category' => 'Antibiotic', 'stock_quantity' => 80, 'unit_price' => 35.00, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Azithromycin 500mg', 'category' => 'Antibiotic', 'stock_quantity' => 0, 'unit_price' => 45.75, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Cefuroxime 250mg', 'category' => 'Antibiotic', 'stock_quantity' => 5, 'unit_price' => 55.00, 'unit' => 'tablet', 'status' => 'active'],
            
            // Pain Relief
            ['medicine_name' => 'Paracetamol 500mg', 'category' => 'Pain Relief', 'stock_quantity' => 500, 'unit_price' => 2.50, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Ibuprofen 400mg', 'category' => 'Pain Relief', 'stock_quantity' => 200, 'unit_price' => 8.00, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Tramadol 50mg', 'category' => 'Pain Relief', 'stock_quantity' => 120, 'unit_price' => 12.50, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Mefenamic Acid 500mg', 'category' => 'Pain Relief', 'stock_quantity' => 0, 'unit_price' => 6.75, 'unit' => 'tablet', 'status' => 'active'],
            
            // Cardiovascular
            ['medicine_name' => 'Amlodipine 5mg', 'category' => 'Cardiovascular', 'stock_quantity' => 180, 'unit_price' => 15.00, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Losartan 50mg', 'category' => 'Cardiovascular', 'stock_quantity' => 90, 'unit_price' => 18.50, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Metoprolol 50mg', 'category' => 'Cardiovascular', 'stock_quantity' => 7, 'unit_price' => 20.00, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Atorvastatin 20mg', 'category' => 'Cardiovascular', 'stock_quantity' => 200, 'unit_price' => 22.75, 'unit' => 'tablet', 'status' => 'active'],
            
            // Antidiabetic
            ['medicine_name' => 'Metformin 500mg', 'category' => 'Antidiabetic', 'stock_quantity' => 250, 'unit_price' => 5.50, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Glibenclamide 5mg', 'category' => 'Antidiabetic', 'stock_quantity' => 100, 'unit_price' => 3.25, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Insulin Glargine 100IU/ml', 'category' => 'Antidiabetic', 'stock_quantity' => 30, 'unit_price' => 450.00, 'unit' => 'vial', 'status' => 'active'],
            
            // Respiratory
            ['medicine_name' => 'Salbutamol 2mg/5ml', 'category' => 'Respiratory', 'stock_quantity' => 40, 'unit_price' => 85.00, 'unit' => 'bottle', 'status' => 'active'],
            ['medicine_name' => 'Montelukast 10mg', 'category' => 'Respiratory', 'stock_quantity' => 60, 'unit_price' => 28.50, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Budesonide Inhaler', 'category' => 'Respiratory', 'stock_quantity' => 0, 'unit_price' => 320.00, 'unit' => 'inhaler', 'status' => 'active'],
            
            // Gastrointestinal
            ['medicine_name' => 'Omeprazole 20mg', 'category' => 'Gastrointestinal', 'stock_quantity' => 300, 'unit_price' => 12.00, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Ranitidine 150mg', 'category' => 'Gastrointestinal', 'stock_quantity' => 150, 'unit_price' => 8.50, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Loperamide 2mg', 'category' => 'Gastrointestinal', 'stock_quantity' => 2, 'unit_price' => 4.25, 'unit' => 'tablet', 'status' => 'active'],
            
            // Vitamins
            ['medicine_name' => 'Vitamin C 1000mg', 'category' => 'Vitamins', 'stock_quantity' => 400, 'unit_price' => 3.50, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Vitamin D3 1000IU', 'category' => 'Vitamins', 'stock_quantity' => 250, 'unit_price' => 6.00, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Multivitamin Complex', 'category' => 'Vitamins', 'stock_quantity' => 180, 'unit_price' => 15.75, 'unit' => 'tablet', 'status' => 'active'],
            ['medicine_name' => 'Calcium Carbonate 500mg', 'category' => 'Vitamins', 'stock_quantity' => 320, 'unit_price' => 4.50, 'unit' => 'tablet', 'status' => 'active'],
            
            // Other
            ['medicine_name' => 'Dextrose 5% 500ml', 'category' => 'Other', 'stock_quantity' => 50, 'unit_price' => 120.00, 'unit' => 'bottle', 'status' => 'active'],
            ['medicine_name' => 'Normal Saline 500ml', 'category' => 'Other', 'stock_quantity' => 75, 'unit_price' => 95.00, 'unit' => 'bottle', 'status' => 'active'],
            ['medicine_name' => 'Hydrogen Peroxide 3%', 'category' => 'Other', 'stock_quantity' => 20, 'unit_price' => 45.00, 'unit' => 'bottle', 'status' => 'active'],
        ];

        foreach ($medicines as $medicine) {
            $medicine['created_at'] = $now;
            $medicine['updated_at'] = $now;
            $this->db->table('medicines')->insert($medicine);
        }
    }
}
