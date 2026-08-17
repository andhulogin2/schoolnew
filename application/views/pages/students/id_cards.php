<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Student ID Cards</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Generate and print printable ID cards for students.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0"><a href="#" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">print</span>Bulk Generate</a></div>
    </div>
  
    <div class="flex flex-col md:flex-row gap-3 mb-4">
      <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/60 text-[20px]">search</span>
        <input type="text" placeholder="Search..." class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary transition-colors"/>
      </div>
      <select onchange="window.location.href='<?php echo site_url('students/id_cards'); ?>?class_id=' + this.value" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($this->input->get('class_id') == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
      <button class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">filter_list</span>Filters</button>
    </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach ($students as $st): ?>
      <?php
        $nameParts = explode(' ', trim($st->first_name . ' ' . $st->last_name));
        $initials = '';
        foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
        if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
        $classDisplay = trim(($st->class_name ?: '') . ' ' . ($st->section_name ?: ''));
      ?>
      <div class="elevation-1 rounded-xl overflow-hidden border border-outline-variant/50 bg-surface-container-lowest">
        <div class="h-1.5 w-full bg-gradient-to-r from-secondary to-primary"></div>
        <div class="p-4 flex items-center gap-3">
          <div class="w-14 h-14 rounded-lg bg-primary-fixed text-primary flex items-center justify-center font-semibold"><?php echo html_escape($initials); ?></div>
          <div class="min-w-0">
            <div class="font-headline-md text-headline-md text-on-surface truncate" style="font-size:16px"><?php echo html_escape($st->first_name . ' ' . $st->last_name); ?></div>
            <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($st->admission_number . ' · ' . $classDisplay); ?></div>
          </div>
        </div>
        <div class="flex border-t border-outline-variant/50">
          <a href="<?php echo site_url('students/profile/' . $st->student_id); ?>" class="flex-1 py-2.5 text-label-md text-label-md text-on-surface-variant hover:bg-surface-container-high flex items-center justify-center gap-1.5"><span class="material-symbols-outlined text-[16px]">visibility</span>Preview</a>
          <div class="w-px bg-outline-variant/50"></div>
          <button onclick="window.print()" class="flex-1 py-2.5 text-label-md text-label-md text-secondary hover:bg-surface-container-high flex items-center justify-center gap-1.5"><span class="material-symbols-outlined text-[16px]">print</span>Print</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

