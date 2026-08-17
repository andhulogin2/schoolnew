<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Subjects</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Assign subjects and subject teachers to each class.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0"><a href="#" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">add</span>Add Subject</a></div>
    </div>
  
    <div class="flex flex-col md:flex-row gap-3 mb-4">
      <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/60 text-[20px]">search</span>
        <input type="text" placeholder="Search..." class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary transition-colors"/>
      </div>
      <select onchange="window.location.href='<?php echo site_url('academics/subjects'); ?>?class_id=' + this.value" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($this->input->get('class_id') == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
      <button class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">filter_list</span>Filters</button>
    </div>
  
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead><tr class="border-b border-outline-variant/60"><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Subject</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Code</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Type</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Class</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Teacher</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Status</th></tr></thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php foreach ($subjects as $sub): ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap font-medium"><?php echo html_escape($sub->subject_name); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($sub->subject_code); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($sub->subject_type); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($sub->class_name ?: 'All Classes'); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($sub->teacher_name ?: 'Not Assigned'); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Active</span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between px-4 py-3 border-t border-outline-variant/50 text-body-md font-body-md text-on-surface-variant">
        <span>Showing <?php echo count($subjects); ?> of <?php echo count($subjects); ?> records</span>
        <div class="flex items-center gap-1">
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Previous</button>
          <button class="px-3 py-1.5 rounded-lg bg-primary-fixed text-primary font-medium">1</button>
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Next</button>
        </div>
      </div>
    </div>

