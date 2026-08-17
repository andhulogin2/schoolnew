<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $fullName = ($student) ? trim($student->first_name . ' ' . $student->last_name) : 'Student Profile';
  $nameParts = explode(' ', $fullName);
  $initials = '';
  foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
  if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
  $status = (isset($student->status) && $student->status == 1) ? 'Active' : 'Inactive';
  $statusBadge = ($status == 'Active')
    ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Active</span>'
    : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">Inactive</span>';
  $classDisplay = (isset($student->class_name) ? $student->class_name : '') . ' ' . (isset($student->section_name) ? $student->section_name : '');
?>

  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-5 mb-5 flex flex-col sm:flex-row sm:items-center gap-4">
    <div class="w-20 h-20 rounded-xl bg-primary-fixed text-primary flex items-center justify-center text-2xl font-semibold shrink-0"><?php echo html_escape($initials); ?></div>
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2 flex-wrap">
        <h2 class="font-headline-lg text-headline-lg text-on-surface"><?php echo html_escape($fullName); ?></h2>
        <?php echo $statusBadge; ?>
      </div>
      <p class="text-body-md font-body-md text-on-surface-variant mt-1"><?php echo html_escape(isset($student->admission_number) ? $student->admission_number : ''); ?> · Class <?php echo html_escape($classDisplay); ?> · Academic Year <?php echo html_escape(isset($student->year_name) ? $student->year_name : '2026-2027'); ?></p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo site_url('students'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">arrow_back</span>Back to List</a>
      <a href="<?php echo site_url('students/id_cards'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">badge</span>Print ID Card</a>
    </div>
  </div>
  <div class="flex gap-1 border-b border-outline-variant/60 mb-5 overflow-x-auto">
    <button class="px-4 py-2.5 text-body-md font-body-md border-b-2 border-secondary text-on-surface font-medium">Overview</button>
    <button class="px-4 py-2.5 text-body-md font-body-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface">Personal Info</button>
    <button class="px-4 py-2.5 text-body-md font-body-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface">Parent Info</button>
    <button class="px-4 py-2.5 text-body-md font-body-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface">Academic Info</button>
    <button class="px-4 py-2.5 text-body-md font-body-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface">Documents</button>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
      
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Personal Information</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
        <div class="grid grid-cols-2 gap-4 text-body-md font-body-md">
          <div><div class="text-on-surface-variant text-[12px]">Gender</div><div class="text-on-surface"><?php echo html_escape(isset($student->gender) ? $student->gender : '—'); ?></div></div>
          <div><div class="text-on-surface-variant text-[12px]">Date of Birth</div><div class="text-on-surface"><?php echo html_escape(isset($student->date_of_birth) ? date('d M Y', strtotime($student->date_of_birth)) : '—'); ?></div></div>
          <div><div class="text-on-surface-variant text-[12px]">Blood Group</div><div class="text-on-surface"><?php echo html_escape(isset($student->blood_group) ? $student->blood_group : '—'); ?></div></div>
          <div><div class="text-on-surface-variant text-[12px]">Nationality</div><div class="text-on-surface">Indian</div></div>
          <div class="col-span-2"><div class="text-on-surface-variant text-[12px]">Address</div><div class="text-on-surface"><?php echo html_escape(isset($student->address) ? $student->address : '—'); ?></div></div>
        </div></div>
    </div>
      
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Academic Information</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
        <div class="grid grid-cols-3 gap-4 text-body-md font-body-md">
          <div><div class="text-on-surface-variant text-[12px]">Class</div><div class="text-on-surface"><?php echo html_escape(isset($student->class_name) ? $student->class_name : '—'); ?></div></div>
          <div><div class="text-on-surface-variant text-[12px]">Section</div><div class="text-on-surface"><?php echo html_escape(isset($student->section_name) ? $student->section_name : '—'); ?></div></div>
          <div><div class="text-on-surface-variant text-[12px]">Academic Year</div><div class="text-on-surface"><?php echo html_escape(isset($student->year_name) ? $student->year_name : '2026-2027'); ?></div></div>
        </div></div>
    </div>
    </div>
    <div class="space-y-5">
      
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Parent / Guardian</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
        <div class="text-body-md font-body-md space-y-1.5">
          <div class="text-on-surface font-medium"><?php echo html_escape(isset($student->guardian_name) ? $student->guardian_name : '—'); ?> (<?php echo html_escape(isset($student->guardian_relation) ? $student->guardian_relation : 'Parent'); ?>)</div>
          <div class="text-on-surface-variant"><?php echo html_escape(isset($student->guardian_phone) ? $student->guardian_phone : '—'); ?></div>
          <div class="text-on-surface-variant"><?php echo html_escape(isset($student->guardian_email) ? $student->guardian_email : '—'); ?></div>
        </div></div>
    </div>
      
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Documents</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
        <?php if (!empty($student->documents)): ?>
          <ul class="text-body-md font-body-md text-on-surface-variant space-y-1 list-disc pl-4">
            <?php foreach ($student->documents as $doc): ?>
              <li><?php echo html_escape($doc->document_type); ?>: <?php echo html_escape($doc->document_name); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <div class="text-body-md font-body-md text-on-surface-variant">3 documents uploaded — Birth Certificate, Previous School TC, ID Proof.</div>
        <?php endif; ?>
      </div>
    </div>
    </div>
  </div>

