<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Classes & Grades</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1"><?php echo count($classes); ?> academic class grades configured.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button onclick="openAddClassModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Add Class
        </button>
      </div>
    </div>

    <!-- Classes Grid / Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Class Name</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Class Code</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Academic Session</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Capacity</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Enrolled Students</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30 text-body-md">
            <?php foreach ($classes as $cls): ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 font-semibold text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">school</span>
                    <a href="<?php echo site_url('academics/sections?class_id=' . $cls->class_id); ?>" class="hover:underline"><?php echo html_escape($cls->class_name); ?></a>
                  </div>
                </td>
                <td class="px-4 py-3 font-mono text-primary font-medium whitespace-nowrap"><?php echo html_escape($cls->class_code); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo html_escape($cls->year_name ?: '2026-2027'); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo $cls->capacity; ?> seats</td>
                <td class="px-4 py-3 font-bold text-secondary whitespace-nowrap">
                  <a href="<?php echo site_url('students?class_id=' . $cls->class_id); ?>" class="hover:underline"><?php echo isset($cls->student_count) ? $cls->student_count : 0; ?> students</a>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="<?php echo site_url('academics/sections?class_id=' . $cls->class_id); ?>" class="px-3 py-1 rounded bg-surface-container-high text-on-surface text-label-md hover:bg-surface-container-highest transition-colors inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">view_list</span>Sections</a>
                    <button onclick="openEditClassModal(<?php echo $cls->class_id; ?>, '<?php echo html_escape(addslashes($cls->class_name)); ?>', '<?php echo html_escape(addslashes($cls->class_code)); ?>', <?php echo $cls->capacity; ?>)" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors cursor-pointer" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                    <a href="<?php echo site_url('academics/delete_class/' . $cls->class_id); ?>" onclick="return confirm('Deactivate class?')" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container/20 hover:text-error transition-colors" title="Deactivate"><span class="material-symbols-outlined text-[18px]">delete</span></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal: Add / Edit Class -->
    <div id="modal-class" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface" id="modal-class-title">Add Class</h3>
          <button onclick="document.getElementById('modal-class').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open('academics/classes', array('class' => 'p-6 space-y-4')); ?>
          <input type="hidden" name="action" id="class_action" value="add"/>
          <input type="hidden" name="class_id" id="modal_class_id"/>
          <div>
            <label class="block text-label-md mb-1">Academic Session *</label>
            <select name="academic_year_id" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <?php foreach ($years as $yr): ?>
                <option value="<?php echo $yr->academic_year_id; ?>"><?php echo html_escape($yr->year_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Class / Grade Name *</label>
            <input type="text" name="class_name" id="modal_class_name" required placeholder="e.g. Grade 11" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-label-md mb-1">Class Code</label>
              <input type="text" name="class_code" id="modal_class_code" placeholder="e.g. G11" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md mb-1">Max Capacity</label>
              <input type="number" name="capacity" id="modal_class_capacity" value="40" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-class').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant cursor-pointer">Save Class</button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function openAddClassModal() {
        document.getElementById('class_action').value = 'add';
        document.getElementById('modal-class-title').textContent = 'Add Class';
        document.getElementById('modal_class_id').value = '';
        document.getElementById('modal_class_name').value = '';
        document.getElementById('modal_class_code').value = '';
        document.getElementById('modal_class_capacity').value = '40';
        document.getElementById('modal-class').classList.remove('hidden');
      }
      function openEditClassModal(id, name, code, capacity) {
        document.getElementById('class_action').value = 'edit';
        document.getElementById('modal-class-title').textContent = 'Edit Class';
        document.getElementById('modal_class_id').value = id;
        document.getElementById('modal_class_name').value = name;
        document.getElementById('modal_class_code').value = code;
        document.getElementById('modal_class_capacity').value = capacity;
        document.getElementById('modal-class').classList.remove('hidden');
      }
    </script>
