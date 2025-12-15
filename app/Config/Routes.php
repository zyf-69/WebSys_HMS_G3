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
$routes->post('admin/pharmacy/restock', 'Admin\\Pharmacy::restock');

// Admin appointments
$routes->get('admin/appointments', 'Admin\\Appointments::index');
$routes->post('admin/appointments', 'Admin\\Appointments::store');

// Role-specific dashboards
$routes->get('doctor/dashboard', 'Doctor\\Dashboard::index');
$routes->get('doctor/patients', 'Doctor\\Patients::index');
$routes->get('doctor/appointments', 'Doctor\\Appointments::index');
$routes->get('doctor/schedule', 'Doctor\\Schedule::index');
$routes->post('doctor/schedule', 'Doctor\\Schedule::store');
$routes->get('nurse/dashboard', 'Nurse\\Dashboard::index');
$routes->get('receptionist/dashboard', 'Receptionist\\Dashboard::index');
$routes->get('lab/dashboard', 'Lab\\Dashboard::index');
$routes->get('pharmacy/dashboard', 'Pharmacy\\Dashboard::index');
$routes->post('pharmacy/dashboard/dispense', 'Pharmacy\\Dashboard::dispense');
$routes->get('accounts/dashboard', 'Accounts\\Dashboard::index');
$routes->get('it/dashboard', 'It\\Dashboard::index');

// Patient registration & EHR
$routes->get('patients/register', 'Patient\\Registration::index');
$routes->post('patients/register', 'Patient\\Registration::store');
$routes->get('patients/records', 'Patient\\Registration::records');
$routes->get('patients/edit/(:num)', 'Patient\\Registration::edit/$1');
$routes->post('patients/update/(:num)', 'Patient\\Registration::update/$1');
