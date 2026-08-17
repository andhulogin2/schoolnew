<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <?php if ($this->session->flashdata('success')): ?>
      <div class="mb-4 p-3 rounded-lg bg-secondary-container text-on-secondary-container text-body-md font-body-md">
        <?php echo html_escape($this->session->flashdata('success')); ?>
      </div>
    <?php endif; ?>

    <?php echo form_open('attendance', array('id' => 'attendance-form')); ?>
    <input type="hidden" name="date" value="<?php echo html_escape($date); ?>"/>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Daily Attendance</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Mark attendance for a class and section on a given date.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">check</span>Save Attendance
        </button>
      </div>
    </div>
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-5">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Class</label>
      <select onchange="window.location.href='<?php echo site_url('attendance'); ?>?date=<?php echo $date; ?>&class_id=' + this.value" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($class_id == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Section</label>
      <select onchange="window.location.href='<?php echo site_url('attendance'); ?>?date=<?php echo $date; ?>&class_id=<?php echo $class_id; ?>&section_id=' + this.value" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
        <option value="">All Sections</option>
        <?php foreach ($sections as $sec): ?>
          <option value="<?php echo $sec->section_id; ?>" <?php echo ($section_id == $sec->section_id) ? 'selected' : ''; ?>><?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Date</label>
      <input type="date" value="<?php echo html_escape($date); ?>" onchange="window.location.href='<?php echo site_url('attendance'); ?>?class_id=<?php echo $class_id; ?>&section_id=<?php echo $section_id; ?>&date=' + this.value" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
    </div>
  </div>
  
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead><tr class="border-b border-outline-variant/60"><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Student</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Mark Attendance</th></tr></thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php foreach ($students as $st): ?>
              <?php
                $nameParts = explode(' ', trim($st->first_name . ' ' . $st->last_name));
                $initials = '';
                foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
                if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                $status = $st->attendance_status ?: 'Present';
                $classDisplay = trim(($st->class_name ?: '') . ' ' . ($st->section_name ?: ''));
              ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center text-[11px] font-semibold shrink-0"><?php echo html_escape($initials); ?></div>
                    <div>
                      <div class="font-medium text-on-surface"><?php echo html_escape($st->first_name . ' ' . $st->last_name); ?></div>
                      <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($classDisplay); ?></div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap">
                  <div class="flex gap-2 items-center">
                    <label class="cursor-pointer">
                      <input type="radio" name="attendance[<?php echo $st->student_id; ?>]" value="Present" class="sr-only peer" <?php echo ($status === 'Present') ? 'checked' : ''; ?>>
                      <span class="w-9 h-9 rounded-lg text-[13px] font-semibold flex items-center justify-center border border-outline-variant text-on-surface-variant peer-checked:bg-secondary-container peer-checked:text-on-secondary-container peer-checked:border-secondary transition-colors">P</span>
                    </label>
                    <label class="cursor-pointer">
                      <input type="radio" name="attendance[<?php echo $st->student_id; ?>]" value="Absent" class="sr-only peer" <?php echo ($status === 'Absent') ? 'checked' : ''; ?>>
                      <span class="w-9 h-9 rounded-lg text-[13px] font-semibold flex items-center justify-center border border-outline-variant text-on-surface-variant peer-checked:bg-error-container peer-checked:text-on-error-container peer-checked:border-error transition-colors">A</span>
                    </label>
                    <label class="cursor-pointer">
                      <input type="radio" name="attendance[<?php echo $st->student_id; ?>]" value="Late" class="sr-only peer" <?php echo ($status === 'Late') ? 'checked' : ''; ?>>
                      <span class="w-9 h-9 rounded-lg text-[13px] font-semibold flex items-center justify-center border border-outline-variant text-on-surface-variant peer-checked:bg-tertiary-fixed peer-checked:text-on-tertiary-fixed peer-checked:border-amber-500 transition-colors">L</span>
                    </label>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between px-4 py-3 border-t border-outline-variant/50 text-body-md font-body-md text-on-surface-variant">
        <span>Showing <?php echo count($students); ?> of <?php echo count($students); ?> records</span>
        <div class="flex items-center gap-1">
          <button type="button" class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Previous</button>
          <button type="button" class="px-3 py-1.5 rounded-lg bg-primary-fixed text-primary font-medium">1</button>
          <button type="button" class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Next</button>
        </div>
      </div>
    </div>
    <?php echo form_close(); ?>

