<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Communication Delivery Reports</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Multi-channel delivery analytics, success rates, failed delivery tracking, and exportable audit logs.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('communication/reports?export=csv'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">download</span>Export CSV
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">print</span>Print Report
        </button>
      </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-1">
        <span class="text-xs font-semibold text-on-surface-variant uppercase">Total Dispatches</span>
        <div class="text-title-lg font-bold text-on-surface"><?php echo $stats->total_sent; ?></div>
      </div>
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-1">
        <span class="text-xs font-semibold text-on-surface-variant uppercase">Delivered</span>
        <div class="text-title-lg font-bold text-secondary"><?php echo $stats->delivered; ?></div>
      </div>
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-1">
        <span class="text-xs font-semibold text-on-surface-variant uppercase">Delivery Rate</span>
        <div class="text-title-lg font-bold text-primary"><?php echo $stats->delivery_pct; ?>%</div>
      </div>
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-1">
        <span class="text-xs font-semibold text-on-surface-variant uppercase">Failed Alerts</span>
        <div class="text-title-lg font-bold text-error"><?php echo $stats->failed; ?></div>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('communication/reports'); ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Channel</label>
          <select name="channel" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Channels</option>
            <option value="SMS" <?php echo (($filters['channel'] ?? '') === 'SMS') ? 'selected' : ''; ?>>SMS</option>
            <option value="WhatsApp" <?php echo (($filters['channel'] ?? '') === 'WhatsApp') ? 'selected' : ''; ?>>WhatsApp</option>
            <option value="Email" <?php echo (($filters['channel'] ?? '') === 'Email') ? 'selected' : ''; ?>>Email</option>
            <option value="In-App" <?php echo (($filters['channel'] ?? '') === 'In-App') ? 'selected' : ''; ?>>In-App</option>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">From Date</label>
          <input type="date" name="date_from" value="<?php echo html_escape($filters['date_from'] ?? ''); ?>" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">To Date</label>
          <input type="date" name="date_to" value="<?php echo html_escape($filters['date_to'] ?? ''); ?>" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>
      </form>
    </div>

    <!-- Report Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Detailed Dispatches Log (<?php echo count($messages); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">ID</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Channel</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Recipient</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Contact</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Message Extract</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Status</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Dispatched</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($messages)): ?>
              <tr><td colspan="7" class="px-4 py-8 text-center text-on-surface-variant">No records available for this period.</td></tr>
            <?php else: ?>
              <?php foreach ($messages as $m): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 whitespace-nowrap font-mono text-[12px] text-on-surface-variant">#<?php echo $m->message_id; ?></td>
                  <td class="px-4 py-3 whitespace-nowrap font-semibold text-on-surface"><?php echo html_escape($m->channel); ?></td>
                  <td class="px-4 py-3 whitespace-nowrap font-medium text-on-surface"><?php echo html_escape($m->recipient_name ?: 'Target Group'); ?></td>
                  <td class="px-4 py-3 whitespace-nowrap font-mono text-[12px] text-on-surface-variant"><?php echo html_escape($m->recipient_contact); ?></td>
                  <td class="px-4 py-3 text-[12px] text-on-surface-variant"><?php echo html_escape(substr(strip_tags($m->message), 0, 80)); ?>...</td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-secondary-container text-on-secondary-container">
                      <?php echo html_escape($m->status); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-mono text-[12px] text-on-surface-variant"><?php echo date('d M Y, h:i A', strtotime($m->created_at)); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
