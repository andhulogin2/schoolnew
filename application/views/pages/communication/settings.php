<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
      <div class="mb-4 p-3.5 rounded-xl bg-secondary-container text-on-secondary-container text-body-md font-medium flex items-center gap-2 border border-secondary/20">
        <span class="material-symbols-outlined text-[20px] text-secondary">check_circle</span>
        <?php echo html_escape($this->session->flashdata('success')); ?>
      </div>
    <?php endif; ?>

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Communication Settings & Gateway Config</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Configure multi-channel communication gateways, automated background scheduling, retries, and audit trails.</p>
      </div>
    </div>

    <!-- Settings Form -->
    <?php echo form_open('communication/settings'); ?>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Section 1: Channel Toggles & Providers -->
        <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-4">
          <div class="flex items-center gap-2 pb-3 border-b border-outline-variant/50">
            <span class="material-symbols-outlined text-primary text-[24px]">tune</span>
            <h3 class="font-headline-md text-title-lg font-bold text-on-surface">Channel Enabling & Gateways</h3>
          </div>

          <div class="space-y-3">
            <label class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low border border-outline-variant/40 cursor-pointer">
              <div>
                <strong class="text-on-surface text-body-md block">In-App Notifications</strong>
                <span class="text-[12px] text-on-surface-variant">Deliver instant in-app alerts and notifications.</span>
              </div>
              <input type="checkbox" name="enable_inapp" value="1" <?php echo $settings->enable_inapp ? 'checked' : ''; ?> class="w-5 h-5 rounded text-secondary focus:ring-secondary"/>
            </label>

            <label class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low border border-outline-variant/40 cursor-pointer">
              <div>
                <strong class="text-on-surface text-body-md block">SMS Broadcasts</strong>
                <span class="text-[12px] text-on-surface-variant">Enable outgoing SMS via integrated telecom gateway.</span>
              </div>
              <input type="checkbox" name="enable_sms" value="1" <?php echo $settings->enable_sms ? 'checked' : ''; ?> class="w-5 h-5 rounded text-secondary focus:ring-secondary"/>
            </label>

            <label class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low border border-outline-variant/40 cursor-pointer">
              <div>
                <strong class="text-on-surface text-body-md block">WhatsApp Notifications</strong>
                <span class="text-[12px] text-on-surface-variant">Enable WhatsApp Business API messaging.</span>
              </div>
              <input type="checkbox" name="enable_whatsapp" value="1" <?php echo $settings->enable_whatsapp ? 'checked' : ''; ?> class="w-5 h-5 rounded text-secondary focus:ring-secondary"/>
            </label>

            <label class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low border border-outline-variant/40 cursor-pointer">
              <div>
                <strong class="text-on-surface text-body-md block">Email Newsletters</strong>
                <span class="text-[12px] text-on-surface-variant">Dispatch transactional emails and circulars.</span>
              </div>
              <input type="checkbox" name="enable_email" value="1" <?php echo $settings->enable_email ? 'checked' : ''; ?> class="w-5 h-5 rounded text-secondary focus:ring-secondary"/>
            </label>
          </div>

          <div class="grid grid-cols-2 gap-4 pt-2">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">SMS Sender ID</label>
              <input type="text" name="sms_sender_id" value="<?php echo html_escape($settings->sms_sender_id); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">SMS Provider</label>
              <input type="text" name="sms_provider" value="<?php echo html_escape($settings->sms_provider); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>
        </div>

        <!-- Section 2: Email, Retries & Policies -->
        <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-4">
          <div class="flex items-center gap-2 pb-3 border-b border-outline-variant/50">
            <span class="material-symbols-outlined text-secondary text-[24px]">mail</span>
            <h3 class="font-headline-md text-title-lg font-bold text-on-surface">Email Sender & Dispatch Policies</h3>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Email From Name</label>
              <input type="text" name="email_from_name" value="<?php echo html_escape($settings->email_from_name); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Email From Address</label>
              <input type="email" name="email_from_address" value="<?php echo html_escape($settings->email_from_address); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Max Dispatch Retries</label>
              <input type="number" min="1" max="10" name="max_retries" value="<?php echo (int)$settings->max_retries; ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Retry Interval (Minutes)</label>
              <input type="number" min="5" max="120" name="retry_interval_minutes" value="<?php echo (int)$settings->retry_interval_minutes; ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <label class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low border border-outline-variant/40 cursor-pointer">
            <div>
              <strong class="text-on-surface text-body-md block">Parent-Teacher Direct Chat</strong>
              <span class="text-[12px] text-on-surface-variant">Allow direct messaging between assigned teachers and parents.</span>
            </div>
            <input type="checkbox" name="parent_teacher_direct_messaging" value="1" <?php echo $settings->parent_teacher_direct_messaging ? 'checked' : ''; ?> class="w-5 h-5 rounded text-secondary focus:ring-secondary"/>
          </label>
        </div>
      </div>

      <div class="flex items-center justify-end p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 mb-6">
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">save</span>Save Settings
        </button>
      </div>
    <?php echo form_close(); ?>
