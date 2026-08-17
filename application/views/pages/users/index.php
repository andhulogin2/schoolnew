<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">User Management</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Manage system users and their role-based access.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0"><a href="#" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">person_add</span>Add User</a></div>
    </div>
  
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead><tr class="border-b border-outline-variant/60"><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">User</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Role</th><th class="text-left px-4 py-3 text-label-md text-label-md text-on-surface-variant uppercase tracking-wide whitespace-nowrap">Status</th></tr></thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php foreach ($users as $u): ?>
              <?php
                $nameParts = explode(' ', trim($u->name));
                $uInitials = '';
                foreach ($nameParts as $np) { if (!empty($np)) $uInitials .= strtoupper($np[0]); }
                if (strlen($uInitials) > 2) $uInitials = substr($uInitials, 0, 2);
                $statusBadge = ($u->status == 1) 
                  ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Active</span>'
                  : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">Inactive</span>';
              ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center text-[11px] font-semibold shrink-0"><?php echo html_escape($uInitials); ?></div>
                    <div>
                      <div class="font-medium text-on-surface"><?php echo html_escape($u->name); ?></div>
                      <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($u->email); ?></div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($u->role_name ?: 'User'); ?></td>
                <td class="px-4 py-3 text-body-md font-body-md text-on-surface whitespace-nowrap"><?php echo $statusBadge; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between px-4 py-3 border-t border-outline-variant/50 text-body-md font-body-md text-on-surface-variant">
        <span>Showing <?php echo count($users); ?> of <?php echo count($users); ?> records</span>
        <div class="flex items-center gap-1">
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Previous</button>
          <button class="px-3 py-1.5 rounded-lg bg-primary-fixed text-primary font-medium">1</button>
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high disabled:opacity-40" disabled>Next</button>
        </div>
      </div>
    </div>
  <div class="mt-5">
    
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Roles &amp; Permissions</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <?php foreach ($roles as $r): ?>
          <div class="rounded-lg border border-outline-variant/60 p-3">
            <div class="font-medium text-on-surface text-body-md font-body-md"><?php echo html_escape($r->role_name); ?></div>
            <div class="text-[12px] text-on-surface-variant mt-1"><?php echo html_escape($r->description); ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    </div>
  </div>

