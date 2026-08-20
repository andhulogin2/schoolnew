<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Internal Staff Messaging</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Inter-departmental messaging for teachers, administrative staff, accountants, and school leadership.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('communication/conversations'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">forum</span>Open Chat Console
        </a>
      </div>
    </div>

    <!-- Staff Conversations Directory -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Active Discussions (<?php echo count($conversations); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Topic</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Type</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Latest Message</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Time</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($conversations)): ?>
              <tr><td colspan="5" class="px-4 py-8 text-center text-on-surface-variant">No active internal staff conversations found.</td></tr>
            <?php else: ?>
              <?php foreach ($conversations as $c): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 whitespace-nowrap">
                    <strong class="text-on-surface block"><?php echo html_escape($c->title ?: 'Internal Chat'); ?></strong>
                    <span class="text-[11px] text-on-surface-variant">By: <?php echo html_escape($c->creator_name ?: 'Staff'); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">
                      <?php echo html_escape($c->conversation_type); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-[13px] text-on-surface-variant line-clamp-1">
                    <?php echo html_escape($c->last_message ?: 'No messages yet.'); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-mono text-[12px] text-on-surface-variant">
                    <?php echo date('d M Y, h:i A', strtotime($c->last_message_time ?: $c->created_at)); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <a href="<?php echo site_url('communication/conversation_view/' . $c->conversation_id); ?>" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-primary-container text-on-primary-container text-label-md font-semibold hover:bg-primary/20">
                      <span class="material-symbols-outlined text-[16px]">chat</span>View
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
