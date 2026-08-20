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
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Reminders</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Queue and dispatch automated fee alerts, upcoming due notifications, and overdue warnings to parents.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('fees/reminder_history'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">history</span>Reminder History
        </a>
      </div>
    </div>

    <!-- Reminder Form & Template Presets Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      
      <!-- Reminder Creation Form -->
      <div class="lg:col-span-2 p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-5">
        <div class="flex items-center gap-2.5 pb-3 border-b border-outline-variant/50">
          <span class="material-symbols-outlined text-primary text-[24px]">send</span>
          <h3 class="font-headline-md text-title-lg font-bold text-on-surface">Compose Fee Reminder</h3>
        </div>

        <?php echo form_open('fees/reminders', array('class' => 'space-y-4')); ?>
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Select Student with Outstanding Due *</label>
            <select name="student_id" id="rem-student-select" onchange="updateReminderStudent(this)" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">-- Choose Student --</option>
              <?php foreach ($due_fees as $df): ?>
                <option value="<?php echo $df->student_id; ?>" 
                        data-sfee="<?php echo $df->student_fee_id; ?>"
                        data-name="<?php echo html_escape($df->first_name . ' ' . $df->last_name); ?>"
                        data-due="₹<?php echo number_format($df->due_amount, 2); ?>"
                        data-duedate="<?php echo date('d M Y', strtotime($df->due_date)); ?>"
                        data-overdue="<?php echo max(0, (int)$df->days_overdue); ?>">
                  <?php echo html_escape($df->first_name . ' ' . $df->last_name . ' (' . $df->admission_number . ') - Due: ₹' . number_format($df->due_amount, 2) . ' (' . $df->category_name . ')'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <input type="hidden" name="student_fee_id" id="rem-sfee-id" value="0"/>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Reminder Type *</label>
              <select name="reminder_type" id="rem-type" onchange="applyTemplatePreset()" class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="Upcoming Due">Upcoming Due</option>
                <option value="Due Today">Due Today</option>
                <option value="Overdue">Overdue Notice</option>
                <option value="Payment Confirmation">Payment Confirmation</option>
              </select>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Scheduled Date *</label>
              <input type="date" name="scheduled_date" value="<?php echo date('Y-m-d'); ?>" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Message Body *</label>
            <textarea name="message" id="rem-message" rows="4" required placeholder="Type reminder notification message..." class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"><?php echo html_escape($settings->reminder_template_upcoming); ?></textarea>
            <div class="text-[12px] text-on-surface-variant mt-1">Available placeholders: <code class="font-mono">{student_name}</code>, <code class="font-mono">{amount}</code>, <code class="font-mono">{due_date}</code>, <code class="font-mono">{days_overdue}</code></div>
          </div>

          <div class="pt-4 border-t border-outline-variant/50 flex items-center justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">notifications</span>Queue Fee Reminder
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>

      <!-- Quick Template Presets -->
      <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-outline-variant/50">
          <span class="material-symbols-outlined text-primary text-[22px]">format_quote</span>
          <h3 class="font-headline-md text-title-md font-bold text-on-surface">Template Presets</h3>
        </div>

        <div class="space-y-3 text-body-md">
          <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] font-semibold text-primary block">Upcoming Due Template</span>
            <p class="text-[13px] text-on-surface mt-1"><?php echo html_escape($settings->reminder_template_upcoming); ?></p>
          </div>

          <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] font-semibold text-error block">Overdue Warning Template</span>
            <p class="text-[13px] text-on-surface mt-1"><?php echo html_escape($settings->reminder_template_overdue); ?></p>
          </div>

          <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] font-semibold text-secondary block">Payment Receipt Template</span>
            <p class="text-[13px] text-on-surface mt-1"><?php echo html_escape($settings->reminder_template_payment); ?></p>
          </div>
        </div>
      </div>
    </div>

    <script>
      var upcomingTpl = <?php echo json_encode($settings->reminder_template_upcoming); ?>;
      var overdueTpl = <?php echo json_encode($settings->reminder_template_overdue); ?>;
      var paymentTpl = <?php echo json_encode($settings->reminder_template_payment); ?>;

      function updateReminderStudent(select) {
        var opt = select.options[select.selectedIndex];
        var sfee = opt.getAttribute('data-sfee');
        if (sfee) {
          document.getElementById('rem-sfee-id').value = sfee;
        }
        applyTemplatePreset();
      }

      function applyTemplatePreset() {
        var type = document.getElementById('rem-type').value;
        var select = document.getElementById('rem-student-select');
        var opt = select.options[select.selectedIndex];
        var name = opt.getAttribute('data-name') || '{student_name}';
        var due = opt.getAttribute('data-due') || '{amount}';
        var duedate = opt.getAttribute('data-duedate') || '{due_date}';
        var overdue = opt.getAttribute('data-overdue') || '0';

        var tpl = upcomingTpl;
        if (type === 'Overdue') tpl = overdueTpl;
        else if (type === 'Payment Confirmation') tpl = paymentTpl;

        tpl = tpl.replace(/{student_name}/g, name)
                 .replace(/{amount}/g, due)
                 .replace(/{due_date}/g, duedate)
                 .replace(/{days_overdue}/g, overdue);

        document.getElementById('rem-message').value = tpl;
      }
    </script>
