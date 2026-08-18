<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <?php echo form_open('students/edit/' . $student_id, array('id' => 'student-edit-form')); ?>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Edit Student</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Update student personal and academic information.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <a href="<?php echo site_url('students/profile/' . $student_id); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">close</span>Cancel</a>
      </div>
    </div>
  
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Student Details</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Admission Number <span class="text-error">*</span></label>
      <input type="text" name="admission_number" required value="<?php echo html_escape(isset($student->admission_number) ? $student->admission_number : ''); ?>" placeholder="e.g. EDU2026009" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">First Name <span class="text-error">*</span></label>
      <input type="text" name="first_name" required value="<?php echo html_escape(isset($student->first_name) ? $student->first_name : ''); ?>" placeholder="e.g. Aarav" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Last Name</label>
      <input type="text" name="last_name" value="<?php echo html_escape(isset($student->last_name) ? $student->last_name : ''); ?>" placeholder="e.g. Nair" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Gender</label>
      <select name="gender" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
        <option value="Male" <?php echo ($student->gender === 'Male') ? 'selected' : ''; ?>>Male</option>
        <option value="Female" <?php echo ($student->gender === 'Female') ? 'selected' : ''; ?>>Female</option>
        <option value="Other" <?php echo ($student->gender === 'Other') ? 'selected' : ''; ?>>Other</option>
      </select>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Date of Birth</label>
      <input type="date" name="date_of_birth" value="<?php echo html_escape($student->date_of_birth); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Blood Group</label>
      <select name="blood_group" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
        <?php foreach (array('A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-') as $bg): ?>
          <option value="<?php echo $bg; ?>" <?php echo ($student->blood_group === $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Class</label>
      <select name="class_id" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($student->class_id == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Section</label>
      <select name="section_id" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
        <?php foreach ($sections as $sec): ?>
          <option value="<?php echo $sec->section_id; ?>" <?php echo ($student->section_id == $sec->section_id) ? 'selected' : ''; ?>><?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Roll Number</label>
      <input type="text" name="roll_number" value="<?php echo html_escape(isset($student->roll_number) ? $student->roll_number : ''); ?>" placeholder="e.g. 15" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Guardian Name</label>
      <input type="text" name="guardian_name" value="<?php echo html_escape(isset($student->guardian_name) ? $student->guardian_name : ''); ?>" placeholder="e.g. Suresh Nair" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Guardian Phone</label>
      <input type="text" name="guardian_phone" value="<?php echo html_escape(isset($student->guardian_phone) ? $student->guardian_phone : ''); ?>" placeholder="+91 98470 11223" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="sm:col-span-2">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Address</label>
      <input type="text" name="address" value="<?php echo html_escape(isset($student->address) ? $student->address : ''); ?>" placeholder="House name, Place, District, PIN" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
    </div>
    <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-outline-variant/50">
      <a href="<?php echo site_url('students/profile/' . $student_id); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors">Cancel</a>
      <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer"><span class="material-symbols-outlined text-[18px]">check</span>Update Student</button>
    </div>
  </div>
    </div>
    <?php echo form_close(); ?>
