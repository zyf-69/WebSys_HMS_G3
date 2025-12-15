<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class TestPrices extends BaseConfig
{
    /**
     * Test prices by test type and test name
     * Prices are in PHP (Philippine Peso)
     */
    public $prices = [
        'Blood Test' => [
            'Complete Blood Count (CBC)' => 350.00,
            'Blood Glucose (Fasting)' => 250.00,
            'Lipid Profile' => 450.00,
            'Liver Function Test (LFT)' => 550.00,
            'Kidney Function Test (KFT)' => 550.00,
            'Thyroid Function Test (TFT)' => 650.00,
            'Hemoglobin A1C' => 500.00,
            'Blood Group & Rh Factor' => 200.00,
            'Coagulation Profile' => 400.00,
            'Erythrocyte Sedimentation Rate (ESR)' => 150.00,
            'C-Reactive Protein (CRP)' => 300.00,
            'Vitamin D' => 800.00,
            'Vitamin B12' => 600.00,
            'Iron Studies' => 450.00,
            'Hormone Panel' => 1200.00,
        ],
        'Urine Test' => [
            'Urinalysis (Complete)' => 200.00,
            'Urine Culture & Sensitivity' => 400.00,
            'Urine Microalbumin' => 350.00,
            '24-Hour Urine Collection' => 300.00,
            'Pregnancy Test (Urine)' => 150.00,
            'Drug Screening (Urine)' => 500.00,
        ],
        'X-Ray' => [
            'Chest X-Ray' => 400.00,
            'Abdominal X-Ray' => 450.00,
            'Skull X-Ray' => 500.00,
            'Spine X-Ray' => 600.00,
            'Pelvis X-Ray' => 450.00,
            'Extremity X-Ray' => 350.00,
            'Dental X-Ray' => 300.00,
            'Mammography' => 1200.00,
        ],
        'CT Scan' => [
            'CT Head' => 3000.00,
            'CT Chest' => 3500.00,
            'CT Abdomen & Pelvis' => 4500.00,
            'CT Angiography' => 5000.00,
            'CT Spine' => 3500.00,
            'CT Sinus' => 2500.00,
            'CT Coronary Angiography' => 6000.00,
        ],
        'MRI' => [
            'MRI Brain' => 5000.00,
            'MRI Spine' => 5500.00,
            'MRI Joint (Knee/Shoulder)' => 4500.00,
            'MRI Abdomen' => 5500.00,
            'MRI Pelvis' => 5500.00,
            'MRA (Magnetic Resonance Angiography)' => 6000.00,
        ],
        'Ultrasound' => [
            'Abdominal Ultrasound' => 800.00,
            'Pelvic Ultrasound' => 900.00,
            'Obstetric Ultrasound' => 1000.00,
            'Transvaginal Ultrasound' => 1000.00,
            'Thyroid Ultrasound' => 700.00,
            'Breast Ultrasound' => 900.00,
            'Doppler Ultrasound' => 1200.00,
            'Echocardiography' => 1500.00,
        ],
        'ECG' => [
            'Electrocardiogram (ECG/EKG)' => 400.00,
            'Stress Test (Treadmill)' => 2000.00,
            'Holter Monitor (24-Hour)' => 2500.00,
            'Echocardiogram' => 1500.00,
        ],
        'Biopsy' => [
            'Tissue Biopsy' => 2000.00,
            'Bone Marrow Biopsy' => 3000.00,
            'Liver Biopsy' => 3500.00,
            'Kidney Biopsy' => 3500.00,
            'Lymph Node Biopsy' => 2500.00,
            'Skin Biopsy' => 1500.00,
        ],
        'Culture' => [
            'Blood Culture' => 600.00,
            'Urine Culture' => 400.00,
            'Sputum Culture' => 400.00,
            'Wound Culture' => 450.00,
            'Stool Culture' => 400.00,
            'Throat Culture' => 350.00,
        ],
        'Other' => [
            'Stool Test' => 250.00,
            'Sputum Test' => 200.00,
            'Tuberculosis Test (TB)' => 500.00,
            'HIV Test' => 600.00,
            'Hepatitis Panel' => 800.00,
            'Pap Smear' => 700.00,
            'Semen Analysis' => 800.00,
            'Other (Specify in Notes)' => 500.00,
        ],
    ];

    /**
     * Get price for a specific test
     * 
     * @param string $testType The test type
     * @param string $testName The test name
     * @return float The price, or default price if not found
     */
    public function getPrice($testType, $testName = null)
    {
        // If test name is provided, try to get specific price
        if ($testName && isset($this->prices[$testType][$testName])) {
            return (float) $this->prices[$testType][$testName];
        }
        
        // If only test type is provided, return average/default price for that type
        if (isset($this->prices[$testType])) {
            $prices = array_values($this->prices[$testType]);
            return (float) (array_sum($prices) / count($prices));
        }
        
        // Default price if not found
        return 500.00;
    }
}

