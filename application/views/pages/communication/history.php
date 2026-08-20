<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Notification History & Audit Log</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Full delivery audit trail across all communication channels (In-App, SMS, WhatsApp, and Email).</p>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('communication/history'); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Delivery Status</label>
          <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Statuses</option>
            <option value="Sent" <?php echo (($filters['status'] ?? '') === 'Sent') ? 'selected' : ''; ?>>Sent</option>
            <option value="Delivered" <?php echo (($filters['status'] ?? '') === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
            <option value="Scheduled" <?php echo (($filters['status'] ?? '') === 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
            <option value="Failed" <?php echo (($filters['status'] ?? '') === 'Failed') ? 'selected' : ''; ?>>Failed</option>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Search Recipient/Message</label>
          <input type="text" name="search" value="<?php echo html_escape($filters['search'] ?? ''); ?>" placeholder="Name, phone, keywords..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>

        <div class="flex items-end">
          <button type="submit" class="w-full py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant cursor-pointer">
            Filter History
          </button>
        </div>
      </form>
    </div>

    <!-- History Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Communication Records (<?php echo count($messages); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Channel</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Recipient</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Message Content</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Timestamp</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Delivery Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($messages)): ?>
              <tr><td colspan="5" class="px-4 py-8 text-center text-on-surface-variant">No communication history found matching filters.</td></tr>
            <?php else: ?>
              <?php foreach ($messages as $m): ?>
                <?php
                  $chBadge = ($m->channel === 'WhatsApp') ? 'bg-emerald-100 text-emerald-800' : (($m->channel === 'SMS') ? 'bg-amber-100 text-amber-800' : 'bg-primary-container text-on-primary-container');
                  $stBadge = ($m->status === 'Delivered' || $m->status === 'Sent') ? 'bg-secondary-container text-on-secondary-container' : (($m->status === 'Failed') ? 'bg-error-container text-error' : 'bg-amber-100 text-amber-900');
                ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold <?php echo $chBadge; ?>"><?php echo html_escape($m->channel); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap">
                    <strong class="text-on-surface block"><?php echo html_escape($m->recipient_name ?: $m->recipient_type); ?></strong>
                    <span class="text-[11px] text-on-surface-variant font-mono"><?php echo html_escape($m->recipient_contact); ?></span>
                  </td>
                  <td class="px-4 py-3 text-[13px] text-on-surface">
                    <?php if ($m->subject): ?>
                      <strong class="block text-primary text-[12px]"><?php echo html_escape($m->subject); ?></strong>
                    <?php endif; ?>
                    <span class="line-clamp-2"><?php echo html_escape(substr(strip_tags($m->message), 0, 140)); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-mono text-[12px] text-on-surface-variant">
                    <?php echo date('d M Y, h:i A', strtotime($m->created_at)); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold <?php echo $stBadge; ?>">
                      <?php echo html_escape($m->status); ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
