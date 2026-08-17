<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Reports</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Basic reports available in Phase 1 — search, filter, and export.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0"></div>
    </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-5 flex flex-col">
    <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center mb-3"><span class="material-symbols-outlined text-[20px]">group</span></div>
    <h3 class="font-headline-md text-headline-md text-on-surface" style="font-size:16px">Student Report</h3>
    <p class="text-body-md font-body-md text-on-surface-variant mt-1 flex-1">Class-wise, section-wise, and status-wise student listings.</p>
    <div class="flex gap-2 mt-4">
      <a href="<?php echo site_url('students'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">visibility</span>View Students</a>
    </div>
  </div>
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-5 flex flex-col">
    <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center mb-3"><span class="material-symbols-outlined text-[20px]">badge</span></div>
    <h3 class="font-headline-md text-headline-md text-on-surface" style="font-size:16px">Staff Report</h3>
    <p class="text-body-md font-body-md text-on-surface-variant mt-1 flex-1">Department and designation-wise staff listings.</p>
    <div class="flex gap-2 mt-4">
      <a href="<?php echo site_url('staff'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">visibility</span>View Staff</a>
    </div>
  </div>
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-5 flex flex-col">
    <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center mb-3"><span class="material-symbols-outlined text-[20px]">meeting_room</span></div>
    <h3 class="font-headline-md text-headline-md text-on-surface" style="font-size:16px">Class / Section Report</h3>
    <p class="text-body-md font-body-md text-on-surface-variant mt-1 flex-1">Capacity, enrolment, and class-teacher summaries.</p>
    <div class="flex gap-2 mt-4">
      <a href="<?php echo site_url('academics/classes'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">visibility</span>View Classes</a>
    </div>
  </div>
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-5 flex flex-col">
    <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center mb-3"><span class="material-symbols-outlined text-[20px]">fact_check</span></div>
    <h3 class="font-headline-md text-headline-md text-on-surface" style="font-size:16px">Attendance Report</h3>
    <p class="text-body-md font-body-md text-on-surface-variant mt-1 flex-1">Daily, monthly, and class-wise attendance summaries.</p>
    <div class="flex gap-2 mt-4">
      <a href="<?php echo site_url('attendance/reports'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">visibility</span>View Reports</a>
    </div>
  </div></div>

