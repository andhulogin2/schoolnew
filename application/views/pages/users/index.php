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

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">User Management</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Manage user authentication accounts, roles, access statuses, and security credentials.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('users/create'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">person_add</span>Create User
        </a>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('users/list'); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Role</label>
          <select name="role_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Roles</option>
            <?php foreach ($roles as $r): ?>
              <option value="<?php echo $r->role_id; ?>" <?php echo (($filters['role_id'] ?? '') == $r->role_id) ? 'selected' : ''; ?>><?php echo html_escape($r->role_name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">User Type</label>
          <select name="user_type" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All User Types</option>
            <option value="Admin" <?php echo (($filters['user_type'] ?? '') === 'Admin') ? 'selected' : ''; ?>>Admin</option>
            <option value="Principal" <?php echo (($filters['user_type'] ?? '') === 'Principal') ? 'selected' : ''; ?>>Principal</option>
            <option value="Teacher" <?php echo (($filters['user_type'] ?? '') === 'Teacher') ? 'selected' : ''; ?>>Teacher</option>
            <option value="Accountant" <?php echo (($filters['user_type'] ?? '') === 'Accountant') ? 'selected' : ''; ?>>Accountant</option>
            <option value="Transport Manager" <?php echo (($filters['user_type'] ?? '') === 'Transport Manager') ? 'selected' : ''; ?>>Transport Manager</option>
            <option value="Receptionist" <?php echo (($filters['user_type'] ?? '') === 'Receptionist') ? 'selected' : ''; ?>>Receptionist</option>
            <option value="Parent" <?php echo (($filters['user_type'] ?? '') === 'Parent') ? 'selected' : ''; ?>>Parent</option>
            <option value="Student" <?php echo (($filters['user_type'] ?? '') === 'Student') ? 'selected' : ''; ?>>Student</option>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Status</label>
          <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Statuses</option>
            <option value="Active" <?php echo (($filters['status'] ?? '') === 'Active') ? 'selected' : ''; ?>>Active</option>
            <option value="Inactive" <?php echo (($filters['status'] ?? '') === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
            <option value="Locked" <?php echo (($filters['status'] ?? '') === 'Locked') ? 'selected' : ''; ?>>Locked</option>
            <option value="Suspended" <?php echo (($filters['status'] ?? '') === 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Search</label>
          <div class="flex gap-2">
            <input type="text" name="search" value="<?php echo html_escape($filters['search'] ?? ''); ?>" placeholder="Name, username, email..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant cursor-pointer">Go</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Users Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Registered Users (<?php echo count($users); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">User</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Username</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Role</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Email & Phone</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Status</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Last Login</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($users)): ?>
              <tr><td colspan="7" class="px-4 py-8 text-center text-on-surface-variant">No users found matching current filters.</td></tr>
            <?php else: ?>
              <?php foreach ($users as $u): ?>
                <?php
                  $stBadge = ($u->status === 'Active') ? 'bg-secondary-container text-on-secondary-container' : (($u->status === 'Locked') ? 'bg-error-container text-error font-bold' : 'bg-surface-container-high text-on-surface-variant');
                ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 whitespace-nowrap">
                    <strong class="text-on-surface block"><?php echo html_escape($u->name); ?></strong>
                    <span class="text-[11px] text-on-surface-variant"><?php echo html_escape($u->user_type); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-mono font-bold text-primary text-[12px]">
                    <?php echo html_escape($u->username); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-primary-container text-on-primary-container font-mono">
                      <?php echo html_escape($u->role_name); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap text-xs">
                    <span class="block text-on-surface font-mono"><?php echo html_escape($u->email); ?></span>
                    <span class="text-on-surface-variant"><?php echo html_escape($u->phone ?: 'N/A'); ?></span>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?php echo $stBadge; ?>">
                      <?php echo html_escape($u->status); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-mono text-[11px] text-on-surface">
                    <?php echo $u->last_login_at ? date('d M Y, h:i A', strtotime($u->last_login_at)) : 'Never'; ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1.5">
                      <a href="<?php echo site_url('users/details/' . $u->user_id); ?>" class="p-1 rounded hover:bg-surface-container-high text-primary font-semibold text-xs inline-flex items-center gap-1" title="View Profile">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                      </a>
                      <a href="<?php echo site_url('users/edit/' . $u->user_id); ?>" class="p-1 rounded hover:bg-surface-container-high text-secondary font-semibold text-xs inline-flex items-center gap-1" title="Edit User">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                      </a>
                      <?php if ($u->status === 'Locked'): ?>
                        <a href="<?php echo site_url('users/unlock/' . $u->user_id); ?>" class="p-1 rounded bg-error-container text-error font-semibold text-xs inline-flex items-center gap-1" title="Unlock Account">
                          <span class="material-symbols-outlined text-[16px]">lock_open</span>
                        </a>
                      <?php else: ?>
                        <a href="<?php echo site_url('users/toggle_status/' . $u->user_id); ?>" onclick="return confirm('Toggle active/inactive status for this user?');" class="p-1 rounded hover:bg-surface-container-high text-on-surface-variant font-semibold text-xs inline-flex items-center gap-1" title="Toggle Status">
                          <span class="material-symbols-outlined text-[16px]">power_settings_new</span>
                        </a>
                      <?php endif; ?>
                      <a href="<?php echo site_url('users/delete/' . $u->user_id); ?>" onclick="return confirm('Are you sure you want to delete user <?php echo html_escape($u->username); ?>?');" class="p-1 rounded hover:bg-error-container text-error font-semibold text-xs inline-flex items-center gap-1" title="Delete User">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
