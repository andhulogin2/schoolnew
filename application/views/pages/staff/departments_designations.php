<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Departments & Designations</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Configure school academic departments, administrative divisions, and official job designations.</p>
      </div>
      <div class="flex items-center gap-2">
        <button onclick="document.getElementById('modal-add-dept').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer"><span class="material-symbols-outlined text-[18px]">domain_add</span>Add Department</button>
        <button onclick="document.getElementById('modal-add-desig').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-primary text-on-primary text-label-md hover:bg-primary/90 transition-colors shadow-sm cursor-pointer"><span class="material-symbols-outlined text-[18px]">badge</span>Add Designation</button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Departments Section -->
      <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">domain</span>Departments (<?php echo count($departments); ?>)
          </h3>
        </div>
        <div class="table-scroll overflow-x-auto">
          <table class="w-full data-table zebra border-collapse">
            <thead>
              <tr class="border-b border-outline-variant/60">
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase">Department Name</th>
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase">Staff Count</th>
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/30 text-body-md">
              <?php foreach ($departments as $d): ?>
                <tr class='hover:bg-surface-container-low transition-colors'>
                  <td class="px-4 py-3 text-on-surface font-semibold">
                    <?php echo html_escape($d->department_name); ?>
                    <?php if ($d->description): ?>
                      <div class="text-[12px] text-on-surface-variant font-normal"><?php echo html_escape($d->description); ?></div>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3 font-mono text-primary font-medium"><?php echo isset($d->staff_count) ? $d->staff_count : 0; ?> staff</td>
                  <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Active</span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Designations Section -->
      <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary text-[20px]">badge</span>Designations (<?php echo count($designations); ?>)
          </h3>
        </div>
        <div class="table-scroll overflow-x-auto">
          <table class="w-full data-table zebra border-collapse">
            <thead>
              <tr class="border-b border-outline-variant/60">
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase">Designation Name</th>
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase">Category</th>
                <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase">Staff Count</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/30 text-body-md">
              <?php foreach ($designations as $dg): ?>
                <tr class='hover:bg-surface-container-low transition-colors'>
                  <td class="px-4 py-3 text-on-surface font-semibold">
                    <?php echo html_escape($dg->designation_name); ?>
                    <?php if ($dg->description): ?>
                      <div class="text-[12px] text-on-surface-variant font-normal"><?php echo html_escape($dg->description); ?></div>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3 text-on-surface-variant"><?php echo html_escape($dg->category); ?></td>
                  <td class="px-4 py-3 font-mono text-primary font-medium"><?php echo isset($dg->staff_count) ? $dg->staff_count : 0; ?> staff</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Modal: Add Department -->
    <div id="modal-add-dept" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface">Add Department</h3>
          <button onclick="document.getElementById('modal-add-dept').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open('staff/departments_designations', array('class' => 'p-6 space-y-4')); ?>
          <input type="hidden" name="action" value="add_department"/>
          <div>
            <label class="block text-label-md mb-1">Department Name *</label>
            <input type="text" name="department_name" required placeholder="e.g. Physical Education" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
          </div>
          <div>
            <label class="block text-label-md mb-1">Description</label>
            <input type="text" name="description" placeholder="Department responsibilities" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-add-dept').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant cursor-pointer">Save Department</button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <!-- Modal: Add Designation -->
    <div id="modal-add-desig" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface">Add Designation</h3>
          <button onclick="document.getElementById('modal-add-desig').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open('staff/departments_designations', array('class' => 'p-6 space-y-4')); ?>
          <input type="hidden" name="action" value="add_designation"/>
          <div>
            <label class="block text-label-md mb-1">Designation Name *</label>
            <input type="text" name="designation_name" required placeholder="e.g. Senior Lab Instructor" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
          </div>
          <div>
            <label class="block text-label-md mb-1">Category *</label>
            <select name="category" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <option value="Teaching">Teaching</option>
              <option value="Administration">Administration</option>
              <option value="Finance">Finance</option>
              <option value="Support">Support</option>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Description</label>
            <input type="text" name="description" placeholder="Designation duties" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-add-desig').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-on-primary text-label-md hover:bg-primary/90 cursor-pointer">Save Designation</button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>
