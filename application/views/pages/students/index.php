<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">All Students</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1"><?php echo count($students); ?> students found.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0"><a href="<?php echo site_url('students/add'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">person_add</span>Add Student</a><a href="#" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">download</span>Export</a></div>
    </div>
  
    <div class="flex flex-col md:flex-row gap-3 mb-4">
      <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/60 text-[20px]">search</span>
        <input type="text" placeholder="Search..." value="<?php echo html_escape($this->input->get('search')); ?>" onkeydown="if(event.key==='Enter') window.location.href='<?php echo site_url('students'); ?>?search=' + encodeURIComponent(this.value)" class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary transition-colors"/>
      </div>
      <select onchange="window.location.href='<?php echo site_url('students'); ?>?class_id=' + this.value" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($this->input->get('class_id') == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="window.location.href='<?php echo site_url('students'); ?>?section_id=' + this.value" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Sections</option>
        <?php foreach ($sections as $sec): ?>
          <option value="<?php echo $sec->section_id; ?>" <?php echo ($this->input->get('section_id') == $sec->section_id) ? 'selected' : ''; ?>><?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="window.location.href='<?php echo site_url('students'); ?>?status=' + this.value" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Status</option>
        <option value="1" <?php echo ($this->input->get('status') === '1') ? 'selected' : ''; ?>>Active</option>
        <option value="0" <?php echo ($this->input->get('status') === '0') ? 'selected' : ''; ?>>Inactive</option>
      </select>
      <button class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">filter_list</span>Filters</button>
    </div>
  
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead><tr class="border-b border-outline-variant/60"><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Admission No.</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Student</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Class</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Gender</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Date of Birth</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Guardian</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Phone</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Status</th></tr></thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php foreach ($students as $st): ?>
              <?php
                $nameParts = explode(' ', trim($st->first_name . ' ' . $st->last_name));
                $initials = '';
                foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
                if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                $statusBadge = ($st->status == 1)
                  ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Active</span>'
                  : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">Inactive</span>';
                $classDisplay = trim(($st->class_name ?: '') . ' ' . ($st->section_name ?: ''));
              ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap">
                  <a href="<?php echo site_url('students/profile/' . $st->student_id); ?>" class="text-primary font-medium hover:underline"><?php echo html_escape($st->admission_number); ?></a>
                </td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center text-[11px] font-semibold shrink-0"><?php echo html_escape($initials); ?></div>
                    <div>
                      <div class="font-medium text-on-surface"><?php echo html_escape($st->first_name . ' ' . $st->last_name); ?></div>
                      <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($classDisplay . ' · ' . $st->gender); ?></div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($classDisplay); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($st->gender); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo date('d M Y', strtotime($st->date_of_birth)); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($st->guardian_name); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($st->guardian_phone); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo $statusBadge; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between px-4 py-3 border-t border-outline-variant/50 text-body-md font-body-md text-on-surface-variant">
        <span>Showing <?php echo count($students); ?> of <?php echo count($students); ?> records</span>
        <div class="flex items-center gap-1">
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Previous</button>
          <button class="px-3 py-1.5 rounded-lg bg-primary-fixed text-primary font-medium">1</button>
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Next</button>
        </div>
      </div>
    </div>

