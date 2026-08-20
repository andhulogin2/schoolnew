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
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Discounts & Concessions</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Configure scholarship percentages, staff ward concessions, sibling discounts, and fee relief schemes.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <button type="button" onclick="openDiscountModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Add Scheme
        </button>
      </div>
    </div>

    <!-- Schemes Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Configured Schemes (<?php echo count($discounts); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Scheme Name</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Type</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-primary uppercase whitespace-nowrap">Discount Value</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Max Cap</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Category</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Status</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($discounts)): ?>
              <tr><td colspan="7" class="px-4 py-8 text-center text-on-surface-variant">No discount schemes configured yet.</td></tr>
            <?php else: ?>
              <?php foreach ($discounts as $d): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface">
                    <?php echo html_escape($d->name); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface">
                      <?php echo html_escape($d->discount_type); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-primary whitespace-nowrap">
                    <?php echo ($d->discount_type === 'Percentage') ? ($d->discount_value . '%') : ('₹' . number_format($d->discount_value, 2)); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono text-on-surface-variant whitespace-nowrap">
                    <?php echo ($d->max_discount > 0) ? ('₹' . number_format($d->max_discount, 2)) : 'No Limit'; ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?php echo ($d->is_concession) ? 'bg-amber-100 text-amber-900' : 'bg-primary-fixed text-primary'; ?>">
                      <?php echo ($d->is_concession) ? 'Concession' : 'General Discount'; ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?php echo ($d->status == 1) ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container-high text-on-surface-variant'; ?>">
                      <?php echo ($d->status == 1) ? 'Active' : 'Inactive'; ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1">
                      <!-- Edit -->
                      <button type="button" onclick='editDiscountModal(<?php echo json_encode($d); ?>)' class="p-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant transition-colors cursor-pointer" title="Edit">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                      </button>

                      <!-- Delete -->
                      <?php echo form_open('fees/discounts', array('class' => 'inline', 'onsubmit' => 'return confirm("Delete this discount scheme?");')); ?>
                        <input type="hidden" name="action" value="delete"/>
                        <input type="hidden" name="discount_id" value="<?php echo $d->discount_id; ?>"/>
                        <button type="submit" class="p-1.5 rounded-lg hover:bg-error-container text-error transition-colors cursor-pointer" title="Delete">
                          <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                      <?php echo form_close(); ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CREATE / EDIT DISCOUNT MODAL -->
    <div id="discount-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant max-w-md w-full p-6 elevation-3 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50">
          <h3 id="modal-disc-title" class="font-headline-md text-title-lg text-on-surface font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[22px]">loyalty</span>Add Discount Scheme
          </h3>
          <button onclick="closeDiscountModal()" class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <?php echo form_open('fees/discounts', array('class' => 'space-y-4')); ?>
          <input type="hidden" name="discount_id" id="modal-disc-id" value="0"/>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Scheme Name *</label>
            <input type="text" name="name" id="modal-disc-name" required placeholder="e.g. Sibling Discount, Staff Ward Scholarship" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Discount Type *</label>
              <select name="discount_type" id="modal-disc-type" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="Percentage">Percentage (%)</option>
                <option value="Fixed Amount">Fixed Amount (₹)</option>
              </select>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Discount Value *</label>
              <input type="number" step="0.1" min="0.1" name="discount_value" id="modal-disc-value" required placeholder="e.g. 15.0" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Maximum Discount Cap (₹)</label>
            <input type="number" step="0.5" name="max_discount" id="modal-disc-max" placeholder="Optional upper limit in ₹" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
          </div>

          <div class="space-y-2 pt-2">
            <label class="flex items-center gap-2 text-body-md text-on-surface cursor-pointer">
              <input type="checkbox" name="is_concession" id="modal-disc-concession" value="1" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500"/>
              <span>Mark as Special Concession / Financial Aid</span>
            </label>
            <label class="flex items-center gap-2 text-body-md text-on-surface cursor-pointer">
              <input type="checkbox" name="status" id="modal-disc-status" value="1" checked class="w-4 h-4 rounded text-secondary focus:ring-secondary"/>
              <span>Active Scheme</span>
            </label>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-outline-variant/50">
            <button type="button" onclick="closeDiscountModal()" class="px-4 py-2 rounded-lg bg-surface-container-high text-on-surface text-label-md font-medium hover:bg-surface-container-highest cursor-pointer">
              Cancel
            </button>
            <button type="submit" class="px-5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant cursor-pointer">
              Save Scheme
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function openDiscountModal() {
        document.getElementById('modal-disc-id').value = '0';
        document.getElementById('modal-disc-title').textContent = 'Add Discount Scheme';
        document.getElementById('modal-disc-name').value = '';
        document.getElementById('modal-disc-type').value = 'Percentage';
        document.getElementById('modal-disc-value').value = '10.0';
        document.getElementById('modal-disc-max').value = '';
        document.getElementById('modal-disc-concession').checked = false;
        document.getElementById('modal-disc-status').checked = true;

        var modal = document.getElementById('discount-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function editDiscountModal(item) {
        document.getElementById('modal-disc-id').value = item.discount_id;
        document.getElementById('modal-disc-title').textContent = 'Edit Discount Scheme';
        document.getElementById('modal-disc-name').value = item.name;
        document.getElementById('modal-disc-type').value = item.discount_type;
        document.getElementById('modal-disc-value').value = item.discount_value;
        document.getElementById('modal-disc-max').value = item.max_discount || '';
        document.getElementById('modal-disc-concession').checked = (item.is_concession == 1);
        document.getElementById('modal-disc-status').checked = (item.status == 1);

        var modal = document.getElementById('discount-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function closeDiscountModal() {
        var modal = document.getElementById('discount-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    </script>
