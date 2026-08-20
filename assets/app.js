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
// controller/method map in application/config/routes.php + the controllers
// themselves (Dashboard, Students, Staff, Academics, Attendance,
// Examinations, Fees, Homework, Communication, Leave, Transport,
// Certificates, Reports, Users, Settings, Auth, Unauthorized).
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
  "staff": "staff",
  "teachers": "staff/teachers",
  "non-teaching-staff": "staff/non_teaching",
  "departments-designations": "staff/departments_designations",
  "departments": "staff/departments",
  "designations": "staff/designations",
  "staff-documents": "staff/documents",
  "teacher-workload": "staff/workload",
  "staff-attendance": "staff/attendance",
  "staff-leave": "staff/leave",
  "academic-years": "academics/years",
  "classes": "academics/classes",
  "sections": "academics/sections",
  "subjects": "academics/subjects",
  "class-teachers": "academics/class_teachers",
  "subject-teachers": "academics/subject_teachers",
  "timetable": "academics/timetable",
  "academic-calendar": "academics/calendar",
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
  "notices": "communication",
  "announcements": "communication/announcements",
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
  "transport": "transport",
  "certificates": "certificates",
  "reports": "reports",
  "user-management": "users",
  "settings": "settings",
  "unauthorized": "unauthorized",
  "login": "auth/login",
  "logout": "auth/logout",
};

// Builds an absolute app URL for a given page key, e.g. url("students") ->
// "http://host/index.php/students" (or "http://host/students" once
// mod_rewrite / index_page = '' is in effect).
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

// Sidebar nav model. `key` must match body[data-page] on the target page,
// and must exist in PAGE_URLS above so its link resolves correctly.
// `soon: true` items are visibly reachable but show a "Coming Soon" state.
const NAV = [
  { key: "dashboard", label: "Dashboard", icon: "dashboard" },
  {
    key: "students", label: "Student Management", icon: "group",
    children: [
      { key: "students", label: "All Students" },
      { key: "student-registration", label: "Student Registration" },
      { key: "admissions", label: "Admission Management" },
      { key: "student-documents", label: "Student Documents" },
      { key: "student-id-cards", label: "Student ID Cards" },
      { key: "student-promotion", label: "Student Promotion" },
      { key: "student-transfers", label: "Transfer / TC Management" },
      { key: "student-search", label: "Student Search & Filtering" },
    ],
  },
  {
    key: "staff", label: "Staff Management", icon: "badge",
    children: [
      { key: "staff-directory", label: "Staff Directory" },
      { key: "staff-attendance", label: "Staff Attendance" },
      { key: "staff-leave", label: "Staff Leave Management" },
    ],
  },
  {
    key: "academics", label: "Academic Management", icon: "auto_stories",
    children: [
      { key: "academic-years", label: "Academic Years" },
      { key: "classes", label: "Class Management" },
      { key: "sections", label: "Section Management" },
      { key: "subjects", label: "Subject Management" },
      { key: "class-teachers", label: "Class Teachers" },
      { key: "subject-teachers", label: "Subject Teachers" },
      { key: "academic-calendar", label: "Academic Calendar" },
    ],
  },
  {
    key: "attendance", label: "Student Attendance", icon: "fact_check",
    children: [
      { key: "attendance-dashboard", label: "Attendance Dashboard" },
      { key: "attendance-daily", label: "Daily Attendance" },
      { key: "attendance-periods", label: "Period Management" },
      { key: "attendance-period-wise", label: "Period-wise Attendance" },
      { key: "attendance-class", label: "Class Attendance" },
      { key: "attendance-section", label: "Section Attendance" },
      { key: "attendance-history", label: "Attendance History" },
      { key: "attendance-tracking", label: "Absent / Late Tracking" },
      { key: "attendance-calendar", label: "Attendance Calendar" },
      { key: "attendance-reports", label: "Attendance Reports" },
      { key: "attendance-notifications", label: "Parent Notifications" },
      { key: "attendance-notification-history", label: "Notification History" },
      { key: "attendance-settings", label: "Attendance Settings" },
    ],
  },
  {
    key: "examinations", label: "Examinations & Results", icon: "assignment_turned_in",
    children: [
      { key: "exam-dashboard", label: "Exam Dashboard" },
      { key: "exams", label: "Exam Creation" },
      { key: "exam-types", label: "Exam Types" },
      { key: "exam-schedules", label: "Exam Schedule" },
      { key: "exam-allocations", label: "Subject Allocation" },
      { key: "marks-entry", label: "Marks Entry" },
      { key: "marks-verification", label: "Marks Verification" },
      { key: "grade-management", label: "Grade Management" },
      { key: "result-calculation", label: "Result Calculation" },
      { key: "results", label: "Student Results" },
      { key: "exam-ranks", label: "Rank / Position" },
      { key: "report-cards", label: "Report Cards" },
      { key: "progress-reports", label: "Progress Reports" },
      { key: "result-publishing", label: "Publish / Lock" },
      { key: "exam-reports", label: "Exam Reports" },
      { key: "exam-settings", label: "Exam Settings" },
    ],
  },
  {
    key: "fees", label: "Fees & Finance", icon: "payments",
    children: [
      { key: "fee-dashboard", label: "Fee Dashboard" },
      { key: "fee-categories", label: "Fee Categories" },
      { key: "fee-structures", label: "Fee Structure" },
      { key: "fee-assign", label: "Fee Assignment" },
      { key: "fee-collect", label: "Fee Collection" },
      { key: "fee-receipts", label: "Receipts" },
      { key: "fee-discounts", label: "Discounts & Concessions" },
      { key: "fee-dues", label: "Due Fees" },
      { key: "fee-reminders", label: "Fee Reminders" },
      { key: "fee-history", label: "Payment History" },
      { key: "fee-adjustments", label: "Fee Adjustments" },
      { key: "fee-refunds", label: "Refunds" },
      { key: "fee-expenses", label: "Expenses" },
      { key: "fee-income", label: "Income Management" },
      { key: "fee-reports", label: "Finance Reports" },
      { key: "fee-defaulters", label: "Defaulter List" },
      { key: "finance-settings", label: "Fee Settings" },
    ],
  },
  {
    key: "timetable", label: "Timetable", icon: "calendar_month",
    children: [
      { key: "timetable-dashboard", label: "Timetable Dashboard" },
      { key: "class-timetable", label: "Class Timetable" },
      { key: "teacher-timetable", label: "Teacher Timetable" },
      { key: "subject-allocation", label: "Subject Allocation" },
      { key: "timetable-builder", label: "Timetable Builder" },
      { key: "free-periods", label: "Free Periods & Substitution" },
      { key: "timetable-conflicts", label: "Conflict Management" },
      { key: "timetable-publish", label: "Publish / Lock" },
      { key: "timetable-reports", label: "Timetable Reports" },
      { key: "timetable-settings", label: "Timetable Settings" },
    ],
  },
  {
    key: "homework", label: "Homework / Assignments", icon: "assignment",
    children: [
      { key: "homework-dashboard", label: "Homework Dashboard" },
      { key: "homework-assignments", label: "Assignments" },
      { key: "homework-create", label: "Create Assignment" },
      { key: "homework-calendar", label: "Assignment Calendar" },
      { key: "homework-subjects", label: "Subject-wise Assignments" },
      { key: "homework-classes", label: "Class-wise Assignments" },
      { key: "homework-submissions", label: "Submission Tracking" },
      { key: "homework-types", label: "Assignment Types" },
      { key: "homework-reports", label: "Assignment Reports" },
      { key: "homework-settings", label: "Homework Settings" },
    ],
  },
  {
    key: "communication", label: "Communication", icon: "campaign",
    children: [
      { key: "comm-dashboard", label: "Communication Dashboard" },
      { key: "notices", label: "Notices" },
      { key: "announcements", label: "Announcements" },
      { key: "comm-sms", label: "SMS" },
      { key: "comm-whatsapp", label: "WhatsApp" },
      { key: "comm-email", label: "Email" },
      { key: "comm-templates", label: "Notification Templates" },
      { key: "comm-scheduled", label: "Scheduled Notifications" },
      { key: "comm-history", label: "Notification History" },
      { key: "comm-reports", label: "Delivery Reports" },
      { key: "comm-parent-teacher", label: "Parent-Teacher Communication" },
      { key: "comm-messages", label: "Internal Messaging" },
      { key: "comm-conversations", label: "Conversations" },
      { key: "comm-groups", label: "Communication Groups" },
      { key: "comm-settings", label: "Communication Settings" },
    ],
  },
  {
    key: "leave", label: "Leave Management", icon: "event_busy",
    children: [
      { key: "leave-dashboard", label: "Leave Dashboard" },
      { key: "leave-student", label: "Student Leave" },
      { key: "leave-staff", label: "Staff Leave" },
      { key: "leave-types", label: "Leave Types" },
      { key: "leave-request", label: "Leave Requests" },
      { key: "leave-approval", label: "Leave Approval" },
      { key: "leave-balance", label: "Leave Balance" },
      { key: "leave-calendar", label: "Leave Calendar" },
      { key: "leave-history", label: "Leave History" },
      { key: "leave-reports", label: "Leave Reports" },
      { key: "leave-settings", label: "Leave Settings" },
    ],
  },
  { key: "transport", label: "Transport", icon: "directions_bus", soon: true },
  { key: "certificates", label: "Certificates", icon: "workspace_premium" },
  { key: "reports", label: "Reports", icon: "bar_chart" },
  { key: "user-management", label: "User Management", icon: "manage_accounts" },
  { key: "settings", label: "Settings", icon: "settings" },
  { key: "unauthorized", label: "Access Restricted", icon: "lock" },
];

const PAGE_TITLES = {
  "dashboard": "Dashboard", "students": "Student Directory", "student-admission": "Student Admission", "student-profile": "Student Profile",
  "student-categories": "Student Categories", "student-houses": "Student Houses", "student-roll": "Roll Number Assignment", "student-promote": "Student Promotion",
  "staff-directory": "Staff Directory", "staff-attendance": "Staff Attendance", "staff-leave": "Staff Leave Management",
  "academic-years": "Academic Years", "classes": "Class Management", "sections": "Section Management",
  "subjects": "Subject Management", "class-teachers": "Class Teachers", "subject-teachers": "Subject Teachers",
  "timetable": "Timetable", "academic-calendar": "Academic Calendar",
  "attendance-dashboard": "Attendance Dashboard", "attendance-daily": "Daily Attendance", "attendance-periods": "Period Management",
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
  "fee-dashboard": "Fee Dashboard", "fee-structure": "Fee Structure", "student-fees": "Student Fees", "fee-collection": "Fee Collection",
  "timetable-dashboard": "Timetable Dashboard", "class-timetable": "Class Timetable", "teacher-timetable": "Teacher Timetable",
  "subject-allocation": "Subject Allocation", "timetable-builder": "Timetable Builder", "free-periods": "Free Periods & Substitution",
  "timetable-conflicts": "Conflict Management", "timetable-publish": "Publish / Lock", "timetable-reports": "Timetable Reports",
  "timetable-settings": "Timetable Settings",
  "homework-dashboard": "Homework Dashboard", "homework-assignments": "Assignments", "homework-create": "Create Assignment",
  "homework-calendar": "Assignment Calendar", "homework-subjects": "Subject-wise Assignments", "homework-classes": "Class-wise Assignments",
  "homework-submissions": "Submission Tracking", "homework-types": "Assignment Types", "homework-reports": "Assignment Reports",
  "homework-settings": "Homework Settings",
  "comm-dashboard": "Communication Dashboard", "notices": "Notices", "announcements": "Announcements",
  "comm-sms": "SMS", "comm-whatsapp": "WhatsApp", "comm-email": "Email", "comm-templates": "Notification Templates",
  "comm-scheduled": "Scheduled Notifications", "comm-history": "Notification History", "comm-reports": "Delivery Reports",
  "comm-parent-teacher": "Parent-Teacher Communication", "comm-messages": "Internal Messaging", "comm-conversations": "Conversations",
  "comm-groups": "Communication Groups", "comm-settings": "Communication Settings",
  "leave-dashboard": "Leave Dashboard", "leave-student": "Student Leave", "leave-staff": "Staff Leave",
  "leave-types": "Leave Types", "leave-request": "Leave Request", "leave-approval": "Leave Approval",
  "leave-balance": "Leave Balance", "leave-calendar": "Leave Calendar", "leave-history": "Leave History",
  "leave-reports": "Leave Reports", "leave-settings": "Leave Settings", "leave-management": "Leave Management",
  "transport": "Transport", "certificates": "Certificates", "reports": "Reports",
  "user-management": "User Management", "settings": "School Settings",
  "unauthorized": "Access Restricted",
};

function iconSpan(name, extra) {
  return `<span class="material-symbols-outlined ${extra || ""}">${name}</span>`;
}

function findParentKey(pageKey) {
  for (const item of NAV) {
    if (item.children && item.children.some((c) => c.key === pageKey)) return item.key;
  }
  return null;
}

function renderNavItem(item, activeKey, openKey) {
  const isParentActive = item.key === activeKey || item.key === openKey;
  if (item.children) {
    const isOpen = item.key === openKey;
    const childrenHtml = item.children
      .map((c) => {
        const active = c.key === activeKey;
        return `
        <a href="${url(c.key)}" class="flex items-center justify-between pl-11 pr-3 py-2 rounded-lg text-body-md font-body-md transition-colors
          ${active ? "bg-primary-fixed text-primary font-medium" : "text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface"}">
          <span class="truncate">${c.label}</span>
          ${c.soon ? '<span class="ml-2 shrink-0 rounded-full bg-tertiary-container/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-on-tertiary-container">Soon</span>' : ""}
        </a>`;
      })
      .join("");
    return `
      <div class="nav-group" data-group-key="${item.key}">
        <button type="button" data-toggle-group="${item.key}"
          class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-body-md font-body-md transition-colors
          ${isParentActive ? "text-on-surface font-medium" : "text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface"}">
          <span class="flex items-center gap-3">
            ${iconSpan(item.icon, "text-[20px]")}
            <span class="sidebar-label truncate">${item.label}</span>
          </span>
          ${iconSpan("expand_more", `sidebar-label text-[18px] transition-transform ${isOpen ? "rotate-180" : ""}`)}
        </button>
        <div class="nav-children mt-0.5 space-y-0.5 ${isOpen ? "" : "hidden"}">${childrenHtml}</div>
      </div>`;
  }
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

function renderSidebar(activeKey) {
  const openKey = findParentKey(activeKey);
  const navHtml = NAV.map((item) => renderNavItem(item, activeKey, openKey)).join("");
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

  // Submenu expand/collapse
  document.querySelectorAll("[data-toggle-group]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const group = btn.closest(".nav-group");
      const children = group.querySelector(".nav-children");
      const chevron = btn.querySelector(".material-symbols-outlined:last-child");
      children.classList.toggle("hidden");
      chevron.classList.toggle("rotate-180");
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
