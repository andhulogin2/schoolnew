<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*
 * EduCore route aliases
 * Default CI3 controller/method routing already resolves most URIs
 * (e.g. /students/add -> Students::add()). These aliases only cover
 * the handful of URIs that need a nicer public path than the raw
 * controller/method pair.
 */
$route['login']              = 'auth/login';
$route['logout']             = 'auth/logout';
$route['students/register']   = 'students/register';
$route['students/admissions'] = 'students/admissions';
$route['students/documents']  = 'students/documents';
$route['students/id_cards']   = 'students/id_cards';
$route['students/promotion']  = 'students/promotion';
$route['students/transfers']  = 'students/transfers';
$route['students/search']     = 'students/search';
$route['students/tc/(:num)']  = 'students/tc/$1';

$route['staff/register']                    = 'staff/register';
$route['staff/teachers']                    = 'staff/teachers';
$route['staff/non_teaching']                = 'staff/non_teaching';
$route['staff/departments_designations']    = 'staff/departments_designations';
$route['staff/documents']                   = 'staff/documents';
$route['staff/workload']                    = 'staff/workload';
$route['staff/attendance']                  = 'staff/attendance';
$route['staff/leave']                       = 'staff/leave';

$route['academics/years']            = 'academics/years';
$route['academics/classes']          = 'academics/classes';
$route['academics/sections']         = 'academics/sections';
$route['academics/subjects']         = 'academics/subjects';
$route['academics/class_teachers']   = 'academics/class_teachers';
$route['academics/subject_teachers'] = 'academics/subject_teachers';
$route['academics/timetable']        = 'academics/timetable';

$route['attendance']         = 'attendance/index';
$route['examinations']       = 'examinations/index';
$route['fees']                = 'fees/index';
$route['homework']           = 'homework/index';
$route['communication']      = 'communication/index';
$route['unauthorized']       = 'unauthorized/index';
