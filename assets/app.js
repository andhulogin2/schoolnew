/* ==========================================================================
   EduCore — shared app shell (sidebar + header) and small UI helpers.
   Loaded on every page via <script src="<?php echo base_url('assets/app.js'); ?>">.
   Renders into #sidebar-root and #header-root, wires up all interactivity,
   and highlights the active nav item based on body[data-page].

   CI3 NOTE: this file is a static asset, so it can't run PHP. Every
   controller/template sets `window.APP_BASE_URL` (the CodeIgniter base_url())
   before this script loads. All internal links below are built from
   PAGE_URLS + APP_BASE_URL instead of hardcoded "*.html" files, so they
   resolve to the real CodeIgniter controller/method routes.
   ========================================================================== */

// Page key -> CodeIgniter URI (relative, no leading slash). Mirrors the
// controller/method map in application/config/routes.php + the controllers.
const PAGE_URLS = {
  "dashboard": "dashboard",
  "students": "students",
  "student-registration": "students/register",
  "student-add": "students/register",
  "admissions": "students/admissions",
  "student-documents": "students/documents",
  "student-id-cards": "students/id_cards",
  "student-promotion": "students/promotion",
  "student-transfers": "students/transfers",
  "student-search": "students/search",
  "student-profile": "students/profile",
  "student-reports": "students",
  "staff": "staff",
  "staff-search": "staff",
  "teachers": "staff/teachers",
  "non-teaching-staff": "staff/non_teaching",
  "departments-designations": "staff/departments_designations",
  "departments": "staff/departments",
  "designations": "staff/designations",
  "staff-documents": "staff/documents",
  "teacher-workload": "staff/workload",
  "staff-attendance": "staff/attendance",
  "staff-leave": "staff/leave",
  "staff-reports": "staff",
  "academics": "academics/years",
  "academic-years": "academics/years",
  "classes": "academics/classes",
  "sections": "academics/sections",
  "subjects": "academics/subjects",
  "class-teachers": "academics/class_teachers",
  "subject-teachers": "academics/subject_teachers",
  "timetable": "academics/timetable",
  "academic-calendar": "academics/calendar",
  "academic-reports": "academics/years",
  "attendance": "attendance",
  "attendance-dashboard": "attendance",
  "attendance-daily": "attendance/daily",
  "attendance-periods": "attendance/periods",
  "attendance-period-wise": "attendance/period_wise",
  "attendance-class": "attendance/class_attendance",
  "attendance-section": "attendance/section_attendance",
  "attendance-history": "attendance/history",
  "attendance-tracking": "attendance/tracking",
  "attendance-calendar": "attendance/calendar",
  "attendance-reports": "attendance/reports",
  "attendance-notifications": "attendance/notifications",
  "attendance-notification-history": "attendance/notification_history",
  "attendance-settings": "attendance/settings",
  "exam-dashboard": "examinations",
  "exams": "examinations/exams",
  "exam-types": "examinations/types",
  "exam-schedules": "examinations/schedules",
  "exam-allocations": "examinations/allocations",
  "marks-entry": "examinations/marks_entry",
  "marks-verification": "examinations/verification",
  "grade-management": "examinations/grades",
  "result-calculation": "examinations/calculate",
  "results": "examinations/results",
  "exam-ranks": "examinations/ranks",
  "report-cards": "examinations/report_cards",
  "progress-reports": "examinations/progress_reports",
  "result-publishing": "examinations/publishing",
  "exam-reports": "examinations/reports",
  "exam-settings": "examinations/settings",
  "fee-dashboard": "fees",
  "fee-categories": "fees/categories",
  "fee-structures": "fees/structures",
  "fee-assignments": "fees/assignments",
  "student-fees": "fees/student_fees",
  "fee-collection": "fees/collection",
  "payment-history": "fees/payments",
  "fee-receipts": "fees/receipts",
  "fee-discounts": "fees/discounts",
  "due-fees": "fees/due_fees",
  "fee-reminders": "fees/reminders",
  "fee-adjustments": "fees/adjustments",
  "fee-refunds": "fees/refunds",
  "collection-reports": "fees/reports?type=collection",
  "student-fee-reports": "fees/reports?type=student",
  "due-reports": "fees/reports?type=due",
  "finance-settings": "fees/settings",
  "timetable-dashboard": "timetable",
  "class-timetable": "timetable/classes",
  "teacher-timetable": "timetable/teachers",
  "subject-allocation": "timetable/allocations",
  "timetable-builder": "timetable/builder",
  "free-periods": "timetable/free_periods",
  "timetable-conflicts": "timetable/conflicts",
  "timetable-publish": "timetable/publish_lock",
  "timetable-reports": "timetable/reports",
  "timetable-settings": "timetable/settings",
  "homework-dashboard": "homework",
  "homework-assignments": "homework/assignments",
  "homework-create": "homework/create",
  "homework-calendar": "homework/calendar",
  "homework-subjects": "homework/subjects",
  "homework-classes": "homework/classes",
  "homework-submissions": "homework/submissions",
  "homework-types": "homework/types",
  "homework-reports": "homework/reports",
  "homework-settings": "homework/settings",
  "comm-dashboard": "communication",
  "comm-templates": "communication/templates",
  "comm-sms-templates": "communication/sms_templates",
  "comm-whatsapp-templates": "communication/whatsapp_templates",
  "comm-email-templates": "communication/email_templates",
  "comm-automated": "communication/automated_notifications",
  "comm-rules": "communication/automated_notifications",
  "comm-queue": "communication/queue",
  "comm-history": "communication/history",
  "comm-reports": "communication/reports",
  "comm-settings": "communication/settings",
  "comm-failed": "communication/failed",
  "notices": "communication/notices",
  "announcements": "communication/announcements",
  "communication": "communication",
  "leave-dashboard": "leave",
  "leave-student": "leave/student_leave",
  "leave-staff": "leave/staff_leave",
  "leave-types": "leave/types",
  "leave-request": "leave/request",
  "leave-approval": "leave/approval",
  "leave-balance": "leave/balances",
  "leave-calendar": "leave/calendar",
  "leave-history": "leave/history",
  "leave-reports": "leave/reports",
  "leave-settings": "leave/settings",
  "leave-management": "leave",
  "transport-dashboard": "transport",
  "transport-vehicles": "transport/vehicles",
  "transport-drivers": "transport/drivers",
  "transport-routes": "transport/routes",
  "transport-stops": "transport/stops",
  "transport-assignments": "transport/assignments",
  "transport-bulk": "transport/bulk_assign",
  "transport-fees": "transport/fees",
  "transport-maintenance": "transport/maintenance",
  "transport-maintenance-history": "transport/maintenance_history",
  "transport-documents": "transport/documents",
  "transport-reports": "transport/reports",
  "transport-settings": "transport/settings",
  "transport": "transport",
  "certificates-dashboard": "certificates",
  "certificates-requests": "certificates/requests",
  "certificates-types": "certificates/types",
  "certificates-bonafide": "certificates/bonafide",
  "certificates-transfer": "certificates/transfer_certificate",
  "certificates-study": "certificates/study_certificate",
  "certificates-conduct": "certificates/conduct_certificate",
  "certificates-generate": "certificates/generate",
  "certificates-templates": "certificates/templates",
  "certificates-documents": "certificates/documents",
  "certificates-doc-categories": "certificates/document_categories",
  "certificates-doc-verification": "certificates/document_verification",
  "certificates-history": "certificates/history",
  "certificates-reports": "certificates/reports",
  "certificates-settings": "certificates/settings",
  "reports": "reports",
  "fee-reports": "fees/reports?type=due",
  "result-reports": "examinations/results",
  "library-reports": "reports",
  "inventory-reports": "reports",
  "user-dashboard": "users",
  "users": "users/list",
  "user-create": "users/create",
  "user-details": "users/details",
  "user-roles": "users/roles",
  "user-permissions": "users/permissions",
  "user-role-permissions": "users/role_permissions",
  "user-parents": "users/parents",
  "user-students": "users/students",
  "user-teachers": "users/teachers",
  "user-staff": "users/staff",
  "user-login-activity": "users/login_activity",
  "user-security-settings": "users/security_settings",
  "user-audit-logs": "users/audit_logs",
  "user-management": "users",
  "settings": "settings",
  "unauthorized": "unauthorized",
  "login": "auth/login",
  "logout": "auth/logout",
};

// Builds an absolute app URL for a given page key
function url(pageKey) {
  const base = window.APP_BASE_URL || "";
  const uri = PAGE_URLS[pageKey] || "";
  return base + uri;
}

const CURRENT_USER = window.CURRENT_USER || {
  name: "Anjali Menon",
  role: "Super Admin",
  email: "anjali.menon@gmail.com",
  initials: "AM",
};

// Reorganized 3-Level Sidebar Nav Model:
// Level 1: Main Module (with icon)
// Level 2: Logical Group (collapsible)
// Level 3: Existing Pages (linked to existing routes)
const NAV = [
  { key: "dashboard", label: "Dashboard", icon: "dashboard" },
  {
    key: "students", label: "Student Management", icon: "school",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "students", label: "Student Dashboard" },
          { key: "student-search", label: "Student Search" },
        ],
      },
      {
        label: "Admissions",
        items: [
          { key: "student-registration", label: "Student Registration" },
          { key: "admissions", label: "Admission Management" },
        ],
      },
      {
        label: "Student Profiles",
        items: [
          { key: "student-profile", label: "Student Profile" },
          { key: "student-documents", label: "Student Documents" },
        ],
      },
      {
        label: "Student Services",
        items: [
          { key: "student-id-cards", label: "Student ID Cards" },
          { key: "student-promotion", label: "Student Promotion" },
          { key: "student-transfers", label: "Transfer / TC Management" },
        ],
      },
      {
        label: "Reports",
        items: [
          { key: "student-reports", label: "Student Reports" },
        ],
      },
    ],
  },
  {
    key: "staff", label: "Staff / Teacher Management", icon: "badge",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "staff", label: "Staff Dashboard" },
        ],
      },
      {
        label: "Staff Profiles",
        items: [
          { key: "teachers", label: "Teacher Profiles" },
          { key: "non-teaching-staff", label: "Staff Profiles" },
          { key: "staff-documents", label: "Staff Documents" },
        ],
      },
      {
        label: "Organization",
        items: [
          { key: "departments", label: "Departments" },
          { key: "designations", label: "Designations" },
        ],
      },
      {
        label: "Workload",
        items: [
          { key: "teacher-workload", label: "Teacher Workload" },
        ],
      },
      {
        label: "Attendance & Leave",
        items: [
          { key: "staff-attendance", label: "Staff Attendance" },
          { key: "staff-leave", label: "Staff Leave" },
        ],
      },
      {
        label: "Reports",
        items: [
          { key: "staff-reports", label: "Staff Reports" },
        ],
      },
    ],
  },
  {
    key: "academics", label: "Academic Management", icon: "menu_book",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "academics", label: "Academic Dashboard" },
        ],
      },
      {
        label: "Academic Setup",
        items: [
          { key: "academic-years", label: "Academic Year" },
          { key: "classes", label: "Classes" },
          { key: "sections", label: "Sections / Divisions" },
          { key: "subjects", label: "Subjects" },
        ],
      },
      {
        label: "Teacher Allocation",
        items: [
          { key: "class-teachers", label: "Class Teachers" },
          { key: "subject-teachers", label: "Subject Teachers" },
        ],
      },
      {
        label: "Academic Calendar",
        items: [
          { key: "academic-calendar", label: "Academic Calendar" },
          { key: "timetable", label: "Timetable Schedule" },
        ],
      },
      {
        label: "Reports",
        items: [
          { key: "academic-reports", label: "Academic Reports" },
        ],
      },
    ],
  },
  {
    key: "attendance", label: "Student Attendance", icon: "fact_check",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "attendance-dashboard", label: "Attendance Dashboard" },
        ],
      },
      {
        label: "Attendance",
        items: [
          { key: "attendance-daily", label: "Daily Attendance" },
          { key: "attendance-class", label: "Class Attendance" },
          { key: "attendance-section", label: "Section Attendance" },
          { key: "attendance-period-wise", label: "Period-wise Attendance" },
        ],
      },
      {
        label: "Period Setup",
        items: [
          { key: "attendance-periods", label: "Period Management" },
        ],
      },
      {
        label: "Tracking & History",
        items: [
          { key: "attendance-history", label: "Attendance History" },
          { key: "attendance-tracking", label: "Absent / Late Tracking" },
          { key: "attendance-calendar", label: "Attendance Calendar" },
        ],
      },
      {
        label: "Reports",
        items: [
          { key: "attendance-reports", label: "Attendance Reports" },
        ],
      },
      {
        label: "Notifications",
        items: [
          { key: "attendance-notifications", label: "Parent Notifications" },
          { key: "attendance-notification-history", label: "Notification History" },
        ],
      },
      {
        label: "Settings",
        items: [
          { key: "attendance-settings", label: "Attendance Settings" },
        ],
      },
    ],
  },
  {
    key: "examinations", label: "Examination & Results", icon: "assignment_turned_in",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "exam-dashboard", label: "Examination Dashboard" },
        ],
      },
      {
        label: "Exam Setup",
        items: [
          { key: "exams", label: "Exam Creation" },
          { key: "exam-types", label: "Exam Types" },
          { key: "grade-management", label: "Grade Management" },
        ],
      },
      {
        label: "Exam Schedule",
        items: [
          { key: "exam-schedules", label: "Exam Schedule" },
          { key: "exam-allocations", label: "Subject Allocations" },
        ],
      },
      {
        label: "Marks & Results",
        items: [
          { key: "marks-entry", label: "Marks Entry" },
          { key: "marks-verification", label: "Marks Verification" },
          { key: "result-calculation", label: "Result Calculation" },
          { key: "result-publishing", label: "Result Publishing" },
        ],
      },
      {
        label: "Reports & Cards",
        items: [
          { key: "report-cards", label: "Report Cards" },
          { key: "progress-reports", label: "Progress Reports" },
          { key: "results", label: "Student Results" },
        ],
      },
      {
        label: "Ranking",
        items: [
          { key: "exam-ranks", label: "Rank / Position" },
        ],
      },
      {
        label: "Reports",
        items: [
          { key: "exam-reports", label: "Examination Reports" },
        ],
      },
      {
        label: "Settings",
        items: [
          { key: "exam-settings", label: "Examination Settings" },
        ],
      },
    ],
  },
  {
    key: "fees", label: "Fees & Finance", icon: "payments",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "fee-dashboard", label: "Finance Dashboard" },
        ],
      },
      {
        label: "Fee Setup",
        items: [
          { key: "fee-categories", label: "Fee Categories" },
          { key: "fee-structures", label: "Fee Structure" },
          { key: "fee-adjustments", label: "Fee Adjustments" },
        ],
      },
      {
        label: "Student Fees",
        items: [
          { key: "fee-assignments", label: "Student Fee Assignment" },
          { key: "student-fees", label: "Student Fees" },
          { key: "due-fees", label: "Due Fees" },
          { key: "fee-discounts", label: "Discounts / Concessions" },
        ],
      },
      {
        label: "Payments",
        items: [
          { key: "fee-collection", label: "Fee Collection" },
          { key: "payment-history", label: "Payment History" },
          { key: "fee-receipts", label: "Receipts" },
          { key: "fee-refunds", label: "Fee Refunds" },
        ],
      },
      {
        label: "Reminders",
        items: [
          { key: "fee-reminders", label: "Fee Reminders" },
        ],
      },
      {
        label: "Reports",
        items: [
          { key: "collection-reports", label: "Collection Reports" },
          { key: "due-reports", label: "Fee Reports" },
        ],
      },
      {
        label: "Settings",
        items: [
          { key: "finance-settings", label: "Fee Settings" },
        ],
      },
    ],
  },
  {
    key: "timetable", label: "Timetable", icon: "calendar_month",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "timetable-dashboard", label: "Timetable Dashboard" },
        ],
      },
      {
        label: "Timetables",
        items: [
          { key: "class-timetable", label: "Class Timetable" },
          { key: "teacher-timetable", label: "Teacher Timetable" },
        ],
      },
      {
        label: "Allocation",
        items: [
          { key: "subject-allocation", label: "Subject Allocation" },
        ],
      },
      {
        label: "Period Setup",
        items: [
          { key: "timetable-builder", label: "Timetable Builder" },
          { key: "free-periods", label: "Free Periods" },
        ],
      },
      {
        label: "Publishing",
        items: [
          { key: "timetable-conflicts", label: "Conflict Management" },
          { key: "timetable-publish", label: "Publish / Lock" },
        ],
      },
      {
        label: "Reports",
        items: [
          { key: "timetable-reports", label: "Timetable Reports" },
        ],
      },
      {
        label: "Settings",
        items: [
          { key: "timetable-settings", label: "Timetable Settings" },
        ],
      },
    ],
  },
  {
    key: "homework", label: "Homework / Assignments", icon: "assignment",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "homework-dashboard", label: "Homework Dashboard" },
        ],
      },
      {
        label: "Assignments",
        items: [
          { key: "homework-create", label: "Homework Creation" },
          { key: "homework-assignments", label: "Assignments List" },
          { key: "homework-subjects", label: "Subject-wise Assignments" },
          { key: "homework-classes", label: "Class-wise Assignments" },
        ],
      },
      {
        label: "Submissions",
        items: [
          { key: "homework-submissions", label: "Submission Tracking" },
          { key: "homework-calendar", label: "Assignment Calendar" },
        ],
      },
      {
        label: "Setup",
        items: [
          { key: "homework-types", label: "Assignment Types" },
        ],
      },
      {
        label: "Reports",
        items: [
          { key: "homework-reports", label: "Homework Reports" },
        ],
      },
      {
        label: "Settings",
        items: [
          { key: "homework-settings", label: "Homework Settings" },
        ],
      },
    ],
  },
  {
    key: "communication", label: "Communication / Notifications", icon: "campaign",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "comm-dashboard", label: "Notification Dashboard" },
        ],
      },
      {
        label: "Notices & Circulars",
        items: [
          { key: "notices", label: "Notices" },
          { key: "announcements", label: "Announcements" },
        ],
      },
      {
        label: "Templates",
        items: [
          { key: "comm-templates", label: "Notification Templates" },
          { key: "comm-sms-templates", label: "SMS Templates" },
          { key: "comm-whatsapp-templates", label: "WhatsApp Templates" },
          { key: "comm-email-templates", label: "Email Templates" },
        ],
      },
      {
        label: "Automation",
        items: [
          { key: "comm-automated", label: "Automated Notifications" },
          { key: "comm-rules", label: "Notification Rules" },
        ],
      },
      {
        label: "Queue & Delivery",
        items: [
          { key: "comm-queue", label: "Notification Queue" },
          { key: "comm-reports", label: "Delivery Reports" },
          { key: "comm-failed", label: "Failed Notifications" },
        ],
      },
      {
        label: "History",
        items: [
          { key: "comm-history", label: "Notification History" },
        ],
      },
      {
        label: "Settings",
        items: [
          { key: "comm-settings", label: "Notification Settings" },
        ],
      },
    ],
  },
  {
    key: "leave", label: "Leave Management", icon: "event_busy",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "leave-dashboard", label: "Leave Dashboard" },
        ],
      },
      {
        label: "Leave Requests",
        items: [
          { key: "leave-student", label: "Student Leave" },
          { key: "leave-staff", label: "Staff Leave" },
          { key: "leave-request", label: "Apply Leave" },
        ],
      },
      {
        label: "Approval & Balance",
        items: [
          { key: "leave-approval", label: "Leave Approval" },
          { key: "leave-balance", label: "Leave Balance" },
        ],
      },
      {
        label: "Calendar & History",
        items: [
          { key: "leave-calendar", label: "Leave Calendar" },
          { key: "leave-history", label: "Leave History" },
        ],
      },
      {
        label: "Setup & Reports",
        items: [
          { key: "leave-types", label: "Leave Types" },
          { key: "leave-reports", label: "Leave Reports" },
          { key: "leave-settings", label: "Leave Settings" },
        ],
      },
    ],
  },
  {
    key: "transport", label: "Transport Management", icon: "directions_bus",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "transport-dashboard", label: "Transport Dashboard" },
        ],
      },
      {
        label: "Fleet & Drivers",
        items: [
          { key: "transport-vehicles", label: "Vehicles" },
          { key: "transport-drivers", label: "Drivers" },
        ],
      },
      {
        label: "Routes & Stops",
        items: [
          { key: "transport-routes", label: "Routes" },
          { key: "transport-stops", label: "Stops" },
        ],
      },
      {
        label: "Student Assignment",
        items: [
          { key: "transport-assignments", label: "Student Transport Assignment" },
          { key: "transport-bulk", label: "Bulk Student Assignment" },
        ],
      },
      {
        label: "Fees & Maintenance",
        items: [
          { key: "transport-fees", label: "Transport Fees" },
          { key: "transport-maintenance", label: "Vehicle Maintenance" },
          { key: "transport-maintenance-history", label: "Maintenance History" },
        ],
      },
      {
        label: "Documents & Reports",
        items: [
          { key: "transport-documents", label: "Transport Documents" },
          { key: "transport-reports", label: "Transport Reports" },
          { key: "transport-settings", label: "Transport Settings" },
        ],
      },
    ],
  },
  {
    key: "certificates", label: "Certificate & Documents", icon: "workspace_premium",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "certificates-dashboard", label: "Certificate Dashboard" },
        ],
      },
      {
        label: "Certificates",
        items: [
          { key: "certificates-bonafide", label: "Bonafide Certificate" },
          { key: "certificates-transfer", label: "Transfer Certificate" },
          { key: "certificates-study", label: "Study Certificate" },
          { key: "certificates-conduct", label: "Conduct Certificate" },
        ],
      },
      {
        label: "Requests & Generation",
        items: [
          { key: "certificates-requests", label: "Certificate Requests" },
          { key: "certificates-generate", label: "Certificate Generation" },
          { key: "certificates-templates", label: "Certificate Templates" },
        ],
      },
      {
        label: "Student Documents",
        items: [
          { key: "certificates-documents", label: "Student Documents" },
          { key: "certificates-doc-categories", label: "Document Categories" },
          { key: "certificates-doc-verification", label: "Document Verification" },
        ],
      },
      {
        label: "History & Reports",
        items: [
          { key: "certificates-history", label: "Certificate History" },
          { key: "certificates-reports", label: "Certificate Reports" },
          { key: "certificates-settings", label: "Certificate Settings" },
        ],
      },
    ],
  },
  {
    key: "reports", label: "Reports", icon: "bar_chart",
    groups: [
      { key: "reports", label: "Overview" },
      { key: "student-reports", label: "Student Reports" },
      { key: "attendance-reports", label: "Attendance Reports" },
      {
        label: "Finance Reports",
        items: [
          { key: "fee-reports", aliases: ["due-reports", "student-fee-reports"], label: "Fee Reports" },
          { key: "collection-reports", label: "Collection Reports" },
        ],
      },
      {
        label: "Examination & Results",
        items: [
          { key: "exam-reports", label: "Exam Reports" },
          { key: "result-reports", aliases: ["results"], label: "Result Reports" },
        ],
      },
      { key: "staff-reports", label: "Staff Reports" },
      { key: "academic-reports", label: "Academic Reports" },
      { key: "transport-reports", label: "Transport Reports" },
      { key: "library-reports", label: "Library Reports", soon: true },
      { key: "inventory-reports", label: "Inventory Reports", soon: true },
    ],
  },
  {
    key: "user-management", label: "User & Permission Management", icon: "manage_accounts",
    groups: [
      {
        label: "Overview",
        items: [
          { key: "user-dashboard", label: "User Dashboard" },
        ],
      },
      {
        label: "Users",
        items: [
          { key: "users", label: "Users" },
          { key: "user-create", label: "Add User" },
        ],
      },
      {
        label: "Roles & Permissions",
        items: [
          { key: "user-roles", label: "Roles" },
          { key: "user-permissions", label: "Permissions" },
        ],
      },
      {
        label: "Accounts",
        items: [
          { key: "user-parents", label: "Parent Accounts" },
          { key: "user-students", label: "Student Accounts" },
          { key: "user-teachers", label: "Teacher Accounts" },
          { key: "user-staff", label: "Staff Accounts" },
        ],
      },
      {
        label: "Security & Audits",
        items: [
          { key: "user-login-activity", label: "Login Activity" },
          { key: "user-security-settings", label: "Security Settings" },
          { key: "user-audit-logs", label: "Permission Audit Logs" },
        ],
      },
    ],
  },
  { key: "settings", label: "School Settings", icon: "settings" },
  // Second Phase Modules
  {
    key: "portal", label: "Parent & Student Portal", icon: "diversity_1", soon: true,
    groups: [
      { label: "Overview", items: [{ key: "portal-dashboard", label: "Portal Dashboard", soon: true }] }
    ]
  },
  {
    key: "library", label: "Library Management", icon: "local_library", soon: true,
    groups: [
      { label: "Overview", items: [{ key: "library-dashboard", label: "Library Dashboard", soon: true }] }
    ]
  },
  {
    key: "hostel", label: "Hostel Management", icon: "hotel", soon: true,
    groups: [
      { label: "Overview", items: [{ key: "hostel-dashboard", label: "Hostel Dashboard", soon: true }] }
    ]
  },
  {
    key: "inventory", label: "Inventory / Store", icon: "inventory_2", soon: true,
    groups: [
      { label: "Overview", items: [{ key: "inventory-dashboard", label: "Inventory Dashboard", soon: true }] }
    ]
  },
  {
    key: "events", label: "Events & Activities", icon: "event", soon: true,
    groups: [
      { label: "Overview", items: [{ key: "events-dashboard", label: "Events Dashboard", soon: true }] }
    ]
  },
  { key: "unauthorized", label: "Access Restricted", icon: "lock" },
];

const PAGE_TITLES = {
  "dashboard": "Dashboard", "students": "Student Directory", "student-registration": "Student Registration", "student-admission": "Student Admission", "student-profile": "Student Profile",
  "student-categories": "Student Categories", "student-houses": "Student Houses", "student-roll": "Roll Number Assignment", "student-promote": "Student Promotion",
  "student-documents": "Student Documents", "student-id-cards": "Student ID Cards", "student-promotion": "Student Promotion", "student-transfers": "Transfer / TC Management", "student-search": "Student Search", "student-reports": "Student Reports",
  "staff": "Staff Directory", "staff-directory": "Staff Directory", "staff-search": "Staff Search", "teachers": "Teacher Profiles", "non-teaching-staff": "Staff Profiles",
  "departments-designations": "Departments & Designations", "departments": "Departments", "designations": "Designations", "staff-documents": "Staff Documents",
  "teacher-workload": "Teacher Workload", "staff-attendance": "Staff Attendance", "staff-leave": "Staff Leave Management", "staff-reports": "Staff Reports",
  "academics": "Academic Management", "academic-years": "Academic Years", "classes": "Class Management", "sections": "Section Management",
  "subjects": "Subject Management", "class-teachers": "Class Teachers", "subject-teachers": "Subject Teachers",
  "timetable": "Timetable", "academic-calendar": "Academic Calendar", "academic-reports": "Academic Reports",
  "attendance": "Attendance Dashboard", "attendance-dashboard": "Attendance Dashboard", "attendance-daily": "Daily Attendance", "attendance-periods": "Period Management",
  "attendance-period-wise": "Period-wise Attendance", "attendance-class": "Class Attendance", "attendance-section": "Section Attendance",
  "attendance-history": "Attendance History", "attendance-tracking": "Absent / Late Tracking", "attendance-calendar": "Attendance Calendar",
  "attendance-reports": "Attendance Reports", "attendance-notifications": "Parent Notifications", "attendance-notification-history": "Notification History",
  "attendance-settings": "Attendance Settings",
  "exam-dashboard": "Examination Dashboard", "exams": "Exam Management", "exam-types": "Exam Types",
  "exam-schedules": "Exam Schedules", "exam-allocations": "Subject Allocation", "marks-entry": "Marks Entry",
  "marks-verification": "Marks Verification", "grade-management": "Grade Management", "result-calculation": "Result Calculation",
  "results": "Student Results", "exam-ranks": "Rank / Position", "report-cards": "Report Cards",
  "progress-reports": "Progress Reports", "result-publishing": "Result Publishing", "exam-reports": "Examination Reports",
  "exam-settings": "Examination Settings",
  "fee-dashboard": "Fee Dashboard", "fee-categories": "Fee Categories", "fee-structures": "Fee Structure", "fee-assignments": "Fee Assignment",
  "student-fees": "Student Fees", "fee-collection": "Fee Collection", "payment-history": "Payment History", "fee-receipts": "Receipts",
  "fee-discounts": "Discounts & Concessions", "due-fees": "Due Fees", "fee-reminders": "Fee Reminders", "fee-adjustments": "Fee Adjustments",
  "fee-refunds": "Refunds", "collection-reports": "Collection Reports", "due-reports": "Due Reports", "finance-settings": "Fee Settings",
  "timetable-dashboard": "Timetable Dashboard", "class-timetable": "Class Timetable", "teacher-timetable": "Teacher Timetable",
  "subject-allocation": "Subject Allocation", "timetable-builder": "Timetable Builder", "free-periods": "Free Periods & Substitution",
  "timetable-conflicts": "Conflict Management", "timetable-publish": "Publish / Lock", "timetable-reports": "Timetable Reports",
  "timetable-settings": "Timetable Settings",
  "homework-dashboard": "Homework Dashboard", "homework-assignments": "Assignments", "homework-create": "Create Assignment",
  "homework-calendar": "Assignment Calendar", "homework-subjects": "Subject-wise Assignments", "homework-classes": "Class-wise Assignments",
  "homework-submissions": "Submission Tracking", "homework-types": "Assignment Types", "homework-reports": "Assignment Reports",
  "homework-settings": "Homework Settings",
  "comm-dashboard": "Notification Dashboard", "notices": "Notices & Circulars", "announcements": "Announcements",
  "comm-templates": "Notification Templates", "comm-sms-templates": "SMS Templates",
  "comm-whatsapp-templates": "WhatsApp Templates", "comm-email-templates": "Email Templates",
  "comm-automated": "Automated Notifications", "comm-rules": "Notification Rules",
  "comm-queue": "Notification Queue", "comm-history": "Notification History",
  "comm-reports": "Delivery Reports", "comm-settings": "Notification Settings", "comm-failed": "Failed Notifications",
  "communication": "Communication & Notifications",
  "leave-dashboard": "Leave Dashboard", "leave-student": "Student Leave", "leave-staff": "Staff Leave",
  "leave-types": "Leave Types", "leave-request": "Leave Request", "leave-approval": "Leave Approval",
  "leave-balance": "Leave Balance", "leave-calendar": "Leave Calendar", "leave-history": "Leave History",
  "leave-reports": "Leave Reports", "leave-settings": "Leave Settings", "leave-management": "Leave Management",
  "transport-dashboard": "Transport Dashboard", "transport-vehicles": "Vehicles", "transport-drivers": "Drivers",
  "transport-routes": "Routes", "transport-stops": "Stops", "transport-assignments": "Student Transport Assignments",
  "transport-bulk": "Bulk Student Assignment", "transport-fees": "Transport Fees", "transport-maintenance": "Vehicle Maintenance",
  "transport-maintenance-history": "Maintenance History", "transport-documents": "Transport Documents", "transport-reports": "Transport Reports",
  "transport-settings": "Transport Settings", "transport": "Transport Management",
  "certificates-dashboard": "Certificate Dashboard", "certificates-requests": "Certificate Requests",
  "certificates-types": "Certificate Types", "certificates-bonafide": "Bonafide Certificate",
  "certificates-transfer": "Transfer Certificate", "certificates-study": "Study Certificate",
  "certificates-conduct": "Conduct Certificate", "certificates-generate": "Certificate Generation",
  "certificates-templates": "Certificate Templates", "certificates-documents": "Student Documents",
  "certificates-doc-categories": "Document Categories", "certificates-doc-verification": "Document Verification",
  "certificates-history": "Certificate History", "certificates-reports": "Certificate Reports",
  "certificates-settings": "Certificate Settings", "certificates": "Certificates & Documents",
  "reports": "Reports",
  "fee-reports": "Fee Reports",
  "result-reports": "Result Reports",
  "library-reports": "Library Reports",
  "inventory-reports": "Inventory Reports",
  "user-dashboard": "User & Permission Dashboard",
  "users": "User Management",
  "user-create": "Add User",
  "user-details": "User Profile & Permissions",
  "user-roles": "Role Management",
  "user-permissions": "Permissions Catalog",
  "user-role-permissions": "Role Permissions Matrix",
  "user-parents": "Parent Accounts",
  "user-students": "Student Accounts",
  "user-teachers": "Teacher Accounts",
  "user-staff": "Staff Accounts",
  "user-login-activity": "Login Activity",
  "user-security-settings": "Security Settings",
  "user-audit-logs": "Permission Audit Logs",
  "user-management": "User & Permission Management",
  "settings": "School Settings",
  "unauthorized": "Access Restricted",
};

function iconSpan(name, extra) {
  return `<span class="material-symbols-outlined ${extra || ""}">${name}</span>`;
}

// Find active hierarchy path: Module Key and Group Label for the current active page
function findActiveHierarchy(activeKey) {
  const reportKeys = [
    "reports", "student-reports", "attendance-reports",
    "fee-reports", "due-reports", "student-fee-reports", "collection-reports",
    "exam-reports", "result-reports", "results",
    "staff-reports", "academic-reports", "transport-reports",
    "library-reports", "inventory-reports"
  ];

  if (reportKeys.includes(activeKey)) {
    const repMod = NAV.find((item) => item.key === "reports");
    if (repMod && repMod.groups) {
      for (const group of repMod.groups) {
        if (group.key === activeKey || (group.aliases && group.aliases.includes(activeKey))) {
          return { moduleKey: "reports", groupLabel: null, pageKey: group.key };
        }
        if (group.items) {
          const matched = group.items.find((c) => c.key === activeKey || (c.aliases && c.aliases.includes(activeKey)));
          if (matched) {
            return { moduleKey: "reports", groupLabel: group.label, pageKey: matched.key };
          }
        }
      }
    }
  }

  for (const item of NAV) {
    if (item.key === activeKey) {
      return { moduleKey: item.key, groupLabel: null, pageKey: activeKey };
    }
    if (item.groups) {
      for (const group of item.groups) {
        if (group.key === activeKey || (group.aliases && group.aliases.includes(activeKey))) {
          return { moduleKey: item.key, groupLabel: null, pageKey: group.key };
        }
        if (group.items && group.items.some((c) => c.key === activeKey || (c.aliases && c.aliases.includes(activeKey)))) {
          return { moduleKey: item.key, groupLabel: group.label, pageKey: activeKey };
        }
      }
    }
  }
  return { moduleKey: null, groupLabel: null, pageKey: activeKey };
}

function renderNavItem(item, activeKey, activeHierarchy) {
  const isModuleOpen = item.key === activeHierarchy.moduleKey;

  // Direct link item (e.g. Dashboard, Settings)
  if (!item.groups) {
    const active = item.key === activeKey;
    return `
      <a href="${url(item.key)}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-body-md font-body-md transition-colors
        ${active ? "bg-primary-fixed text-primary font-medium" : "text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface"}">
        <span class="flex items-center gap-3">
          ${iconSpan(item.icon, "text-[20px]")}
          <span class="sidebar-label truncate">${item.label}</span>
        </span>
        ${item.soon ? '<span class="sidebar-label shrink-0 rounded-full bg-tertiary-container/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-on-tertiary-container">Soon</span>' : ""}
      </a>`;
  }

  // Expandable Module with Direct Items or Subgroups
  const groupsHtml = item.groups.map((group) => {
    // Level 2 Direct Item (e.g. Overview, Student Reports, Attendance Reports inside Reports)
    if (group.key) {
      const active = group.key === activeKey || (group.aliases && group.aliases.includes(activeKey));
      return `
        <a href="${url(group.key)}" class="flex items-center justify-between pl-7 pr-3 py-1.5 rounded-lg text-xs font-body-md transition-colors
          ${active ? "bg-primary-fixed text-primary font-bold shadow-xs" : "text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface"}">
          <span class="truncate">${group.label}</span>
          ${group.soon ? '<span class="ml-1.5 shrink-0 rounded-full bg-tertiary-container/10 px-1.5 py-0.2 text-[9px] font-semibold uppercase tracking-wide text-on-tertiary-container">Soon</span>' : ""}
        </a>`;
    }

    // Level 2 Expandable Group (e.g. Finance Reports, Examination & Results)
    const isGroupOpen = isModuleOpen && (
      group.label === activeHierarchy.groupLabel ||
      (group.items && group.items.some((c) => c.key === activeKey || (c.aliases && c.aliases.includes(activeKey))))
    );
    const itemsHtml = group.items.map((c) => {
      const active = c.key === activeKey || (c.aliases && c.aliases.includes(activeKey));
      return `
        <a href="${url(c.key)}" class="flex items-center justify-between pl-11 pr-3 py-1.5 rounded-lg text-xs font-body-md transition-colors
          ${active ? "bg-primary-fixed text-primary font-bold shadow-xs" : "text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface"}">
          <span class="truncate">${c.label}</span>
          ${c.soon ? '<span class="ml-1.5 shrink-0 rounded-full bg-tertiary-container/10 px-1.5 py-0.2 text-[9px] font-semibold uppercase tracking-wide text-on-tertiary-container">Soon</span>' : ""}
        </a>`;
    }).join("");

    return `
      <div class="nav-level-2" data-group-label="${group.label}">
        <button type="button" data-toggle-subgroup
          class="w-full flex items-center justify-between pl-7 pr-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-colors
          ${isGroupOpen ? "text-primary font-bold" : "text-on-surface-variant/80 hover:bg-surface-container-high hover:text-on-surface"}">
          <span class="truncate">${group.label}</span>
          ${iconSpan("chevron_right", `text-[16px] transition-transform ${isGroupOpen ? "rotate-90" : ""}`)}
        </button>
        <div class="nav-sub-items mt-0.5 space-y-0.5 ${isGroupOpen ? "" : "hidden"}">${itemsHtml}</div>
      </div>`;
  }).join("");

  return `
    <div class="nav-group" data-module-key="${item.key}">
      <button type="button" data-toggle-module="${item.key}"
        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-body-md font-body-md transition-colors
        ${isModuleOpen ? "text-on-surface font-semibold bg-surface-container-low" : "text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface"}">
        <span class="flex items-center gap-3">
          ${iconSpan(item.icon, `text-[20px] ${isModuleOpen ? "text-primary" : ""}`)}
          <span class="sidebar-label truncate">${item.label}</span>
        </span>
        ${item.soon ? '<span class="sidebar-label shrink-0 rounded-full bg-tertiary-container/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-on-tertiary-container">Soon</span>' : iconSpan("expand_more", `sidebar-label text-[18px] transition-transform ${isModuleOpen ? "rotate-180" : ""}`)}
      </button>
      <div class="nav-groups mt-1 space-y-1 ${isModuleOpen ? "" : "hidden"}">${groupsHtml}</div>
    </div>`;
}

function renderSidebar(activeKey) {
  const activeHierarchy = findActiveHierarchy(activeKey);
  const navHtml = NAV.map((item) => renderNavItem(item, activeKey, activeHierarchy)).join("");
  return `
  <!-- Mobile overlay -->
  <div id="sidebar-overlay" class="fixed inset-0 bg-on-surface/40 z-30 hidden lg:hidden"></div>

  <aside id="app-sidebar"
    class="fixed lg:sticky top-0 left-0 h-screen w-[264px] shrink-0 bg-surface-container-lowest border-r border-outline-variant/60
    flex flex-col z-40 -translate-x-full lg:translate-x-0 transition-transform duration-200">
    <div class="h-16 flex items-center gap-3 px-4 border-b border-outline-variant/60 shrink-0">
      <div class="w-9 h-9 rounded-full bg-primary-fixed flex items-center justify-center shrink-0">
        ${iconSpan("school", "text-primary text-[20px]")}
      </div>
      <a href="${url("dashboard")}" class="sidebar-label font-headline-md text-headline-md text-primary truncate">EduCore</a>
      <button id="sidebar-collapse-btn" type="button" class="ml-auto hidden lg:flex items-center justify-center w-8 h-8 rounded-lg hover:bg-surface-container-high text-on-surface-variant">
        ${iconSpan("dock_to_right", "text-[20px]")}
      </button>
      <button id="sidebar-close-btn" type="button" class="ml-auto lg:hidden flex items-center justify-center w-8 h-8 rounded-lg hover:bg-surface-container-high text-on-surface-variant">
        ${iconSpan("close", "text-[20px]")}
      </button>
    </div>
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">${navHtml}</nav>
    <div class="border-t border-outline-variant/60 p-3">
      <a href="${url("logout")}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body-md font-body-md text-on-surface-variant hover:bg-error-container hover:text-on-error-container transition-colors">
        ${iconSpan("logout", "text-[20px]")}
        <span class="sidebar-label">Log Out</span>
      </a>
    </div>
  </aside>`;
}

function renderHeader(pageKey, breadcrumb) {
  const title = PAGE_TITLES[pageKey] || "EduCore";
  const crumbHtml = (breadcrumb && breadcrumb.length ? breadcrumb : ["Dashboard", title])
    .map((c, i, arr) => (i === arr.length - 1
      ? `<span class="text-on-surface font-medium">${c}</span>`
      : `<span>${c}</span><span class="text-outline">/</span>`))
    .join(" ");

  return `
  <header class="sticky top-0 z-20 h-16 bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant/60 flex items-center gap-3 px-4 lg:px-6">
    <button id="sidebar-open-btn" type="button" class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg hover:bg-surface-container-high text-on-surface-variant shrink-0">
      ${iconSpan("menu", "text-[22px]")}
    </button>
    <div class="min-w-0">
      <div class="flex items-center gap-1.5 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide">${crumbHtml}</div>
      <h1 class="font-headline-lg-mobile text-headline-lg-mobile lg:font-headline-lg lg:text-headline-lg text-on-surface truncate">${title}</h1>
    </div>

    <div class="hidden md:flex items-center flex-1 max-w-sm ml-4">
      <div class="relative w-full">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/60 text-[20px]">search</span>
        <input type="text" placeholder="Search students, staff, records..."
          class="w-full pl-10 pr-3 py-2 rounded-lg border border-outline-variant bg-surface-container-low text-body-md font-body-md text-on-surface placeholder-on-surface-variant/50 focus:ring-2 focus:ring-primary/10 focus:border-primary transition-colors" />
      </div>
    </div>

    <div class="ml-auto flex items-center gap-1.5 shrink-0">
      <button type="button" class="relative flex items-center justify-center w-9 h-9 rounded-lg hover:bg-surface-container-high text-on-surface-variant">
        ${iconSpan("notifications", "text-[22px]")}
        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-error ring-2 ring-surface-container-lowest"></span>
      </button>
      <div class="relative">
        <button id="profile-menu-btn" type="button" class="flex items-center gap-2 pl-1.5 pr-2 py-1.5 rounded-lg hover:bg-surface-container-high transition-colors">
          <div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center text-label-md text-label-md font-semibold">${CURRENT_USER.initials}</div>
          <div class="hidden sm:block text-left leading-tight">
            <div class="text-body-md font-body-md font-medium text-on-surface">${CURRENT_USER.name}</div>
            <div class="text-[11px] text-on-surface-variant">${CURRENT_USER.role}</div>
          </div>
          ${iconSpan("expand_more", "hidden sm:block text-[18px] text-on-surface-variant")}
        </button>
        <div id="profile-menu" class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-outline-variant/60 bg-surface-container-lowest shadow-[0_10px_15px_-3px_rgba(0,0,0,0.08)] py-2 z-30">
          <div class="px-3.5 py-2 border-b border-outline-variant/60">
            <div class="text-body-md font-body-md font-medium text-on-surface">${CURRENT_USER.name}</div>
            <div class="text-[12px] text-on-surface-variant truncate">${CURRENT_USER.email}</div>
            <span class="inline-block mt-1.5 rounded-full bg-primary-fixed text-primary text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5">${CURRENT_USER.role}</span>
          </div>
          <a href="${url("settings")}" class="flex items-center gap-2.5 px-3.5 py-2 text-body-md font-body-md text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface">${iconSpan("settings", "text-[18px]")} School Settings</a>
          <a href="${url("user-management")}" class="flex items-center gap-2.5 px-3.5 py-2 text-body-md font-body-md text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface">${iconSpan("manage_accounts", "text-[18px]")} User Management</a>
          <div class="my-1 border-t border-outline-variant/60"></div>
          <a href="${url("logout")}" class="flex items-center gap-2.5 px-3.5 py-2 text-body-md font-body-md text-error hover:bg-error-container/40">${iconSpan("logout", "text-[18px]")} Log Out</a>
        </div>
      </div>
    </div>
  </header>`;
}

function initShell() {
  const pageKey = document.body.dataset.page || "dashboard";
  const breadcrumb = document.body.dataset.breadcrumb ? JSON.parse(document.body.dataset.breadcrumb) : null;

  const sidebarRoot = document.getElementById("sidebar-root");
  const headerRoot = document.getElementById("header-root");
  if (sidebarRoot) sidebarRoot.outerHTML = renderSidebar(pageKey);
  if (headerRoot) headerRoot.outerHTML = renderHeader(pageKey, breadcrumb);

  // Mobile drawer
  const sidebar = document.getElementById("app-sidebar");
  const overlay = document.getElementById("sidebar-overlay");
  const openBtn = document.getElementById("sidebar-open-btn");
  const closeBtn = document.getElementById("sidebar-close-btn");
  function openDrawer() { sidebar.classList.remove("-translate-x-full"); overlay.classList.remove("hidden"); }
  function closeDrawer() { sidebar.classList.add("-translate-x-full"); overlay.classList.add("hidden"); }
  openBtn && openBtn.addEventListener("click", openDrawer);
  closeBtn && closeBtn.addEventListener("click", closeDrawer);
  overlay && overlay.addEventListener("click", closeDrawer);

  // Desktop collapse (icon rail)
  const collapseBtn = document.getElementById("sidebar-collapse-btn");
  collapseBtn && collapseBtn.addEventListener("click", () => {
    document.body.classList.toggle("sidebar-collapsed");
  });

  // Level 1: Main Module expand/collapse
  document.querySelectorAll("[data-toggle-module]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const group = btn.closest(".nav-group");
      const groupsDiv = group.querySelector(".nav-groups");
      const chevron = btn.querySelector(".material-symbols-outlined:last-child");
      if (groupsDiv) {
        groupsDiv.classList.toggle("hidden");
        if (chevron) chevron.classList.toggle("rotate-180");
      }
    });
  });

  // Level 2: Subgroup expand/collapse (accordion behavior)
  document.querySelectorAll("[data-toggle-subgroup]").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation();
      const level2 = btn.closest(".nav-level-2");
      const parentContainer = level2 ? level2.closest(".nav-groups") : null;
      const subItems = level2 ? level2.querySelector(".nav-sub-items") : null;
      const chevron = btn.querySelector(".material-symbols-outlined:last-child");
      const isCurrentlyOpen = subItems && !subItems.classList.contains("hidden");

      // Accordion behavior: only one dropdown group remains expanded at a time inside module
      if (parentContainer) {
        parentContainer.querySelectorAll(".nav-level-2").forEach((otherL2) => {
          if (otherL2 !== level2) {
            const otherSub = otherL2.querySelector(".nav-sub-items");
            const otherChevron = otherL2.querySelector("[data-toggle-subgroup] .material-symbols-outlined:last-child");
            if (otherSub) otherSub.classList.add("hidden");
            if (otherChevron) {
              otherChevron.classList.remove("rotate-90");
              otherChevron.classList.remove("rotate-180");
            }
          }
        });
      }

      if (subItems) {
        if (isCurrentlyOpen) {
          subItems.classList.add("hidden");
          if (chevron) {
            chevron.classList.remove("rotate-90");
            chevron.classList.remove("rotate-180");
          }
        } else {
          subItems.classList.remove("hidden");
          if (chevron) {
            chevron.classList.add("rotate-90");
          }
        }
      }
    });
  });

  // Profile dropdown
  const profileBtn = document.getElementById("profile-menu-btn");
  const profileMenu = document.getElementById("profile-menu");
  if (profileBtn) {
    profileBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      profileMenu.classList.toggle("hidden");
    });
    document.addEventListener("click", (e) => {
      if (!profileMenu.contains(e.target)) profileMenu.classList.add("hidden");
    });
  }
}

// ---- Small reusable UI helpers used inline by pages ----
function badge(status) {
  const map = {
    Active: "bg-secondary-container text-on-secondary-container",
    Inactive: "bg-surface-container-high text-on-surface-variant",
    Pending: "bg-tertiary-container/10 text-on-tertiary-container",
    Present: "bg-secondary-container text-on-secondary-container",
    Absent: "bg-error-container text-on-error-container",
    Late: "bg-tertiary-container/10 text-on-tertiary-container",
    Overdue: "bg-error-container text-on-error-container",
    Paid: "bg-secondary-container text-on-secondary-container",
    Archived: "bg-surface-container-high text-on-surface-variant",
  };
  const cls = map[status] || "bg-surface-container-high text-on-surface-variant";
  return `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold ${cls}">${status}</span>`;
}

document.addEventListener("DOMContentLoaded", initShell);
