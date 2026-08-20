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
        <h2 class="font-headline-md text-headline-md text-on-surface">Notification Templates</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Manage reusable message templates with dynamic placeholders for attendance alerts, fee reminders, homework, and exams.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <button onclick="document.getElementById('templateModal').showModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Create Template
        </button>
      </div>
    </div>

    <!-- Supported Variables Hint Banner -->
    <div class="p-4 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-primary text-[24px]">code</span>
        <div>
          <strong class="text-body-md text-on-surface block font-bold">Dynamic Variable Placeholders:</strong>
          <span class="text-xs text-on-surface-variant"><code>{student_name}</code>, <code>{parent_name}</code>, <code>{class}</code>, <code>{section}</code>, <code>{subject}</code>, <code>{amount}</code>, <code>{due_date}</code>, <code>{date}</code>, <code>{school_name}</code></span>
        </div>
      </div>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
      <?php if (empty($templates)): ?>
        <div class="col-span-3 p-8 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 text-center text-on-surface-variant">
          No templates configured yet. Click 'Create Template' above.
        </div>
      <?php else: ?>
        <?php foreach ($templates as $t): ?>
          <?php
            $chBadge = ($t->channel === 'WhatsApp') ? 'bg-emerald-100 text-emerald-800' : (($t->channel === 'SMS') ? 'bg-amber-100 text-amber-800' : 'bg-primary-container text-on-primary-container');
          ?>
          <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 flex flex-col justify-between space-y-4 hover:border-primary transition-all">
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold <?php echo $chBadge; ?>"><?php echo html_escape($t->channel); ?></span>
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-surface-container-high text-on-surface-variant"><?php echo html_escape($t->communication_type); ?></span>
              </div>
              <h3 class="font-headline-md text-title-md font-bold text-on-surface"><?php echo html_escape($t->template_name); ?></h3>
              <?php if ($t->subject): ?>
                <div class="text-xs font-semibold text-primary">Subject: <?php echo html_escape($t->subject); ?></div>
              <?php endif; ?>
              <p class="text-body-md text-on-surface-variant font-mono text-[12px] bg-surface-container-low p-3 rounded-lg line-clamp-3 leading-relaxed">
                <?php echo html_escape($t->message_template); ?>
              </p>
            </div>

            <div class="pt-3 border-t border-outline-variant/40 flex items-center justify-between">
              <span class="text-[11px] text-on-surface-variant">Vars: <?php echo html_escape($t->variables); ?></span>
              <div class="flex items-center gap-1">
                <?php echo form_open('communication/templates', ['class' => 'inline']); ?>
                  <input type="hidden" name="template_id" value="<?php echo $t->template_id; ?>"/>
                  <input type="hidden" name="action" value="delete"/>
                  <button type="submit" onclick="return confirm('Delete this template?');" class="p-1 rounded hover:bg-error-container text-error cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                  </button>
                <?php echo form_close(); ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Create Template Modal Dialog -->
    <dialog id="templateModal" class="p-0 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-3 w-full max-w-lg backdrop:bg-scrim/40">
      <div class="p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-title-md font-bold text-on-surface">Create Notification Template</h3>
          <button onclick="document.getElementById('templateModal').close()" class="p-1 rounded-full hover:bg-surface-container-high text-on-surface-variant">
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <?php echo form_open('communication/templates'); ?>
          <div class="space-y-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Template Name *</label>
              <input type="text" name="template_name" required placeholder="e.g. Student Absence Reminder" class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Channel *</label>
                <select name="channel" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                  <option value="SMS">SMS</option>
                  <option value="WhatsApp">WhatsApp</option>
                  <option value="Email">Email</option>
                  <option value="In-App">In-App</option>
                </select>
              </div>

              <div>
                <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Type *</label>
                <select name="communication_type" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                  <option value="General">General</option>
                  <option value="Attendance">Attendance</option>
                  <option value="Fee Reminder">Fee Reminder</option>
                  <option value="Homework">Homework</option>
                  <option value="Examination">Examination</option>
                  <option value="Emergency">Emergency</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Subject (for Email / Notices)</label>
              <input type="text" name="subject" placeholder="e.g. Important Fee Notification" class="w-full px-3.5 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>

            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Message Body *</label>
              <textarea name="message_template" rows="4" required placeholder="Dear {parent_name}, your child {student_name}..." class="w-full px-3.5 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary font-mono text-[12px]"></textarea>
            </div>

            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Variable Tags</label>
              <input type="text" name="variables" value="{student_name}, {parent_name}, {date}, {school_name}" class="w-full px-3.5 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono text-[12px]"/>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-outline-variant/50">
              <button type="button" onclick="document.getElementById('templateModal').close()" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high text-label-md">Cancel</button>
              <button type="submit" class="px-5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">Save Template</button>
            </div>
          </div>
        <?php echo form_close(); ?>
      </div>
    </dialog>
