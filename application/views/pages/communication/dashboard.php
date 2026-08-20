<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
      <div class="mb-4 p-3.5 rounded-xl bg-secondary-container text-on-secondary-container text-body-md font-medium flex items-center gap-2 border border-secondary/20">
        <span class="material-symbols-outlined text-[20px] text-secondary">check_circle</span>
        <?php echo html_escape($this->session->flashdata('success')); ?>
      </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
      <div class="mb-4 p-3.5 rounded-xl bg-error-container text-on-error-container text-body-md font-medium flex items-center gap-2 border border-error/20">
        <span class="material-symbols-outlined text-[20px] text-error">error</span>
        <?php echo html_escape($this->session->flashdata('error')); ?>
      </div>
    <?php endif; ?>

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Communication Hub</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Centralized institutional messaging, circulars, SMS broadcasts, WhatsApp alerts, and parent-teacher communication.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('communication/create_notice'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">post_add</span>Create Notice
        </a>
        <a href="<?php echo site_url('communication/sms'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">send</span>Broadcast Message
        </a>
      </div>
    </div>

    <!-- STATS CARDS GRID -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <!-- Notices & Announcements -->
      <div class="p-5 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-2">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="text-label-md font-semibold uppercase tracking-wider text-xs">Active Circulars</span>
          <span class="material-symbols-outlined text-primary text-[22px]">campaign</span>
        </div>
        <div class="text-headline-md font-bold text-on-surface"><?php echo $stats->total_notices + $stats->total_announcements; ?></div>
        <div class="text-xs text-on-surface-variant flex items-center gap-1">
          <span class="font-semibold text-primary"><?php echo $stats->total_notices; ?></span> Notices • <span class="font-semibold text-secondary"><?php echo $stats->total_announcements; ?></span> Announcements
        </div>
      </div>

      <!-- Messages Sent -->
      <div class="p-5 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-2">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="text-label-md font-semibold uppercase tracking-wider text-xs">Total Dispatched</span>
          <span class="material-symbols-outlined text-secondary text-[22px]">mark_email_read</span>
        </div>
        <div class="text-headline-md font-bold text-secondary"><?php echo $stats->total_sent; ?></div>
        <div class="text-xs text-on-surface-variant flex items-center gap-1">
          <span>SMS: <strong><?php echo $stats->sms_sent; ?></strong></span> • <span>WA: <strong><?php echo $stats->whatsapp_sent; ?></strong></span> • <span>Email: <strong><?php echo $stats->email_sent; ?></strong></span>
        </div>
      </div>

      <!-- Delivery Rate -->
      <div class="p-5 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-2">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="text-label-md font-semibold uppercase tracking-wider text-xs">Delivery Rate</span>
          <span class="material-symbols-outlined text-primary text-[22px]">verified</span>
        </div>
        <div class="text-headline-md font-bold text-primary"><?php echo $stats->delivery_pct; ?>%</div>
        <div class="w-full bg-surface-container-high rounded-full h-1.5 overflow-hidden mt-1">
          <div class="bg-primary h-1.5 rounded-full" style="width: <?php echo min(100, $stats->delivery_pct); ?>%"></div>
        </div>
      </div>

      <!-- Scheduled / Pending Queue -->
      <div class="p-5 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-2">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span class="text-label-md font-semibold uppercase tracking-wider text-xs">Scheduled Queue</span>
          <span class="material-symbols-outlined text-amber-500 text-[22px]">schedule_send</span>
        </div>
        <div class="text-headline-md font-bold text-amber-600"><?php echo $stats->pending; ?></div>
        <div class="text-xs text-on-surface-variant flex items-center gap-1">
          <span class="font-semibold text-error"><?php echo $stats->failed; ?></span> failed deliveries
        </div>
      </div>
    </div>

    <!-- 2 Column Section: Recent Notices + Recent Dispatches -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      
      <!-- Recent Notices -->
      <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-title-md font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[22px]">article</span>Recent Notices & Circulars
          </h3>
          <a href="<?php echo site_url('communication/notices'); ?>" class="text-body-md text-primary hover:underline font-semibold text-xs">View All</a>
        </div>

        <div class="divide-y divide-outline-variant/40">
          <?php if (empty($recent_notices)): ?>
            <div class="py-6 text-center text-on-surface-variant text-body-md">No notices published yet.</div>
          <?php else: ?>
            <?php foreach ($recent_notices as $n): ?>
              <?php
                $prioColor = ($n->priority === 'Urgent') ? 'bg-error-container text-error font-bold' : (($n->priority === 'Important') ? 'bg-amber-100 text-amber-900 font-bold' : 'bg-surface-container-high text-on-surface-variant');
              ?>
              <div class="py-3 flex items-start justify-between gap-3">
                <div class="space-y-1">
                  <div class="flex items-center gap-2">
                    <strong class="text-body-md text-on-surface"><?php echo html_escape($n->title); ?></strong>
                    <span class="px-2 py-0.5 rounded text-[10px] <?php echo $prioColor; ?>"><?php echo html_escape($n->priority); ?></span>
                  </div>
                  <div class="text-[12px] text-on-surface-variant">
                    <span>Audience: <strong><?php echo html_escape($n->target_role ?: 'All'); ?></strong></span> • 
                    <span>Date: <?php echo date('d M Y', strtotime($n->publish_date)); ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Recent Message Dispatches -->
      <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-title-md font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary text-[22px]">send</span>Recent Outgoing Dispatches
          </h3>
          <a href="<?php echo site_url('communication/history'); ?>" class="text-body-md text-primary hover:underline font-semibold text-xs">View History</a>
        </div>

        <div class="divide-y divide-outline-variant/40">
          <?php if (empty($recent_messages)): ?>
            <div class="py-6 text-center text-on-surface-variant text-body-md">No communication dispatches recorded yet.</div>
          <?php else: ?>
            <?php foreach ($recent_messages as $m): ?>
              <?php
                $chBadge = ($m->channel === 'WhatsApp') ? 'bg-emerald-100 text-emerald-800' : (($m->channel === 'SMS') ? 'bg-amber-100 text-amber-800' : 'bg-primary-container text-on-primary-container');
              ?>
              <div class="py-3 flex items-start justify-between gap-3">
                <div class="space-y-1">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?php echo $chBadge; ?>"><?php echo html_escape($m->channel); ?></span>
                    <strong class="text-body-md text-on-surface"><?php echo html_escape($m->recipient_name ?: 'Recipients'); ?></strong>
                  </div>
                  <p class="text-[12px] text-on-surface-variant line-clamp-1"><?php echo html_escape(substr(strip_tags($m->message), 0, 80)); ?></p>
                </div>
                <div class="text-right shrink-0">
                  <span class="text-[11px] font-mono text-on-surface-variant"><?php echo date('d M, h:i A', strtotime($m->created_at)); ?></span>
                  <span class="block text-[11px] font-semibold text-secondary"><?php echo html_escape($m->status); ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
