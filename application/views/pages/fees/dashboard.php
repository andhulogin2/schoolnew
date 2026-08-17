<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Dashboard</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Overview of collections, pending and overdue fees.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0"><a href="<?php echo site_url('reports'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">chevron_right</span>View Reports</a></div>
    </div>
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">payments</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+8%</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">₹ <?php echo number_format($metrics['today_collection']); ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Today's Collection</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">account_balance_wallet</span></div>
        <span class="text-[12px] font-semibold text-on-secondary-container">+12%</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">₹ <?php echo number_format($metrics['monthly_collection']); ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Monthly Collection</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-tertiary-container/10 text-on-tertiary-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">hourglass_top</span></div>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">₹ <?php echo number_format($metrics['pending_fees']); ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Pending Fees</div>
    </div>
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-error-container text-on-error-container flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">error</span></div>
        <span class="text-[12px] font-semibold text-error">-4%</span>
      </div>
      <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">₹ <?php echo number_format($metrics['overdue_fees']); ?></div>
      <div class="text-body-md font-body-md text-on-surface-variant">Overdue Fees</div>
    </div></div>
  
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Recent Fee Activity</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead><tr class="border-b border-outline-variant/60"><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Student</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Fee Head</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Amount</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Status</th></tr></thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php foreach ($recent_activity as $act): ?>
              <?php
                $nameParts = explode(' ', trim($act->first_name . ' ' . $act->last_name));
                $initials = '';
                foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
                if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                $statusBadge = ($act->payment_status == 'Paid')
                  ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Paid</span>'
                  : (($act->payment_status == 'Overdue')
                    ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-error-container text-on-error-container">Overdue</span>'
                    : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-tertiary-container/10 text-on-tertiary-container">Pending</span>');
              ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center text-[11px] font-semibold shrink-0"><?php echo html_escape($initials); ?></div>
                    <div>
                      <div class="font-medium text-on-surface"><?php echo html_escape($act->first_name . ' ' . $act->last_name); ?></div>
                      <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($act->class_name . ' ' . $act->section_name); ?></div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($act->head_name ?: 'Term Fees'); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap">₹ <?php echo number_format($act->amount); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo $statusBadge; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between px-4 py-3 border-t border-outline-variant/50 text-body-md font-body-md text-on-surface-variant">
        <span>Showing <?php echo count($recent_activity); ?> of <?php echo count($recent_activity); ?> records</span>
        <div class="flex items-center gap-1">
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Previous</button>
          <button class="px-3 py-1.5 rounded-lg bg-primary-fixed text-primary font-medium">1</button>
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Next</button>
        </div>
      </div>
    </div></div>
    </div>

