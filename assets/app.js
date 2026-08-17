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
  "student-add": "students/add",
  "student-id-cards": "students/id_cards",
  "student-profile": "students/profile",
  "staff": "staff",
  "teachers": "staff/teachers",
  "departments": "staff/departments",
  "designations": "staff/designations",
  "academic-years": "academics/years",
  "classes": "academics/classes",
  "sections": "academics/sections",
  "subjects": "academics/subjects",
  "attendance-daily": "attendance",
  "attendance-reports": "attendance/reports",
  "exams": "examinations",
  "results": "examinations/results",
  "fee-dashboard": "fees",
  "fee-structure": "fees/structure",
  "student-fees": "fees/student_fees",
  "fee-collection": "fees/collection",
  "homework-assignments": "homework",
  "homework-submissions": "homework/submissions",
  "notices": "communication",
  "announcements": "communication/announcements",
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
    key: "students", label: "Students", icon: "group",
    children: [
      { key: "students", label: "All Students" },
      { key: "student-add", label: "Add Student" },
      { key: "student-id-cards", label: "Student ID Cards" },
    ],
  },
  {
    key: "staff", label: "Staff", icon: "badge",
    children: [
      { key: "staff", label: "All Staff" },
      { key: "teachers", label: "Teachers" },
      { key: "departments", label: "Departments" },
      { key: "designations", label: "Designations" },
    ],
  },
  {
    key: "academics", label: "Academics", icon: "school",
    children: [
      { key: "academic-years", label: "Academic Years" },
      { key: "classes", label: "Classes" },
      { key: "sections", label: "Sections" },
      { key: "subjects", label: "Subjects" },
    ],
  },
  {
    key: "attendance", label: "Attendance", icon: "fact_check",
    children: [
      { key: "attendance-daily", label: "Daily Attendance" },
      { key: "attendance-reports", label: "Attendance Reports" },
    ],
  },
  {
    key: "examinations", label: "Examinations", icon: "quiz",
    children: [
      { key: "exams", label: "Exams", soon: true },
      { key: "results", label: "Results", soon: true },
    ],
  },
  {
    key: "fees", label: "Fees", icon: "payments",
    children: [
      { key: "fee-dashboard", label: "Fee Dashboard" },
      { key: "fee-structure", label: "Fee Structure", soon: true },
      { key: "student-fees", label: "Student Fees", soon: true },
      { key: "fee-collection", label: "Fee Collection", soon: true },
    ],
  },
  {
    key: "homework", label: "Homework", icon: "assignment",
    children: [
      { key: "homework-assignments", label: "Assignments", soon: true },
      { key: "homework-submissions", label: "Submissions", soon: true },
    ],
  },
  {
    key: "communication", label: "Communication", icon: "campaign",
    children: [
      { key: "notices", label: "Notices" },
      { key: "announcements", label: "Announcements" },
    ],
  },
  { key: "leave-management", label: "Leave Management", icon: "event_busy", soon: true },
  { key: "transport", label: "Transport", icon: "directions_bus", soon: true },
  { key: "certificates", label: "Certificates", icon: "workspace_premium" },
  { key: "reports", label: "Reports", icon: "bar_chart" },
  { key: "user-management", label: "User Management", icon: "manage_accounts" },
  { key: "settings", label: "Settings", icon: "settings" },
];

const PAGE_TITLES = {
  "dashboard": "Dashboard",
  "students": "All Students", "student-add": "Add Student", "student-profile": "Student Profile",
  "student-id-cards": "Student ID Cards",
  "staff": "All Staff", "teachers": "Teachers", "departments": "Departments", "designations": "Designations",
  "academic-years": "Academic Years", "classes": "Classes", "sections": "Sections", "subjects": "Subjects",
  "attendance-daily": "Daily Attendance", "attendance-reports": "Attendance Reports",
  "exams": "Exams", "results": "Results",
  "fee-dashboard": "Fee Dashboard", "fee-structure": "Fee Structure", "student-fees": "Student Fees", "fee-collection": "Fee Collection",
  "homework-assignments": "Assignments", "homework-submissions": "Submissions",
  "notices": "Notices", "announcements": "Announcements",
  "leave-management": "Leave Management", "transport": "Transport",
  "certificates": "Certificates", "reports": "Reports",
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
