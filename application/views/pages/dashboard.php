<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $user = $this->session->userdata('user');
  $firstName = $user ? explode(' ', $user['full_name'])[0] : 'Admin';
?>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Welcome back, <?php echo html_escape($firstName); ?></h2>
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
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface"><?php echo number_format($total_students); ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Total Students</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">person</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+1</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface"><?php echo number_format($total_teachers); ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Total Teachers</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">badge</span></div>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface"><?php echo number_format($total_staff); ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Total Staff</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-tertiary-container/10 text-on-tertiary-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">meeting_room</span></div>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface"><?php echo number_format($total_classes); ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Total Classes</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">fact_check</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+0.8%</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface"><?php echo $attendance['percentage']; ?>%</div>
      <div class="text-body-md font-body-md text-on-surface-variant">Today's Attendance</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">payments</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+12%</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">₹ <?php echo number_format($fees['monthly_collection']); ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Fees Collected (MTD)</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-error-container text-on-error-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">receipt_long</span></div>
        <span class="text-[12px] font-semibold text-error">-4%</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">₹ <?php echo number_format($fees['pending_fees']); ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Pending Fees</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">person_add</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+5</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface"><?php echo $total_students; ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Total Admissions</div>
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
    <div><div class="font-headline-lg text-headline-lg text-on-secondary-container"><?php echo $attendance['present']; ?></div><div class="text-body-md font-body-md text-on-surface-variant">Present</div></div>
    <div><div class="font-headline-lg text-headline-lg text-error"><?php echo $attendance['absent']; ?></div><div class="text-body-md font-body-md text-on-surface-variant">Absent</div></div>
    <div><div class="font-headline-lg text-headline-lg text-on-tertiary-container"><?php echo $attendance['late']; ?></div><div class="text-body-md font-body-md text-on-surface-variant">Late</div></div>
  </div>
  <div class="w-full h-2.5 rounded-full bg-surface-container-high overflow-hidden flex">
    <div class="h-full bg-secondary" style="width:<?php echo max(10, min(95, $attendance['percentage'])); ?>%"></div>
    <div class="h-full bg-tertiary-fixed-dim" style="width:5%"></div>
    <div class="h-full bg-error" style="width:5%"></div>
  </div>
  <p class="text-body-md font-body-md text-on-surface-variant mt-2"><?php echo $attendance['percentage']; ?>% attendance across all active sections today.</p>
</div>
    </div></div>
    
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Student Overview</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
  <div class="space-y-3">
    <div class="flex items-center justify-between text-body-md font-body-md"><span class="text-on-surface-variant">Active Students</span><span class="font-semibold text-on-surface"><?php echo $total_students; ?></span></div>
    <div class="flex items-center justify-between text-body-md font-body-md"><span class="text-on-surface-variant">Boys / Girls</span><span class="font-semibold text-on-surface"><?php echo (isset($gender['boys']) ? $gender['boys'] : 0) . ' / ' . (isset($gender['girls']) ? $gender['girls'] : 0); ?></span></div>
    <div class="pt-2 border-t border-outline-variant/50">
      <div class="text-label-md text-label-md text-on-surface-variant uppercase mb-2">Students by Class</div>
      <?php foreach (array_slice($class_dist, 0, 4) as $cd): ?>
        <?php $barW = ($total_students > 0) ? round(($cd->student_count / $total_students) * 100) : 50; ?>
        <div class="flex items-center gap-2 mb-1.5">
          <span class="w-20 text-[12px] text-on-surface-variant truncate"><?php echo html_escape($cd->class_name); ?></span>
          <div class="flex-1 h-2 rounded-full bg-surface-container-high overflow-hidden">
            <div class="h-full bg-primary-container" style="width:<?php echo max(15, min(100, $barW * 3)); ?>%"></div>
          </div>
          <span class="text-[12px] text-on-surface-variant"><?php echo $cd->student_count; ?></span>
        </div>
      <?php endforeach; ?>
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
    <div><div class="text-body-md font-body-md text-on-surface-variant">Today's Collection</div><div class="font-headline-md text-headline-md text-on-surface">₹ <?php echo number_format($fees['today_collection']); ?></div></div>
    <div><div class="text-body-md font-body-md text-on-surface-variant">Monthly Collection</div><div class="font-headline-md text-headline-md text-on-surface">₹ <?php echo number_format($fees['monthly_collection']); ?></div></div>
    <div><div class="text-body-md font-body-md text-on-surface-variant">Pending Fees</div><div class="font-headline-md text-headline-md text-on-tertiary-container">₹ <?php echo number_format($fees['pending_fees']); ?></div></div>
    <div><div class="text-body-md font-body-md text-on-surface-variant">Overdue Fees</div><div class="font-headline-md text-headline-md text-error">₹ <?php echo number_format($fees['overdue_fees']); ?></div></div>
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
    <?php foreach ($events as $ev): ?>
      <?php
        $eventDate = strtotime($ev->start_date);
        $dayNum = date('d', $eventDate);
        $monthStr = strtoupper(date('M', $eventDate));
      ?>
      <li class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex flex-col items-center justify-center text-[11px] font-semibold leading-none shrink-0">
          <span><?php echo $dayNum; ?></span>
          <span><?php echo $monthStr; ?></span>
        </div>
        <div class="min-w-0">
          <div class="text-body-md font-body-md text-on-surface truncate"><?php echo html_escape($ev->title); ?></div>
          <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($ev->description ?: 'School Event'); ?></div>
        </div>
      </li>
    <?php endforeach; ?>
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
    <?php foreach ($notices as $not): ?>
      <li class="py-3 first:pt-0 last:pb-0">
        <div class="flex items-start justify-between gap-2">
          <div>
            <div class="text-body-md font-body-md text-on-surface font-medium"><?php echo html_escape($not->title); ?></div>
            <div class="text-[12px] text-on-surface-variant mt-0.5"><?php echo html_escape($not->posted_by_name ?: 'Administration'); ?></div>
          </div>
          <span class="text-[11px] text-on-surface-variant whitespace-nowrap"><?php echo date('d M', strtotime($not->publish_date ?: $not->created_at)); ?></span>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
    </div>
  </div>

