<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth
 *
 * Ported from index.html (redirect-to-login) and login.html.
 * This is a prototype/UI-only auth flow, same as the original static
 * HTML: any submitted credentials are accepted. Wire up a real user
 * model + password check here before going to production.
 */
class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        } else {
            redirect('auth/login');
        }
    }

    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
            return;
        }

        if ($this->input->method() === 'post')
        {
            $this->form_validation->set_rules('email', 'Email Address or Username', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run() === TRUE)
            {
                $email = $this->input->post('email', TRUE);
                $password = $this->input->post('password');

                $user = $this->User_model->verify_credentials($email, $password);

                if ($user)
                {
                    // Generate initials
                    $parts = explode(' ', trim($user->name));
                    $initials = '';
                    foreach ($parts as $p) {
                        if (!empty($p)) $initials .= strtoupper($p[0]);
                    }
                    if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                    if (empty($initials)) $initials = 'U';

                    $this->session->set_userdata(array(
                        'logged_in'     => TRUE,
                        'user_id'       => $user->user_id,
                        'user_name'     => $user->name,
                        'user_email'    => $user->email,
                        'user_role'     => $user->role_name,
                        'user_initials' => $initials,
                        'user'          => array(
                            'name'     => $user->name,
                            'role'     => $user->role_name,
                            'email'    => $user->email,
                            'initials' => $initials,
                        ),
                    ));

                    redirect('dashboard');
                    return;
                }
                else
                {
                    $this->session->set_flashdata('error', 'Invalid email address or password.');
                }
            }
            else
            {
                $this->session->set_flashdata('error', validation_errors('', ''));
            }
        }

        $this->load->view('auth/login');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
