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
$route['academics/calendar']         = 'academics/calendar';

$route['attendance']                       = 'attendance/index';
$route['attendance/dashboard']             = 'attendance/index';
$route['attendance/daily']                 = 'attendance/daily';
$route['attendance/periods']               = 'attendance/periods';
$route['attendance/periods/(:any)']        = 'attendance/periods/$1';
$route['attendance/period_wise']           = 'attendance/period_wise';
$route['attendance/class_attendance']      = 'attendance/class_attendance';
$route['attendance/section_attendance']    = 'attendance/section_attendance';
$route['attendance/history']               = 'attendance/history';
$route['attendance/tracking']              = 'attendance/tracking';
$route['attendance/calendar']              = 'attendance/calendar';
$route['attendance/reports']               = 'attendance/reports';
$route['attendance/notifications']         = 'attendance/notifications';
$route['attendance/notification_history']  = 'attendance/notification_history';
$route['attendance/settings']              = 'attendance/settings';

$route['examinations']                      = 'examinations/index';
$route['examinations/dashboard']            = 'examinations/index';
$route['examinations/exams']                = 'examinations/exams';
$route['examinations/types']                = 'examinations/types';
$route['examinations/schedules']            = 'examinations/schedules';
$route['examinations/allocations']          = 'examinations/allocations';
$route['examinations/marks_entry']          = 'examinations/marks_entry';
$route['examinations/verification']         = 'examinations/verification';
$route['examinations/grades']               = 'examinations/grades';
$route['examinations/calculate']            = 'examinations/calculate';
$route['examinations/results']              = 'examinations/results';
$route['examinations/result_detail/(:num)'] = 'examinations/result_detail/$1';
$route['examinations/ranks']                = 'examinations/ranks';
$route['examinations/report_cards']         = 'examinations/report_cards';
$route['examinations/report_card/(:num)']   = 'examinations/report_card/$1';
$route['examinations/progress_reports']     = 'examinations/progress_reports';
$route['examinations/progress_report/(:num)']= 'examinations/progress_report/$1';
$route['examinations/publishing']           = 'examinations/publishing';
$route['examinations/reports']              = 'examinations/reports';
$route['examinations/settings']             = 'examinations/settings';

$route['fees']                          = 'fees/index';
$route['fees/dashboard']                = 'fees/index';
$route['fees/categories']               = 'fees/categories';
$route['fees/structures']               = 'fees/structures';
$route['fees/structure']                = 'fees/structures';
$route['fees/assignments']              = 'fees/assignments';
$route['fees/student_fees']             = 'fees/student_fees';
$route['fees/student_fees/(:num)']      = 'fees/student_fees/$1';
$route['fees/collection']               = 'fees/collection';
$route['fees/payments']                 = 'fees/payments';
$route['fees/receipts']                 = 'fees/receipts';
$route['fees/receipt/(:num)']           = 'fees/receipt/$1';
$route['fees/receipt_history']          = 'fees/receipts';
$route['fees/discounts']                = 'fees/discounts';
$route['fees/due_fees']                 = 'fees/due_fees';
$route['fees/reminders']                = 'fees/reminders';
$route['fees/reminder_history']         = 'fees/reminder_history';
$route['fees/adjustments']              = 'fees/adjustments';
$route['fees/refunds']                  = 'fees/refunds';
$route['fees/reports']                  = 'fees/reports';
$route['fees/settings']                 = 'fees/settings';

$route['timetable']                     = 'timetable/index';
$route['timetable/dashboard']           = 'timetable/index';
$route['timetable/classes']             = 'timetable/classes';
$route['timetable/teachers']            = 'timetable/teachers';
$route['timetable/allocations']         = 'timetable/allocations';
$route['timetable/builder']             = 'timetable/builder';
$route['timetable/free_periods']        = 'timetable/free_periods';
$route['timetable/conflicts']           = 'timetable/conflicts';
$route['timetable/publish_lock']        = 'timetable/publish_lock';
$route['timetable/reports']             = 'timetable/reports';
$route['timetable/settings']            = 'timetable/settings';
$route['timetable/delete_slot/(:num)']  = 'timetable/delete_slot/$1';
$route['timetable/ajax_get_entry/(:num)']= 'timetable/ajax_get_entry/$1';

$route['homework']                           = 'homework/index';
$route['homework/dashboard']                 = 'homework/index';
$route['homework/assignments']               = 'homework/assignments';
$route['homework/create']                    = 'homework/create';
$route['homework/edit/(:num)']               = 'homework/edit/$1';
$route['homework/details/(:num)']            = 'homework/details/$1';
$route['homework/types']                     = 'homework/types';
$route['homework/subjects']                  = 'homework/subjects';
$route['homework/classes']                   = 'homework/classes';
$route['homework/calendar']                  = 'homework/calendar';
$route['homework/submissions']               = 'homework/submissions';
$route['homework/submission_detail/(:num)']  = 'homework/submission_detail/$1';
$route['homework/review/(:num)']             = 'homework/review/$1';
$route['homework/student_view/(:num)']       = 'homework/student_view/$1';
$route['homework/reports']                   = 'homework/reports';
$route['homework/settings']                  = 'homework/settings';
$route['homework/duplicate/(:num)']          = 'homework/duplicate/$1';
$route['homework/publish/(:num)']            = 'homework/publish/$1';
$route['homework/archive/(:num)']            = 'homework/archive/$1';
$route['homework/delete/(:num)']             = 'homework/delete/$1';

$route['communication']                           = 'communication/index';
$route['communication/dashboard']                 = 'communication/index';
$route['communication/notices']                   = 'communication/notices';
$route['communication/create_notice']             = 'communication/create_notice';
$route['communication/delete_notice/(:num)']      = 'communication/delete_notice/$1';
$route['communication/archive_notice/(:num)']     = 'communication/archive_notice/$1';
$route['communication/announcements']             = 'communication/announcements';
$route['communication/announcement_details/(:num)']= 'communication/announcement_details/$1';
$route['communication/delete_announcement/(:num)']= 'communication/delete_announcement/$1';
$route['communication/sms']                       = 'communication/sms';
$route['communication/whatsapp']                  = 'communication/whatsapp';
$route['communication/email']                     = 'communication/email';
$route['communication/templates']                 = 'communication/templates';
$route['communication/scheduled']                 = 'communication/scheduled';
$route['communication/history']                   = 'communication/history';
$route['communication/reports']                   = 'communication/reports';
$route['communication/parent_teacher']            = 'communication/parent_teacher';
$route['communication/messages']                  = 'communication/messages';
$route['communication/conversations']             = 'communication/conversations';
$route['communication/conversation_view/(:num)']  = 'communication/conversation_view/$1';
$route['communication/groups']                    = 'communication/groups';
$route['communication/settings']                  = 'communication/settings';

$route['leave']                                   = 'leave/index';
$route['leave/dashboard']                         = 'leave/index';
$route['leave/student_leave']                     = 'leave/student_leave';
$route['leave/staff_leave']                       = 'leave/staff_leave';
$route['leave/types']                             = 'leave/types';
$route['leave/request']                           = 'leave/request';
$route['leave/approval']                          = 'leave/approval';
$route['leave/approve/(:num)']                    = 'leave/approve_action/$1';
$route['leave/reject/(:num)']                     = 'leave/reject_action/$1';
$route['leave/clarification/(:num)']              = 'leave/clarification_action/$1';
$route['leave/cancel/(:num)']                     = 'leave/cancel_action/$1';
$route['leave/balances']                          = 'leave/balances';
$route['leave/calendar']                          = 'leave/calendar';
$route['leave/history']                           = 'leave/history';
$route['leave/details/(:num)']                    = 'leave/details/$1';
$route['leave/reports']                           = 'leave/reports';
$route['leave/settings']                          = 'leave/settings';

$route['unauthorized']       = 'unauthorized/index';

