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
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Adjustments & Waivers</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Audit log of approved fee adjustments, special financial aid waivers, corrections, and manual concessions.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <button type="button" onclick="openAdjustmentModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Apply Fee Adjustment
        </button>
      </div>
    </div>

    <!-- Adjustments Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Adjustment Audit Records (<?php echo count($adjustments); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Invoice #</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student Name</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Category</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Type</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Previous Payable</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-primary uppercase whitespace-nowrap">Adjusted Amount</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-secondary uppercase whitespace-nowrap">New Payable</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Reason / Justification</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($adjustments)): ?>
              <tr><td colspan="9" class="px-4 py-8 text-center text-on-surface-variant">No fee adjustments logged yet.</td></tr>
            <?php else: ?>
              <?php foreach ($adjustments as $adj): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 font-mono font-bold text-primary whitespace-nowrap">
                    <?php echo html_escape($adj->invoice_no); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface">
                    <?php echo html_escape($adj->first_name . ' ' . $adj->last_name); ?>
                    <span class="text-[11px] text-on-surface-variant block font-mono font-normal"><?php echo html_escape($adj->admission_number); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-medium text-on-surface">
                    <?php echo html_escape($adj->category_name); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface">
                      <?php echo html_escape($adj->adjustment_type); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-right font-mono text-on-surface-variant whitespace-nowrap">
                    ₹<?php echo number_format($adj->previous_amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-primary whitespace-nowrap">
                    -₹<?php echo number_format($adj->adjustment_amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-secondary whitespace-nowrap text-base">
                    ₹<?php echo number_format($adj->new_amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-on-surface text-[13px]">
                    <?php echo html_escape($adj->reason); ?>
                  </td>
                  <td class="px-4 py-3 text-center font-mono text-[12px] whitespace-nowrap text-on-surface-variant">
                    <?php echo date('d M Y, h:i A', strtotime($adj->created_at)); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CREATE ADJUSTMENT MODAL -->
    <div id="adjustment-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant max-w-lg w-full p-6 elevation-3 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-title-lg text-on-surface font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[22px]">price_change</span>Apply Fee Adjustment
          </h3>
          <button onclick="closeAdjustmentModal()" class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <?php echo form_open('fees/adjustments', array('class' => 'space-y-4')); ?>
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Select Student Fee Invoice *</label>
            <select name="student_fee_id" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">-- Choose Invoice --</option>
              <?php foreach ($student_fees as $sf): ?>
                <option value="<?php echo $sf->student_fee_id; ?>">
                  <?php echo html_escape($sf->invoice_no . ' - ' . $sf->first_name . ' ' . $sf->last_name . ' (' . $sf->category_name . ' - Due: ₹' . number_format($sf->due_amount, 2) . ')'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Adjustment Type *</label>
              <select name="adjustment_type" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="Waiver" selected>Fee Waiver</option>
                <option value="Concession">Special Concession</option>
                <option value="Correction">Billing Correction</option>
                <option value="Adjustment">Administrative Adjustment</option>
              </select>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Adjustment Amount (₹) *</label>
              <input type="number" step="0.5" min="1" name="adjustment_amount" required placeholder="0.00" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Mandatory Reason / Justification *</label>
            <textarea name="reason" rows="3" required placeholder="Provide authorized justification (logged in audit log)..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-outline-variant/50">
            <button type="button" onclick="closeAdjustmentModal()" class="px-4 py-2 rounded-lg bg-surface-container-high text-on-surface text-label-md font-medium hover:bg-surface-container-highest cursor-pointer">
              Cancel
            </button>
            <button type="submit" class="px-5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant cursor-pointer">
              Confirm Adjustment
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function openAdjustmentModal() {
        var modal = document.getElementById('adjustment-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function closeAdjustmentModal() {
        var modal = document.getElementById('adjustment-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    </script>
