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
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Payment Refunds</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Process and audit authorized fee refunds, excess payment returns, and admission cancellations.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <button type="button" onclick="openRefundModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">currency_exchange</span>Process Refund
        </button>
      </div>
    </div>

    <!-- Refunds Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Refund Transactions (<?php echo count($refunds); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Receipt #</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student Name</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Original Paid</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-error uppercase whitespace-nowrap">Refund Amount</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Refund Mode</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Refund Reason</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Approved By</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($refunds)): ?>
              <tr><td colspan="9" class="px-4 py-8 text-center text-on-surface-variant">No refund transactions recorded yet.</td></tr>
            <?php else: ?>
              <?php foreach ($refunds as $rf): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 font-mono font-bold text-primary whitespace-nowrap">
                    <?php echo html_escape($rf->receipt_no); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface">
                    <?php echo html_escape($rf->first_name . ' ' . $rf->last_name); ?>
                    <span class="text-[11px] text-on-surface-variant block font-mono font-normal"><?php echo html_escape($rf->admission_number); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap text-on-surface-variant">
                    <?php echo html_escape($rf->class_name . ' ' . $rf->section_name); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono text-on-surface-variant whitespace-nowrap">
                    ₹<?php echo number_format($rf->original_paid, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-error whitespace-nowrap text-base">
                    ₹<?php echo number_format($rf->refund_amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface">
                      <?php echo html_escape($rf->refund_mode); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-on-surface text-[13px]">
                    <?php echo html_escape($rf->refund_reason); ?>
                  </td>
                  <td class="px-4 py-3 text-center text-[12px] font-medium text-on-surface whitespace-nowrap">
                    <?php echo html_escape($rf->approved_by_name ?: 'Administrator'); ?>
                  </td>
                  <td class="px-4 py-3 text-center font-mono text-[12px] whitespace-nowrap text-on-surface-variant">
                    <?php echo date('d M Y', strtotime($rf->created_at)); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PROCESS REFUND MODAL -->
    <div id="refund-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant max-w-lg w-full p-6 elevation-3 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-title-lg text-on-surface font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-error text-[22px]">currency_exchange</span>Process Payment Refund
          </h3>
          <button onclick="closeRefundModal()" class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <?php echo form_open('fees/refunds', array('class' => 'space-y-4')); ?>
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Select Payment Receipt *</label>
            <select name="payment_id" id="ref-payment-select" onchange="updateRefundMax(this)" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">-- Choose Payment --</option>
              <?php foreach ($recent_payments as $rp): ?>
                <option value="<?php echo $rp->payment_id; ?>" data-amount="<?php echo $rp->amount_paid; ?>">
                  <?php echo html_escape($rp->receipt_no . ' - ' . $rp->first_name . ' ' . $rp->last_name . ' (₹' . number_format($rp->amount_paid, 2) . ' on ' . date('d M Y', strtotime($rp->payment_date)) . ')'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Refund Amount (₹) *</label>
              <input type="number" step="0.5" min="1" name="refund_amount" id="ref-amount" required placeholder="0.00" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono font-bold text-error focus:ring-2 focus:ring-error/20 focus:border-error"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Refund Mode *</label>
              <select name="refund_mode" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="Bank Transfer" selected>Bank Transfer / NEFT</option>
                <option value="Cash">Cash Return</option>
                <option value="UPI">UPI Return</option>
                <option value="Cheque">Cheque</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Mandatory Refund Reason *</label>
            <textarea name="refund_reason" rows="3" required placeholder="Detailed reason for refund (e.g. Admission withdrawal, Duplicate payment, Fee overcharge)..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-outline-variant/50">
            <button type="button" onclick="closeRefundModal()" class="px-4 py-2 rounded-lg bg-surface-container-high text-on-surface text-label-md font-medium hover:bg-surface-container-highest cursor-pointer">
              Cancel
            </button>
            <button type="submit" class="px-5 py-2 rounded-lg bg-error text-on-error text-label-md font-semibold hover:bg-error/90 cursor-pointer">
              Confirm & Issue Refund
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function openRefundModal() {
        var modal = document.getElementById('refund-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function closeRefundModal() {
        var modal = document.getElementById('refund-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }

      function updateRefundMax(select) {
        var opt = select.options[select.selectedIndex];
        var amount = opt.getAttribute('data-amount');
        if (amount) {
          document.getElementById('ref-amount').max = amount;
          document.getElementById('ref-amount').value = amount;
        }
      }
    </script>
