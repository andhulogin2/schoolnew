<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Student Promotion Engine</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Batch promote students to next class / academic session while preserving full academic history.</p>
      </div>
    </div>

    <!-- Source Selection Card -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-5 mb-6">
      <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-[20px]">filter_alt</span>Step 1: Select Source Class & Section
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-label-md text-on-surface mb-1">Academic Year</label>
          <select id="src_year" onchange="loadSourceClass()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md">
            <?php foreach ($years as $yr): ?>
              <option value="<?php echo $yr->academic_year_id; ?>" <?php echo ($from_year == $yr->academic_year_id) ? 'selected' : ''; ?>><?php echo html_escape($yr->year_name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-label-md text-on-surface mb-1">Source Class</label>
          <select id="src_class" onchange="loadSourceClass()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md">
            <?php foreach ($classes as $cls): ?>
              <option value="<?php echo $cls->class_id; ?>" <?php echo ($from_class == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-label-md text-on-surface mb-1">Source Section</label>
          <select id="src_section" onchange="loadSourceClass()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md">
            <option value="">All Sections</option>
            <?php foreach ($sections as $sec): ?>
              <?php if ($sec->class_id == $from_class): ?>
                <option value="<?php echo $sec->section_id; ?>" <?php echo ($from_sec == $sec->section_id) ? 'selected' : ''; ?>><?php echo html_escape($sec->section_name); ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <!-- Target & Promotion Form -->
    <?php echo form_open('students/promotion', array('id' => 'promotion-form')); ?>
      <input type="hidden" name="from_academic_year_id" value="<?php echo $from_year; ?>"/>
      <input type="hidden" name="from_class_id" value="<?php echo $from_class; ?>"/>
      <input type="hidden" name="from_section_id" value="<?php echo $from_sec ?: 1; ?>"/>

      <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-5 mb-6">
        <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
          <span class="material-symbols-outlined text-secondary text-[20px]">upgrade</span>Step 2: Select Target Class & Action
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
          <div>
            <label class="block text-label-md text-on-surface mb-1">Target Academic Year *</label>
            <select name="to_academic_year_id" required class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md">
              <?php foreach ($years as $yr): ?>
                <option value="<?php echo $yr->academic_year_id; ?>"><?php echo html_escape($yr->year_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md text-on-surface mb-1">Target Class *</label>
            <select name="to_class_id" required class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md">
              <?php foreach ($classes as $cls): ?>
                <option value="<?php echo $cls->class_id; ?>"><?php echo html_escape($cls->class_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md text-on-surface mb-1">Target Section *</label>
            <select name="to_section_id" required class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md">
              <?php foreach ($sections as $sec): ?>
                <option value="<?php echo $sec->section_id; ?>"><?php echo html_escape($sec->class_name . ' - ' . $sec->section_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md text-on-surface mb-1">Promotion Action</label>
            <select name="promotion_type" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md">
              <option value="Promoted">Promoted</option>
              <option value="Retained">Retained in same class</option>
              <option value="Transferred">Transferred</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Students Checklist Table -->
      <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">checklist</span>Step 3: Select Students to Promote (<?php echo count($students); ?> found)
          </h3>
          <div class="flex items-center gap-2">
            <button type="button" onclick="toggleSelectAll(true)" class="text-label-md text-primary hover:underline">Select All</button>
            <span class="text-outline-variant">·</span>
            <button type="button" onclick="toggleSelectAll(false)" class="text-label-md text-primary hover:underline">Deselect All</button>
          </div>
        </div>
        <div class="table-scroll overflow-x-auto">
          <table class="w-full data-table zebra border-collapse">
            <thead>
              <tr class="border-b border-outline-variant/60">
                <th class="w-12 px-4 py-3 text-center"><input type="checkbox" id="chk-master" onclick="toggleSelectAll(this.checked)" class="rounded text-primary"/></th>
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide">Admission No.</th>
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide">Student Name</th>
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide">Current Class & Section</th>
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide">Gender</th>
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase tracking-wide">Guardian</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40">
              <?php if (empty($students)): ?>
                <tr>
                  <td colspan="6" class="px-4 py-8 text-center text-body-md text-on-surface-variant">No active students in selected class/section.</td>
                </tr>
              <?php endif; ?>
              <?php foreach ($students as $st): ?>
                <tr>
                  <td class="px-4 py-3 text-center">
                    <input type="checkbox" name="student_ids[]" value="<?php echo $st->student_id; ?>" checked class="student-chk rounded text-primary"/>
                  </td>
                  <td class="px-4 py-3 text-body-md font-mono text-primary font-medium"><?php echo html_escape($st->admission_number); ?></td>
                  <td class="px-4 py-3 text-body-md font-medium text-on-surface"><?php echo html_escape($st->first_name . ' ' . $st->last_name); ?></td>
                  <td class="px-4 py-3 text-body-md text-on-surface"><?php echo html_escape($st->class_name . ' ' . $st->section_name); ?></td>
                  <td class="px-4 py-3 text-body-md text-on-surface"><?php echo html_escape($st->gender); ?></td>
                  <td class="px-4 py-3 text-body-md text-on-surface-variant"><?php echo html_escape($st->guardian_name); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php if (!empty($students)): ?>
          <div class="px-5 py-4 bg-surface-container-low border-t border-outline-variant/50 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">verified</span>Execute Batch Promotion
            </button>
          </div>
        <?php endif; ?>
      </div>
    <?php echo form_close(); ?>

    <!-- Recent Promotions History -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-5">
      <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-[20px]">history</span>Recent Promotion Logs
      </h3>
      <div class="table-scroll overflow-x-auto border border-outline-variant/40 rounded-lg">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low">
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">Student</th>
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">From Class</th>
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">Promoted To</th>
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">Type</th>
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($promotions_history)): ?>
              <tr><td colspan="5" class="px-4 py-4 text-center text-body-md text-on-surface-variant">No promotion history logs.</td></tr>
            <?php else: ?>
              <?php foreach (array_slice($promotions_history, 0, 8) as $ph): ?>
                <tr>
                  <td class="px-4 py-2.5 text-body-md text-on-surface font-medium"><?php echo html_escape($ph->first_name . ' ' . $ph->last_name . ' (' . $ph->admission_number . ')'); ?></td>
                  <td class="px-4 py-2.5 text-body-md text-on-surface-variant"><?php echo html_escape($ph->from_class . ' ' . $ph->from_section . ' [' . $ph->from_year . ']'); ?></td>
                  <td class="px-4 py-2.5 text-body-md text-on-surface font-semibold"><?php echo html_escape($ph->to_class . ' ' . $ph->to_section . ' [' . $ph->to_year . ']'); ?></td>
                  <td class="px-4 py-2.5 text-body-md"><span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-secondary-container text-on-secondary-container"><?php echo html_escape($ph->promotion_type); ?></span></td>
                  <td class="px-4 py-2.5 text-body-md text-on-surface-variant"><?php echo date('d M Y', strtotime($ph->promotion_date)); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <script>
      function loadSourceClass() {
        var yr = document.getElementById('src_year').value;
        var cls = document.getElementById('src_class').value;
        var sec = document.getElementById('src_section').value;
        window.location.href = '<?php echo site_url('students/promotion'); ?>?from_year=' + yr + '&from_class=' + cls + '&from_section=' + sec;
      }
      function toggleSelectAll(checked) {
        document.querySelectorAll('.student-chk').forEach(function(chk) { chk.checked = checked; });
        var master = document.getElementById('chk-master');
        if (master) master.checked = checked;
      }
    </script>
