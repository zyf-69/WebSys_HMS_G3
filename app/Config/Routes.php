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

// Admin appointments
$routes->get('admin/appointments', 'Admin\\Appointments::index');
$routes->post('admin/appointments', 'Admin\\Appointments::store');

// Role-specific dashboards
$routes->get('doctor/dashboard', 'Doctor\\Dashboard::index');
$routes->get('nurse/dashboard', 'Nurse\\Dashboard::index');
$routes->get('receptionist/dashboard', 'Receptionist\\Dashboard::index');
$routes->get('lab/dashboard', 'Lab\\Dashboard::index');
$routes->get('pharmacy/dashboard', 'Pharmacy\\Dashboard::index');
$routes->get('accounts/dashboard', 'Accounts\\Dashboard::index');
$routes->get('it/dashboard', 'It\\Dashboard::index');

// Patient registration & EHR
$routes->get('patients/register', 'Patient\\Registration::index');
$routes->post('patients/register', 'Patient\\Registration::store');
$routes->get('patients/records', 'Patient\\Registration::records');
$routes->get('patients/edit/(:num)', 'Patient\\Registration::edit/$1');
$routes->post('patients/update/(:num)', 'Patient\\Registration::update/$1');
