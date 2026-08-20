<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Communication extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Notice_model');
        $this->load->model('Announcement_model');
        $this->load->model('Communication_model');
        $this->load->model('Conversation_model');
        $this->load->model('Communication_group_model');
        $this->load->model('Communication_setting_model');
        $this->load->model('Academic_year_model');
        $this->load->model('Class_model');
        $this->load->model('Section_model');
        $this->load->model('Staff_model');
        $this->load->model('Student_model');
    }

    // 1. Communication Dashboard
    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $data['title'] = 'Communication Dashboard';
        $data['stats'] = $this->Communication_model->get_dashboard_stats();
        $data['recent_notices'] = $this->Notice_model->get_recent(5);
        $data['recent_announcements'] = $this->Announcement_model->get_recent(5);
        $data['recent_messages'] = $this->Communication_model->get_messages([], 6);

        $this->render('pages/communication/dashboard', $data);
    }

    // 2. Notices Directory
    public function notices()
    {
        $filters = [
            'category'    => $this->input->get('category') ?: NULL,
            'priority'    => $this->input->get('priority') ?: NULL,
            'target_role' => $this->input->get('target_role') ?: NULL,
            'status'      => $this->input->get('status') ?: NULL,
            'search'      => $this->input->get('search') ?: NULL,
        ];

        $data['title'] = 'Notices & Circulars';
        $data['filters'] = $filters;
        $data['notices'] = $this->Notice_model->get_all($filters);

        $this->render('pages/communication/notices', $data);
    }

    // 3. Create Notice
    public function create_notice()
    {
        if ($this->input->post()) {
            $attachment = NULL;
            if (!empty($_FILES['attachment']['name'])) {
                $upload_path = './uploads/notices/';
                if (!is_dir($upload_path)) mkdir($upload_path, 0777, TRUE);

                $config['upload_path']   = $upload_path;
                $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png|zip|txt';
                $config['max_size']      = 10240;
                $this->load->library('upload', $config);

                if ($this->upload->do_upload('attachment')) {
                    $upData = $this->upload->data();
                    $attachment = $upData['file_name'];
                }
            }

            $status = $this->input->post('submit_action') === 'publish' ? 'Published' : ($this->input->post('submit_action') === 'schedule' ? 'Scheduled' : 'Draft');

            $noticeData = [
                'title'        => trim($this->input->post('title')),
                'category'     => $this->input->post('category') ?: 'General',
                'content'      => trim($this->input->post('content')),
                'target_role'  => $this->input->post('target_role') ?: 'All',
                'target_type'  => $this->input->post('target_type') ?: 'Entire School',
                'class_id'     => $this->input->post('class_id') ?: NULL,
                'section_id'   => $this->input->post('section_id') ?: NULL,
                'priority'     => $this->input->post('priority') ?: 'Normal',
                'attachment'   => $attachment,
                'publish_date' => $this->input->post('publish_date') ?: date('Y-m-d'),
                'expiry_date'  => $this->input->post('expiry_date') ?: NULL,
                'posted_by'    => $this->session->userdata('full_name') ?: 'Admin',
                'posted_by_id' => $this->session->userdata('user_id') ?: 1,
                'status'       => $status,
            ];

            $this->Notice_model->insert($noticeData);
            $this->session->set_flashdata('success', "Notice '{$noticeData['title']}' created successfully (" . $status . ")!");
            redirect('communication/notices');
            return;
        }

        $data['title'] = 'Create Notice';
        $data['classes'] = $this->Class_model->get_all(TRUE);
        $this->render('pages/communication/create_notice', $data);
    }

    // 4. Announcements Directory
    public function announcements()
    {
        $filters = [
            'priority' => $this->input->get('priority') ?: NULL,
            'audience' => $this->input->get('audience') ?: NULL,
            'status'   => $this->input->get('status') ?: NULL,
            'search'   => $this->input->get('search') ?: NULL,
        ];

        $data['title'] = 'School Announcements';
        $data['filters'] = $filters;
        $data['announcements'] = $this->Announcement_model->get_all($filters);

        $this->render('pages/communication/announcements', $data);
    }

    // 5. Announcement Details
    public function announcement_details($id)
    {
        $announcement = $this->Announcement_model->get_by_id($id);
        if (!$announcement) {
            $this->session->set_flashdata('error', 'Announcement not found.');
            redirect('communication/announcements');
            return;
        }

        $data['title'] = 'Announcement Details';
        $data['announcement'] = $announcement;
        $this->render('pages/communication/announcement_details', $data);
    }

    // 6. SMS Campaign Console
    public function sms()
    {
        if ($this->input->post()) {
            $message = trim($this->input->post('message'));
            $recipient_type = $this->input->post('recipient_type') ?: 'All';
            $is_scheduled = !empty($this->input->post('schedule_time'));

            $msgData = [
                'channel'           => 'SMS',
                'recipient_type'    => $recipient_type,
                'recipient_name'    => $this->input->post('recipient_name') ?: $recipient_type,
                'recipient_contact' => $this->input->post('recipient_phone') ?: 'Bulk Recipients',
                'message'           => $message,
                'sender_id'         => $this->session->userdata('user_id') ?: 1,
                'scheduled_at'      => $is_scheduled ? $this->input->post('schedule_time') : NULL,
                'status'            => $is_scheduled ? 'Scheduled' : 'Sent',
            ];

            $this->Communication_model->dispatch_message($msgData);
            $this->session->set_flashdata('success', $is_scheduled ? 'SMS campaign scheduled successfully.' : 'SMS message dispatched to delivery queue.');
            redirect('communication/sms');
            return;
        }

        $data['title'] = 'SMS Notifications';
        $data['templates'] = $this->Communication_model->get_templates(['channel' => 'SMS']);
        $data['classes'] = $this->Class_model->get_all(TRUE);
        $data['history'] = $this->Communication_model->get_messages(['channel' => 'SMS'], 10);

        $this->render('pages/communication/sms', $data);
    }

    // 7. WhatsApp Notifications Console
    public function whatsapp()
    {
        if ($this->input->post()) {
            $message = trim($this->input->post('message'));
            $recipient_type = $this->input->post('recipient_type') ?: 'All';
            $is_scheduled = !empty($this->input->post('schedule_time'));

            $msgData = [
                'channel'           => 'WhatsApp',
                'recipient_type'    => $recipient_type,
                'recipient_name'    => $this->input->post('recipient_name') ?: $recipient_type,
                'recipient_contact' => $this->input->post('recipient_phone') ?: 'Bulk WhatsApp',
                'message'           => $message,
                'sender_id'         => $this->session->userdata('user_id') ?: 1,
                'scheduled_at'      => $is_scheduled ? $this->input->post('schedule_time') : NULL,
                'status'            => $is_scheduled ? 'Scheduled' : 'Sent',
            ];

            $this->Communication_model->dispatch_message($msgData);
            $this->session->set_flashdata('success', $is_scheduled ? 'WhatsApp message scheduled successfully.' : 'WhatsApp notification queued for delivery.');
            redirect('communication/whatsapp');
            return;
        }

        $data['title'] = 'WhatsApp Notifications';
        $data['templates'] = $this->Communication_model->get_templates(['channel' => 'WhatsApp']);
        $data['classes'] = $this->Class_model->get_all(TRUE);
        $data['history'] = $this->Communication_model->get_messages(['channel' => 'WhatsApp'], 10);

        $this->render('pages/communication/whatsapp', $data);
    }

    // 8. Email Notifications Composer
    public function email()
    {
        if ($this->input->post()) {
            $subject = trim($this->input->post('subject'));
            $message = trim($this->input->post('message'));
            $recipient_type = $this->input->post('recipient_type') ?: 'All';
            $is_scheduled = !empty($this->input->post('schedule_time'));

            $msgData = [
                'channel'           => 'Email',
                'recipient_type'    => $recipient_type,
                'recipient_name'    => $this->input->post('recipient_name') ?: $recipient_type,
                'recipient_contact' => $this->input->post('recipient_email') ?: 'Bulk Email List',
                'subject'           => $subject,
                'message'           => $message,
                'sender_id'         => $this->session->userdata('user_id') ?: 1,
                'scheduled_at'      => $is_scheduled ? $this->input->post('schedule_time') : NULL,
                'status'            => $is_scheduled ? 'Scheduled' : 'Sent',
            ];

            $this->Communication_model->dispatch_message($msgData);
            $this->session->set_flashdata('success', $is_scheduled ? 'Email newsletter scheduled successfully.' : 'Email queued and dispatched successfully.');
            redirect('communication/email');
            return;
        }

        $data['title'] = 'Email Notifications';
        $data['templates'] = $this->Communication_model->get_templates(['channel' => 'Email']);
        $data['classes'] = $this->Class_model->get_all(TRUE);
        $data['history'] = $this->Communication_model->get_messages(['channel' => 'Email'], 10);

        $this->render('pages/communication/email', $data);
    }

    // 9. Notification Templates
    public function templates()
    {
        if ($this->input->post()) {
            $template_id = (int)$this->input->post('template_id');
            $action = $this->input->post('action');

            if ($action === 'delete') {
                $this->Communication_model->delete_template($template_id);
                $this->session->set_flashdata('success', 'Notification template deleted.');
            } else {
                $postData = [
                    'template_name'      => trim($this->input->post('template_name')),
                    'communication_type' => $this->input->post('communication_type'),
                    'channel'            => $this->input->post('channel'),
                    'subject'            => trim($this->input->post('subject')),
                    'message_template'   => trim($this->input->post('message_template')),
                    'variables'          => trim($this->input->post('variables')),
                    'status'             => $this->input->post('status') ? 1 : 0
                ];

                if ($template_id > 0) {
                    $this->Communication_model->update_template($template_id, $postData);
                    $this->session->set_flashdata('success', 'Template updated successfully.');
                } else {
                    $this->Communication_model->insert_template($postData);
                    $this->session->set_flashdata('success', 'New notification template created.');
                }
            }
            redirect('communication/templates');
            return;
        }

        $data['title'] = 'Notification Templates';
        $data['templates'] = $this->Communication_model->get_templates();

        $this->render('pages/communication/templates', $data);
    }

    // 10. Scheduled Notifications Queue
    public function scheduled()
    {
        if ($this->input->get('cancel')) {
            $this->Communication_model->cancel_scheduled((int)$this->input->get('cancel'));
            $this->session->set_flashdata('success', 'Scheduled message cancelled.');
            redirect('communication/scheduled');
            return;
        }

        $data['title'] = 'Scheduled Notifications Queue';
        $data['scheduled'] = $this->Communication_model->get_scheduled_messages();

        $this->render('pages/communication/scheduled', $data);
    }

    // 11. Notification History
    public function history()
    {
        $filters = [
            'channel'       => $this->input->get('channel') ?: NULL,
            'status'        => $this->input->get('status') ?: NULL,
            'source_module' => $this->input->get('source_module') ?: NULL,
            'date_from'     => $this->input->get('date_from') ?: NULL,
            'date_to'       => $this->input->get('date_to') ?: NULL,
            'search'        => $this->input->get('search') ?: NULL,
        ];

        $data['title'] = 'Notification History';
        $data['filters'] = $filters;
        $data['messages'] = $this->Communication_model->get_messages($filters, 100);

        $this->render('pages/communication/history', $data);
    }

    // 12. Delivery Reports
    public function reports()
    {
        $filters = [
            'channel'   => $this->input->get('channel') ?: NULL,
            'date_from' => $this->input->get('date_from') ?: NULL,
            'date_to'   => $this->input->get('date_to') ?: NULL,
        ];

        $messages = $this->Communication_model->get_messages($filters, 500);

        // CSV Export
        if ($this->input->get('export') === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=delivery_report_' . date('Ymd_His') . '.csv');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Channel', 'Recipient', 'Contact', 'Subject', 'Message', 'Status', 'Sent Time', 'Delivered Time']);
            foreach ($messages as $m) {
                fputcsv($out, [
                    $m->message_id, $m->channel, $m->recipient_name, $m->recipient_contact,
                    $m->subject, substr(strip_tags($m->message), 0, 100), $m->status, $m->sent_at, $m->delivered_at
                ]);
            }
            fclose($out);
            exit;
        }

        $data['title'] = 'Delivery Reports';
        $data['stats'] = $this->Communication_model->get_dashboard_stats();
        $data['messages'] = $messages;
        $data['filters'] = $filters;

        $this->render('pages/communication/reports', $data);
    }

    // 13. Parent-Teacher Communication Desk
    public function parent_teacher()
    {
        if ($this->input->post()) {
            $student_id = (int)$this->input->post('student_id');
            $title = trim($this->input->post('title'));
            $first_msg = trim($this->input->post('message'));

            $conv_id = $this->Conversation_model->create_conversation(
                'Parent-Teacher',
                $title,
                $this->session->userdata('user_id') ?: 1,
                [['id' => $student_id, 'type' => 'Parent']]
            );

            if ($first_msg) {
                $this->Conversation_model->send_message($conv_id, $this->session->userdata('user_id') ?: 1, 'Staff', $first_msg);
            }

            $this->session->set_flashdata('success', 'Conversation started with parent.');
            redirect('communication/conversation_view/' . $conv_id);
            return;
        }

        $data['title'] = 'Parent-Teacher Communication';
        $data['conversations'] = $this->Conversation_model->get_conversations_for_user($this->session->userdata('user_id') ?: 1, 'Staff');
        $data['students'] = $this->Student_model->get_all(30);

        $this->render('pages/communication/parent_teacher', $data);
    }

    // 14. Internal Staff Messaging Inbox
    public function messages()
    {
        $data['title'] = 'Internal Staff Messaging';
        $data['conversations'] = $this->Conversation_model->get_conversations_for_user($this->session->userdata('user_id') ?: 1, 'Staff');
        $data['staff'] = $this->Staff_model->get_all();

        $this->render('pages/communication/messages', $data);
    }

    // 15. Conversations 2-Pane Chat View
    public function conversations()
    {
        $active_id = (int)$this->input->get('id');
        $convs = $this->Conversation_model->get_conversations_for_user($this->session->userdata('user_id') ?: 1, 'Staff');

        if (!$active_id && !empty($convs)) {
            $active_id = $convs[0]->conversation_id;
        }

        if ($active_id) {
            $this->Conversation_model->mark_as_read($active_id, $this->session->userdata('user_id') ?: 1, 'Staff');
            $data['active_conv'] = $this->Conversation_model->get_by_id($active_id);
            $data['active_messages'] = $this->Conversation_model->get_messages($active_id);
            $data['active_participants'] = $this->Conversation_model->get_participants($active_id);
        } else {
            $data['active_conv'] = null;
            $data['active_messages'] = [];
            $data['active_participants'] = [];
        }

        $data['title'] = 'Conversations & Chat';
        $data['conversations'] = $convs;
        $data['active_id'] = $active_id;
        $data['staff'] = $this->Staff_model->get_all();

        $this->render('pages/communication/conversations', $data);
    }

    // 16. Message Details / Single Thread
    public function conversation_view($id)
    {
        $conv = $this->Conversation_model->get_by_id($id);
        if (!$conv) {
            $this->session->set_flashdata('error', 'Conversation not found.');
            redirect('communication/conversations');
            return;
        }

        if ($this->input->post()) {
            $text = trim($this->input->post('message_text'));
            if ($text) {
                $this->Conversation_model->send_message($id, $this->session->userdata('user_id') ?: 1, 'Staff', $text);
            }
            redirect('communication/conversation_view/' . $id);
            return;
        }

        $this->Conversation_model->mark_as_read($id, $this->session->userdata('user_id') ?: 1, 'Staff');

        $data['title'] = html_escape($conv->title ?: 'Conversation Thread');
        $data['conv'] = $conv;
        $data['messages'] = $this->Conversation_model->get_messages($id);
        $data['participants'] = $this->Conversation_model->get_participants($id);

        $this->render('pages/communication/conversation_view', $data);
    }

    // 17. Communication Groups
    public function groups()
    {
        if ($this->input->post()) {
            $group_id = (int)$this->input->post('group_id');
            $action = $this->input->post('action');

            if ($action === 'delete') {
                $this->Communication_group_model->delete($group_id);
                $this->session->set_flashdata('success', 'Communication group removed.');
            } else {
                $postData = [
                    'group_name'  => trim($this->input->post('group_name')),
                    'group_type'  => $this->input->post('group_type'),
                    'description' => trim($this->input->post('description')),
                    'status'      => $this->input->post('status') ? 1 : 0
                ];

                if ($group_id > 0) {
                    $this->Communication_group_model->update($group_id, $postData);
                    $this->session->set_flashdata('success', 'Group updated.');
                } else {
                    $postData['created_by'] = $this->session->userdata('user_id') ?: 1;
                    $this->Communication_group_model->insert($postData);
                    $this->session->set_flashdata('success', 'New communication group created.');
                }
            }
            redirect('communication/groups');
            return;
        }

        $data['title'] = 'Communication Groups';
        $data['groups'] = $this->Communication_group_model->get_all();

        $this->render('pages/communication/groups', $data);
    }

    // 18. Communication Settings
    public function settings()
    {
        if ($this->input->post()) {
            $postData = [
                'enable_inapp'                    => $this->input->post('enable_inapp') ? 1 : 0,
                'enable_sms'                      => $this->input->post('enable_sms') ? 1 : 0,
                'enable_whatsapp'                 => $this->input->post('enable_whatsapp') ? 1 : 0,
                'enable_email'                    => $this->input->post('enable_email') ? 1 : 0,
                'sms_provider'                    => trim($this->input->post('sms_provider')),
                'sms_sender_id'                   => trim($this->input->post('sms_sender_id')),
                'whatsapp_provider'               => trim($this->input->post('whatsapp_provider')),
                'email_from_name'                 => trim($this->input->post('email_from_name')),
                'email_from_address'              => trim($this->input->post('email_from_address')),
                'enable_scheduled_jobs'           => $this->input->post('enable_scheduled_jobs') ? 1 : 0,
                'max_retries'                     => (int)$this->input->post('max_retries'),
                'retry_interval_minutes'          => (int)$this->input->post('retry_interval_minutes'),
                'parent_teacher_direct_messaging' => $this->input->post('parent_teacher_direct_messaging') ? 1 : 0
            ];

            $this->Communication_setting_model->update_settings($postData);
            $this->session->set_flashdata('success', 'Communication settings updated successfully.');
            redirect('communication/settings');
            return;
        }

        $data['title'] = 'Communication Settings';
        $data['settings'] = $this->Communication_setting_model->get_settings();
        $data['audit_logs'] = $this->Communication_setting_model->get_audit_logs(25);

        $this->render('pages/communication/settings', $data);
    }

    // Action Helpers
    public function delete_notice($id)
    {
        $this->Notice_model->delete($id);
        $this->session->set_flashdata('success', 'Notice deleted.');
        redirect('communication/notices');
    }

    public function archive_notice($id)
    {
        $this->Notice_model->archive($id);
        $this->session->set_flashdata('success', 'Notice archived.');
        redirect('communication/notices');
    }

    public function delete_announcement($id)
    {
        $this->Announcement_model->delete($id);
        $this->session->set_flashdata('success', 'Announcement deleted.');
        redirect('communication/announcements');
    }
}
