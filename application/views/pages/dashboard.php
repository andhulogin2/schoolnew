<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Welcome back, <?php echo html_escape(isset($current_user->name) ? $current_user->name : 'User'); ?></h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Here's what's happening across your school today.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0"><a href="<?php echo site_url('students/add'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">person_add</span>Add Student</a></div>
    </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">group</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+3.2%</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">1,284</div>
      <div class="text-body-md font-body-md text-on-surface-variant">Total Students</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">person</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+1</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">76</div>
      <div class="text-body-md font-body-md text-on-surface-variant">Total Teachers</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">badge</span></div>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">112</div>
      <div class="text-body-md font-body-md text-on-surface-variant">Total Staff</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-tertiary-container/10 text-on-tertiary-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">meeting_room</span></div>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">42</div>
      <div class="text-body-md font-body-md text-on-surface-variant">Total Classes</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">fact_check</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+0.8%</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">94.6%</div>
      <div class="text-body-md font-body-md text-on-surface-variant">Today's Attendance</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">payments</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+12%</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">₹ 8.4L</div>
      <div class="text-body-md font-body-md text-on-surface-variant">Fees Collected (MTD)</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-error-container text-on-error-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">receipt_long</span></div>
        <span class="text-[12px] font-semibold text-error">-4%</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">₹ 1.9L</div>
      <div class="text-body-md font-body-md text-on-surface-variant">Pending Fees</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">person_add</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+5</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">18</div>
      <div class="text-body-md font-body-md text-on-surface-variant">New Admissions</div>
    </div></div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2">
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Today's Attendance Summary</h3>
        <div class="flex items-center gap-2"><a href="<?php echo site_url('attendance/reports'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">chevron_right</span>View Report</a></div>
      </div>
      <div class="p-5">
  <div class="grid grid-cols-3 gap-4 mb-5 text-center">
    <div><div class="font-headline-lg text-headline-lg text-on-secondary-container">1,148</div><div class="text-body-md font-body-md text-on-surface-variant">Present</div></div>
    <div><div class="font-headline-lg text-headline-lg text-error">54</div><div class="text-body-md font-body-md text-on-surface-variant">Absent</div></div>
    <div><div class="font-headline-lg text-headline-lg text-on-tertiary-container">42</div><div class="text-body-md font-body-md text-on-surface-variant">Late</div></div>
  </div>
  <div class="w-full h-2.5 rounded-full bg-surface-container-high overflow-hidden flex">
    <div class="h-full bg-secondary" style="width:89%"></div>
    <div class="h-full bg-tertiary-fixed-dim" style="width:4%"></div>
    <div class="h-full bg-error" style="width:7%"></div>
  </div>
  <p class="text-body-md font-body-md text-on-surface-variant mt-2">94.6% attendance across all active sections today.</p>
</div>
    </div></div>
    
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Student Overview</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
  <div class="space-y-3">
    <div class="flex items-center justify-between text-body-md font-body-md"><span class="text-on-surface-variant">New Admissions (this month)</span><span class="font-semibold text-on-surface">18</span></div>
    <div class="flex items-center justify-between text-body-md font-body-md"><span class="text-on-surface-variant">Active Students</span><span class="font-semibold text-on-surface">1,266</span></div>
    <div class="flex items-center justify-between text-body-md font-body-md"><span class="text-on-surface-variant">Boys / Girls</span><span class="font-semibold text-on-surface">648 / 636</span></div>
    <div class="pt-2 border-t border-outline-variant/50">
      <div class="text-label-md text-label-md text-on-surface-variant uppercase mb-2">Students by Class (sample)</div>
      <div class="flex items-center gap-2 mb-1.5"><span class="w-16 text-[12px] text-on-surface-variant">Grade 3</span><div class="flex-1 h-2 rounded-full bg-surface-container-high overflow-hidden"><div class="h-full bg-primary-container" style="width:72%"></div></div></div><div class="flex items-center gap-2 mb-1.5"><span class="w-16 text-[12px] text-on-surface-variant">Grade 6</span><div class="flex-1 h-2 rounded-full bg-surface-container-high overflow-hidden"><div class="h-full bg-primary-container" style="width:64%"></div></div></div><div class="flex items-center gap-2 mb-1.5"><span class="w-16 text-[12px] text-on-surface-variant">Grade 9</span><div class="flex-1 h-2 rounded-full bg-surface-container-high overflow-hidden"><div class="h-full bg-primary-container" style="width:80%"></div></div></div><div class="flex items-center gap-2 mb-1.5"><span class="w-16 text-[12px] text-on-surface-variant">Grade 12</span><div class="flex-1 h-2 rounded-full bg-surface-container-high overflow-hidden"><div class="h-full bg-primary-container" style="width:55%"></div></div></div>
    </div>
  </div>
</div>
    </div>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Fees Overview</h3>
        <div class="flex items-center gap-2"><a href="<?php echo site_url('fees'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">chevron_right</span>Open Fee Dashboard</a></div>
      </div>
      <div class="p-5">
  <div class="grid grid-cols-2 gap-4">
    <div><div class="text-body-md font-body-md text-on-surface-variant">Today's Collection</div><div class="font-headline-md text-headline-md text-on-surface">₹ 42,500</div></div>
    <div><div class="text-body-md font-body-md text-on-surface-variant">Monthly Collection</div><div class="font-headline-md text-headline-md text-on-surface">₹ 8,41,200</div></div>
    <div><div class="text-body-md font-body-md text-on-surface-variant">Pending Fees</div><div class="font-headline-md text-headline-md text-on-tertiary-container">₹ 1,92,000</div></div>
    <div><div class="text-body-md font-body-md text-on-surface-variant">Overdue Fees</div><div class="font-headline-md text-headline-md text-error">₹ 38,400</div></div>
  </div>
</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Upcoming Events</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
  <ul class="space-y-3">
    <li class="flex items-center gap-3"><div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex flex-col items-center justify-center text-[11px] font-semibold leading-none shrink-0"><span>18</span><span>AUG</span></div><div class="min-w-0"><div class="text-body-md font-body-md text-on-surface truncate">Mid-Term Exams Begin</div><div class="text-[12px] text-on-surface-variant">Grades 6-12 · All Sections</div></div></li><li class="flex items-center gap-3"><div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex flex-col items-center justify-center text-[11px] font-semibold leading-none shrink-0"><span>22</span><span>AUG</span></div><div class="min-w-0"><div class="text-body-md font-body-md text-on-surface truncate">Independence Day Celebration</div><div class="text-[12px] text-on-surface-variant">Whole School · Assembly Ground</div></div></li><li class="flex items-center gap-3"><div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex flex-col items-center justify-center text-[11px] font-semibold leading-none shrink-0"><span>29</span><span>AUG</span></div><div class="min-w-0"><div class="text-body-md font-body-md text-on-surface truncate">PTA Meeting</div><div class="text-[12px] text-on-surface-variant">Grades 1-5 · 3:00 PM</div></div></li>
  </ul>
</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Recent Notices</h3>
        <div class="flex items-center gap-2"><a href="<?php echo site_url('communication'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">chevron_right</span>View All</a></div>
      </div>
      <div class="p-5">
  <ul class="divide-y divide-outline-variant/50">
    <li class="py-3 first:pt-0 last:pb-0"><div class="flex items-start justify-between gap-2"><div><div class="text-body-md font-body-md text-on-surface font-medium">Revised Bus Timings from Monday</div><div class="text-[12px] text-on-surface-variant mt-0.5">Transport Office</div></div><span class="text-[11px] text-on-surface-variant whitespace-nowrap">2d ago</span></div></li><li class="py-3 first:pt-0 last:pb-0"><div class="flex items-start justify-between gap-2"><div><div class="text-body-md font-body-md text-on-surface font-medium">Annual Sports Day Registrations Open</div><div class="text-[12px] text-on-surface-variant mt-0.5">Physical Education Dept.</div></div><span class="text-[11px] text-on-surface-variant whitespace-nowrap">4d ago</span></div></li><li class="py-3 first:pt-0 last:pb-0"><div class="flex items-start justify-between gap-2"><div><div class="text-body-md font-body-md text-on-surface font-medium">Library Books Due for Return</div><div class="text-[12px] text-on-surface-variant mt-0.5">Library</div></div><span class="text-[11px] text-on-surface-variant whitespace-nowrap">6d ago</span></div></li>
  </ul>
</div>
    </div>
  </div>


