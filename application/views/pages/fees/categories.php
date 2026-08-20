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
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Categories</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Configure fee heads, billing frequency, and student eligibility criteria.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <button type="button" onclick="openCategoryModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Add Fee Category
        </button>
      </div>
    </div>

    <!-- Category List Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">All Fee Heads (<?php echo count($categories); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Category Name</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Code</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Frequency</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Applicable To</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Description</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Status</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($categories)): ?>
              <tr><td colspan="7" class="px-4 py-8 text-center text-on-surface-variant">No fee categories configured yet.</td></tr>
            <?php else: ?>
              <?php foreach ($categories as $c): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface">
                    <?php echo html_escape($c->head_name); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap font-mono font-bold text-primary">
                    <?php echo html_escape($c->category_code ?: '—'); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface">
                      <?php echo html_escape($c->frequency); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap text-on-surface-variant">
                    <?php echo html_escape($c->applicable_to ?: 'All Students'); ?>
                  </td>
                  <td class="px-4 py-3 text-on-surface-variant text-[13px]">
                    <?php echo html_escape($c->description ?: '—'); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?php echo ($c->status == 1) ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container-high text-on-surface-variant'; ?>">
                      <?php echo ($c->status == 1) ? 'Active' : 'Inactive'; ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1">
                      <!-- Edit -->
                      <button type="button" onclick='editCategoryModal(<?php echo json_encode($c); ?>)' class="p-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant transition-colors cursor-pointer" title="Edit">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                      </button>

                      <!-- Delete -->
                      <?php echo form_open('fees/categories', array('class' => 'inline', 'onsubmit' => 'return confirm("Delete this category?");')); ?>
                        <input type="hidden" name="action" value="delete"/>
                        <input type="hidden" name="fee_head_id" value="<?php echo $c->fee_head_id; ?>"/>
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

    <!-- CREATE / EDIT CATEGORY MODAL -->
    <div id="category-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant max-w-md w-full p-6 elevation-3 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50">
          <h3 id="modal-category-title" class="font-headline-md text-title-lg text-on-surface font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[22px]">category</span>Add Fee Category
          </h3>
          <button onclick="closeCategoryModal()" class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <?php echo form_open('fees/categories', array('class' => 'space-y-4')); ?>
          <input type="hidden" name="fee_head_id" id="modal-category-id" value="0"/>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Category Name *</label>
            <input type="text" name="head_name" id="modal-category-name" required placeholder="e.g. Tuition Fee" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Category Code</label>
              <input type="text" name="category_code" id="modal-category-code" placeholder="e.g. TUI" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary uppercase font-mono"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Frequency *</label>
              <select name="frequency" id="modal-category-frequency" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="One Time">One Time</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Half Yearly">Half Yearly</option>
                <option value="Yearly" selected>Yearly</option>
                <option value="Custom">Custom</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Applicable To</label>
            <input type="text" name="applicable_to" id="modal-category-applicable" placeholder="e.g. All Students, Hostellers, Transport Users" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Description</label>
            <textarea name="description" id="modal-category-desc" rows="2" placeholder="Brief details about this fee head..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
          </div>

          <label class="flex items-center gap-2 text-body-md text-on-surface cursor-pointer">
            <input type="checkbox" name="status" id="modal-category-status" value="1" checked class="w-4 h-4 rounded text-secondary focus:ring-secondary"/>
            <span>Active Category</span>
          </label>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-outline-variant/50">
            <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 rounded-lg bg-surface-container-high text-on-surface text-label-md font-medium hover:bg-surface-container-highest cursor-pointer">
              Cancel
            </button>
            <button type="submit" class="px-5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant cursor-pointer">
              Save Category
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function openCategoryModal() {
        document.getElementById('modal-category-id').value = '0';
        document.getElementById('modal-category-title').textContent = 'Add Fee Category';
        document.getElementById('modal-category-name').value = '';
        document.getElementById('modal-category-code').value = '';
        document.getElementById('modal-category-frequency').value = 'Yearly';
        document.getElementById('modal-category-applicable').value = 'All Students';
        document.getElementById('modal-category-desc').value = '';
        document.getElementById('modal-category-status').checked = true;

        var modal = document.getElementById('category-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function editCategoryModal(item) {
        document.getElementById('modal-category-id').value = item.fee_head_id;
        document.getElementById('modal-category-title').textContent = 'Edit Fee Category';
        document.getElementById('modal-category-name').value = item.head_name;
        document.getElementById('modal-category-code').value = item.category_code || '';
        document.getElementById('modal-category-frequency').value = item.frequency || 'Yearly';
        document.getElementById('modal-category-applicable').value = item.applicable_to || 'All Students';
        document.getElementById('modal-category-desc').value = item.description || '';
        document.getElementById('modal-category-status').checked = (item.status == 1);

        var modal = document.getElementById('category-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function closeCategoryModal() {
        var modal = document.getElementById('category-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    </script>
