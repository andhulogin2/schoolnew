<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">All Staff</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1"><?php echo count($staff); ?> faculty and administrative staff members registered.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0 flex-wrap">
        <a href="<?php echo site_url('staff/register'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">person_add</span>Add Staff</a>
        <a href="<?php echo site_url('staff/attendance'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">fact_check</span>Attendance</a>
        <a href="<?php echo site_url('staff/leave'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">event_busy</span>Leave Requests</a>
      </div>
    </div>

    <!-- Filters Bar -->
    <div class="flex flex-col lg:flex-row gap-3 mb-4 flex-wrap">
      <div class="relative flex-1 min-w-[220px]">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/60 text-[20px]">search</span>
        <input type="text" placeholder="Search staff name, employee ID, phone, email..." value="<?php echo html_escape($this->input->get('search')); ?>" onkeydown="if(event.key==='Enter') window.location.href='<?php echo site_url('staff'); ?>?search=' + encodeURIComponent(this.value)" class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary transition-colors"/>
      </div>
      <select onchange="applyFilter('staff_type', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Staff Types</option>
        <option value="teacher" <?php echo ($this->input->get('staff_type') === 'teacher') ? 'selected' : ''; ?>>Teaching Faculty</option>
        <option value="non_teaching" <?php echo ($this->input->get('staff_type') === 'non_teaching') ? 'selected' : ''; ?>>Non-Teaching Staff</option>
      </select>
      <select onchange="applyFilter('department_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Departments</option>
        <?php foreach ($departments as $dept): ?>
          <option value="<?php echo $dept->department_id; ?>" <?php echo ($this->input->get('department_id') == $dept->department_id) ? 'selected' : ''; ?>><?php echo html_escape($dept->department_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="applyFilter('designation_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Designations</option>
        <?php foreach ($designations as $desig): ?>
          <option value="<?php echo $desig->designation_id; ?>" <?php echo ($this->input->get('designation_id') == $desig->designation_id) ? 'selected' : ''; ?>><?php echo html_escape($desig->designation_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="applyFilter('status', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Statuses</option>
        <option value="1" <?php echo ($this->input->get('status') === '1' || $this->input->get('status') === NULL) ? 'selected' : ''; ?>>Active</option>
        <option value="0" <?php echo ($this->input->get('status') === '0') ? 'selected' : ''; ?>>Inactive / Resigned</option>
      </select>
      <a href="<?php echo site_url('staff'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset</a>
    </div>

    <!-- Staff Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Employee ID</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Staff Member</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Staff Type</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Department</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Designation</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Contact</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Joining Date</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Status</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($staff)): ?>
              <tr>
                <td colspan="9" class="px-4 py-8 text-center text-body-md text-on-surface-variant">No staff members found matching current filters.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($staff as $st): ?>
              <?php
                $nameParts = explode(' ', trim($st->full_name));
                $initials = '';
                foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
                if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                $isTeacher = ($st->staff_type === 'teacher');
                $typeBadge = $isTeacher
                  ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-primary-fixed/30 text-primary">Teacher</span>'
                  : '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">Non-Teaching</span>';
                $statusBadge = ($st->status == 1)
                  ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Active</span>'
                  : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">Inactive</span>';
              ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 text-body-md font-mono text-primary font-medium whitespace-nowrap">
                  <a href="<?php echo site_url('staff/profile/' . $st->staff_id); ?>" class="hover:underline"><?php echo html_escape($st->employee_code); ?></a>
                </td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center text-[11px] font-semibold shrink-0"><?php echo html_escape($initials); ?></div>
                    <div>
                      <div class="font-medium text-on-surface"><?php echo html_escape($st->full_name); ?></div>
                      <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($st->gender . ($st->qualification ? ' · ' . $st->qualification : '')); ?></div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-body-md whitespace-nowrap"><?php echo $typeBadge; ?></td>
                <td class="px-4 py-3 text-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($st->department_name ?: '—'); ?></td>
                <td class="px-4 py-3 text-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($st->designation_name ?: '—'); ?></td>
                <td class="px-4 py-3 text-body-md text-on-surface whitespace-nowrap">
                  <div><?php echo html_escape($st->phone); ?></div>
                  <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($st->email); ?></div>
                </td>
                <td class="px-4 py-3 text-body-md text-on-surface whitespace-nowrap"><?php echo date('d M Y', strtotime($st->joining_date)); ?></td>
                <td class="px-4 py-3 text-body-md whitespace-nowrap"><?php echo $statusBadge; ?></td>
                <td class="px-4 py-3 text-body-md text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="<?php echo site_url('staff/profile/' . $st->staff_id); ?>" title="View Profile" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px]">visibility</span></a>
                    <a href="<?php echo site_url('staff/edit/' . $st->staff_id); ?>" title="Edit Staff" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                    <button onclick="confirmDelete(<?php echo $st->staff_id; ?>, '<?php echo html_escape(addslashes($st->full_name)); ?>')" title="Deactivate Staff" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container/20 hover:text-error transition-colors cursor-pointer"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between px-4 py-3 border-t border-outline-variant/50 text-body-md font-body-md text-on-surface-variant">
        <span>Showing <?php echo count($staff); ?> staff records</span>
        <div class="flex items-center gap-3">
          <a href="<?php echo site_url('staff/teachers'); ?>" class="text-label-md text-primary hover:underline">View Teachers</a>
          <span class="text-outline-variant">·</span>
          <a href="<?php echo site_url('staff/non_teaching'); ?>" class="text-label-md text-primary hover:underline">View Non-Teaching Staff</a>
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
        if (confirm('Are you sure you want to deactivate staff member "' + name + '"?\n\nThis will safely update status to inactive without breaking historical attendance, workload, or leave logs.')) {
          window.location.href = '<?php echo site_url('staff/delete/'); ?>' + id;
        }
      }
    </script>
