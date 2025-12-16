<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Root route - redirect to dashboard if logged in, otherwise login
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::loginPost');
$routes->get('logout', 'Auth::logout');

$routes->get('dashboard', 'Auth::dashboard');
$routes->get('admin/dashboard', 'Admin\\Dashboard::index');

// Admin scheduling
$routes->get('admin/scheduling', 'Admin\\Scheduling::index');
$routes->post('admin/scheduling', 'Admin\\Scheduling::store');

// Admin user access & security
$routes->get('admin/user-access', 'Admin\\UserAccess::index');
$routes->post('admin/user-access', 'Admin\\UserAccess::store');
$routes->get('admin/user-access/edit/(:num)', 'Admin\\UserAccess::edit/$1');
// Allow GET on update to gracefully redirect back to edit instead of 404
$routes->get('admin/user-access/update/(:num)', 'Admin\\UserAccess::update/$1');
$routes->post('admin/user-access/update/(:num)', 'Admin\\UserAccess::update/$1');
$routes->patch('admin/user-access/update/(:num)', 'Admin\\UserAccess::update/$1');

// Admin pharmacy
$routes->get('admin/pharmacy', 'Admin\\Pharmacy::index');
$routes->get('admin/pharmacy/restock', 'Admin\\Pharmacy::index'); // Redirect GET requests to index
$routes->post('admin/pharmacy/restock', 'Admin\\Pharmacy::restock');

// Admin appointments
$routes->get('admin/appointments', 'Admin\\Appointments::index');
$routes->post('admin/appointments', 'Admin\\Appointments::store');

// Admin billing
$routes->get('admin/billing', 'Admin\\Billing::index');
$routes->post('admin/billing', 'Admin\\Billing::store');
$routes->post('admin/billing/payment', 'Admin\\Billing::payment');

// Admin laboratory
$routes->get('admin/laboratory', 'Admin\\Laboratory::index');
$routes->post('admin/laboratory', 'Admin\\Laboratory::store');
$routes->post('admin/laboratory/update-status/(:num)', 'Admin\\Laboratory::updateStatus/$1');

// Admin reports
$routes->get('admin/reports', 'Admin\\Reports::index');
$routes->post('admin/reports/generate', 'Admin\\Reports::generate');

// Admin settings
$routes->get('admin/settings', 'Admin\\Settings::index');
$routes->post('admin/settings', 'Admin\\Settings::update');

// Role-specific dashboards
$routes->get('doctor/dashboard', 'Doctor\\Dashboard::index');
$routes->get('doctor/patients', 'Doctor\\Patients::index');
$routes->get('doctor/appointments', 'Doctor\\Appointments::index');
$routes->post('doctor/appointments/complete/(:num)', 'Doctor\\Appointments::complete/$1');
$routes->post('doctor/appointments/create-follow-up', 'Doctor\\Appointments::createFollowUp');
$routes->get('doctor/schedule', 'Doctor\\Schedule::index');
$routes->post('doctor/schedule', 'Doctor\\Schedule::store');
$routes->get('doctor/lab-results', 'Doctor\\LabResults::index');
$routes->post('doctor/lab-results', 'Doctor\\LabResults::store');
$routes->get('doctor/prescriptions', 'Doctor\\Prescriptions::index');
$routes->post('doctor/prescriptions', 'Doctor\\Prescriptions::store');
$routes->get('doctor/medical-reports', 'Doctor\\MedicalReports::index');
$routes->get('doctor/medical-reports/generate/(:num)', 'Doctor\\MedicalReports::generate/$1');
$routes->post('doctor/medical-reports', 'Doctor\\MedicalReports::store');
$routes->get('doctor/medical-reports/view/(:num)', 'Doctor\\MedicalReports::view/$1');
$routes->get('nurse/dashboard', 'Nurse\\Dashboard::index');
$routes->get('nurse/assigned-patients', 'Nurse\\AssignedPatients::index');
$routes->get('nurse/vitals-monitoring', 'Nurse\\VitalsMonitoring::index');
$routes->post('nurse/vitals-monitoring', 'Nurse\\VitalsMonitoring::update');
$routes->get('nurse/medications', 'Nurse\\Medications::index');
$routes->post('nurse/medications/update/(:num)', 'Nurse\\Medications::update/$1');
$routes->get('receptionist/dashboard', 'Receptionist\\Dashboard::index');
$routes->get('receptionist/follow-up', 'Receptionist\\FollowUp::index');
$routes->get('receptionist/prescription-management', 'Receptionist\\PrescriptionManagement::index');
$routes->get('receptionist/walk-in', 'Receptionist\\WalkIn::index');
$routes->post('receptionist/walk-in', 'Receptionist\\WalkIn::store');
$routes->get('lab/dashboard', 'Lab\\Dashboard::index');
$routes->get('lab/test-requests', 'Lab\\TestRequests::index');
$routes->post('lab/test-requests/update-status/(:num)', 'Lab\\TestRequests::updateStatus/$1');
$routes->get('lab/test-results', 'Lab\\TestResults::index');
$routes->get('pharmacy/dashboard', 'Pharmacy\\Dashboard::index');
$routes->post('pharmacy/dashboard/dispense', 'Pharmacy\\Dashboard::dispense');
$routes->get('pharmacy/prescriptions', 'Pharmacy\\Prescriptions::index');
$routes->post('pharmacy/prescriptions/dispense', 'Pharmacy\\Prescriptions::dispense');
$routes->get('pharmacy/inventory', 'Pharmacy\\Inventory::index');
$routes->get('accounts/dashboard', 'Accounts\\Dashboard::index');
$routes->get('accounts/billing', 'Accounts\\Billing::index');
$routes->get('accounts/billing/patient-bills/(:num)', 'Accounts\\Billing::getPatientBills/$1');
$routes->post('accounts/billing', 'Accounts\\Billing::store');
$routes->post('accounts/payments/record', 'Accounts\\Payments::recordPayment');
$routes->post('accounts/payments/refund', 'Accounts\\Payments::recordRefund');
$routes->get('accounts/expenses', 'Accounts\\Expenses::index');
$routes->get('accounts/expenses/patient/(:num)', 'Accounts\\Expenses::getPatientDetails/$1');
$routes->post('accounts/expenses', 'Accounts\\Expenses::store');
$routes->get('accounts/reports', 'Accounts\\Reports::index');
$routes->post('accounts/reports/generate', 'Accounts\\Reports::generate');
$routes->get('it/dashboard', 'It\\Dashboard::index');
$routes->get('it/system-maintenance', 'It\\SystemMaintenance::index');
$routes->get('it/user-management', 'It\\UserManagement::index');
$routes->get('it/user-management/edit/(:num)', 'It\\UserManagement::edit/$1');
$routes->post('it/user-management/update/(:num)', 'It\\UserManagement::update/$1');
$routes->get('it/backups', 'It\\Backups::index');
$routes->post('it/backups/create', 'It\\Backups::create');
$routes->get('it/backups/download/(:segment)', 'It\\Backups::download/$1');
$routes->post('it/backups/restore/(:segment)', 'It\\Backups::restore/$1');
$routes->post('it/backups/delete/(:segment)', 'It\\Backups::delete/$1');

// Patient registration & EHR
$routes->get('patients/register', 'Patient\\Registration::index');
$routes->post('patients/register', 'Patient\\Registration::store');
$routes->get('patients/records', 'Patient\\Registration::records');
$routes->get('patients/edit/(:num)', 'Patient\\Registration::edit/$1');
// Allow GET on update to gracefully redirect back to edit instead of 404
$routes->get('patients/update/(:num)', 'Patient\\Registration::update/$1');
$routes->post('patients/update/(:num)', 'Patient\\Registration::update/$1');
