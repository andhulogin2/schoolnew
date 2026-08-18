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
        <h2 class="font-headline-md text-headline-md text-on-surface">Academic Calendar & Events</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Schedule school terms, examinations, national holidays, vacations, and academic meetings.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button onclick="openAddEventModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Add Event / Holiday
        </button>
      </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-primary-fixed/30 text-primary flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[22px]">calendar_month</span>
          </div>
          <div>
            <div class="text-[12px] text-on-surface-variant font-medium">Total Events</div>
            <div class="text-[20px] font-bold text-on-surface"><?php echo count($events); ?></div>
          </div>
        </div>
      </div>
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-error-container/40 text-error flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[22px]">beach_access</span>
          </div>
          <div>
            <div class="text-[12px] text-on-surface-variant font-medium">Holidays & Breaks</div>
            <div class="text-[20px] font-bold text-on-surface">
              <?php
                $holidays = array_filter($events, function($e) { return in_array($e->event_type, array('Holiday', 'Term Break')); });
                echo count($holidays);
              ?>
            </div>
          </div>
        </div>
      </div>
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[22px]">quiz</span>
          </div>
          <div>
            <div class="text-[12px] text-on-surface-variant font-medium">Exams & Tests</div>
            <div class="text-[20px] font-bold text-on-surface">
              <?php
                $exams = array_filter($events, function($e) { return $e->event_type === 'Exam'; });
                echo count($exams);
              ?>
            </div>
          </div>
        </div>
      </div>
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[22px]">groups</span>
          </div>
          <div>
            <div class="text-[12px] text-on-surface-variant font-medium">Upcoming in 30 Days</div>
            <div class="text-[20px] font-bold text-on-surface"><?php echo count($upcoming); ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters & View Toggle Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
          <select id="filter_year" onchange="applyFilters()" class="px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md">
            <?php foreach ($years as $yr): ?>
              <option value="<?php echo $yr->academic_year_id; ?>" <?php echo ($selected_year == $yr->academic_year_id) ? 'selected' : ''; ?>><?php echo html_escape($yr->year_name); ?></option>
            <?php endforeach; ?>
          </select>
          <select id="filter_type" onchange="applyFilters()" class="px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md">
            <option value="">All Event Types</option>
            <option value="Holiday" <?php echo ($selected_type === 'Holiday') ? 'selected' : ''; ?>>Holiday</option>
            <option value="Exam" <?php echo ($selected_type === 'Exam') ? 'selected' : ''; ?>>Exam</option>
            <option value="Event" <?php echo ($selected_type === 'Event') ? 'selected' : ''; ?>>Event</option>
            <option value="Activity" <?php echo ($selected_type === 'Activity') ? 'selected' : ''; ?>>Activity</option>
            <option value="Meeting" <?php echo ($selected_type === 'Meeting') ? 'selected' : ''; ?>>Meeting</option>
            <option value="Term Break" <?php echo ($selected_type === 'Term Break') ? 'selected' : ''; ?>>Term Break</option>
            <option value="Other" <?php echo ($selected_type === 'Other') ? 'selected' : ''; ?>>Other</option>
          </select>
          <a href="<?php echo site_url('academics/calendar'); ?>" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high text-body-md transition-colors">
            <span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset
          </a>
        </div>

        <!-- View Mode Switcher -->
        <div class="flex items-center p-1 rounded-lg bg-surface-container-high border border-outline-variant/40 self-start md:self-auto">
          <button type="button" id="btn-view-list" onclick="setViewMode('list')" class="px-3 py-1.5 rounded-md text-label-md transition-all flex items-center gap-1.5 cursor-pointer bg-surface-container-lowest text-primary shadow-xs font-semibold">
            <span class="material-symbols-outlined text-[18px]">list</span>List Agenda
          </button>
          <button type="button" id="btn-view-grid" onclick="setViewMode('grid')" class="px-3 py-1.5 rounded-md text-label-md transition-all flex items-center gap-1.5 cursor-pointer text-on-surface-variant hover:text-on-surface">
            <span class="material-symbols-outlined text-[18px]">calendar_view_month</span>Monthly Grid
          </button>
        </div>
      </div>
    </div>

    <!-- 1. List / Agenda View -->
    <div id="view-list" class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Date & Duration</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Event Title</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Category / Type</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Audience</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Venue</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30 text-body-md">
            <?php if (empty($events)): ?>
              <tr>
                <td colspan="6" class="px-4 py-12 text-center text-on-surface-variant">
                  <div class="flex flex-col items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[40px] text-outline">event_busy</span>
                    <p class="font-medium text-[15px]">No academic calendar events found for this filter.</p>
                    <button onclick="openAddEventModal()" class="mt-2 text-primary hover:underline text-body-md font-semibold">+ Add your first event</button>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
            <?php foreach ($events as $evt): ?>
              <?php
                // Badge color styles
                $typeBadge = 'bg-surface-container-high text-on-surface';
                if ($evt->event_type === 'Holiday') $typeBadge = 'bg-red-100 text-red-700 font-semibold';
                if ($evt->event_type === 'Exam') $typeBadge = 'bg-purple-100 text-purple-700 font-semibold';
                if ($evt->event_type === 'Event') $typeBadge = 'bg-blue-100 text-blue-700 font-semibold';
                if ($evt->event_type === 'Activity') $typeBadge = 'bg-emerald-100 text-emerald-700 font-semibold';
                if ($evt->event_type === 'Meeting') $typeBadge = 'bg-amber-100 text-amber-800 font-semibold';
                if ($evt->event_type === 'Term Break') $typeBadge = 'bg-orange-100 text-orange-700 font-semibold';
              ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 font-semibold text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-surface-container-high flex flex-col items-center justify-center shrink-0 border border-outline-variant/40">
                      <span class="text-[10px] font-bold uppercase text-on-surface-variant leading-none"><?php echo date('M', strtotime($evt->start_date)); ?></span>
                      <span class="text-[13px] font-extrabold text-primary leading-none mt-0.5"><?php echo date('d', strtotime($evt->start_date)); ?></span>
                    </div>
                    <div>
                      <div class="text-[13px] text-on-surface">
                        <?php echo date('D, d M Y', strtotime($evt->start_date)); ?>
                        <?php if ($evt->end_date && $evt->end_date !== $evt->start_date): ?>
                          <span class="text-on-surface-variant font-normal text-[12px]">to <?php echo date('d M Y', strtotime($evt->end_date)); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 font-semibold text-on-surface">
                  <div><?php echo html_escape($evt->title); ?></div>
                  <?php if (!empty($evt->description)): ?>
                    <div class="text-[12px] text-on-surface-variant font-normal mt-0.5 line-clamp-1"><?php echo html_escape($evt->description); ?></div>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] <?php echo $typeBadge; ?>">
                    <?php echo html_escape($evt->event_type); ?>
                  </span>
                </td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap">
                  <span class="inline-flex items-center gap-1 text-[12px] font-medium text-on-surface-variant">
                    <span class="material-symbols-outlined text-[14px]">group</span>
                    <?php echo html_escape($evt->audience); ?>
                  </span>
                </td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap">
                  <?php if ($evt->venue): ?>
                    <span class="inline-flex items-center gap-1 text-[12px] text-on-surface">
                      <span class="material-symbols-outlined text-[14px] text-on-surface-variant">location_on</span>
                      <?php echo html_escape($evt->venue); ?>
                    </span>
                  <?php else: ?>
                    <span class="text-on-surface-variant text-[12px]">—</span>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <button onclick="openEditEventModal(<?php echo $evt->calendar_id; ?>)" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors cursor-pointer" title="Edit Event"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                    <a href="<?php echo site_url('academics/delete_calendar_event/' . $evt->calendar_id); ?>" onclick="return confirm('Remove this calendar event?')" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container/20 hover:text-error transition-colors" title="Delete Event"><span class="material-symbols-outlined text-[18px]">delete</span></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. Monthly Visual Calendar Grid View (Initially Hidden or Toggled) -->
    <div id="view-grid" class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6 hidden">
      <?php
        $curMonth = $selected_month;
        $curYear = $selected_cal_year;
        $firstDayOfMonth = mktime(0, 0, 0, $curMonth, 1, $curYear);
        $numberDays = date('t', $firstDayOfMonth);
        $dateComponents = getdate($firstDayOfMonth);
        $monthName = $dateComponents['month'];
        $dayOfWeek = $dateComponents['wday']; // 0 for Sunday
      ?>
      <div class="flex items-center justify-between mb-4 pb-3 border-b border-outline-variant/40">
        <h3 class="text-headline-md font-bold text-on-surface flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">calendar_month</span>
          <?php echo $monthName . ' ' . $curYear; ?>
        </h3>
        <div class="flex items-center gap-2">
          <?php
            $prevMonth = ($curMonth == 1) ? 12 : $curMonth - 1;
            $prevYear = ($curMonth == 1) ? $curYear - 1 : $curYear;
            $nextMonth = ($curMonth == 12) ? 1 : $curMonth + 1;
            $nextYear = ($curMonth == 12) ? $curYear + 1 : $curYear;
          ?>
          <a href="<?php echo site_url('academics/calendar?academic_year_id=' . $selected_year . '&month=' . $prevMonth . '&year=' . $prevYear); ?>" class="p-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">chevron_left</span></a>
          <a href="<?php echo site_url('academics/calendar?academic_year_id=' . $selected_year . '&month=' . date('n') . '&year=' . date('Y')); ?>" class="px-2.5 py-1 text-[12px] font-semibold rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors">Today</a>
          <a href="<?php echo site_url('academics/calendar?academic_year_id=' . $selected_year . '&month=' . $nextMonth . '&year=' . $nextYear); ?>" class="p-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">chevron_right</span></a>
        </div>
      </div>

      <!-- Calendar Weekday Header -->
      <div class="grid grid-cols-7 gap-1 text-center font-bold text-label-md text-on-surface-variant mb-2">
        <div class="py-2 bg-surface-container-low rounded-lg text-red-600">Sun</div>
        <div class="py-2 bg-surface-container-low rounded-lg">Mon</div>
        <div class="py-2 bg-surface-container-low rounded-lg">Tue</div>
        <div class="py-2 bg-surface-container-low rounded-lg">Wed</div>
        <div class="py-2 bg-surface-container-low rounded-lg">Thu</div>
        <div class="py-2 bg-surface-container-low rounded-lg">Fri</div>
        <div class="py-2 bg-surface-container-low rounded-lg">Sat</div>
      </div>

      <!-- Calendar Days Grid -->
      <div class="grid grid-cols-7 gap-1">
        <?php
          // Empty cells before first day
          for ($i = 0; $i < $dayOfWeek; $i++) {
            echo '<div class="min-h-[90px] p-2 bg-surface-container-low/20 rounded-lg border border-outline-variant/20 opacity-40"></div>';
          }

          // Days of the month
          for ($day = 1; $day <= $numberDays; $day++) {
            $currentDateStr = sprintf('%04d-%02d-%02d', $curYear, $curMonth, $day);
            $isToday = ($currentDateStr === date('Y-m-d'));

            // Find matching events for this day
            $dayEvents = array();
            foreach ($events as $ev) {
              if ($currentDateStr >= $ev->start_date && $currentDateStr <= ($ev->end_date ?: $ev->start_date)) {
                $dayEvents[] = $ev;
              }
            }

            echo '<div class="min-h-[90px] p-2 rounded-lg border border-outline-variant/40 ' . ($isToday ? 'bg-primary-fixed/20 border-primary/40 font-bold' : 'bg-surface-container-lowest') . ' flex flex-col justify-between">';
            echo '  <div class="flex items-center justify-between">';
            echo '    <span class="text-[13px] ' . ($isToday ? 'w-6 h-6 rounded-full bg-primary text-on-primary flex items-center justify-center' : 'text-on-surface font-semibold') . '">' . $day . '</span>';
            echo '  </div>';
            echo '  <div class="space-y-1 mt-1 flex-1 overflow-hidden">';
            foreach ($dayEvents as $dev) {
              $chipClass = 'bg-blue-100 text-blue-800';
              if ($dev->event_type === 'Holiday') $chipClass = 'bg-red-100 text-red-800';
              if ($dev->event_type === 'Exam') $chipClass = 'bg-purple-100 text-purple-800';
              if ($dev->event_type === 'Activity') $chipClass = 'bg-emerald-100 text-emerald-800';
              if ($dev->event_type === 'Meeting') $chipClass = 'bg-amber-100 text-amber-900';
              if ($dev->event_type === 'Term Break') $chipClass = 'bg-orange-100 text-orange-800';

              echo '    <div onclick="openEditEventModal(' . $dev->calendar_id . ')" class="text-[10px] px-1.5 py-0.5 rounded ' . $chipClass . ' truncate font-medium cursor-pointer" title="' . html_escape($dev->title) . '">';
              echo html_escape($dev->title);
              echo '    </div>';
            }
            echo '  </div>';
            echo '</div>';
          }
        ?>
      </div>
    </div>

    <!-- Modal: Add / Edit Academic Calendar Event -->
    <div id="modal-event" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface" id="modal-event-title">Add Calendar Event / Holiday</h3>
          <button onclick="document.getElementById('modal-event').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high cursor-pointer"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open('academics/calendar', array('class' => 'p-6 space-y-4')); ?>
          <input type="hidden" name="action" id="event_action" value="add"/>
          <input type="hidden" name="calendar_id" id="modal_calendar_id"/>

          <div>
            <label class="block text-label-md mb-1">Academic Session *</label>
            <select name="academic_year_id" id="modal_event_year" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <?php foreach ($years as $yr): ?>
                <option value="<?php echo $yr->academic_year_id; ?>"><?php echo html_escape($yr->year_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-label-md mb-1">Event / Holiday Title *</label>
            <input type="text" name="title" id="modal_event_title" required placeholder="e.g. Republic Day, Annual Sports Meet, Mid-Term Exam" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-label-md mb-1">Category / Type *</label>
              <select name="event_type" id="modal_event_type" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <option value="Holiday">Holiday</option>
                <option value="Exam">Exam / Assessment</option>
                <option value="Event">Event / Celebration</option>
                <option value="Activity">Activity / Sports</option>
                <option value="Meeting">Meeting / PTM</option>
                <option value="Term Break">Term Break / Vacation</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div>
              <label class="block text-label-md mb-1">Audience *</label>
              <select name="audience" id="modal_event_audience" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <option value="Whole School">Whole School</option>
                <option value="Students">Students Only</option>
                <option value="Teachers">Teachers Only</option>
                <option value="Parents">Parents & Guardians</option>
                <option value="Staff">All Staff</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-label-md mb-1">Start Date *</label>
              <input type="date" name="start_date" id="modal_event_start_date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md mb-1">End Date</label>
              <input type="date" name="end_date" id="modal_event_end_date" value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
          </div>

          <div>
            <label class="block text-label-md mb-1">Venue / Location</label>
            <input type="text" name="venue" id="modal_event_venue" placeholder="e.g. Auditorium, Main Ground, Online" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
          </div>

          <div>
            <label class="block text-label-md mb-1">Description / Notes</label>
            <textarea name="description" id="modal_event_description" rows="2" placeholder="Provide event schedule details, instructions, or agenda notes..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-event').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant cursor-pointer">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant cursor-pointer">Save Event</button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function applyFilters() {
        var yr = document.getElementById('filter_year').value;
        var type = document.getElementById('filter_type').value;
        window.location.href = '<?php echo site_url('academics/calendar'); ?>?academic_year_id=' + yr + '&event_type=' + encodeURIComponent(type);
      }

      function setViewMode(mode) {
        var listV = document.getElementById('view-list');
        var gridV = document.getElementById('view-grid');
        var btnList = document.getElementById('btn-view-list');
        var btnGrid = document.getElementById('btn-view-grid');

        if (mode === 'grid') {
          listV.classList.add('hidden');
          gridV.classList.remove('hidden');
          btnGrid.className = "px-3 py-1.5 rounded-md text-label-md transition-all flex items-center gap-1.5 cursor-pointer bg-surface-container-lowest text-primary shadow-xs font-semibold";
          btnList.className = "px-3 py-1.5 rounded-md text-label-md transition-all flex items-center gap-1.5 cursor-pointer text-on-surface-variant hover:text-on-surface";
        } else {
          gridV.classList.add('hidden');
          listV.classList.remove('hidden');
          btnList.className = "px-3 py-1.5 rounded-md text-label-md transition-all flex items-center gap-1.5 cursor-pointer bg-surface-container-lowest text-primary shadow-xs font-semibold";
          btnGrid.className = "px-3 py-1.5 rounded-md text-label-md transition-all flex items-center gap-1.5 cursor-pointer text-on-surface-variant hover:text-on-surface";
        }
      }

      function openAddEventModal() {
        document.getElementById('event_action').value = 'add';
        document.getElementById('modal_calendar_id').value = '';
        document.getElementById('modal-event-title').innerText = 'Add Calendar Event / Holiday';
        document.getElementById('modal_event_title').value = '';
        document.getElementById('modal_event_type').value = 'Holiday';
        document.getElementById('modal_event_audience').value = 'Whole School';
        document.getElementById('modal_event_start_date').value = '<?php echo date('Y-m-d'); ?>';
        document.getElementById('modal_event_end_date').value = '<?php echo date('Y-m-d'); ?>';
        document.getElementById('modal_event_venue').value = '';
        document.getElementById('modal_event_description').value = '';
        document.getElementById('modal-event').classList.remove('hidden');
      }

      function openEditEventModal(id) {
        fetch('<?php echo site_url('academics/ajax_get_calendar_event/'); ?>' + id)
          .then(res => res.json())
          .then(data => {
            if (data.success && data.event) {
              var ev = data.event;
              document.getElementById('event_action').value = 'edit';
              document.getElementById('modal_calendar_id').value = ev.calendar_id;
              document.getElementById('modal-event-title').innerText = 'Edit Calendar Event / Holiday';
              document.getElementById('modal_event_year').value = ev.academic_year_id;
              document.getElementById('modal_event_title').value = ev.title;
              document.getElementById('modal_event_type').value = ev.event_type;
              document.getElementById('modal_event_audience').value = ev.audience;
              document.getElementById('modal_event_start_date').value = ev.start_date;
              document.getElementById('modal_event_end_date').value = ev.end_date || ev.start_date;
              document.getElementById('modal_event_venue').value = ev.venue || '';
              document.getElementById('modal_event_description').value = ev.description || '';
              document.getElementById('modal-event').classList.remove('hidden');
            }
          });
      }
    </script>
