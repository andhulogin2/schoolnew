<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Designations</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Manage job titles used across staff profiles.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0"><a href="#" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">add</span>Add Designation</a></div>
    </div>
  
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead><tr class="border-b border-outline-variant/60"><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Designation</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Category</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Staff Count</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Status</th></tr></thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php foreach ($designations as $ds): ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap font-medium"><?php echo html_escape($ds->designation_name); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($ds->category); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($ds->staff_count); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Active</span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between px-4 py-3 border-t border-outline-variant/50 text-body-md font-body-md text-on-surface-variant">
        <span>Showing <?php echo count($designations); ?> of <?php echo count($designations); ?> records</span>
        <div class="flex items-center gap-1">
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Previous</button>
          <button class="px-3 py-1.5 rounded-lg bg-primary-fixed text-primary font-medium">1</button>
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Next</button>
        </div>
      </div>
    </div>

