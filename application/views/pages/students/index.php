<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">All Students</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1"><?php echo count($students); ?> students found across all active sections.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0 flex-wrap">
        <a href="<?php echo site_url('students/register'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">person_add</span>Student Registration</a>
        <a href="<?php echo site_url('students/promotion'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">upgrade</span>Promotion</a>
        <a href="<?php echo site_url('students/transfers'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">move_up</span>Transfer / TC</a>
      </div>
    </div>
  
    <div class="flex flex-col lg:flex-row gap-3 mb-4 flex-wrap">
      <div class="relative flex-1 min-w-[220px]">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/60 text-[20px]">search</span>
        <input type="text" placeholder="Search by name, admission no, roll no, parent..." value="<?php echo html_escape($this->input->get('search')); ?>" onkeydown="if(event.key==='Enter') window.location.href='<?php echo site_url('students'); ?>?search=' + encodeURIComponent(this.value)" class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary transition-colors"/>
      </div>
      <select onchange="applyFilter('academic_year_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Academic Years</option>
        <?php foreach ($years as $yr): ?>
          <option value="<?php echo $yr->academic_year_id; ?>" <?php echo ($this->input->get('academic_year_id') == $yr->academic_year_id) ? 'selected' : ''; ?>><?php echo html_escape($yr->year_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="applyFilter('class_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($this->input->get('class_id') == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="applyFilter('section_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Sections</option>
        <?php foreach ($sections as $sec): ?>
          <option value="<?php echo $sec->section_id; ?>" <?php echo ($this->input->get('section_id') == $sec->section_id) ? 'selected' : ''; ?>><?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="applyFilter('gender', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Genders</option>
        <option value="Male" <?php echo ($this->input->get('gender') === 'Male') ? 'selected' : ''; ?>>Male</option>
        <option value="Female" <?php echo ($this->input->get('gender') === 'Female') ? 'selected' : ''; ?>>Female</option>
      </select>
      <select onchange="applyFilter('status', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Status</option>
        <option value="1" <?php echo ($this->input->get('status') === '1') ? 'selected' : ''; ?>>Active</option>
        <option value="0" <?php echo ($this->input->get('status') === '0') ? 'selected' : ''; ?>>Inactive / Transferred</option>
      </select>
      <a href="<?php echo site_url('students'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset</a>
    </div>
  
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Admission No.</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Student</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Class & Section</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Gender</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Date of Birth</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Guardian</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Phone</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Status</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($students)): ?>
              <tr>
                <td colspan="9" class="px-4 py-8 text-center text-body-md text-on-surface-variant">No students found matching current criteria.</td>
              </tr>
            <?php endif; ?>
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
                  <?php if (!empty($st->roll_number)): ?>
                    <span class="text-[11px] text-on-surface-variant ml-1 font-mono">#<?php echo html_escape($st->roll_number); ?></span>
                  <?php endif; ?>
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
                <td class="px-4 py-3 text-body-md font-body-md text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="<?php echo site_url('students/profile/' . $st->student_id); ?>" title="View Profile" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px]">visibility</span></a>
                    <a href="<?php echo site_url('students/edit/' . $st->student_id); ?>" title="Edit Student" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                    <button onclick="confirmDelete(<?php echo $st->student_id; ?>, '<?php echo html_escape(addslashes($st->first_name . ' ' . $st->last_name)); ?>')" title="Delete Student" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container/20 hover:text-error transition-colors cursor-pointer"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between px-4 py-3 border-t border-outline-variant/50 text-body-md font-body-md text-on-surface-variant">
        <span>Showing <?php echo count($students); ?> records</span>
        <div class="flex items-center gap-1">
          <a href="<?php echo site_url('students/id_cards'); ?>" class="inline-flex items-center gap-1 text-label-md text-primary hover:underline"><span class="material-symbols-outlined text-[16px]">badge</span>Print ID Cards</a>
        </div>
      </div>
    </div>

    <script>
      function applyFilter(key, val) {
        var url = new URL(window.location.href);
        if (val) { url.searchParams.set(key, val); } else { url.searchParams.delete(key); }
        window.location.href = url.toString();
      }
      function confirmDelete(id, name) {
        if (confirm('Are you sure you want to deactivate student "' + name + '"?\n\nThis will safely update their status to inactive without deleting historical data.')) {
          window.location.href = '<?php echo site_url('students/delete/'); ?>' + id;
        }
      }
    </script>
