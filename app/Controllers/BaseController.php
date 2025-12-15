<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form'];

    /**
     * @var \CodeIgniter\Session\Session
     */
    protected $session;

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = service('session');
    }

    protected function requireLogin()
    {
        if (!$this->session->get('isLoggedIn')) {
            $this->session->setFlashdata('error', 'Please log in to continue.');
            return redirect()->to(site_url('login'));
        }

        return true;
    }

    /**
     * @param string|array $roles
     */
    protected function hasRole($roles): bool
    {
        $currentRole = $this->session->get('role');
        if (empty($currentRole)) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($currentRole, $roles, true);
        }

        return $currentRole === $roles;
    }

    protected function getSessionData(): array
    {
        return [
            'user_id'    => $this->session->get('user_id'),
            'username'   => $this->session->get('username'),
            'email'      => $this->session->get('email'),
            'first_name' => $this->session->get('first_name'),
            'last_name'  => $this->session->get('last_name'),
            'role'       => $this->session->get('role'),
            'role_id'    => $this->session->get('role_id'),
        ];
    }

    /**
     * Auto-generate a bill for a patient based on service provided
     * 
     * @param int $patientId Patient ID
     * @param string $billType Bill type (Pharmacy, Laboratory, Consultation, Procedure, Room & Board)
     * @param float $amount Total amount
     * @param string $description Description of the service/item
     * @return int|false Bill ID on success, false on failure
     */
    protected function generateBill($patientId, $billType, $amount, $description = null)
    {
        if (!$patientId || !$billType || $amount <= 0) {
            log_message('error', 'Invalid parameters for bill generation: patient_id=' . $patientId . ', bill_type=' . $billType . ', amount=' . $amount);
            return false;
        }

        $db = db_connect();

        try {
            // Generate invoice number: INV-YYYY-NNNN
            $year = date('Y');
            $lastInvoice = $db->table('bills')
                ->like('invoice_number', "INV-{$year}-", 'after')
                ->orderBy('invoice_number', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
            
            if ($lastInvoice && !empty($lastInvoice['invoice_number'])) {
                // Extract the sequence number
                $parts = explode('-', $lastInvoice['invoice_number']);
                $sequence = isset($parts[2]) ? (int) $parts[2] : 0;
                $sequence++;
            } else {
                $sequence = 1;
            }
            
            $invoiceNumber = sprintf('INV-%s-%04d', $year, $sequence);

            $billData = [
                'patient_id' => $patientId,
                'invoice_number' => $invoiceNumber,
                'bill_type' => $billType,
                'total_amount' => $amount,
                'description' => $description,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $db->table('bills')->insert($billData);
            $billId = $db->insertID();

            log_message('info', 'Bill generated: ID=' . $billId . ', Invoice=' . $invoiceNumber . ', Patient=' . $patientId . ', Type=' . $billType . ', Amount=' . $amount);
            return $billId;
        } catch (\Exception $e) {
            log_message('error', 'Failed to generate bill: ' . $e->getMessage());
            return false;
        }
    }
}
